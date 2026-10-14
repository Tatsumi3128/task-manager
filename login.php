<?php
require_once __DIR__ . '/includes/functions.php';
if (current_user()) redirect('dashboard.php');

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT id, name, email, password, role FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    if ($row && password_verify($password, $row['password'])) {
        session_regenerate_id(true); // prevents session fixation
        $_SESSION['user'] = [
            'id'   => (int)$row['id'],
            'name' => $row['name'],
            'role' => $row['role'],
        ];
        redirect('dashboard.php');
    }
   
    $error = 'Invalid email or password.';
}

$pageTitle = 'Login';
require __DIR__ . '/includes/header.php';
?>
<div class="card auth-card">
    <div class="card-body p-4">
        <h1 class="h4 mb-3">Log in</h1>
        <?php if ($error): ?><div class="alert alert-danger py-2"><?= e($error) ?></div><?php endif; ?>
        <form method="post" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?= e($email) ?>">
                <div class="invalid-feedback">Please enter a valid email.</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="invalid-feedback">Please enter your password.</div>
            </div>
            <button class="btn btn-primary w-100">Log in</button>
        </form>
        <p class="text-center mt-3 mb-0">No account yet? <a href="register.php">Register</a></p>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
