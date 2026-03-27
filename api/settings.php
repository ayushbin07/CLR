<?php
// api/settings.php
require_once __DIR__ . '/../includes/config.php';
requireAuth();

$action = $_GET['action'] ?? '';

match ($action) {
    'update_profile'  => updateProfile(),
    'change_password' => changePassword(),
    default           => jsonResponse(['error' => 'Unknown action'], 400),
};

function updateProfile(): void {
    verifyCsrf();
    $user = auth();

    $name = trim($_POST['name'] ?? '');
    $classId = $_POST['class_id'] ?? null;
    $avatarText = trim($_POST['avatar_text'] ?? '');
    $avatarSeed = $avatarText; // keep seed aligned to avatar text
    $avatarStyle = trim($_POST['avatar_style'] ?? '');
    if ($name === '') jsonResponse(['error' => 'Name required'], 422);
    if (strlen($avatarSeed) > 50) jsonResponse(['error' => 'Avatar text too long'], 422);
    if (strlen($avatarText) > 100) jsonResponse(['error' => 'Avatar text too long'], 422);
    if ($avatarText === '') {
        $avatarText = null;
    }
    if ($avatarSeed === '' || $avatarSeed === null) {
        $avatarSeed = $user['name'];
    }

    $allowedStyles = ['avataaars', 'bottts-neutral', 'pixel-art', 'thumbs', 'identicon', 'fun-emoji'];
    if (!in_array($avatarStyle, $allowedStyles, true)) {
        $avatarStyle = $user['avatar_style'] ?? 'avataaars';
    }

    $stmt = db()->prepare('UPDATE users SET name = ?, class_id = ?, avatar_seed = ?, avatar_style = ?, avatar_text = ? WHERE id = ?');
    $stmt->execute([$name, $classId ?: null, $avatarSeed, $avatarStyle, $avatarText, $user['id']]);

    $_SESSION['user']['name']      = $name;
    $_SESSION['user']['class_id']  = $classId ?: null;
    $_SESSION['user']['avatar_seed'] = $avatarSeed;
    $_SESSION['user']['avatar_style'] = $avatarStyle;
    $_SESSION['user']['avatar_text'] = $avatarText;

    jsonResponse(['success' => true]);
}

function changePassword(): void {
    verifyCsrf();
    $user = auth();

    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$current || !$new || !$confirm) jsonResponse(['error' => 'All fields required'], 422);
    if ($new !== $confirm) jsonResponse(['error' => 'Passwords do not match'], 422);
    if (strlen($new) < 6) jsonResponse(['error' => 'Password must be at least 6 characters'], 422);

    $stmt = db()->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->execute([$user['id']]);
    $hash = $stmt->fetchColumn();
    if (!$hash || !password_verify($current, $hash)) {
        jsonResponse(['error' => 'Current password incorrect'], 403);
    }

    $newHash = password_hash($new, PASSWORD_BCRYPT);
    db()->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([$newHash, $user['id']]);
    jsonResponse(['success' => true]);
}
