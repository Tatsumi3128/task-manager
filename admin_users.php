<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

$me = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);
    $action = $_POST['action'] ?? '';
    $uid    = (int)($_POST['id'] ?? 0);

    if ($uid === $me['id']) {
        flash('danger', 'You cannot change or delete your own account here.');
    } elseif ($action === 'role') {
        $role = $_POST['role'] === 'admin' ? 'admin' : 'user';
        db()->prepare('UPDATE users SET role = ? WHERE id = ?')->execute([$role, $uid]);
        flash('success', 'Role updated.');
    } elseif ($action === 'delete') {
        db()->prepare('DELETE FROM users WHERE id = ?')->execute([$uid]); 
        flash('success', 'User deleted.');
    }
    redirect('admin_users.php');
}

$users = db()->query(
    'SELECT u.id, u.name, u.email, u.role, u.created_at, COUNT(t.id) AS task_count
     FROM users u LEFT JOIN tasks t ON t.user_id = u.id
     GROUP BY u.id ORDER BY u.id'
)->fetchAll();

$pageTitle = 'Manage Users';
require __DIR__ . '/includes/header.php';
?>
<h1 class="h3 mb-3">Manage Users</h1>
<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr><th>Name</th><th>Email</th><th>Tasks</th><th>Role</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= e($u['name']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><?= (int)$u['task_count'] ?></td>
                    <td>
                        <form method="post" class="d-flex gap-1">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="role">
                            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                            <select name="role" class="form-select form-select-sm" style="width:auto"
                                    <?= (int)$u['id'] === $me['id'] ? 'disabled' : '' ?>>
                                <option value="user"  <?= $u['role'] === 'user' ? 'selected' : '' ?>>user</option>
                                <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                            </select>
                            <?php if ((int)$u['id'] !== $me['id']): ?>
                                <button class="btn btn-sm btn-outline-primary">Save</button>
                            <?php endif; ?>
                        </form>
                    </td>
                    <td class="text-end">
                        <?php if ((int)$u['id'] !== $me['id']): ?>
                            <form method="post" class="d-inline delete-form" data-message="Delete this user and all their tasks?">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted small">You</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
