<?php

require_once __DIR__ . '/includes/functions.php';
header('Content-Type: application/json');

if (!current_user() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Not allowed']);
    exit;
}
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    http_response_code(419);
    echo json_encode(['ok' => false, 'error' => 'Bad token']);
    exit;
}

$id   = (int)($_POST['id'] ?? 0);
$user = current_user();

$stmt = db()->prepare('SELECT status, user_id FROM tasks WHERE id = ?');
$stmt->execute([$id]);
$task = $stmt->fetch();

if (!$task || (!is_admin() && (int)$task['user_id'] !== $user['id'])) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'Task not found']);
    exit;
}

$new = $task['status'] === 'done' ? 'pending' : 'done';
db()->prepare('UPDATE tasks SET status = ? WHERE id = ?')->execute([$new, $id]);
echo json_encode(['ok' => true, 'status' => $new]);
