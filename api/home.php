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

    $stmt = db()->prepare(
        "SELECT a.*, u.name AS creator_name
         FROM assignments a
         JOIN users u ON u.id = a.created_by
         WHERE (a.visibility = 'public' OR a.class_id = ?)
           AND a.status = 'pending'
         ORDER BY a.deadline ASC
         LIMIT 3"
    );
    $stmt->execute([$classId]);
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
