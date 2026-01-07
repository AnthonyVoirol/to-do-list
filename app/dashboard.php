<?php
require_once __DIR__ . '/../config/dbConfig.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../src/services/auth.php';
getUserInfo($conn);
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/auth/signIn.php");
    exit();
}
function message()
{
    if (isset($_SESSION['flash_message'])) {
        $msg = json_encode($_SESSION['flash_message']);
        echo "<script>showNotification($msg);</script>";
        unset($_SESSION['flash_message']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Taskly</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=1.0">
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
            
            const userId = "<?php echo $_SESSION['user_id']; ?>";
            await OneSignal.login(userId);
            
            const permission = await OneSignal.Notifications.permission;
            
            if (permission === false) {
                await OneSignal.Slidedown.promptPush();
            }
        });
    </script>
</head>
<body>
    <header>
        <h1>Taskly</h1>
        <div class="profile-container">
            <img class="avatar" id="avatar" src="<?php echo '../assets/avatars/' . $_SESSION['avatar'] . '.png'; ?>"
                alt="avatar">
            <div class="profile-menu" id="profileMenu">
                <ul>
                    <li><a href="../public/settings.php">Paramètres</a></li>
                    <li><a href="../public/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </header>
    <main>
        <div class="sort-container">
            <label for="sortTasks">Trier par :</label>
            <select id="sortTasks">
                <option value="deadLine">Date d'échéance</option>
                <option value="importance">Importance</option>
                <option value="isSchool">École</option>
            </select>
        </div>
        <section id="main">
            <!-- task -->
        </section>
        <section id="done">
            <!-- isDone -->
        </section>
        <button id="addTask">+</button>
    </main>
    <footer>
        <p>Fait par Ant.V</p>
    </footer>
    <script src="../assets/js/script.js?v=1.3"></script>
    <script src="../assets/js/account.js?v=1.2"></script>
    <?php message(); ?>
</body>
</html>