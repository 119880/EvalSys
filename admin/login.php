<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/login.php';

$method = $_SERVER['REQUEST_METHOD'];

$result = false;

if (!empty($_GET['logout']))
{
  $_SESSION['admin'] = null;
  redirect("/admin/login.php");
}

if ($method == "POST") {
  $result = admin_login(mysqli_real_escape_string($conn, $_POST['email']), mysqli_real_escape_string($conn, $_POST['password']));
  if ($result) {
    redirect("/admin");
  }
}

if (!empty($_SESSION['admin'])) {
  redirect("/admin");
}

?>

<html lang="en">
  <head>
    <title><?php echo $TITLE; ?> | Admin Dashboard</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    <link href="/assets/css/signin.css" rel="stylesheet">
  </head>
  <body class="text-center">
    
<main class="form-signin">
    <?php
      if (!$result && $method == 'POST') {
        echo '<div class="alert alert-danger" role="alert">Invalid Credentials</div>';
      }
    ?>
  <form method="post">
    <h1 class="h3 mb-3 fw-normal">Admin Login</h1>

    <div class="form-floating">
      <input name="email" type="email" class="form-control" id="floatingInput" placeholder="Email" required>
      <label for="floatingInput">Email address</label>
    </div>
    <div class="form-floating position-relative">
      <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password">
      <label for="floatingPassword">Password</label>
      <i class="uil uil-eye-slash toggle-password position-absolute" id="togglePassword" style="top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"></i>
    </div>

    <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
  </form>
</main>


<script src="<?= '/assets/js/password-script.js' ?>"></script>
  </body>
</html>
