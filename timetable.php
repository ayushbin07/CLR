<?php
// api/timetable.php
require_once __DIR__ . '/../includes/config.php';
requireAuth();
requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

match (true) {
    $method === 'POST' && $action === 'add'    => addSlot(),
    $method === 'POST' && $action === 'import' => importSlots(),
    $method === 'POST' && $action === 'delete' => deleteSlot(),
    $method === 'GET'  && $action === 'list'   => listSlots(),
    default => jsonResponse(['error' => 'Unknown action'], 400),
};

function addSlot(): void {
    verifyCsrf();
    $classId  = (int)($_POST['class_id']   ?? 0);
    $subject  = trim($_POST['subject']     ?? '');
    $room     = trim($_POST['room']        ?? '');
    $day      = $_POST['day_of_week']      ?? '';
    $start    = $_POST['start_time']       ?? '';
    $end      = $_POST['end_time']         ?? '';

    $validDays = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    if (!$classId || !$subject || !$day || !$start || !$end) {
        jsonResponse(['error' => 'Missing fields'], 422);
    }
    if (!in_array($day, $validDays, true)) {
        jsonResponse(['error' => 'Invalid day'], 422);
    }

    $stmt = db()->prepare(
        'INSERT INTO timetable (class_id, subject, room, day_of_week, start_time, end_time)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$classId, $subject, $room, $day, $start, $end]);
    jsonResponse(['success' => true, 'id' => db()->lastInsertId()]);
}

function importSlots(): void {
    verifyCsrf();
    $json  = $_POST['slots'] ?? '[]';
    $slots = json_decode($json, true);

    if (!is_array($slots)) jsonResponse(['error' => 'Invalid JSON array'], 422);

    $validDays = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    $stmt = db()->prepare(
        'INSERT INTO timetable (class_id, subject, room, day_of_week, start_time, end_time)
         VALUES (?, ?, ?, ?, ?, ?)'
    );

    $count = 0;
    db()->beginTransaction();
    try {
        foreach ($slots as $slot) {
            $classId = (int)($slot['class_id']   ?? 0);
            $subject = trim($slot['subject']     ?? '');
            $day     = $slot['day_of_week']      ?? '';
            $start   = $slot['start_time']       ?? '';
            $end     = $slot['end_time']         ?? '';
            $room    = trim($slot['room']        ?? '');

            if (!$classId || !$subject || !in_array($day, $validDays, true) || !$start || !$end) continue;
            $stmt->execute([$classId, $subject, $room, $day, $start, $end]);
            $count++;
        }
        db()->commit();
    } catch (\Exception $e) {
        db()->rollBack();
        jsonResponse(['error' => $e->getMessage()], 500);
    }

    jsonResponse(['success' => true, 'count' => $count]);
}

function deleteSlot(): void {
    verifyCsrf();
    $id = (int)($_POST['id'] ?? 0);
    db()->prepare('DELETE FROM timetable WHERE id = ?')->execute([$id]);
    jsonResponse(['success' => true]);
}

function listSlots(): void {
    $classId = (int)($_GET['class_id'] ?? 0);
    if (!$classId) jsonResponse(['error' => 'class_id required'], 422);
    $stmt = db()->prepare('SELECT * FROM timetable WHERE class_id = ? ORDER BY FIELD(day_of_week,"Mon","Tue","Wed","Thu","Fri","Sat","Sun"), start_time');
    $stmt->execute([$classId]);
    jsonResponse($stmt->fetchAll());
}
