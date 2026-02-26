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

function seo_meta(string $title, string $description, array $keywords = []): array
{
    $config = load_config();
    $siteName = $config['site_name'] ?? 'Doctor Portfolio';
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $scheme = $isHttps ? 'https' : 'http';

    return [
        'title' => $title . ' | ' . $siteName,
        'description' => $description,
        'keywords' => implode(', ', $keywords),
        'canonical' => rtrim($config['site_url'] ?: ($scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')), '/') . ($_SERVER['REQUEST_URI'] ?? '/'),
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

function run_auto_migrations(): void
{
    static $done = false;
    if ($done || !is_installed()) {
        return;
    }

    $pdo = db();
    $columns = $pdo->query("SHOW COLUMNS FROM articles LIKE 'featured_image'")->fetchAll();
    if (!$columns) {
        $pdo->exec('ALTER TABLE articles ADD COLUMN featured_image VARCHAR(255) NULL AFTER body');
    }

    $indexes = $pdo->query("SHOW INDEX FROM appointment_slots WHERE Key_name = 'uniq_slot'")->fetchAll();
    if (!$indexes) {
        $pdo->exec('ALTER TABLE appointment_slots ADD UNIQUE KEY uniq_slot (slot_date, slot_time)');
    }

    $done = true;
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

function upload_image(string $field): ?string
{
    if (empty($_FILES[$field]['name']) || !is_uploaded_file($_FILES[$field]['tmp_name'])) {
        return null;
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($_FILES[$field]['tmp_name']);
    if (!isset($allowed[$mime])) {
        return null;
    }

    $name = uniqid('img_', true) . '.' . $allowed[$mime];
    $targetDir = base_path('uploads');
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    $target = $targetDir . '/' . $name;
    if (move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
        return '/uploads/' . $name;
    }

    return null;
}
