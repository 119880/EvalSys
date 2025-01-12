<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/user.php';

if ($_SESSION['user']->type == 'Dean')
{
    redirect("/");
    exit;
}

is_user_pending();

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST")
{
    if (!isset($_FILES["image"]))
    {
        echo "<script>alert('Someting went wrong!');
        location.href='/login.php?logout=1'</script>";
        exit;
    }

    $data = (object) $_POST;
    $file = (object) $_FILES;

    echo post_form($file, $data);
}

?>

<html>
<head>
    <?php include "component/head.php"; ?>
</head>
<body class="bg-dark text-white">
    <?php include "component/nav.php"; ?>
    <div class="cover-container d-flex w-100 h-100 align-items-center justify-content-center flex-column">
        <div class="text-center">
            <h1 class="fs-1 fw-bold">Enter your information</h1>
            <h4 class="fs-4 fw-bold">
                <?php echo ($SETTINGS->semester == 1) ? "For Academic Year " . $SETTINGS->academicYear . '-' . ($SETTINGS->academicYear + 1) . ' 1st Semester'
                    : "For Academic Year " . $SETTINGS->academicYear . '-' . ($SETTINGS->academicYear + 1) . ' 2nd Semester';?>
                </h4>
        </div>
        <form class="w-50 row g-3" method="post" enctype="multipart/form-data">
            <div class="col-12">
                <label for="schoolField" class="form-label">School ID</label>
                <input name="school_id" type="number" class="form-control" id="schoolField" placeholder="School ID" required value="<?php echo $_SESSION['user']->school_id;?>">
            </div>
            <div class="col-12">
                <label for="fnameField" class="form-label">First Name</label>
                <input name="fname" type="text" class="form-control" id="fnameField" placeholder="First Name" required value="<?php echo $_SESSION['user']->fname;?>">
            </div>
            <div class="col-12">
                <label for="mnameField" class="form-label">Middle Name</label>
                <input name="mname" type="text" class="form-control" id="mnameField" placeholder="Middle Name" value="<?php echo $_SESSION['user']->mname;?>">
            </div>
            <div class="col-12">
                <label for="lnameField" class="form-label">Last Name</label>
                <input name="lname" type="text" class="form-control" id="lnameField" placeholder="Last Name" required value="<?php echo $_SESSION['user']->lname;?>">
            </div>
            <div class="col-md-3">
                <label for="courseField" class="form-label">Course</label>
                <select name="course" id="courseField" class="form-select" aria-label="Default select example" required>
                    <option selected disabled>--Select Course--</option>
                    <?php
                        $data = course_list();

                        while ($course = $data->fetch_assoc())
                        {
                            echo '<option value="'.$course["id"].'">'.$course["acronym"].'</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="workField" class="form-label">Working Student?</label>
                <select name="is_working" id="workField" class="form-select" aria-label="Default select example" required>
                    <option selected disabled>--Select Type--</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="formFile" class="form-label">Upload your study load</label>
                <input name="image" class="form-control" type="file" accept="image/png, image/jpeg" id="formFile" required>
            </div>
            <button type="submit" class="btn btn-primary hide-button">Submit</button>
            <div class="d-flex justify-content-center d-none loading">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </form>
    </div>
    <?php include "component/script.php"; ?>
    <script>
        $.switch = () => {
            $('.loading').toggleClass('d-none');
            $('.hide-button').toggleClass('d-none');  
        } 

        $('#submitBtn').click(function () {
            $.switch();
        })
  </script>
</body>
</html>