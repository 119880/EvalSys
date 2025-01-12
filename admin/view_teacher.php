<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
    $data = (object) $_POST;
    
    echo put_teacher($data);
    exit;
}

if (empty($_GET['id']))
{
    echo 'Invalid Request';
    exit;
}

$response = teacher_info(mysqli_real_escape_string($conn, $_GET['id']));

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
                    <span class="text">Teacher Information</span>
                </div>
                <div>
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
                    <a href="#" id="updateBtn" class="btn btn-primary">Update</a>
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
            course = $('#courseField').val();

            // basic validation
            if (!fname || !lname || !course) {
                alert("Fill the required fields!");
                return;
            }

            $.post("/admin/view_teacher.php", {
                id: id,
                fname: fname,
                mname: mname,
                lname: lname,
                course: course
            }, function (e) {
                if (e.code == 200) {
                    alert(e);
                }
                else {
                    alert(e);
                }
            })

        })
    </script>
</body>
</html>