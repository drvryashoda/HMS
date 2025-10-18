<?php
require_once __DIR__ . '/auth.php';
require_role(['admin']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Cygnet Clinics</title>
    <link rel="stylesheet" href="/cygnetclinics.com/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <div class="logo">Cygnet Clinics Admin</div>
        <nav class="nav">
            <a href="/cygnetclinics.com/admin/dashboard.php">Dashboard</a>
            <a href="/cygnetclinics.com/admin/manage_banners.php">Banners</a>
            <a href="/cygnetclinics.com/admin/manage_services.php">Services</a>
            <a href="/cygnetclinics.com/admin/manage_blogs.php">Blogs</a>
            <a href="/cygnetclinics.com/admin/manage_staff.php">Staff</a>
            <a href="/cygnetclinics.com/admin/manage_contact.php">Contact</a>
            <a href="/cygnetclinics.com/admin/logout.php" class="btn btn-primary">Logout</a>
        </nav>
    </div>
</header>
<main class="container" style="padding: 2rem 0;">
