<?php

require_once __DIR__ . '/includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('dashboard.php');
csrf_check($_POST['csrf'] ?? null);

$id   = (int)($_POST['id'] ?? 0);
$user = current_user();

if (is_admin()) {
    $stmt = db()->prepare('DELETE FROM tasks WHERE id = ?');
    $stmt->execute([$id]);
} else {
    $stmt = db()->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user['id']]);
}

flash($stmt->rowCount() ? 'success' : 'danger', $stmt->rowCount() ? 'Task deleted.' : 'Task not found.');
redirect('dashboard.php');
