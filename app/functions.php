<?php

function base_path(string $path = ''): string
{
    $base = __DIR__ . '/..';
    return $path ? $base . '/' . ltrim($path, '/') : $base;
}

function load_config(): array
{
    $configFile = base_path('app/config.php');
    if (!file_exists($configFile)) {
        return require base_path('app/config.sample.php');
    }

    return require $configFile;
}

function is_installed(): bool
{
    $config = load_config();
    return !empty($config['installed']);
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function ensure_installed_or_redirect(): void
{
    if (!is_installed() && basename($_SERVER['SCRIPT_NAME']) !== 'index.php') {
        redirect('/install/');
    }
}

function seo_meta(string $title, string $description, array $keywords = []): array
{
    $config = load_config();
    $siteName = $config['site_name'] ?? 'Doctor Portfolio';

    return [
        'title' => $title . ' | ' . $siteName,
        'description' => $description,
        'keywords' => implode(', ', $keywords),
        'canonical' => rtrim($config['site_url'] ?: ((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']), '/') . $_SERVER['REQUEST_URI'],
    ];
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = load_config();
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $config['db_host'], $config['db_name']);

    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function generate_slug(string $title): string
{
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    return $slug ?: 'post-' . time();
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

function current_user(): ?array
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, name, email FROM admins WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $_SESSION['admin_id']]);
    return $stmt->fetch() ?: null;
}

function require_admin(): void
{
    if (!current_user()) {
        redirect('/login.php');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['_csrf'];
}

function verify_csrf(string $token): bool
{
    return isset($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
}
