<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST")
{
    $data = (object) $_POST;

    echo update_settings($data);
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
                    <i class="uil uil-setting"></i>
                    <span class="text">Form Settings</span>
                </div>
                <div>
                    <div class="mb-3">
                        <label for="titleField" class="form-label">Title</label>
                        <input id="titleField" type="text" class="form-control" id="schoolField" placeholder="School ID" value="<?php echo $SETTINGS->title?>">
                    </div>
                    <div class="mb-3">
                        <label for="ayField" class="form-label">A.Y</label>
                        <input id="ayField" type="number" class="form-control" id="fnameField" placeholder="First Name" value="<?php echo $SETTINGS->academicYear?>">
                    </div>
                    <div class="mb-3">
                        <label for="semesterField" class="form-label">Semester</label>
                        <select id="semesterField" class="form-select" aria-label="Default select example">
                            <option value="1" <?php echo ($SETTINGS->semester == "1") ? 'selected' : ''; ?>>1st Semester</option>
                            <option value="2" <?php echo ($SETTINGS->semester == "2") ? 'selected' : ''; ?>>2nd Semester</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="startField" class="form-label">Date Start</label>
                        <div class="input-group date datepicker" data-provide="datepicker">
                            <input id="startField" type="text" class="form-control" value="<?php echo $SETTINGS->start?>">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="endField" class="form-label">Date End</label>
                        <div class="input-group date datepicker" data-provide="datepicker">
                            <input id="endField" type="text" class="form-control" value="<?php echo $SETTINGS->end?>">
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>
                    </div>
                    <a href="#" id="addBtn" class="btn btn-primary">Update</a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'scripts.php'; ?>
    <script>
        $().ready( function () {
            $('#accountTable').DataTable();
        } );

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            startDate: '0d'
        });

        $("#addBtn").click(function () {
            title = $('#titleField').val();
            ay = $('#ayField').val();
            semester = $('#semesterField').val();
            start = $('#startField').val();
            end = $('#endField').val();

            // basic validation
            if (!title || !ay || !semester || !start || !end) {
                alert("Fill the required fields!");
                return;
            }

            if (start > end) {
                alert("The start date cannot be later than the end date. Please enter a valid date range.");
                return;
            }

            $.post("/admin/form_settings.php", {
                title: title,
                academicYear: ay,
                semester: semester,
                start: start,
                end: end,
            }, function (e) {
                data = JSON.parse(e);
                if (data) {
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