<?php
session_start();
$avatar = $_SESSION['avatar'] ?? "default.png";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte</title>
    <link rel="stylesheet" href="../css/settings.css?v=Beta">
</head>

<body>
    <a href="../../">retour</a>
    <div class="main">
        <section class="settings">
            <h1>Paramètres</h1>
            <div class="btnSettings">
                <button class="noButton" id="btnAccount">Compte</button>
                <button class="noButton">Apparence</button>
                <button class="noButton">Notification</button>
            </div>
        </section>
        <section id="display">

        </section>
    </div>
    <script>
        const pathAvatar = "<?php echo '../avatars/' . $avatar . '.png'; ?>";
    </script>
    <script src="../js/settings.js?v=Beta"></script>
</body>

</html>