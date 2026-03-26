<?php
// api/habits.php
require_once __DIR__ . '/../includes/config.php';
requireAuth();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

match (true) {
    $method === 'GET'  && $action === 'list'     => listHabits(),
    $method === 'GET'  && $action === 'heatmap'  => habitHeatmap(),
    $method === 'GET'  && $action === 'overall'  => overallHeatmap(),
    $method === 'POST' && $action === 'create'   => createHabit(),
    $method === 'POST' && $action === 'log'      => toggleLog(),
    $method === 'POST' && $action === 'delete'   => deleteHabit(),
    default => jsonResponse(['error' => 'Unknown action'], 400),
};

function listHabits(): void {
    $userId = auth()['id'];
    $habits = db()->prepare('SELECT * FROM habits WHERE user_id = ? ORDER BY id DESC');
    $habits->execute([$userId]);
    $rows = $habits->fetchAll();

    $today = date('Y-m-d');
    $result = [];
    foreach ($rows as $h) {
        $doneToday = hasLog($h['id'], $today);
        $streak    = calcStreak($h['id']);
        $consist   = calcConsistency($h['id'], 30);
        $result[] = [
            'id'          => (int)$h['id'],
            'name'        => $h['name'],
            'done_today'  => $doneToday,
            'streak'      => $streak,
            'consistency' => $consist,
        ];
    }

    jsonResponse($result);
}

function habitHeatmap(): void {
    $userId = auth()['id'];
    $habitId = (int)($_GET['habit_id'] ?? 0);
    $days    = max(1, min(365, (int)($_GET['days'] ?? 30)));

    assertOwnership($habitId, $userId);

    $dates = dateRange($days);
    $data = [];
    foreach ($dates as $d) {
        $data[] = [
            'date'      => $d,
            'completed' => hasLog($habitId, $d),
        ];
    }

    jsonResponse($data);
}

function overallHeatmap(): void {
    $userId = auth()['id'];
    $days   = 90;
    $dates  = dateRange($days);

    $habitsStmt = db()->prepare('SELECT id FROM habits WHERE user_id = ?');
    $habitsStmt->execute([$userId]);
    $habitIds = array_column($habitsStmt->fetchAll(), 'id');

    if (empty($habitIds)) {
        jsonResponse([]);
    }

    $logStmt = db()->prepare('SELECT habit_id, date FROM habit_logs WHERE habit_id IN (' . implode(',', array_fill(0, count($habitIds), '?')) . ')');
    $logStmt->execute($habitIds);
    $logs = $logStmt->fetchAll();
    $logSet = [];
    foreach ($logs as $l) {
        $logSet[$l['habit_id']][$l['date']] = true;
    }

    $data = [];
    $habitCount = count($habitIds);
    foreach ($dates as $d) {
        $hits = 0;
        foreach ($habitIds as $hid) {
            if (!empty($logSet[$hid][$d])) $hits++;
        }
        $ratio = $habitCount ? ($hits / $habitCount) : 0;
        $intensity = $ratio == 0 ? 0 : ($ratio <= 0.33 ? 1 : ($ratio <= 0.66 ? 2 : 3));
        $data[] = ['date' => $d, 'intensity' => $intensity];
    }

    jsonResponse($data);
}

function createHabit(): void {
    verifyCsrf();
    $userId = auth()['id'];
    $name = trim($_POST['name'] ?? '');
    if (!$name) jsonResponse(['error' => 'Name required'], 422);

    $stmt = db()->prepare('INSERT INTO habits (user_id, name) VALUES (?, ?)');
    $stmt->execute([$userId, $name]);
    jsonResponse(['success' => true, 'id' => db()->lastInsertId()]);
}

function toggleLog(): void {
    verifyCsrf();
    $userId   = auth()['id'];
    $habitId  = (int)($_POST['habit_id'] ?? 0);
    $date     = $_POST['date'] ?? date('Y-m-d');
    assertOwnership($habitId, $userId);

    $stmt = db()->prepare('SELECT id FROM habit_logs WHERE habit_id = ? AND date = ?');
    $stmt->execute([$habitId, $date]);
    $existing = $stmt->fetch();

    if ($existing) {
        db()->prepare('DELETE FROM habit_logs WHERE id = ?')->execute([$existing['id']]);
        jsonResponse(['success' => true, 'completed' => false]);
    }

    db()->prepare('INSERT INTO habit_logs (habit_id, date, completed) VALUES (?, ?, 1)')->execute([$habitId, $date]);
    jsonResponse(['success' => true, 'completed' => true]);
}

function deleteHabit(): void {
    verifyCsrf();
    $userId = auth()['id'];
    $habitId = (int)($_POST['id'] ?? 0);
    assertOwnership($habitId, $userId);
    db()->prepare('DELETE FROM habit_logs WHERE habit_id = ?')->execute([$habitId]);
    db()->prepare('DELETE FROM habits WHERE id = ?')->execute([$habitId]);
    jsonResponse(['success' => true]);
}

// -----------------------------------------------
// Helpers
function hasLog(int $habitId, string $date): bool {
    $stmt = db()->prepare('SELECT 1 FROM habit_logs WHERE habit_id = ? AND date = ? LIMIT 1');
    $stmt->execute([$habitId, $date]);
    return (bool)$stmt->fetchColumn();
}

function calcStreak(int $habitId): int {
    $streak = 0;
    for ($i = 0; ; $i++) {
        $date = date('Y-m-d', strtotime("-{$i} days"));
        if (hasLog($habitId, $date)) {
            $streak++;
        } else {
            break;
        }
    }
    return $streak;
}

function calcConsistency(int $habitId, int $days): int {
    $dates = dateRange($days);
    $done = 0;
    foreach ($dates as $d) {
        if (hasLog($habitId, $d)) $done++;
    }
    return (int)round(($done / max(1, count($dates))) * 100);
}

function dateRange(int $days): array {
    $dates = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $dates[] = date('Y-m-d', strtotime("-{$i} days"));
    }
    return $dates;
}

function assertOwnership(int $habitId, int $userId): void {
    $stmt = db()->prepare('SELECT user_id FROM habits WHERE id = ?');
    $stmt->execute([$habitId]);
    $owner = $stmt->fetchColumn();
    if (!$owner) jsonResponse(['error' => 'Not found'], 404);
    if ((int)$owner !== $userId) jsonResponse(['error' => 'Forbidden'], 403);
}
