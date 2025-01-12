<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/login.php';

// send_registration();
// exit;

$method = $_SERVER['REQUEST_METHOD'];

$result = false;

if (!empty($_GET['logout']))
{
  session_destroy();
  redirect("/login.php");
}

if ($method == "POST") {
    if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        // echo "<script>alert('Invalid Email Address');</script>"; 
        signup(mysqli_real_escape_string($conn, $_POST['email']));
        exit;
    }
}

if (!empty($_SESSION['user'])) {
  redirect("/");
}

?>


<html lang="en">
  <head>
    <title><?php echo $TITLE; ?></title>
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">

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

    <link href="./assets/css/signin.css" rel="stylesheet">
  </head>
  <body class="text-center">
    
<main class="form-signin">
<?php
      if (!$result && $method == 'POST') {
        echo '<div class="alert alert-danger" role="alert">Invalid Email Address</div>';
      }
    ?>
  <form method="post">
    <h1 class="h3 mb-3 fw-normal">Enter your email address.</h1>

    <div class="form-floating">
      <input name="email" type="email" class="form-control" id="floatingInput" placeholder="Email Address">
      <label for="floatingInput">Email Address</label>
    </div>
    <button id="submitBtn" class="w-100 btn btn-lg btn-primary hide-button" type="submit">Send</button>
    <div class="d-flex justify-content-center d-none loading">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
  </form>

  <?php include 'component/script.php'; ?>
  <script>
    $.switch = () => {
          $('.loading').toggleClass('d-none');
          $('.hide-button').toggleClass('d-none');  
      } 

    $('#submitBtn').click(function () {
      $.switch();
    })
  </script>
</main>


    
  </body>
</html>
