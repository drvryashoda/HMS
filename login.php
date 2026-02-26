<?php
require_once __DIR__ . '/app/bootstrap.php';

if (current_user()) {
    redirect('/admin/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM admins WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        redirect('/admin/index.php');
    }

    $error = 'Invalid login credentials.';
}

$meta = seo_meta('Admin Login', 'Secure login for doctor portfolio admin panel', ['admin login']);
require __DIR__ . '/app/header.php';
?>
<section class="container section narrow">
<h1>Admin Login</h1>
<?php if ($error): ?><p class="alert error"><?= e($error) ?></p><?php endif; ?>
<form method="post" class="card form-grid">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
</section>
<?php require __DIR__ . '/app/footer.php'; ?>
