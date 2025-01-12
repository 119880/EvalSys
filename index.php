<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/user.php';

is_form_open();

if ($_SESSION['user']->type == "Dean")
{
    redirect("/select_evaluate.php?type=Working Student");
    exit;
}

is_user_verified();

?>

<html>
<head>
    <?php include "component/head.php"; ?>
</head>
<body class="bg-dark text-white">
    <?php include "component/nav.php"; ?>
    <div class="cover-container d-flex w-100 h-100 align-items-center justify-content-center flex-column">
        <div>
            <h1 class="fs-1 fw-bold">Choose</h1>
        </div>
        <div>
            <a href="/select_evaluate.php?type=Teacher"class="btn btn-primary fw-bold fs-5" style="width: 100px; height: 50px;">Teacher</a>
            <a href="/select_evaluate.php?type=Staff"class="btn btn-primary fw-bold fs-5" style="width: 100px; height: 50px;">Staff</a>
        </div>
    </div>
    <?php include "component/script.php"; ?>
</body>
</html>