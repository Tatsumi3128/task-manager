<?php

require_once __DIR__ . '/includes/functions.php';
require_login();

$user = current_user();
$id   = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$task = ['title' => '', 'description' => '', 'status' => 'pending', 'due_date' => ''];

if ($id) {
    $stmt = db()->prepare('SELECT * FROM tasks WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    
    if (!$found || (!is_admin() && (int)$found['user_id'] !== $user['id'])) {
        http_response_code(404);
        exit('Task not found.');
    }
    $task = $found;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);

    $task['title']       = trim($_POST['title'] ?? '');
    $task['description'] = trim($_POST['description'] ?? '');
    $task['status']      = $_POST['status'] ?? 'pending';
    $task['due_date']    = $_POST['due_date'] ?? '';

    // Server-side validation
    if ($task['title'] === '' || mb_strlen($task['title']) > 150) $errors[] = 'Title is required (max 150 characters).';
    if (!in_array($task['status'], ['pending', 'done'], true))   $errors[] = 'Invalid status.';
    if ($task['due_date'] !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $task['due_date']);
        if (!$d || $d->format('Y-m-d') !== $task['due_date'])    $errors[] = 'Invalid due date.';
    }

    if (!$errors) {
        $due = $task['due_date'] === '' ? null : $task['due_date'];
        if ($id) {
            $stmt = db()->prepare('UPDATE tasks SET title = ?, description = ?, status = ?, due_date = ? WHERE id = ?');
            $stmt->execute([$task['title'], $task['description'], $task['status'], $due, $id]);
            flash('success', 'Task updated.');
        } else {
            $stmt = db()->prepare('INSERT INTO tasks (user_id, title, description, status, due_date) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$user['id'], $task['title'], $task['description'], $task['status'], $due]);
            flash('success', 'Task created.');
        }
        redirect('dashboard.php');
    }
}

$pageTitle = $id ? 'Edit Task' : 'New Task';
require __DIR__ . '/includes/header.php';
?>
<div class="card mx-auto" style="max-width: 640px;">
    <div class="card-body p-4">
        <h1 class="h4 mb-3"><?= e($pageTitle) ?></h1>
        <?php foreach ($errors as $err): ?>
            <div class="alert alert-danger py-2"><?= e($err) ?></div>
        <?php endforeach; ?>
        <form method="post" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)$id ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" maxlength="150" required value="<?= e($task['title']) ?>">
                <div class="invalid-feedback">Please enter a title.</div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= e($task['description']) ?></textarea>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-12 col-sm-6">
                    <label for="due_date" class="form-label">Due date</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" value="<?= e($task['due_date']) ?>">
                </div>
                <div class="col-12 col-sm-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="pending" <?= $task['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="done"    <?= $task['status'] === 'done' ? 'selected' : '' ?>>Done</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary">Save</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
