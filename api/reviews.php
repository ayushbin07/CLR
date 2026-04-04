<?php
// api/reviews.php
require_once __DIR__ . '/../includes/config.php';
requireAuth();

$action = $_GET['action'] ?? '';

match ($action) {
    'list'   => listReviews(),
    'add'    => addReview(),
    'delete' => deleteReview(),
    default  => jsonResponse(['error' => 'Unknown action'], 400),
};

function listReviews(): void {
    $rows = db()->query(
        'SELECT r.id, r.comment, r.created_at,
                u.name AS user_name, u.avatar_path, u.avatar_seed, u.avatar_text, u.avatar_style,
                c.name AS class_name
         FROM reviews r
         JOIN users u ON u.id = r.user_id
         LEFT JOIN classes c ON c.id = u.class_id
         ORDER BY r.created_at DESC
         LIMIT 50'
    )->fetchAll();
    jsonResponse($rows);
}

function addReview(): void {
    verifyCsrf();
    $user    = auth();
    $comment = trim($_POST['comment'] ?? '');
    if ($comment === '') jsonResponse(['error' => 'Comment cannot be empty.'], 422);
    if (strlen($comment) > 300) jsonResponse(['error' => 'Keep it under 300 characters!'], 422);

    db()->prepare('INSERT INTO reviews (user_id, comment) VALUES (?, ?)')
        ->execute([$user['id'], $comment]);
    jsonResponse(['success' => true]);
}

function deleteReview(): void {
    verifyCsrf();
    $user = auth();
    if (($user['role'] ?? '') !== 'admin') jsonResponse(['error' => 'Admins only.'], 403);

    $id = (int)($_POST['id'] ?? 0);
    if (!$id) jsonResponse(['error' => 'Invalid ID'], 422);

    db()->prepare('DELETE FROM reviews WHERE id = ?')->execute([$id]);
    jsonResponse(['success' => true]);
}
