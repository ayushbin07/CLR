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
        'id'           => $user['id'],
        'name'         => $user['name'],
        'email'        => $user['email'],
        'class_id'     => $user['class_id'],
        'role'         => $user['role'],
        'avatar_seed'  => $user['avatar_seed'] ?? $user['name'],
        'avatar_style' => $user['avatar_style'] ?? 'avataaars',
        'avatar_text'  => $user['avatar_text'] ?? null,
    ];

    jsonResponse(['success' => true, 'redirect' => BASE_URL . '/index.php']);
}

// -----------------------------------------------
function handleRegister(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['error' => 'POST required'], 405);
    }

    $name         = trim($_POST['name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $password     = $_POST['password'] ?? '';
    $class_id     = (int)($_POST['class_id'] ?? 0);
    $avatarText   = trim($_POST['avatar_text'] ?? '');
    $avatarSeed   = $avatarText; // keep seed aligned to avatar text
    $avatarStyle  = trim($_POST['avatar_style'] ?? '');

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
    if (strlen($avatarSeed) > 50) {
        jsonResponse(['error' => 'Avatar seed too long'], 422);
    }
    if (strlen($avatarText) > 100) {
        jsonResponse(['error' => 'Avatar text too long'], 422);
    }
    if ($avatarText === '') {
        $avatarText = null;
    }
    if ($avatarSeed === '' || $avatarSeed === null) {
        $avatarSeed = $name;
    }

    $allowedStyles = ['avataaars', 'bottts-neutral', 'pixel-art', 'thumbs', 'identicon', 'fun-emoji'];
    if (!in_array($avatarStyle, $allowedStyles, true)) {
        $avatarStyle = 'avataaars';
    }

    $stmt = db()->prepare(
        'INSERT INTO users (name, email, password, class_id, avatar_seed, avatar_style, avatar_text) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$name, $email, $hash, $class_id ?: null, $avatarSeed, $avatarStyle, $avatarText]);
    $userId = db()->lastInsertId();

    $_SESSION['user'] = [
        'id'          => $userId,
        'name'        => $name,
        'email'       => $email,
        'class_id'    => $class_id ?: null,
        'role'        => 'user',
        'avatar_seed' => $avatarSeed,
        'avatar_style'=> $avatarStyle,
        'avatar_text' => $avatarText,
    ];

    jsonResponse(['success' => true, 'redirect' => BASE_URL . '/index.php']);
}

// -----------------------------------------------
function handleLogout(): void {
    session_destroy();
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}
