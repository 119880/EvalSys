<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST")
{
    $data = (object) $_POST;

    echo post_edp($data);
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
                    <span class="text">Add New EDP</span>
                </div>
                <div>
                    <div class="mb-3">
                        <label for="idField" class="form-label">EDP Code</label>
                        <input id="idField" type="number" class="form-control" id="schoolField" placeholder="EDP Code" required>
                    </div>
                    <div class="mb-3">
                        <label for="yearLevelField" class="form-label">Year Level</label>
                        <select id="yearLevelField" class="form-select" aria-label="Default select example">
                            <option selected disabled>--Select Year Level--</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                            <option value="5">5th Year</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="yearField" class="form-label">Year Level</label>
                        <select id="yearField" class="form-select" aria-label="Default select example">
                            <option selected disabled>--Select AY--</option>
                            <option value="<?php echo $SETTINGS->academicYear;?>"><?php echo $SETTINGS->academicYear . '-' . (intval($SETTINGS->academicYear) + 1);?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="semesterField" class="form-label">Semester</label>
                        <select id="semesterField" class="form-select" aria-label="Default select example">
                            <option selected disabled>--Select Semester--</option>
                            <option value="1">1st</option>
                            <option value="2">2nd</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="subjectField" class="form-label">Subject</label>
                        <input id="subjectField" class="form-control" list="datalistOptions" id="exampleDataList" placeholder="Type to search...">
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
                        <input id="teacherField" class="form-control" list="datalistTeacher" id="exampleDataList" placeholder="Type to search...">
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

            $.post("/admin/add_edp.php", {
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
                    location.reload();
                    return;
                }

                alert("Failed!");
            })

        })
    </script>
</body>
</html>