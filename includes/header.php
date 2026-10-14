<?php
require_once __DIR__ . '/functions.php';
$user  = current_user();
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'TaskFlow') ?> | TaskFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<header>
    <nav class="navbar navbar-expand-md navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">TaskFlow</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"
                    aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav ms-auto align-items-md-center">
                    <?php if ($user): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">My Tasks</a></li>
                        <?php if (is_admin()): ?>
                            <li class="nav-item"><a class="nav-link" href="admin_users.php">Manage Users</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><span class="nav-link text-white-50">
                            <?= e($user['name']) ?> (<?= e($user['role']) ?>)</span></li>
                        <li class="nav-item"><a class="btn btn-light btn-sm ms-md-2" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main class="container py-4">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
