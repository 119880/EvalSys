<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST")
{
    $data = (object) $_POST;

    echo post_subject($data);
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
                    <span class="text">Add New Subject</span>
                </div>
                <div>
                    <div class="mb-3">
                        <label for="nameField" class="form-label">Name Code</label>
                        <input id="nameField" type="text" class="form-control" id="fnameField" placeholder="Name Code" required>
                    </div>
                    <div class="mb-3">
                        <label for="descriptionField" class="form-label">Description</label>
                        <input id="descriptionField" type="text" class="form-control" id="mnameField" placeholder="Description">
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
            name = $('#nameField').val();
            description = $('#descriptionField').val();

            // basic validation
            if (!name || !description) {
                alert("Fill the required fields!");
                return;
            }

            $.post("/admin/add_subject.php", {
                name: name,
                description: description,
            }, function (e) {
                data = JSON.parse(e);
                if (data.code == 200) {
                    alert("Success!");   
                    window.history.go(-2);
                    return;
                }

                alert("Failed!");
            })

        })
    </script>
</body>
</html>