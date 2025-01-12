<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST")
{
    $data = (object) $_POST;

    echo post_user($data);
    exit;
}

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
                    <span class="text">Add New User</span>
                </div>
                <div>
                    <div class="mb-3">
                        <label for="schoolField" class="form-label">School ID</label>
                        <input id="schoolField" type="number" class="form-control" id="schoolField" placeholder="EDP Code" required>
                    </div>
                    <div class="mb-3">
                        <label for="fnameField" class="form-label">First Name</label>
                        <input id="fnameField" type="text" class="form-control" id="fnameField" placeholder="First Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="mnameField" class="form-label">Middle Name</label>
                        <input id="mnameField" type="text" class="form-control" id="mnameField" placeholder="Middle Name">
                    </div>
                    <div class="mb-3">
                        <label for="lnameField" class="form-label">Last Name</label>
                        <input id="lnameField" type="text" class="form-control" id="lnameField" placeholder="Last Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="emailField" class="form-label">Email Address</label>
                        <input id="emailField" type="email" class="form-control" placeholder="Email Address" required>
                    </div>
                    <div class="mb-3">
                        <label for="courseField" class="form-label">Course</label>
                        <select id="courseField" class="form-select" aria-label="Default select example">
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
                    <div class="mb-3">
                        <label for="workField" class="form-label">Working Student?</label>
                        <select id="workField" class="form-select" aria-label="Default select example">
                            <option selected disabled>--Select Type--</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="typeField" class="form-label">Type</label>
                        <select id="typeField" class="form-select" aria-label="Default select example">
                            <option selected disabled>--Select Type--</option>
                            <option value="Student">Student</option>
                            <option value="Dean">Dean</option>
                        </select>
                    </div>
                    <a href="#" id="addBtn" class="btn btn-primary">Add</a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'scripts.php'; ?>
    <script>
        $().ready( function () {
            $('#accountTable').DataTable();
        } );

        $("#addBtn").click(function () {
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

            $.post("/admin/add_account.php", {
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
                    alert("New Password: " + data.password);   
                    location.reload();
                    return;
                }

                alert("Failed!");
            })

        })
    </script>
</body>
</html>