<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cygnet Clinics</title>
    <link rel="stylesheet" href="/cygnetclinics.com/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script defer src="/cygnetclinics.com/assets/js/main.js"></script>
</head>
<body>
<header class="site-header">
    <div class="container">
        <div class="logo">Cygnet Clinics</div>
        <nav class="nav">
            <a href="/cygnetclinics.com/index.php#home">Home</a>
            <a href="/cygnetclinics.com/index.php#about">About</a>
            <a href="/cygnetclinics.com/index.php#services">Services</a>
            <a href="/cygnetclinics.com/index.php#blog">Blogs</a>
            <a href="/cygnetclinics.com/index.php#contact">Contact</a>
            <a href="/cygnetclinics.com/login.php" class="btn btn-primary">Portal Login</a>
        </nav>
    </div>
</header>
<main>
