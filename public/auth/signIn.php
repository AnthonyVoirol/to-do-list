<?php
require_once __DIR__ . '/../../config/dbConfig.php';
require_once __DIR__ . '/../../src/services/auth.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $rememberMeCheck = isset($_POST['rememberMe']);

  $message = login_user(
    $conn,
    trim($_POST['email'] ?? ''),
    $_POST['password'] ?? '',
    $rememberMeCheck
  );

  if ($message === 'Login successful.') {
    $_SESSION['flash_message'] = 'Login successful!';
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
  <title>Connexion - Taskly</title>
  <link rel="stylesheet" href="../../assets/css/auth.css" />
  <link rel="icon" type="image/png" href="../../assets/img/flavicon.png">
</head>

<body>
  <section class="sectionLogin">
    <form class="formLogin" action="signIn.php" method="POST">
      <h1>Content de vous revoir</h1>
      <?php if (isset($message) && $message !== 'Login successful.'): ?>
        <p style="color: red;"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>
      <article class="input">
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Mot de passe" required />
      </article>
      <article class="checkboxArticle">
        <div class="checkbox">
          <input type="checkbox" name="rememberMe" />
          <label>Se souvenir de moi</label>
        </div>
        <a href="#">Mot de passe oublié</a>
      </article>
      <button type="submit">Se connecter</button>
      <a href="signUp.php" class="subtitle">Pas encore de compte ?</a>
    </form>
  </section>
</body>

</html>