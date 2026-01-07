<?php
session_start();
require_once __DIR__ . '/../config/dbConfig.php';
require_once __DIR__ . '/../src/services/auth.php';

getUserInfo($conn);

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/signIn.php");
    exit();
}

$avatar = $_SESSION['avatar'] ?? 'default';
$username = $_SESSION['username'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Taskly</title>
    <link rel="stylesheet" href="../assets/css/settings.css?v=1.2">
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
        window.OneSignalDeferred = window.OneSignalDeferred || [];
        OneSignalDeferred.push(async function (OneSignal) {
            await OneSignal.init({
                appId: "5bc6a16f-4a8c-444d-a5e1-88e03c418b5e",
                safari_web_id: "web.onesignal.auto.1172fa5f-6e39-45ba-9a29-ceb4d8311220",
                notifyButton: {
                    enable: false
                },
                allowLocalhostAsSecureOrigin: true
            });
        });
    </script>
</head>

<body>
    <a href="../app/dashboard.php">← Retour</a>
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
        const pathAvatar = "<?php echo '../assets/avatars/' . $avatar . '.png'; ?>";
        const username = "<?php echo $username ?>"; 
    </script>
    <script src="../assets/js/settings.js?v=2"></script>
</body>

</html>