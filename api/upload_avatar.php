<?php
// api/upload_avatar.php
require_once __DIR__ . '/../includes/config.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

verifyCsrf();
$user = auth();

$maxBytes  = 5 * 1024 * 1024; // 5 MB
$allowed   = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$uploadDir = __DIR__ . '/../uploads/avatars/';

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
    jsonResponse(['error' => 'No file uploaded.'], 422);
}

$file = $_FILES['avatar'];

// Funny size check
if ($file['size'] > $maxBytes) {
    $mb = round($file['size'] / 1024 / 1024, 1);
    jsonResponse([
        'error' => "Bro, {$mb}MB?? 💀 We said 5MB max. Do you compress photos or just YOLO them straight from the camera? Try again!"
    ], 422);
}

if ($file['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(['error' => 'Upload failed (error code: ' . $file['error'] . ')'], 500);
}

// Validate MIME type via finfo (not just extension)
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

if (!in_array($mimeType, $allowed, true)) {
    jsonResponse(['error' => 'Invalid file type. Only JPG, PNG, WebP, or GIF allowed.'], 422);
}

// Map MIME to extension
$ext = match($mimeType) {
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
    default      => 'jpg',
};

// Delete old uploaded avatar if exists
$oldPath = $user['avatar_path'] ?? null;
if ($oldPath) {
    $oldFull = __DIR__ . '/../' . ltrim($oldPath, '/');
    if (file_exists($oldFull)) {
        @unlink($oldFull);
    }
}

// Safe filename using user ID
$filename  = 'user_' . $user['id'] . '.' . $ext;
$destPath  = $uploadDir . $filename;
$publicUrl = 'uploads/avatars/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    jsonResponse(['error' => 'Failed to save file. Check server permissions.'], 500);
}

// Store relative path in DB
db()->prepare('UPDATE users SET avatar_path = ? WHERE id = ?')
    ->execute([$publicUrl, $user['id']]);

$_SESSION['user']['avatar_path'] = $publicUrl;

// Return the public URL with cache-buster so browser refreshes the image
jsonResponse([
    'success'   => true,
    'avatar_url' => BASE_URL . '/' . $publicUrl . '?v=' . time(),
]);
