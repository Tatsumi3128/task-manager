<?php
require_once __DIR__ . '/includes/functions.php';
if (current_user()) redirect('dashboard.php');

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check($_POST['csrf'] ?? null);

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

   
    if ($name === '' || mb_strlen($name) > 100)          $errors[] = 'Name is required (max 100 characters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))      $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8)                           $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm)                          $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'That email is already registered.';
        }
    }

    if (!$errors) {
      
        $stmt = db()->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, "user")');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        flash('success', 'Account created! You can now log in.');
        redirect('login.php');
    }
}

$pageTitle = 'Register';
require __DIR__ . '/includes/header.php';
?>
<div class="card auth-card">
    <div class="card-body p-4">
        <h1 class="h4 mb-3">Create an account</h1>
        <?php foreach ($errors as $err): ?>
            <div class="alert alert-danger py-2"><?= e($err) ?></div>
        <?php endforeach; ?>
        <form method="post" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="name" class="form-label">Full name</label>
                <input type="text" class="form-control" id="name" name="name" maxlength="100" required value="<?= e($name) ?>">
                <div class="invalid-feedback">Please enter your name.</div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?= e($email) ?>">
                <div class="invalid-feedback">Please enter a valid email.</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password (min. 8 characters)</label>
                <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                <div class="invalid-feedback">Password must be at least 8 characters.</div>
            </div>
            <div class="mb-3">
                <label for="confirm" class="form-label">Confirm password</label>
                <input type="password" class="form-control" id="confirm" name="confirm" required data-match="password">
                <div class="invalid-feedback">Passwords must match.</div>
            </div>
            <button class="btn btn-primary w-100">Register</button>
        </form>
        <p class="text-center mt-3 mb-0">Already have an account? <a href="login.php">Log in</a></p>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
