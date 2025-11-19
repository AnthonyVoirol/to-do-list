<?php

function register_user(mysqli $conn, string $username, string $email, string $password): string
{
    if ($username === '' || $email === '' || $password === '') {
        return 'All fields are required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Invalid email address.';
    }

    if (strlen($password) < 4) {
        return 'Password must contain at least 4 characters.';
    }

    $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        return 'Username or email already in use.';
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $username, $email, $passwordHash);
    $stmt->execute();
    $user_id = $stmt->insert_id;
    $stmt->close();

    session_start();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;

    return 'Registration successful.';
}

function login_user(mysqli $conn, string $email, string $password): string
{
    if ($email === '' || $password === '') {
        return 'All fields are required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Invalid email address.';
    }

    $stmt = $conn->prepare('SELECT id, username, password_hash FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        return 'No account found with that email.';
    }

    if (!password_verify($password, $user['password_hash'])) {
        return 'Incorrect password.';
    }

    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    return 'Login successful.';
}

function getUserInfo(mysqli $conn): string
{
    if (!isset($_SESSION['user_id'])) {
        return '';
    }

    $stmt = $conn->prepare('SELECT avatar_path FROM users WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && isset($user['avatar_path'])) {
        $_SESSION['avatar'] = $user['avatar_path'];
        return $user['avatar_path'];
    } else {
        $_SESSION['avatar'] = 'avatars_default';
        return $_SESSION['avatar'];
    }
}


?>