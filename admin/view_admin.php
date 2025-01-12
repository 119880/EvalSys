<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST")
{

    if (!empty($_POST['reset'])) {
        echo reset_password("admin", mysqli_real_escape_string($conn, $_POST['id']));
        exit;
    }

    $data = (object) $_POST;

    echo put_admin($data);
    exit;
}

if (empty($_GET['id']))
{
    echo 'Invalid Request';
    exit;
}

$response = admin_info(mysqli_real_escape_string($conn, $_GET['id']));

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
                    <span class="text">Edit Admin</span>
                </div>
                <div>
                    <div class="mb-3">
                        <label for="fnameField" class="form-label">First Name</label>
                        <input id="fnameField" type="text" class="form-control" id="fnameField" placeholder="First Name" value="<?php echo $user->fname;?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="mnameField" class="form-label">Middle Name</label>
                        <input id="mnameField" type="text" class="form-control" id="mnameField" placeholder="Middle Name" value="<?php echo $user->mname;?>">
                    </div>
                    <div class="mb-3">
                        <label for="lnameField" class="form-label">Last Name</label>
                        <input id="lnameField" type="text" class="form-control" id="lnameField" placeholder="Last Name" value="<?php echo $user->lname;?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="emailField" class="form-label">Email Address</label>
                        <input id="emailField" type="email" class="form-control" id="lnameField" placeholder="Email Address" value="<?php echo $user->email;?>" required>
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

            $.post("/admin/view_admin.php", {
                id: id,
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

        $("#resetBtn").click(function() {
            $.post("/admin/view_admin.php", {
                id: <?php echo $user->id ?>,
                reset: 1
            }, function (e) {
                data = JSON.parse(e);
                if (data.code == 200) {
                    alert("New Password: " + data.password);
                    location.href = "/admin/admins.php";
                    return;
                }
                
                alert("Failed!");
            })
        });
    </script>
</body>
</html>