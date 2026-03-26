<?php
// api/todos.php
require_once __DIR__ . '/../includes/config.php';
requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

match (true) {
    $method === 'GET'  && $action === 'list'   => listTodos(),
    $method === 'POST' && $action === 'create' => createTodo(),
    $method === 'POST' && $action === 'update' => updateTodo(),
    $method === 'POST' && $action === 'toggle' => toggleTodo(),
    $method === 'POST' && $action === 'delete' => deleteTodo(),
    default => jsonResponse(['error' => 'Unknown action'], 400),
};

function listTodos(): void {
    $user = auth();
    $stmt = db()->prepare(
        'SELECT * FROM todos WHERE user_id = ? ORDER BY is_completed ASC, created_at DESC'
    );
    $stmt->execute([$user['id']]);
    jsonResponse($stmt->fetchAll());
}

function createTodo(): void {
    verifyCsrf();
    $user  = auth();
    $title = trim($_POST['title'] ?? '');
    if (!$title) jsonResponse(['error' => 'Title required'], 422);

    $stmt = db()->prepare('INSERT INTO todos (user_id, title) VALUES (?, ?)');
    $stmt->execute([$user['id'], $title]);
    jsonResponse(['success' => true, 'id' => db()->lastInsertId(), 'title' => $title]);
}

function updateTodo(): void {
    verifyCsrf();
    $user  = auth();
    $id    = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    if (!$title) jsonResponse(['error' => 'Title required'], 422);

    $stmt = db()->prepare('SELECT id FROM todos WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user['id']]);
    if (!$stmt->fetch()) jsonResponse(['error' => 'Not found'], 404);

    db()->prepare('UPDATE todos SET title = ? WHERE id = ?')->execute([$title, $id]);
    jsonResponse(['success' => true]);
}

function toggleTodo(): void {
    verifyCsrf();
    $user = auth();
    $id   = (int)($_POST['id'] ?? 0);

    $stmt = db()->prepare('SELECT * FROM todos WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user['id']]);
    $row = $stmt->fetch();
    if (!$row) jsonResponse(['error' => 'Not found'], 404);

    $new = $row['is_completed'] ? 0 : 1;
    db()->prepare('UPDATE todos SET is_completed = ? WHERE id = ?')->execute([$new, $id]);
    jsonResponse(['success' => true, 'is_completed' => (bool)$new]);
}

function deleteTodo(): void {
    verifyCsrf();
    $user = auth();
    $id   = (int)($_POST['id'] ?? 0);
    db()->prepare('DELETE FROM todos WHERE id = ? AND user_id = ?')->execute([$id, $user['id']]);
    jsonResponse(['success' => true]);
}
