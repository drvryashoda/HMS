<?php
require_once __DIR__ . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    flash('error', 'Please login to continue.');
    redirect('/cygnetclinics.com/login.php');
}

$user = $_SESSION['user'];
