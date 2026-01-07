<?php
$host = "localhost";
$db = "todolist_dev";
$user = "root"; 
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
