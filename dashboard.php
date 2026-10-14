<?php
require_once __DIR__ . '/includes/functions.php';
require_login();

$user   = current_user();
$filter = $_GET['status'] ?? 'all';

// READ: admins see everyone's tasks, users see only their own
$sql    = 'SELECT t.*, u.name AS owner FROM tasks t JOIN users u ON u.id = t.user_id WHERE 1=1';
$params = [];
if (!is_admin()) {
    $sql .= ' AND t.user_id = ?';
    $params[] = $user['id'];
}
if (in_array($filter, ['pending', 'done'], true)) {
    $sql .= ' AND t.status = ?';
    $params[] = $filter;
}
$sql .= ' ORDER BY t.status ASC, t.due_date IS NULL, t.due_date ASC, t.id DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll();

$pageTitle = 'My Tasks';
require __DIR__ . '/includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0"><?= is_admin() ? 'All Tasks' : 'My Tasks' ?></h1>
    <a href="task_form.php" class="btn btn-primary">+ New Task</a>
</div>

<div class="card mb-3">
    <div class="card-body row g-2">
        <div class="col-12 col-md-8">
            
            <input type="search" id="taskSearch" class="form-control" placeholder="Search tasks...">
        </div>
        <div class="col-12 col-md-4">
            <form method="get">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="all"     <?= $filter === 'all' ? 'selected' : '' ?>>All statuses</option>
                    <option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="done"    <?= $filter === 'done' ? 'selected' : '' ?>>Done</option>
                </select>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Task</th>
                    <?php if (is_admin()): ?><th>Owner</th><?php endif; ?>
                    <th>Due</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="taskBody">
            <?php if (!$tasks): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No tasks yet. Create your first one!</td></tr>
            <?php endif; ?>
            <?php foreach ($tasks as $t): ?>
                <tr class="task-row <?= $t['status'] === 'done' ? 'task-done' : '' ?>">
                    <td>
                        <div class="task-title fw-semibold"><?= e($t['title']) ?></div>
                        <?php if ($t['description']): ?>
                            <div class="small text-muted"><?= e($t['description']) ?></div>
                        <?php endif; ?>
                    </td>
                    <?php if (is_admin()): ?><td><?= e($t['owner']) ?></td><?php endif; ?>
                    <td><?= $t['due_date'] ? e($t['due_date']) : '&mdash;' ?></td>
                    <td>
                        <!-- Toggled via fetch() in app.js -->
                        <button type="button"
                                class="btn btn-sm toggle-status <?= $t['status'] === 'done' ? 'btn-success' : 'btn-outline-secondary' ?>"
                                data-id="<?= (int)$t['id'] ?>">
                            <?= $t['status'] === 'done' ? 'Done' : 'Pending' ?>
                        </button>
                    </td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="task_form.php?id=<?= (int)$t['id'] ?>">Edit</a>
                        <form method="post" action="task_delete.php" class="d-inline delete-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
