<?php
require_once __DIR__ . '/auth.php';
$allowed = $allowedRoles ?? [];
if (!empty($allowed)) {
    require_role($allowed);
}
$title = $portalTitle ?? 'Portal';
$navItems = $portalNav ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> | Cygnet Clinics</title>
    <link rel="stylesheet" href="/cygnetclinics.com/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <div class="logo"><?php echo htmlspecialchars($title); ?></div>
        <nav class="nav">
            <?php foreach ($navItems as $item): ?>
                <a href="<?php echo $item['href']; ?>"><?php echo $item['label']; ?></a>
            <?php endforeach; ?>
            <a href="/cygnetclinics.com/index.php">Website</a>
            <a href="/cygnetclinics.com/<?php echo $user['role']; ?>/logout.php" class="btn btn-primary">Logout</a>
        </nav>
    </div>
</header>
<main class="container" style="padding: 2rem 0;">
