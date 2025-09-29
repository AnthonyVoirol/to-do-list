<?php
$host = "localhost";
$db = "antweb_to-do-list-db";
$user = "antweb_DB-to-do-list"; 
$pass = 'V5@nm$mAG3eh+NrU';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
