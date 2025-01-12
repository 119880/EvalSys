<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
    $data = (object) $_POST;
    
    echo put_subject($data);
    exit;
}

if (empty($_GET['id']))
{
    echo 'Invalid Request';
    exit;
}

$response = subject_info(mysqli_real_escape_string($conn, $_GET['id']));

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
                    <span class="text">Subject</span>
                </div>
                <div>
                <div class="mb-3">
                        <label for="nameField" class="form-label">Name Code</label>
                        <input id="nameField" type="text" class="form-control" id="fnameField" placeholder="Name Code" value="<?php echo $user->name; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="descriptionField" class="form-label">Middle Name</label>
                        <input id="descriptionField" type="text" class="form-control" id="mnameField" placeholder="Description" value="<?php echo $user->description; ?>">
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
            name = $('#nameField').val();
            description = $('#descriptionField').val();

            // basic validation
            if (!name || !description) {
                alert("Fill the required fields!");
                return;
            }

            $.post("/admin/view_subject.php", {
                id: id,
                name: name,
                description: description,
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