<?php
// api/timetable.php
require_once __DIR__ . '/../includes/config.php';
requireAuth();
requireAdmin();

$action = $_GET['action'] ?? '';

match ($action) {
    'add'     => addSlot(),
    'import'  => importSlots(),
    'replace' => replaceSlots(),
    'list'    => listSlots(),
    'delete'  => deleteSlot(),
    default   => jsonResponse(['error' => 'Unknown action'], 400),
};

function normalizeDay(string $day): ?string {
    $d = strtolower(trim($day));
    if ($d === '') return null;
    // Accept full names or short forms
    $map = [
        'mon' => 'Mon', 'monday'    => 'Mon',
        'tue' => 'Tue', 'tues'      => 'Tue', 'tuesday'   => 'Tue',
        'wed' => 'Wed', 'weds'      => 'Wed', 'wednesday' => 'Wed',
        'thu' => 'Thu', 'thur'      => 'Thu', 'thurs'     => 'Thu', 'thursday'  => 'Thu',
        'fri' => 'Fri', 'friday'    => 'Fri',
        'sat' => 'Sat', 'saturday'  => 'Sat',
        'sun' => 'Sun', 'sunday'    => 'Sun',
    ];
    return $map[$d] ?? ($map[substr($d, 0, 3)] ?? null);
}

function addSlot(): void {
    verifyCsrf();
    $classId = (int)($_POST['class_id'] ?? 0);
    $subject = trim($_POST['subject'] ?? '');
    $room    = trim($_POST['room'] ?? '');
    $day     = normalizeDay($_POST['day_of_week'] ?? '');
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
        $day     = normalizeDay($slot['day_of_week'] ?? '');
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

function replaceSlots(): void {
    verifyCsrf();
    $classId = (int)($_POST['class_id'] ?? 0);
    $day     = normalizeDay($_POST['day_of_week'] ?? '');
    $slotsJson = $_POST['slots'] ?? '[]';
    $slots = json_decode($slotsJson, true);
    if (!$classId) jsonResponse(['error' => 'class_id required'], 422);
    if (!is_array($slots)) jsonResponse(['error' => 'Invalid JSON'], 422);

    // Delete existing scope
    if ($day) {
        $del = db()->prepare('DELETE FROM timetable WHERE class_id = ? AND day_of_week = ?');
        $del->execute([$classId, $day]);
    } else {
        $del = db()->prepare('DELETE FROM timetable WHERE class_id = ?');
        $del->execute([$classId]);
    }

    $count = 0;
    $stmt = db()->prepare(
        'INSERT INTO timetable (class_id, subject, room, day_of_week, start_time, end_time)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    foreach ($slots as $slot) {
        $subject = trim($slot['subject'] ?? '');
        $room    = trim($slot['room'] ?? '');
        $dayVal  = normalizeDay($slot['day_of_week'] ?? ($day ?: ''));
        $start   = $slot['start_time'] ?? '';
        $end     = $slot['end_time'] ?? '';
        if (!$subject || !$dayVal || !$start || !$end) continue;
        $stmt->execute([$classId, $subject, $room ?: null, $dayVal, $start, $end]);
        $count++;
    }
    jsonResponse(['success' => true, 'count' => $count]);
}

function listSlots(): void {
    $classId = (int)($_GET['class_id'] ?? 0);
    $day     = $_GET['day_of_week'] ?? '';
    if (!$classId) jsonResponse(['error' => 'class_id required'], 422);

    $sql = 'SELECT * FROM timetable WHERE class_id = ?';
    $params = [$classId];
    if ($day) { $sql .= ' AND day_of_week = ?'; $params[] = $day; }
    $sql .= ' ORDER BY day_of_week ASC, start_time ASC';
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    jsonResponse($stmt->fetchAll());
}

function deleteSlot(): void {
    verifyCsrf();
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) jsonResponse(['error' => 'id required'], 422);
    $stmt = db()->prepare('DELETE FROM timetable WHERE id = ?');
    $stmt->execute([$id]);
    jsonResponse(['success' => true]);
}
