<?php
require_once __DIR__ . '/../app/functions.php';

if (is_installed()) {
    redirect('/');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = trim($_POST['db_host'] ?? '');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = $_POST['db_pass'] ?? '';
    $siteUrl = rtrim(trim($_POST['site_url'] ?? ''), '/');
    $siteName = trim($_POST['site_name'] ?? 'Doctor Portfolio');
    $doctorEmail = trim($_POST['doctor_email'] ?? '');
    $adminName = trim($_POST['admin_name'] ?? 'Admin');
    $adminEmail = trim($_POST['admin_email'] ?? '');
    $adminPassword = $_POST['admin_password'] ?? '';

    if (!$dbHost || !$dbName || !$dbUser || !$doctorEmail || !$adminEmail || !$adminPassword) {
        $errors[] = 'Please fill in all required fields.';
    }

    if (!$errors) {
        try {
            $pdo = new PDO(sprintf('mysql:host=%s;charset=utf8mb4', $dbHost), $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `$dbName`");
            $schema = file_get_contents(__DIR__ . '/../app/schema.sql');
            $pdo->exec($schema);

            $configData = [
                'db_host' => $dbHost,
                'db_name' => $dbName,
                'db_user' => $dbUser,
                'db_pass' => $dbPass,
                'site_url' => $siteUrl,
                'doctor_email' => $doctorEmail,
                'site_name' => $siteName,
                'admin_email' => $adminEmail,
                'installed' => true,
            ];

            $configPhp = "<?php\nreturn " . var_export($configData, true) . ";\n";
            file_put_contents(__DIR__ . '/../app/config.php', $configPhp);

            $insertAdmin = $pdo->prepare('INSERT INTO admins (name, email, password, created_at) VALUES (:name, :email, :password, :created_at)');
            $insertAdmin->execute([
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => password_hash($adminPassword, PASSWORD_BCRYPT),
                'created_at' => now(),
            ]);

            $settingStmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
            foreach ([
                'doctor_email' => $doctorEmail,
                'site_name' => $siteName,
            ] as $k => $v) {
                $settingStmt->execute(['k' => $k, 'v' => $v]);
            }

            $success = true;
        } catch (Throwable $e) {
            $errors[] = 'Installation failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Doctor Portfolio</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="container install-wrap">
    <h1>Doctor Portfolio Installation</h1>
    <?php foreach ($errors as $error): ?>
        <p class="alert error"><?= e($error) ?></p>
    <?php endforeach; ?>
    <?php if ($success): ?>
        <p class="alert success">Installation completed. <a href="/">Go to website</a></p>
    <?php else: ?>
    <form method="post" class="card form-grid">
        <h2>Database</h2>
        <input name="db_host" placeholder="DB Host" value="localhost" required>
        <input name="db_name" placeholder="DB Name" required>
        <input name="db_user" placeholder="DB User" required>
        <input name="db_pass" type="password" placeholder="DB Password">
        <h2>Website & Admin</h2>
        <input name="site_url" placeholder="https://example.com" required>
        <input name="site_name" placeholder="Website Name" value="Dr. Portfolio" required>
        <input name="doctor_email" type="email" placeholder="Doctor Email" required>
        <input name="admin_name" placeholder="Admin Name" value="Administrator" required>
        <input name="admin_email" type="email" placeholder="Admin Email" required>
        <input name="admin_password" type="password" placeholder="Admin Password" required>
        <button type="submit">Install Now</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
