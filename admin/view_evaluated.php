<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST")
{
    $data = (object) $_POST;

    echo get_user_results(
        mysqli_real_escape_string($conn, $_POST['id']),
        mysqli_real_escape_string($conn, $_POST['type']),
        mysqli_real_escape_string($conn, $_POST['form_type']),
        mysqli_real_escape_string($conn, $_POST['year']),
        mysqli_real_escape_string($conn, $_POST['semester']));
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
                    <span class="text">Evaluation Form for <?php echo $user->fname . ' ' . $user->lname . ' (' . $user->id . ')';?></span>
                </div>
                <div class="row mx-3">
                    <?php 
                        echo ($user->type == 'Student') ? '<div class="col-4 mb-3">
                        <label for="typeField" class="form-label">Type</label>
                        <select id="typeField" class="form-select" aria-label="Default select example">
                            <option selected disabled>--Select Type--</option>
                            <option value="Teacher">Teacher</option>
                            <option value="Staff">Staff</option>
                        </select>
                        </div>' : '';
                    ?>
                    <div class="col-4 mb-3">
                    <label for="yearField" class="form-label">Academic Year</label>
                        <select id="yearField" class="form-select" aria-label="Default select example">
                            <option selected disabled>--Select Year--</option>
                            <?php
                                $data = get_result_academic_year();
                                var_dump($data);
                                foreach ($data as $year)
                                {
                                    $next_year = intval($year) + 1;
                                    echo '<option value="'.$year.'">'.$year.'-'.($next_year).'</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-4 mb-3">
                    <label for="semesterField" class="form-label">Semester</label>
                        <select id="semesterField" class="form-select" aria-label="Default select example">
                            <option selected disabled>--Select Semester--</option>
                            <option value="1">1st Semester</option>
                            <option value="2">2nd Semester</option>
                        </select>
                    </div>
                    <a href="#" id="enterBtn" class="btn btn-primary">Enter</a>
                </div>
                <div class="mt-5">
                    <table data-order='[[ 0, "desc" ]]' id="accountTable" class="table-main">
                        <thead>
                            <tr>
                                <th>Date Submitted</th>
                                <th>Full Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <?php include 'scripts.php'; ?>
    <script>
        const table = new DataTable('#accountTable', {
            select: true
        });

        $("#enterBtn").click(function () {
            table.clear().draw();
            
            id = '<?php echo $user->id; ?>';
            type = '<?php echo $user->type; ?>';
            form_type = <?php echo ($user->type == 'Student') ? "$('#typeField').val()" : "'Working Student'"; ?>;
            year = $('#yearField').val();
            semester = $('#semesterField').val();

            // basic validation
            if (!form_type || !year || !semester) {
                alert("Fill the required fields!");
                return;
            }

            $.post("/admin/view_evaluated.php", {
                id: id,
                type: type,
                form_type: form_type,
                year: year,
                semester: semester
            }, function (e) {
                data = JSON.parse(e);
                if (data.code == 200) {
                    console.log(e);
                    if (!data.data.length)
                {
                    return 0;
                }
                    for (let row of data.data)
                    {
                        table.row.add(
                            (data.type == "Teacher") ? 
                            [row.created_at, `EDP CODE: ${row.edp_code} (${row.subject_name}) ${row.fname} ${row.lname}`, row.is_done] 
                            : [row.created_at, `${row.fname} ${row.lname}`, row.is_done] 
                        ).draw();   
                    }

                    return;
                }

                alert("Failed!");
            })

        })
    </script>
</body>
</html>