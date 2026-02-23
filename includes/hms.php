<?php
require_once __DIR__ . '/../config/database.php';

function fetchAll(string $table, string $orderBy = 'id DESC'): array
{
    $stmt = Database::connection()->query("SELECT * FROM {$table} ORDER BY {$orderBy}");
    return $stmt->fetchAll();
}

function countTable(string $table): int
{
    return (int) Database::connection()->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
}

function addFlash(string $message, string $type = 'success'): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function getFlash(): ?array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}
