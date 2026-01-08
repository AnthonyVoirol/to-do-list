<?php
require_once __DIR__ . '/../config/dbConfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_COOKIE['remember_me'])) {
    $stmt = $conn->prepare("DELETE FROM user_tokens WHERE token = ?");
    $stmt->bind_param("s", $_COOKIE['remember_me']);
    $stmt->execute();
    $stmt->close();

    setcookie("remember_me", "", time() - 3600, "/", "", true, true);
}

session_unset();
session_destroy();

header("Location: ../index.html");
exit;
?>