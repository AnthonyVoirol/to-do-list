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
  <link rel="stylesheet" href="../../assets/css/auth.css" />
</head>

<body>
  <section class="sectionLogin">
    <form class="formLogin" action="signUp.php" method="POST">
      <h1>Welcome</h1>
      <?php if (isset($message) && $message !== 'Registration successful.'): ?>
        <p style="color: red;"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>
      <article class="input">
        <input type="username" name="username" placeholder="Username" required />
        <input type="text" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
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