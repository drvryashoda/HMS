<?php
require_once __DIR__ . '/db.php';

function sanitize($value)
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function redirect($path)
{
    header('Location: ' . $path);
    exit;
}

function is_post()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function flash($key, $message = null)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if ($message === null) {
        if (!empty($_SESSION['flash'][$key])) {
            $value = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $value;
        }
        return null;
    }

    $_SESSION['flash'][$key] = $message;
}

function current_user()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user'] ?? null;
}

function require_role($roles)
{
    $user = current_user();
    if (!$user || !in_array($user['role'], (array)$roles, true)) {
        redirect('/cygnetclinics.com/login.php');
    }
}

function fetch_all($table)
{
    global $pdo;
    $statement = $pdo->query("SELECT * FROM {$table} ORDER BY created_at DESC");
    return $statement->fetchAll();
}

function fetch_one($table, $id)
{
    global $pdo;
    $statement = $pdo->prepare("SELECT * FROM {$table} WHERE id = :id");
    $statement->execute(['id' => $id]);
    return $statement->fetch();
}

function insert($table, array $data)
{
    global $pdo;
    $columns = array_keys($data);
    $placeholders = array_map(fn($column) => ':' . $column, $columns);
    $sql = sprintf(
        'INSERT INTO %s (%s) VALUES (%s)',
        $table,
        implode(', ', $columns),
        implode(', ', $placeholders)
    );
    $statement = $pdo->prepare($sql);
    $statement->execute($data);
    return $pdo->lastInsertId();
}

function update($table, $id, array $data)
{
    global $pdo;
    $columns = array_keys($data);
    $assignments = array_map(fn($column) => $column . ' = :' . $column, $columns);
    $sql = sprintf(
        'UPDATE %s SET %s WHERE id = :id',
        $table,
        implode(', ', $assignments)
    );
    $data['id'] = $id;
    $statement = $pdo->prepare($sql);
    return $statement->execute($data);
}

function delete_row($table, $id)
{
    global $pdo;
    $statement = $pdo->prepare("DELETE FROM {$table} WHERE id = :id");
    return $statement->execute(['id' => $id]);
}

function authenticate($email, $password)
{
    global $pdo;
    $statement = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return null;
}
