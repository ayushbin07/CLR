<?php
// api/assignments.php
require_once __DIR__ . '/../includes/config.php';
requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

match (true) {
    $method === 'GET'  && $action === 'list'   => listAssignments(),
    $method === 'POST' && $action === 'create' => createAssignment(),
    $method === 'POST' && $action === 'update' => updateAssignment(),
    $method === 'POST' && $action === 'delete' => deleteAssignment(),
    $method === 'POST' && $action === 'toggle' => toggleStatus(),
    default => jsonResponse(['error' => 'Unknown action'], 400),
};

// -----------------------------------------------
function listAssignments(): void {
    $user     = auth();
    $filter   = $_GET['filter'] ?? 'all';   // all | pending | completed
    $classId  = $user['class_id'];

    $where = 'WHERE (a.visibility = "public" OR a.class_id = ?) ';
    $params = [$classId];

    if ($filter === 'pending') {
        $where .= 'AND a.status = "pending" ';
    } elseif ($filter === 'completed') {
        $where .= 'AND a.status = "completed" ';
    }

    $sql = "SELECT a.*, u.name AS creator_name
            FROM assignments a
            JOIN users u ON u.id = a.created_by
            {$where}
            ORDER BY a.deadline ASC";

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    jsonResponse($stmt->fetchAll());
}

// -----------------------------------------------
function createAssignment(): void {
    verifyCsrf();
    $user = auth();

    $title      = trim($_POST['title'] ?? '');
    $subject    = trim($_POST['subject'] ?? '');
    $deadline   = trim($_POST['deadline'] ?? '');
    $type       = $_POST['type'] ?? 'assignment';
    $link       = trim($_POST['link'] ?? '') ?: null;
    $visibility = $_POST['visibility'] ?? 'class';

    if (!$title || !$deadline) {
        jsonResponse(['error' => 'Title and deadline are required'], 422);
    }

    $validTypes = ['assignment', 'presentation', 'project'];
    if (!in_array($type, $validTypes, true)) $type = 'assignment';
    if (!in_array($visibility, ['public', 'class'], true)) $visibility = 'class';

    $stmt = db()->prepare(
        'INSERT INTO assignments (title, subject, deadline, type, link, visibility, class_id, created_by)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $title, $subject, $deadline, $type, $link,
        $visibility, $user['class_id'], $user['id'],
    ]);

    jsonResponse(['success' => true, 'id' => db()->lastInsertId()]);
}

// -----------------------------------------------
function updateAssignment(): void {
    verifyCsrf();
    $user = auth();
    $id   = (int)($_POST['id'] ?? 0);

    $stmt = db()->prepare('SELECT * FROM assignments WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if (!$row) jsonResponse(['error' => 'Not found'], 404);
    if ($row['created_by'] !== $user['id'] && $user['role'] !== 'admin') {
        jsonResponse(['error' => 'Forbidden'], 403);
    }

    $title      = trim($_POST['title']    ?? $row['title']);
    $subject    = trim($_POST['subject']  ?? $row['subject']);
    $deadline   = trim($_POST['deadline'] ?? $row['deadline']);
    $type       = $_POST['type']          ?? $row['type'];
    $link       = trim($_POST['link']     ?? '') ?: null;
    $visibility = $_POST['visibility']    ?? $row['visibility'];

    $stmt = db()->prepare(
        'UPDATE assignments SET title=?, subject=?, deadline=?, type=?, link=?, visibility=? WHERE id=?'
    );
    $stmt->execute([$title, $subject, $deadline, $type, $link, $visibility, $id]);
    jsonResponse(['success' => true]);
}

// -----------------------------------------------
function deleteAssignment(): void {
    verifyCsrf();
    $user = auth();
    $id   = (int)($_POST['id'] ?? 0);

    $stmt = db()->prepare('SELECT created_by FROM assignments WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if (!$row) jsonResponse(['error' => 'Not found'], 404);
    if ($row['created_by'] !== $user['id'] && $user['role'] !== 'admin') {
        jsonResponse(['error' => 'Forbidden'], 403);
    }

    db()->prepare('DELETE FROM assignments WHERE id = ?')->execute([$id]);
    jsonResponse(['success' => true]);
}

// -----------------------------------------------
function toggleStatus(): void {
    verifyCsrf();
    $user = auth();
    $id   = (int)($_POST['id'] ?? 0);

    $stmt = db()->prepare('SELECT * FROM assignments WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if (!$row) jsonResponse(['error' => 'Not found'], 404);
    if ($row['created_by'] !== $user['id'] && $user['role'] !== 'admin') {
        jsonResponse(['error' => 'Forbidden'], 403);
    }

    $newStatus = $row['status'] === 'pending' ? 'completed' : 'pending';
    db()->prepare('UPDATE assignments SET status = ? WHERE id = ?')->execute([$newStatus, $id]);
    jsonResponse(['success' => true, 'status' => $newStatus]);
}
