<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
    $data = (object) $_POST;
    
    echo put_edp($data);
    exit;
}

if (empty($_GET['id']))
{
    echo 'Invalid Request';
    exit;
}

$response = edp_info(mysqli_real_escape_string($conn, $_GET['id']));

if (!$response->num_rows) {
    echo 'ID not found';
    exit;
}

$edp = (object) $response->fetch_assoc();

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
                    <span class="text">View EDP</span>
                </div>
                <div>
                    <div class="mb-3">
                        <label for="idField" class="form-label">EDP Code</label>
                        <input id="idField" type="number" class="form-control" id="schoolField" placeholder="EDP Code" value="<?php echo $edp->edp_code; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="yearLevelField" class="form-label">Year Level</label>
                        <select id="yearLevelField" class="form-select" aria-label="Default select example">
                            <option selected disabled>--Select Year Level--</option>
                            <option value="1" <?php echo $edp->year_level == 1 ? 'selected' : ''; ?>>1st Year</option>
                            <option value="2" <?php echo $edp->year_level == 2 ? 'selected' : ''; ?>>2nd Year</option>
                            <option value="3" <?php echo $edp->year_level == 3 ? 'selected' : ''; ?>>3rd Year</option>
                            <option value="4" <?php echo $edp->year_level == 4 ? 'selected' : ''; ?>>4th Year</option>
                            <option value="5" <?php echo $edp->year_level == 5 ? 'selected' : ''; ?>>5th Year</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="yearField" class="form-label">Year Level</label>
                        <select id="yearField" class="form-select" aria-label="Default select example">
                            <option value="<?php echo $edp->year;?>"><?php echo  $edp->year . '-' . (intval($edp->year) + 1);?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="semesterField" class="form-label">Semester</label>
                        <select id="semesterField" class="form-select" aria-label="Default select example">
                            <option selected disabled>--Select Semester--</option>
                            <option value="1" <?php echo $edp->semester == 1 ? 'selected' : ''; ?>>1st</option>
                            <option value="2" <?php echo $edp->semester == 2 ? 'selected' : ''; ?>>2nd</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="subjectField" class="form-label">Subject</label>
                        <input id="subjectField" class="form-control" list="datalistOptions" id="exampleDataList" placeholder="Type to search..." value="<?php echo $edp->subject_id; ?>">
                        <datalist id="datalistOptions">
                            <?php
                                $data = subject_list();

                                while ($subject = $data->fetch_assoc())
                                {
                                    echo '<option value="'.$subject["id"].'">'.$subject["name"].' - '.$subject['description'].'</option>';
                                }
                            ?>
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label for="teacherField" class="form-label">Instructor</label>
                        <input id="teacherField" class="form-control" list="datalistTeacher" id="exampleDataList" placeholder="Type to search..." value="<?php echo $edp->teacher_id; ?>">
                        <datalist id="datalistTeacher">
                            <?php
                                $data = get_teachers();

                                while ($teacher = $data->fetch_assoc())
                                {
                                    echo '<option value="'.$teacher["id"].'">'.$teacher["fname"].' '.$teacher['lname'].'</option>';
                                }
                            ?>
                        </datalist>
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
            id = '<?php echo $_GET['id']; ?>';
            edpid = $('#idField').val();
            yearLevel = $('#yearLevelField').val();
            year = $('#yearField').val();
            semester = $('#semesterField').val();
            subject = $('#subjectField').val();
            teacher = $('#teacherField').val();

            // basic validation
            if (!edpid || !yearLevel || !year || !semester || !subject || !teacher) {
                alert("Fill the required fields!");
                return;
            }

            $.post("/admin/view_edp.php", {
                id: id,
                edpid: edpid,
                year_level: yearLevel,
                year: year,
                semester: semester,
                subject: subject,
                teacher: teacher
            }, function (e) {
                data = JSON.parse(e);
                if (data.code == 200) {
                    alert("Success!");   
                    location.href = '/admin/edp.php';
                    return;
                }

                alert("Failed!");
            })

        })
    </script>
</body>
</html>