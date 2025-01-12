<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$data = overview_data();
$history = get_recent();

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
            <div class="overview">
                <div class="title">
                    <i class="uil uil-chart"></i>
                    <span class="text">Dashboard</span>
                </div>

                <div class="boxes">
                    <div class="box box2">
                        <i class="uil uil-comments"></i>
                        <span class="text">Feedbacks</span>
                        <span class="number"><?php echo $data->comments ?></span>
                    </div>
                    <div class="box box3">
                        <i class="uil uil-share"></i>
                        <span class="text">Total Evaluate</span>
                        <span class="number"><?php echo $data->total ?></span>
                    </div>
                </div>
            </div>

            <div class="activity">
                <div class="title">
                    <i class="uil uil-clock-three"></i>
                    <span class="text">Recent Activity</span>
                </div>

                <table data-order='[[ 0, "desc" ]]' id="accountTable" class="display">
                    <thead>
                        <tr>
                            <th>Date Submitted</th>
                            <th>User</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while ($data = $history->fetch_assoc())
                            {
                                echo '<tr>
                                    <td>'.$data['created_at'].'</td>
                                    <td>('.$data['type'].') '.$data['fname'].' '.$data['lname'].'</td>
                                    <td>'.$data['message'].'</td>
                                    </tr>';
                            }
                        ?>
                    </tbody>
                </table>
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