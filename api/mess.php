<?php
// api/mess.php
require_once __DIR__ . '/../includes/config.php';
requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

match (true) {
    $method === 'GET'  && $action === 'today'  => todayMenu(),
    $method === 'GET'  && $action === 'all'    => allMenu(),
    $method === 'POST' && $action === 'react'  => reactMenu(),
    $method === 'POST' && $action === 'menu'   => saveMenu(),
    $method === 'POST' && $action === 'import' => importMenu(),
    default => jsonResponse(['error' => 'Unknown action'], 400),
};

function todayMenu(): void {
    $userId = auth()['id'];
    $today = date('Y-m-d');
    $sql = "SELECT mm.*,
            (SELECT COUNT(*) FROM mess_reactions mr WHERE mr.mess_id = mm.id AND mr.reaction = 'like')    AS likes,
            (SELECT COUNT(*) FROM mess_reactions mr WHERE mr.mess_id = mm.id AND mr.reaction = 'dislike') AS dislikes,
            (SELECT mr.reaction FROM mess_reactions mr WHERE mr.mess_id = mm.id AND mr.user_id = ? LIMIT 1) AS my_reaction
        FROM mess_menu mm
        WHERE mm.date = ?
        ORDER BY FIELD(meal_type, 'breakfast','lunch','lunch_international','snacks','dinner')";
    $stmt = db()->prepare($sql);
    $stmt->execute([$userId, $today]);
    jsonResponse($stmt->fetchAll());
}

function allMenu(): void {
    $userId = auth()['id'];
    $sql = "SELECT mm.*,
            (SELECT COUNT(*) FROM mess_reactions mr WHERE mr.mess_id = mm.id AND mr.reaction = 'like')    AS likes,
            (SELECT COUNT(*) FROM mess_reactions mr WHERE mr.mess_id = mm.id AND mr.reaction = 'dislike') AS dislikes,
            (SELECT mr.reaction FROM mess_reactions mr WHERE mr.mess_id = mm.id AND mr.user_id = ? LIMIT 1) AS my_reaction
        FROM mess_menu mm
        ORDER BY mm.date DESC, FIELD(meal_type, 'breakfast','lunch','lunch_international','snacks','dinner')";
    $stmt = db()->prepare($sql);
    $stmt->execute([$userId]);
    jsonResponse($stmt->fetchAll());
}

function reactMenu(): void {
    verifyCsrf();
    $userId   = auth()['id'];
    $messId   = (int)($_POST['mess_id'] ?? 0);
    $reaction = $_POST['reaction'] ?? '';

    if (!in_array($reaction, ['like','dislike'], true)) {
        jsonResponse(['error' => 'Invalid reaction'], 422);
    }

    $exists = db()->prepare('SELECT id FROM mess_menu WHERE id = ?');
    $exists->execute([$messId]);
    if (!$exists->fetchColumn()) jsonResponse(['error' => 'Menu not found'], 404);

    $stmt = db()->prepare(
        'INSERT INTO mess_reactions (user_id, mess_id, reaction) VALUES (?, ?, ?) 
         ON DUPLICATE KEY UPDATE reaction = VALUES(reaction)'
    );
    $stmt->execute([$userId, $messId, $reaction]);
    jsonResponse(['success' => true]);
}

function saveMenu(): void {
    verifyCsrf();
    requireAdmin();

    $date      = $_POST['date'] ?? date('Y-m-d');
    $mealType  = $_POST['meal_type'] ?? '';
    $items     = trim($_POST['items'] ?? '');

    if (!in_array($mealType, ['breakfast','lunch','lunch_international','snacks','dinner'], true)) {
        jsonResponse(['error' => 'Invalid meal type'], 422);
    }
    if (!$items) jsonResponse(['error' => 'Items required'], 422);

    $stmt = db()->prepare(
        'INSERT INTO mess_menu (date, meal_type, items) VALUES (?, ?, ?) 
         ON DUPLICATE KEY UPDATE items = VALUES(items)'
    );
    $stmt->execute([$date, $mealType, $items]);
    jsonResponse(['success' => true]);
}

function importMenu(): void {
    verifyCsrf();
    requireAdmin();

    $payload = $_POST['menus'] ?? '[]';
    $data = json_decode($payload, true);
    if (!is_array($data)) {
        jsonResponse(['error' => 'Invalid JSON'], 422);
    }

    $stmt = db()->prepare(
        'INSERT INTO mess_menu (date, meal_type, items) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE items = VALUES(items)'
    );

    $count = 0;
    foreach ($data as $row) {
        $date     = $row['date'] ?? null;
        $mealType = $row['meal_type'] ?? '';
        $items    = trim($row['items'] ?? '');
        if (!$date || !in_array($mealType, ['breakfast','lunch','lunch_international','snacks','dinner'], true) || !$items) {
            continue;
        }
        $stmt->execute([$date, $mealType, $items]);
        $count++;
    }

    jsonResponse(['success' => true, 'count' => $count]);
}
