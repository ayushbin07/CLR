<?php
// api/home.php
require_once __DIR__ . '/../includes/config.php';
requireAuth();

actionRouter();

function actionRouter(): void {
    $action = $_GET['action'] ?? 'dashboard';
    match ($action) {
        'dashboard' => getDashboard(),
        'timetable' => getTimetable(),
        default     => jsonResponse(['error' => 'Unknown action'], 400),
    };
}

// -----------------------------------------------
function getDashboard(): void {
    $user    = auth();
    $classId = $user['class_id'];
    $dayAbbr = date('D'); // Mon, Tue …

        ensureAssignmentStatusTable();

        $statusExpr = "COALESCE(s.status, 'pending')";
        $stmt = db()->prepare(
                "SELECT a.*, u.name AS creator_name, {$statusExpr} AS status
                 FROM assignments a
                 JOIN users u ON u.id = a.created_by
                 LEFT JOIN assignment_statuses s ON s.assignment_id = a.id AND s.user_id = ?
                 WHERE (a.visibility = 'public' OR a.class_id = ?)
                     AND {$statusExpr} = 'pending'
                 ORDER BY a.deadline ASC
                 LIMIT 3"
        );
        $stmt->execute([$user['id'], $classId]);
        $assignments = $stmt->fetchAll();

        $stmt = db()->prepare(
                "SELECT * FROM timetable
                 WHERE class_id = ?
                     AND LOWER(day_of_week) LIKE LOWER(CONCAT(?, '%'))
                 ORDER BY start_time ASC"
        );
        $stmt->execute([$classId, $dayAbbr]);
    $classes = $stmt->fetchAll();

    $stmt = db()->prepare(
        "SELECT * FROM todos WHERE user_id = ? AND is_completed = 0 ORDER BY created_at DESC"
    );
    $stmt->execute([$user['id']]);
    $todos = $stmt->fetchAll();

    jsonResponse(compact('assignments', 'classes', 'todos'));
}

// -------------------------------------------------
function ensureAssignmentStatusTable(): void {
    static $created = false;
    if ($created) return;

    db()->exec(
        "CREATE TABLE IF NOT EXISTS assignment_statuses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            assignment_id INT NOT NULL,
            user_id INT NOT NULL,
            status ENUM('pending','completed') DEFAULT 'pending',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY assignment_user (assignment_id, user_id),
            FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $created = true;
}

// -----------------------------------------------
function getTimetable(): void {
    $user    = auth();
    $classId = $user['class_id'];
    $day     = $_GET['day'] ?? date('D');

    $stmt = db()->prepare(
        "SELECT * FROM timetable
         WHERE class_id = ?
           AND LOWER(day_of_week) LIKE LOWER(CONCAT(?, '%'))
         ORDER BY start_time ASC"
    );
    $stmt->execute([$classId, $day]);
    jsonResponse($stmt->fetchAll());
}
