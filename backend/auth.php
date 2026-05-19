<?php
require_once 'config.php';

function register($pdo, $name, $email, $password, $role) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $email, $hashed_password, $role]);
    return $pdo->lastInsertId();
}

function login($pdo, $email, $password) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return false;
}

// Usage example:
// $user_id = register($pdo, 'admin', 'admin123', 'admin');
// $user = login($pdo, 'admin', 'admin123');