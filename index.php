<?php
require_once 'assets/php/dbConfig.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'assets/php/auth.php';
getUserInfo($conn);
if (!isset($_SESSION['user_id'])) {
    header("Location: assets/php/signIn.php");
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
    <title>Taskly</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1.0">
</head>

<body>
    <header>
        <h1>Taskly</h1>
        <div class="profile-container">
            <img class="avatar" id="avatar" src="<?php echo 'assets/avatars/' . $_SESSION['avatar'] . '.png'; ?>"
                alt="avatar">
            <div class="profile-menu" id="profileMenu">
                <ul>
                    <li><a href="assets/php/settings.php">Paramètres</a></li>
                    <li><a href="assets/php/logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </header>
    <main>
        <div class="sort-container">
            <label for="sortTasks">Trier par :</label>
            <select id="sortTasks">
                <option value="default">Par défaut</option>
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
    <script src="assets/js/script.js?v=1.1"></script>
    <script src="assets/js/account.js?v=1.1"></script>
    <?php message(); ?>
</body>

</html>