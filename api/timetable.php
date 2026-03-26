<?php
// api/timetable.php
require_once __DIR__ . '/../includes/config.php';
requireAuth();
requireAdmin();

$action = $_GET['action'] ?? '';

match ($action) {
    'add'    => addSlot(),
    'import' => importSlots(),
    default  => jsonResponse(['error' => 'Unknown action'], 400),
};

function addSlot(): void {
    verifyCsrf();
    $classId = (int)($_POST['class_id'] ?? 0);
    $subject = trim($_POST['subject'] ?? '');
    $room    = trim($_POST['room'] ?? '');
    $day     = $_POST['day_of_week'] ?? '';
    $start   = $_POST['start_time'] ?? '';
    $end     = $_POST['end_time'] ?? '';

    if (!$classId || !$subject || !$day || !$start || !$end) {
        jsonResponse(['error' => 'All fields are required'], 422);
    }

    $stmt = db()->prepare(
        'INSERT INTO timetable (class_id, subject, room, day_of_week, start_time, end_time)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$classId, $subject, $room ?: null, $day, $start, $end]);
    jsonResponse(['success' => true]);
}

function importSlots(): void {
    verifyCsrf();
    $slotsJson = $_POST['slots'] ?? '[]';
    $slots = json_decode($slotsJson, true);
    if (!is_array($slots)) {
        jsonResponse(['error' => 'Invalid JSON'], 422);
    }

    $count = 0;
    $stmt = db()->prepare(
        'INSERT INTO timetable (class_id, subject, room, day_of_week, start_time, end_time)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    foreach ($slots as $slot) {
        $classId = (int)($slot['class_id'] ?? 0);
        $subject = trim($slot['subject'] ?? '');
        $room    = trim($slot['room'] ?? '');
        $day     = $slot['day_of_week'] ?? '';
        $start   = $slot['start_time'] ?? '';
        $end     = $slot['end_time'] ?? '';
        if (!$classId || !$subject || !$day || !$start || !$end) {
            continue;
        }
        $stmt->execute([$classId, $subject, $room ?: null, $day, $start, $end]);
        $count++;
    }

    jsonResponse(['success' => true, 'count' => $count]);
}
