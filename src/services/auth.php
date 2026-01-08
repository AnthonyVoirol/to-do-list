<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function register_user(mysqli $conn, string $username, string $email, string $password, bool $rememberMe): string
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

    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;

    if ($rememberMe) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + (86400 * 30));

        $stmt = $conn->prepare("INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $token, $expires);
        $stmt->execute();
        $stmt->close();

        setcookie("remember_me", $token, time() + (86400 * 30), "/", "", true, true);
    }

    return 'Registration successful.';
}

function login_user(mysqli $conn, string $email, string $password, bool $rememberMe): string
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

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return 'Incorrect email or password.';
    }

    if ($rememberMe) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + (86400 * 30));

        $stmt = $conn->prepare("INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user['id'], $token, $expires);
        $stmt->execute();
        $stmt->close();

        setcookie("remember_me", $token, time() + (86400 * 30), "/", "", true, true);
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    return 'Login successful.';
}

function getUserInfo(mysqli $conn)
{
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
        $token = $_COOKIE['remember_me'];

        $stmt = $conn->prepare("SELECT user_id, expires_at FROM user_tokens WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row && strtotime($row['expires_at']) > time()) {
            $_SESSION['user_id'] = $row['user_id'];

            $newToken = bin2hex(random_bytes(32));
            $newExpires = date("Y-m-d H:i:s", time() + (86400 * 30));

            $update = $conn->prepare("UPDATE user_tokens SET token = ?, expires_at = ? WHERE token = ?");
            $update->bind_param("sss", $newToken, $newExpires, $token);
            $update->execute();
            $update->close();

            setcookie("remember_me", $newToken, time() + (86400 * 30), "/", "", true, true);
        }
    }

    if (!isset($_SESSION['user_id']))
        return '';

    $stmt = $conn->prepare("SELECT username, avatar_path FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!empty($user['avatar_path'])) {
        $_SESSION['avatar'] = $user['avatar_path'];
    } else {
        $_SESSION['avatar'] = 'default';
    }

    $avatarPath = __DIR__ . '/../../assets/avatars/' . $_SESSION['avatar'] . '.png';
    if (!file_exists($avatarPath) || $_SESSION['avatar'] === 'default') {
        $_SESSION['avatar'] = 'avatars_default';
    }
}
?>