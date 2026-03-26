<?php
// api/auth.php
require_once __DIR__ . '/../includes/config.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

match ($action) {
    'login'    => handleLogin(),
    'register' => handleRegister(),
    'logout'   => handleLogout(),
    default    => jsonResponse(['error' => 'Unknown action'], 400),
};

// -----------------------------------------------
function handleLogin(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['error' => 'POST required'], 405);
    }

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        jsonResponse(['error' => 'Email and password required'], 422);
    }

    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        jsonResponse(['error' => 'Invalid credentials'], 401);
    }

    $_SESSION['user'] = [
        'id'          => $user['id'],
        'name'        => $user['name'],
        'email'       => $user['email'],
        'class_id'    => $user['class_id'],
        'role'        => $user['role'],
        'avatar_seed' => $user['avatar_seed'] ?? $user['name'],
    ];

    jsonResponse(['success' => true, 'redirect' => BASE_URL . '/index.php']);
}

// -----------------------------------------------
function handleRegister(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['error' => 'POST required'], 405);
    }

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $class_id = (int)($_POST['class_id'] ?? 0);

    if (!$name || !$email || !$password) {
        jsonResponse(['error' => 'Name, email and password required'], 422);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(['error' => 'Invalid email'], 422);
    }
    if (strlen($password) < 6) {
        jsonResponse(['error' => 'Password must be at least 6 characters'], 422);
    }

    $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        jsonResponse(['error' => 'Email already registered'], 409);
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = db()->prepare(
        'INSERT INTO users (name, email, password, class_id, avatar_seed) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$name, $email, $hash, $class_id ?: null, $name]);
    $userId = db()->lastInsertId();

    $_SESSION['user'] = [
        'id'          => $userId,
        'name'        => $name,
        'email'       => $email,
        'class_id'    => $class_id ?: null,
        'role'        => 'user',
        'avatar_seed' => $name,
    ];

    jsonResponse(['success' => true, 'redirect' => BASE_URL . '/index.php']);
}

// -----------------------------------------------
function handleLogout(): void {
    session_destroy();
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}
