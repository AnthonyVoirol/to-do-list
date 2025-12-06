<?php
session_start();
require_once 'dbConfig.php';

$stmt = $conn->prepare("SELECT username, avatar_path, avatar_timestamp FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$avatar = $user['avatar_path'] ?? "default";
$username = $user['username'];
$avatarTimestamp = $user['avatar_timestamp'] ?? time();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte</title>
    <link rel="stylesheet" href="../css/settings.css?v=1.2">
</head>

<body>
    <a href="../../">retour</a>
    <div class="main">
        <section class="settings">
            <h1>Paramètres</h1>
            <div class="btnSettings">
                <button class="noButton" id="btnAccount">Compte</button>
                <button class="noButton" id="btnAppearance">Apparence</button>
                <button class="noButton" id="btnNotification">Notification</button>
            </div>
        </section>
        <section id="display">

        </section>
    </div>
    <script>
        const pathAvatar = "<?php echo '../avatars/' . $avatar . '.png'; ?>";
        const username = "<?php echo $username ?>"; 
        const avatarTimestamp = "<?php echo $avatarTimestamp ?>"; 
    </script>
    <script src="../js/settings.js?v=1.4"></script>
</body>

</html>