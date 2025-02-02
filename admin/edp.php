<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST")
{
    echo delete_edp((object) $_POST);
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
                    <span class="text">EDP</span>
                </div>
                <a href="/admin/add_edp.php" class="btn btn-dark acc-delete">Add EDP</a>
                <table id="accountTable" class="table-main">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Subject</th>
                            <th>Descriptive Title</th>
                            <th>Instructor</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $edp = edp_list();

                            while ($data = $edp->fetch_assoc())
                            {
                                echo '<tr class="acc-data">
                                <td class="acc-id">'.$data["edp_code"].'</td>
                                <td>'.$data["name"].'</td>
                                <td>'.$data["description"].'</td>
                                <td>'.$data["fname"].' '.$data["lname"].'</td>
                                <td>
                                    <a href="/admin/view_edp.php?id='.$data["id"].'" class="btn btn-dark">Edit</a>
                                    <button type="button" class="btn btn-dark acc-delete" data-bs-toggle="modal" data-bs-target="#deleteBtn">Delete</button>
                                </td> 
                                </tr>';
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="modal" id="deleteBtn" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete EDP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="alertMsg" class="">
                    </div>
                    <label id="switchConfirmText2"></label>
                    <div class="mt-1">
                        <label>EDP CODE: <span id="acc-id-del"><span></label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="deleteConfirmed" type="button" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'scripts.php'; ?>
    <script>
        $().ready( function () {
            $('#accountTable').DataTable();
        } );

        $('.acc-delete').each(function () {
            $(this).click(function () {
                id = $(this).closest('.acc-data').find('.acc-id').text();
                $('#acc-id-del').text(id);
                $('#switchConfirmText2').text('Are you sure to delete this?'); 
            })
        });

        $("#deleteConfirmed").click(function() {
            $.post("/admin/edp.php", {
                id: $('#acc-id-del').text()
            }, function (e) {
                data = JSON.parse(e);
                if (data.code == 200) {
                    alert("Success!");   
                    location.reload();
                    return;
                }

                alert("Failed!");
            })
        });
    </script>
</body>
</html>