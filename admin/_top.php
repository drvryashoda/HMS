<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_admin();
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header"><div class="container nav-wrap"><a class="brand" href="/admin/index.php">Admin</a><nav>
<a href="/admin/index.php">Dashboard</a>
<a href="/admin/articles.php">Articles</a>
<a href="/admin/categories.php">Categories</a>
<a href="/admin/tags.php">Tags</a>
<a href="/admin/slots.php">Slots</a>
<a href="/logout.php">Logout</a>
</nav></div></header>
<main class="container section">
<p>Welcome, <?= e($user['name']) ?></p>
