<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {

    $data = (object) $_POST;
    
    echo put_user($data);
    exit;
}

if (empty($_GET['id']))
{
    echo 'Invalid Request';
    exit;
}

$response = student_form_info(mysqli_real_escape_string($conn, $_GET['id']));

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
                <a href="/admin/studyload.php?id=<?php echo $user->user_id; ?>" class="btn btn-dark mb-2">Study Load</a>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="fnameField" class="form-label">First Name</label>
                        <input id="fnameField" type="text" class="form-control" id="fnameField" placeholder="First Name" value="<?php echo $user->fname; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="mnameField" class="form-label">Middle Name</label>
                        <input id="mnameField" type="text" class="form-control" id="mnameField" placeholder="Middle Name" value="<?php echo $user->mname; ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="lnameField" class="form-label">Last Name</label>
                        <input id="lnameField" type="text" class="form-control" id="lnameField" placeholder="Last Name" value="<?php echo $user->lname; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="schoolField" class="form-label">School ID</label>
                        <input id="schoolField" type="text" class="form-control" id="schoolField" placeholder="School ID" value="<?php echo $user->school_id; ?>" required>
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label for="workField" class="form-label">Working Student?</label>
                        <select id="workField" class="form-select" aria-label="Default select example">
                            <option selected disabled>--Select Type--</option>
                            <option value="1" <?php echo $user->is_working == 1 ? 'selected' : ''; ?>>Yes</option>
                            <option value="0" <?php echo $user->is_working == 0 ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="workField" class="form-label">Image</label>
                        <div>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#showBtn"><img src="<?php echo $EDP_IMAGE_PATH_VIEW . $user->image;?>" alt="" height="250px"></a>
                        </div>
                    </div>
                    <div class="hide-button">
                        <a href="#" id="verifyBtn" class="btn btn-primary">Verify</a>
                        <button data-bs-toggle="modal" data-bs-target="#rejectBtn" class="btn btn-primary">Reject</button>
                    </div>
                    <div class="d-flex justify-content-center d-none loading">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal" id="showBtn" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="<?php echo $EDP_IMAGE_PATH_VIEW . $user->image;?>" class="w-100">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="rejectBtn" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select id="reasonField" class="form-select" aria-label="Default select example">
                        <option selected disabled>--Select Reason--</option>
                        <?php 
                            foreach ($REJECT_REASON as $reason)
                            {
                                echo '<option value="'.$reason.'">'.$reason.'</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="modal-footer hide-button">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="rejectSendBtn" type="button" class="btn btn-danger">Reject</button>
                </div>
                <div class="d-flex justify-content-center d-none loading">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php include 'scripts.php'; ?>
    <script>
        $().ready( function () {
            $('#accountTable').DataTable();
        } );

        $.switch = () => {
            $('.loading').toggleClass('d-none');
            $('.hide-button').toggleClass('d-none');  
        } 

        $("#verifyBtn").click(function () {

            id = <?php echo $user->user_id ?>;
            schoolId = $('#schoolField').val();
            fname = $('#fnameField').val();
            mname = $('#mnameField').val();
            lname = $('#lnameField').val();
            course = $('#courseField').val();
            work = $('#workField').val();

            // basic validation
            if (!schoolId || !fname || !lname || !course || !work) {
                alert("Fill the required fields!");
                return;
            }

            $.switch();

            $.post("/admin/view_form.php", {
                id: id,
                school_id: schoolId,
                fname: fname,
                mname: mname,
                lname: lname,
                course: course,
                is_working: work,
                email: '<?php echo $user->email;?>',
                type: 'Student',
                form_id: '<?php echo $user->stu_form_id;?>',
                status: 'Verified',
                reason: null,
            }, function (e) {
                $.switch();
                data = JSON.parse(e);
                if (data.code == 200) {
                    alert("Success!");
                    location.href = '/admin/verification.php';
                    return;
                }
                
                alert("Failed!");
            })

        });

        $("#rejectSendBtn").click(function () {


            id = <?php echo $user->user_id ?>;
            schoolId = $('#schoolField').val();
            fname = $('#fnameField').val();
            mname = $('#mnameField').val();
            lname = $('#lnameField').val();
            course = $('#courseField').val();
            work = $('#workField').val();
            reason = $('#reasonField').val();

            // basic validation
            if (!schoolId || !fname || !lname || !course || !work || !reason) {
                alert("Fill the required fields!");
                return;
            }

            $.switch();

            $.post("/admin/view_form.php", {
                id: id,
                school_id: schoolId,
                fname: fname,
                mname: mname,
                lname: lname,
                course: course,
                is_working: work,
                email: '<?php echo $user->email;?>',
                type: 'Student',
                form_id: '<?php echo $user->stu_form_id;?>',
                status: 'Rejected',
                reason: reason,
            }, function (e) {
                $.switch();
                data = JSON.parse(e);
                if (data.code == 200) {
                    alert("Success!");
                    location.href = '/admin/verification.php';
                    return;
                }
                
                alert("Failed!");
            })

        });

    </script>
</body>
</html>