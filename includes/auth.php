<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function loginUser(string $email, string $password): bool
{
    $stmt = Database::connection()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return false;
    }

    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];

    return true;
}

function logoutUser(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function roleBadge(string $role): string
{
    $map = [
        'admin' => 'badge-admin',
        'doctor' => 'badge-doctor',
        'nurse' => 'badge-nurse',
        'receptionist' => 'badge-reception',
        'pharmacist' => 'badge-pharmacy',
        'lab_tech' => 'badge-lab',
    ];

    return $map[$role] ?? 'badge-default';
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
