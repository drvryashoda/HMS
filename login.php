<?php
require_once __DIR__ . '/includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (loginUser($email, $password)) {
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid credentials. Please try again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body">
<div class="auth-card">
    <h1>Hospital Information Management System</h1>
    <p>Sign in to manage patients, doctors, appointments, billing, and more.</p>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="auth-form">
        <label>Email</label>
        <input type="email" name="email" required placeholder="admin@hospital.com">
        <label>Password</label>
        <input type="password" name="password" required placeholder="••••••••">
        <button type="submit">Sign In</button>
    </form>
    <small>Default admin: admin@hospital.com / password123</small>
</div>
</body>
</html>
