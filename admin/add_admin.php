<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST")
{
    $data = (object) $_POST;

    echo post_admin($data);
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
                    <span class="text">Add Admin</span>
                </div>
                <div>
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
                        <input id="emailField" type="email" class="form-control" id="lnameField" placeholder="Email Address" required>
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
            fname = $('#fnameField').val();
            mname = $('#mnameField').val();
            lname = $('#lnameField').val();
            email = $('#emailField').val();

            // basic validation
            if (!fname || !lname || !email) {
                alert("Fill the required fields!");
                return;
            }

            if (!(/^[^\s@]+@[^\s@]+\.[^\s@]+$/).test(email))
            {
                alert("Invalid Email Address.");
                return;
            }

            $.post("/admin/add_admin.php", {
                fname: fname,
                mname: mname,
                lname: lname,
                email: email,
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