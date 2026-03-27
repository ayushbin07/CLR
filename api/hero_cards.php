<?php
// api/hero_cards.php
require_once __DIR__ . '/../includes/config.php';
requireAuth();

$action = $_GET['action'] ?? 'list';

ensureHeroCardsLinkColumn();

match ($action) {
    'list'   => listCards(),
    'save'   => saveCard(),
    'delete' => deleteCard(),
    default  => jsonResponse(['error' => 'Unknown action'], 400),
};

function listCards(): void {
    $includeInactive = isset($_GET['all']) && $_GET['all'] === '1';
    if ($includeInactive) { requireAdmin(); }

    $sql = 'SELECT id, title, subtitle, link, image_url, sort_order, is_active FROM hero_cards';
    if (!$includeInactive) {
        $sql .= ' WHERE is_active = 1';
    }
    $sql .= ' ORDER BY sort_order ASC, id ASC';

    $stmt = db()->query($sql);
    jsonResponse($stmt->fetchAll());
}

function saveCard(): void {
    requireAdmin();
    verifyCsrf();

    $id        = (int)($_POST['id'] ?? 0);
    $title     = trim($_POST['title'] ?? '');
    $subtitle  = trim($_POST['subtitle'] ?? '');
    $link      = trim($_POST['link'] ?? '') ?: null;
    $imageUrl  = trim($_POST['image_url'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 1);
    $isActive  = isset($_POST['is_active']) ? (int)!!$_POST['is_active'] : 1;

    if ($title === '' || $subtitle === '' || $imageUrl === '') {
        jsonResponse(['error' => 'Title, subtitle, and image URL are required'], 422);
    }

    if ($sortOrder <= 0) $sortOrder = 1;

    if ($id) {
        $stmt = db()->prepare(
            'UPDATE hero_cards
             SET title = ?, subtitle = ?, link = ?, image_url = ?, sort_order = ?, is_active = ?
             WHERE id = ?'
        );
        $stmt->execute([$title, $subtitle, $link, $imageUrl, $sortOrder, $isActive, $id]);
    } else {
        $stmt = db()->prepare(
            'INSERT INTO hero_cards (title, subtitle, link, image_url, sort_order, is_active)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$title, $subtitle, $link, $imageUrl, $sortOrder, $isActive]);
        $id = (int)db()->lastInsertId();
    }

    jsonResponse(['success' => true, 'id' => $id]);
}

// -------------------------------------------------
function ensureHeroCardsLinkColumn(): void {
    static $checked = false;
    if ($checked) return;
    $exists = db()->query("SHOW COLUMNS FROM hero_cards LIKE 'link'")->fetch();
    if (!$exists) {
        db()->exec("ALTER TABLE hero_cards ADD COLUMN link TEXT NULL AFTER subtitle");
    }
    $checked = true;
}

function deleteCard(): void {
    requireAdmin();
    verifyCsrf();

    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        jsonResponse(['error' => 'Missing id'], 422);
    }

    $stmt = db()->prepare('DELETE FROM hero_cards WHERE id = ?');
    $stmt->execute([$id]);

    jsonResponse(['success' => true]);
}
