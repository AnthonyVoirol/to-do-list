<?php
require_once 'dbConfig.php';
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  session_start();

  $message = login_user(
    $conn,
    trim($_POST['email'] ?? ''),
    $_POST['password'] ?? ''
  );

  if ($message === 'Login successful.') {
    $_SESSION['flash_message'] = 'Login successful!';
    header('Location: ../../');
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="../css/auth.css" />
</head>

<body>
  <section class="sectionLogin">
    <form class="formLogin" action="signIn.php" method="POST">
      <h1>Welcome Back</h1>
      <article class="input">
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
      </article>
      <article class="checkboxArticle">
        <div class="checkbox">
          <input type="checkbox" name="rememberMe" />
          <label>Remember me</label>
        </div>
        <a href="#">Forgot password?</a>
      </article>
      <button type="submit">Login</button>
      <a href="signUp.php" class="subtitle">Not have an account ?</a>
    </form>
  </section>
</body>

</html>