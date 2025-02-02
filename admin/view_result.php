<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if (empty($_GET['id']) || empty($_GET['type']) || empty($_GET['year']) || empty($_GET['semester']))
{
    echo 'Invalid Request';
    exit;
}

$response = eval_result(
    mysqli_real_escape_string($conn, $_GET['type']),
    mysqli_real_escape_string($conn, $_GET['id']),
    mysqli_real_escape_string($conn, $_GET['year']),
    mysqli_real_escape_string($conn, $_GET['semester']));

if (!$response)
{
    echo 'Invalid Request';
    exit;
}

if (!$response->data->num_rows) {
    echo 'Question not found';
    exit;
}

$info = (object) $response->info->fetch_assoc();

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
                    <span class="text"><?php echo ($_GET['type'] == "Teacher")? $info->edp_code . " (" . $info->name . ") " . $info->fname . " " . $info->lname : $info->fname . " " . $info->lname;?>'s Evaluation Results</span>
                </div>
                <div class="mt-3">
                    <table data-page-length='25' id="accountTable" class="table-main">
                        <thead>
                            <tr>
                                <th>Skills</th>
                                <th>Rating</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $total_row = $response->data->num_rows;
                                $total_points = 0;

                                while ($result = $response->data->fetch_assoc())
                                {
                                    echo '<tr>
                                        <td>'.$result['question'].'</td>
                                        <td>'.$result['average'].'</td>
                                        <td>'.remarks_enum($result['average']).'</td>
                                    </tr>';

                                    $total_points += $result['average'];
                                }

                                $total_rating = $total_points / $total_row;
                            ?>
                        </tbody>
                    </table>
                </div>
                <div>
                    <p class="fs-5 fw-bold">FINAL RATING : <?php echo round($total_rating, 4) . " (" . remarks_enum($total_rating) . ")";?></p>
                </div>
                <div class="mt-2">
                    <div class="mb-3">
                        <a href="<?php echo '/admin/print.php?id='. $_GET['id'] . '&type=' . $_GET['type'] . '&year=' . $_GET['year'] . '&semester=' . $_GET['semester'];?>" class="btn btn-primary">Print</a>
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            Open Feedbacks
                        </button>
                    </div>
                    <div class="collapse" id="collapseExample">
                        <?php
                            while ($fb = $response->feedback->fetch_assoc())
                            {
                                echo '<div class="card card-body">
                                        '.$fb['feedback'].'
                                    </div>';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'scripts.php'; ?>
    <script>
        $().ready( function () {
            $('#accountTable').DataTable();
        } );
    </script>
</body>
</html>