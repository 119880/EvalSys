<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {

    if (!empty($_POST['reset'])) {
        echo reset_password("user", mysqli_real_escape_string($conn, $_POST['id']));
        exit;
    }

    $data = (object) $_POST;
    
    echo put_user($data);
    exit;
}

if (empty($_GET['id']))
{
    echo 'Invalid Request';
    exit;
}

$response = user_info(mysqli_real_escape_string($conn, $_GET['id']));

if (!$response->num_rows) {
    echo 'ID not found';
    exit;
}

$user = (object) $response->fetch_assoc();

?>

<html lang="en">
<head>
    <?php include 'head.php'; ?>
</head>
<body>
    <?php include 'nav.php'; ?>
    <section class="dashboard">
        <?php include 'top.php' ?>

        <div class="dash-content">
            <div class="activity">
                <div class="title">
                    <i class="uil uil-user"></i>
                    <span class="text">User Information</span>
                </div>
                <div>
                    <div class="mb-3">
                        <label for="schoolField" class="form-label">School ID</label>
                        <input id="schoolField" type="text" class="form-control" id="schoolField" placeholder="School ID" value="<?php echo $user->school_id; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="fnameField" class="form-label">First Name</label>
                        <input id="fnameField" type="text" class="form-control" id="fnameField" placeholder="First Name" value="<?php echo $user->fname; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="mnameField" class="form-label">Middle Name</label>
                        <input id="mnameField" type="text" class="form-control" id="mnameField" placeholder="Middle Name" value="<?php echo $user->mname; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="lnameField" class="form-label">Last Name</label>
                        <input id="lnameField" type="text" class="form-control" id="lnameField" placeholder="Last Name" value="<?php echo $user->lname; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="emailField" class="form-label">Email Address</label>
                        <input id="emailField" type="text" class="form-control" id="lnameField" placeholder="Email Address" value="<?php echo $user->email; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="courseField" class="form-label">Course</label>
                        <select id="courseField" class="form-select" aria-label="Default select example">
                            <?php
                                $data = course_list();

                                while ($course = $data->fetch_assoc())
                                {
                                    if ($user->course_id == $course['id'])
                                    {
                                        echo '<option value="'.$course["id"].'" selected>'.$course["acronym"].'</option>';
                                        continue;
                                    }
                                    
                                    echo '<option value="'.$course["id"].'">'.$course["acronym"].'</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="workField" class="form-label">Working Student?</label>
                        <select id="workField" class="form-select" aria-label="Default select example">
                            <option selected disabled>--Select Type--</option>
                            <option value="1" <?php echo $user->is_working == 1 ? 'selected' : ''; ?>>Yes</option>
                            <option value="0" <?php echo $user->is_working == 0 ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="typeField" class="form-label">Type</label>
                        <select id="typeField" class="form-select" aria-label="Default select example">
                            <option value="Student" <?php echo $user->type == 'Student' ? 'selected' : ''; ?>>Student</option>
                            <option value="Dean" <?php echo $user->type == 'Dean' ? 'selected' : ''; ?>>Dean</option>
                        </select>
                    </div>
                    <a href="#" id="updateBtn" class="btn btn-primary">Update</a>
                    <a href="#" id="resetBtn" class="btn btn-primary">Reset Password</a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'scripts.php'; ?>
    <script>
        $().ready( function () {
            $('#accountTable').DataTable();
        } );

        $("#updateBtn").click(function () {
            id = <?php echo $user->id ?>;
            schoolId = $('#schoolField').val();
            fname = $('#fnameField').val();
            mname = $('#mnameField').val();
            lname = $('#lnameField').val();
            email = $('#emailField').val();
            course = $('#courseField').val();
            work = $('#workField').val();
            type = $('#typeField').val();

            // basic validation
            if (!schoolId || !fname || !lname || !email || !course || !work || !type) {
                alert("Fill the required fields!");
                return;
            }

            $.post("/admin/view_account.php", {
                id: id,
                school_id: schoolId,
                fname: fname,
                mname: mname,
                lname: lname,
                email: email,
                course: course,
                is_working: work,
                type: type
            }, function (e) {
                data = JSON.parse(e);
                if (data.code == 200) {
                    alert("Success!");
                    location.reload();
                    return;
                }
                
                alert("Failed!");
            })

        })

        $("#resetBtn").click(function() {
            $.post("/admin/view_account.php", {
                id: <?php echo $user->id ?>,
                reset: 1
            }, function (e) {
                data = JSON.parse(e);
                if (data.code == 200) {
                    alert("New Password: " + data.password);
                    location.href = "/admin/account.php";
                    return;
                }
                
                alert("Failed!");
            })
        });
    </script>
</body>
</html>