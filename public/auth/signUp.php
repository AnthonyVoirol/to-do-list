<?php
require_once __DIR__ . '/../../config/dbConfig.php';
require_once __DIR__ . '/../../src/services/auth.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $rememberMeCheck = isset($_POST['rememberMe']);
  $message = register_user(
    $conn,
    trim($_POST['username'] ?? ''),
    trim($_POST['email'] ?? ''),
    $_POST['password'] ?? '',
    $rememberMeCheck
  );

  if ($message === 'Registration successful.') {
    $_SESSION['flash_message'] = 'Registration successful!';
    header('Location: ../../app/dashboard.php');
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>S'inscrire - Taskly</title>
  <link rel="icon" type="image/png" href="../../assets/img/flavicon.png">
  <meta name="description"
    content="Créez votre compte Taskly gratuit en 30 secondes. Aucune carte bancaire requise. Commencez à organiser vos tâches dès maintenant.">

  <meta property="og:title" content="Inscription Gratuite - Taskly">
  <meta property="og:description"
    content="Créez votre compte gratuit et commencez à organiser vos tâches efficacement. Aucune carte bancaire requise.">
  <meta property="og:image" content="https://taskly.voirol.tech.com/assets/img/taskly-preview.jpg">
  <meta property="og:url" content="https://taskly.voirol.tech.com/public/auth/signUp.php">
  <meta property="og:type" content="website">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Inscription Gratuite - Taskly">
  <meta name="twitter:description"
    content="Rejoignez Taskly gratuitement. Organisation simple et efficace de toutes vos tâches.">
  <meta name="twitter:image" content="https://taskly.voirol.tech.com/assets/img/taskly-preview.jpg">

  <link rel="canonical" href="https://taskly.voirol.tech.com/public/auth/signUp.php">
  <link rel="stylesheet" href="../../assets/css/auth.css" />
</head>

<body>
  <section class="sectionLogin">
    <form class="formLogin" action="signUp.php" method="POST">
      <h1>Bienvenue</h1>
      <?php if (isset($message) && $message !== 'Registration successful.'): ?>
        <p style="color: red;"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>
      <article class="input">
        <input type="username" name="username" placeholder="Nom d'utilisateur" required />
        <input type="text" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Mot de passe" required />
      </article>
      <article class="checkboxArticle">
        <div class="checkbox">
          <input type="checkbox" name="rememberMe" />
          <label>Se souvenir de moi</label>
        </div>
      </article>
      <button type="submit">S'inscrire</button>
      <a href="signIn.php" class="subtitle">J'ai déjà un compte</a>
    </form>
  </section>
</body>

</html>