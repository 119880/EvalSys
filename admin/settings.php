<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/profile.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST")
{
    $data = (object) $_POST;

    if (!empty($data->method))
    {
        echo change_password($data);
        exit;
    }

    echo update_profile($data);
    exit;
}

$response = profile_info();

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
                    <i class="uil uil-setting"></i>
                    <span class="text">Profile Settings</span>
                </div>
                <div>
                    <div class="mb-3">
                        <label for="fnameField" class="form-label">First Name</label>
                        <input id="fnameField" type="text" class="form-control" placeholder="First Name" value="<?php echo $user->fname;?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="mnameField" class="form-label">Middle Name</label>
                        <input id="mnameField" type="text" class="form-control" placeholder="Middle Name" value="<?php echo $user->mname;?>">
                    </div>
                    <div class="mb-3">
                        <label for="lnameField" class="form-label">Last Name</label>
                        <input id="lnameField" type="text" class="form-control" placeholder="Last Name" value="<?php echo $user->lname;?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="emailField" class="form-label">Email Address</label>
                        <input id="emailField" type="email" class="form-control" placeholder="Email Address" value="<?php echo $user->email;?>" required>
                    </div>
                    <a href="#" id="updateBtn" class="btn btn-primary">Update</a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changeBtn">Change Password</button>
                </div>
            </div>
        </div>
    </section>

    <div class="modal" id="changeBtn" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="currentField" class="form-label">Current Password</label>
                        <input id="currentField" type="password" class="form-control" placeholder="Current Password">
                    </div>
                    <div class="mb-3">
                        <label for="passwordField" class="form-label">New Password</label>
                        <input id="passwordField" type="password" class="form-control" placeholder="New Password">
                    </div>
                    <div class="mb-3">
                        <label for="cpasswordField" class="form-label">Confirm Password</label>
                        <input id="cpasswordField" type="password" class="form-control" placeholder="Confirm Password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="passBtn" type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'scripts.php'; ?>
    <script>
        $("#updateBtn").click(function () {
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

            $.post("/admin/settings.php", {
                fname: fname,
                mname: mname,
                lname: lname,
                email: email,
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

        $("#passBtn").click(function() {
            current = $("#currentField").val();
            password = $("#passwordField").val();
            cpassword = $("#cpasswordField").val();

            if (!current || !password || !cpassword) {
                alert("Fill the required fields!");
                return;
            }

            if (password != cpassword) {
                alert("Password not match");
                return;
            }

            $.post("/admin/settings.php", {
                method: "change_password",
                current: current,
                password: password,
                cpassword: cpassword
            }, function (e) {
                data = JSON.parse(e);
                if (data.code == 200) {
                    alert("Your password has been updated!");
                    return;
                }
                
                alert("Failed!");
            })
        });
    </script>
</body>
</html>