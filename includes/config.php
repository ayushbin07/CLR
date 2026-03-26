<?php
// includes/config.php
// -----------------------------------------------
// Database configuration — edit these values
// -----------------------------------------------
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // XAMPP default
define('DB_PASS', '');            // XAMPP default (empty)
define('DB_NAME', 'sanctuary');
define('DB_PORT', 3306);

define('APP_NAME', 'Sanctuary');

// Derive BASE_URL from the project folder under the webroot so links work even if the folder name changes.
$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$docRoot  = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
$project  = str_replace('\\', '/', realpath(__DIR__ . '/..'));
$basePath = $docRoot ? trim(str_replace($docRoot, '', $project), '/') : '';
$baseUrl  = $basePath ? "$scheme://$host/$basePath" : "$scheme://$host";
define('BASE_URL', $baseUrl);

// -----------------------------------------------
// PDO connection (singleton)
// -----------------------------------------------
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

// -----------------------------------------------
// Session bootstrap
// -----------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400 * 7,   // 7 days
        'path'     => '/',
        'secure'   => false,       // set true in production with HTTPS
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// -----------------------------------------------
// Auth helpers
// -----------------------------------------------
function auth(): array|false {
    return $_SESSION['user'] ?? false;
}

function requireAuth(): void {
    if (!auth()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function requireAdmin(): void {
    $user = auth();
    if (!$user || $user['role'] !== 'admin') {
        http_response_code(403);
        die('Access denied.');
    }
}

// -----------------------------------------------
// JSON response helper
// -----------------------------------------------
function jsonResponse(mixed $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// -----------------------------------------------
// CSRF helpers
// -----------------------------------------------
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        jsonResponse(['error' => 'Invalid CSRF token'], 403);
    }
}
