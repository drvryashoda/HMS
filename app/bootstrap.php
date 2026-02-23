<?php
session_start();
require_once __DIR__ . '/functions.php';

if (!is_installed() && strpos($_SERVER['REQUEST_URI'], '/install') !== 0) {
    redirect('/install/');
}
