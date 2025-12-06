<?php
session_start();
$avatar = $_SESSION['avatar'] ?? "default";

if (!isset($_SESSION['username'])) {
    echo "error, username doesn't exist";
    exit;
}
$username = $_SESSION['username'];
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
    </script>
    <script src="../js/settings.js?v=1.2"></script>
</body>

</html>