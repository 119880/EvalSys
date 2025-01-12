<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/user.php';

is_form_open();

if ($_SESSION['user']->type != 'Dean'){
    is_user_verified();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST")
{
    echo set_evaluatee((object) $_POST);
    exit;
}

$_SESSION['evaluatee'] = null;

if (empty($_GET['type']))
{
    echo 'Invalid Request';
    exit;
}

$response = null;

if ($_GET['type'] == 'Teacher' && $_SESSION['user']->type == "Student")
{
    $response = get_teachers(mysqli_real_escape_string($conn, $_SESSION['user']->id));
}
else if ($_GET['type'] == 'Staff' && $_SESSION['user']->type == "Student") 
{
    $response = get_staff($_SESSION['user']->id);
}
else if ($_GET['type'] == 'Working Student' && $_SESSION['user']->type == "Dean")
{
    $response = get_working_student($_SESSION['user']->id);
}
else {
    echo 'Invalid Type';
    exit;
}

$_SESSION['type'] = $_GET['type'];

if (!$response->num_rows && $_GET['type'] == "Teacher") {
    echo ($SETTINGS->semester == '1') ? 'You are not currently enrolled or you are already submitted the form for the <b> Academic Year '. $SETTINGS->academicYear . '-'.(intval($SETTINGS->academicYear) + 1).' 1st Semester</b>'
        : 'You are not currently enrolled or you are already submitted the form for the <b> Academic Year '. $SETTINGS->academicYear . '-'.(intval($SETTINGS->academicYear) + 1).' 2nd Semester</b>';
    exit;
}
if (!$response->num_rows) {
    echo ($SETTINGS->semester == '1') ? 'You are already submitted the form for the <b> Academic Year '. $SETTINGS->academicYear . '-'.(intval($SETTINGS->academicYear) + 1).' 1st Semester</b>'
        : 'You are already submitted the form for the <b> Academic Year '. $SETTINGS->academicYear . '-'.(intval($SETTINGS->academicYear) + 1).' 2nd Semester</b>';
    exit;
}

?>

<html>
<head>
    <?php include "component/head.php"; ?>
</head>
<body class="bg-dark text-white">
    <?php include "component/nav.php"; ?>
    <div class="cover-container d-flex w-100 h-100 align-items-center justify-content-center flex-column">
        <div class="mb-3 w-50">
            <div>
                <h1 class="fs-1 fw-bold">Select <?php echo $_GET['type']; ?></h1>
            </div>
            <input id="evalField" class="form-control" list="datalistOptions" id="exampleDataList" placeholder="Type to search...">
            <datalist id="datalistOptions">
                <?php

                    while ($eval = $response->fetch_assoc())
                    {
                        if ($_SESSION['type'] == "Teacher") {
                            echo '<option value="'.$eval["edp_id"].'">(EDP '.$eval["edp_code"].') '.$eval['name'].' - '.$eval["fname"].' '.$eval['lname'].'</option>';
                        }
                        else {
                            echo '<option value="'.$eval["id"].'">'.$eval["fname"].' '.$eval['lname'].'</option>';
                        }
                    }
                ?>
            </datalist>
            <a href="#" id="selectBtn" class="btn btn-primary mt-3">Select</a>
        </div>
    </div>
    <?php include "component/script.php"; ?>
    <script>
        $("#selectBtn").click(function () {
            id = $('#evalField').val();

            // basic validation
            if (!id) {
                alert("Fill the required fields!");
                return;
            }

            $.post("/select_evaluate.php", {
                id: id,
                type: "<?php echo $_GET['type'];?>"
            }, function (e) {
                data = JSON.parse(e);
                if (data) {
                    location.href = "/question.php";
                    return;
                }

                alert("Something went wrong. Please try again.");
            })

        })
    </script>
</body>
</html>