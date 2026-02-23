<?php
$config = load_config();
$meta = $meta ?? seo_meta('Doctor Portfolio', 'Professional medical portfolio and health insights');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($meta['title']) ?></title>
    <meta name="description" content="<?= e($meta['description']) ?>" />
    <meta name="keywords" content="<?= e($meta['keywords']) ?>" />
    <link rel="canonical" href="<?= e($meta['canonical']) ?>" />
    <meta property="og:title" content="<?= e($meta['title']) ?>" />
    <meta property="og:description" content="<?= e($meta['description']) ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= e($meta['canonical']) ?>" />
    <meta name="twitter:card" content="summary_large_image" />
    <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="/"><?= e($config['site_name'] ?? 'Dr. Portfolio') ?></a>
        <nav>
            <a href="/">Home</a>
            <a href="/about.php">About</a>
            <a href="/blog.php">Articles</a>
            <a href="/appointments.php">Appointments</a>
            <a href="/contact.php">Contact</a>
            <a href="/login.php">Admin</a>
        </nav>
    </div>
</header>
<main>
