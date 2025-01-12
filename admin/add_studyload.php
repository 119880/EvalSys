<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
    $data = (object) $_POST;
    
    echo post_studyload($data);
    exit;
}

if (empty($_GET['id']))
{
    echo 'Invalid Request';
    exit;
}

$response = user_info(mysqli_real_escape_string($conn, $_GET['id']));

$user = (object) $response->fetch_assoc();

$response = study_load_image(mysqli_real_escape_string($conn, $_GET['id']));

$form = (object) $response->fetch_assoc();

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
                    <span class="text">Add Study Load for <?php echo "(".$user->school_id.")".$user->fname." ".$user->lname;?></span>
                </div>
                <div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <div>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#showBtn"><img src="<?php echo $EDP_IMAGE_PATH_VIEW . $form->image;?>" alt="" height="250px"></a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edpField" class="form-label">EDP Code</label>
                        <input id="edpField" class="form-control" list="datalistOptions" id="exampleDataList" placeholder="Type to search...">
                        <datalist id="datalistOptions">
                            <?php
                                $data = edp_list_current();

                                while ($subject = $data->fetch_assoc())
                                {
                                    echo '<option value="'.$subject["id"].'">('.$subject["edp_code"].')'.$subject["name"].' - '.$subject['description'].'</option>';
                                }
                            ?>
                        </datalist>
                    </div>
                    <a href="#" id="addBtn" class="btn btn-primary">Add</a>
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
                    <img src="<?php echo $EDP_IMAGE_PATH_VIEW . $form->image;?>" class="w-100">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'scripts.php'; ?>
    <script>
        $().ready( function () {
            $('#accountTable').DataTable();
        } );

        $("#addBtn").click(function () {
            id = <?php echo $user->id; ?>;
            edp = $('#edpField').val();

            // basic validation
            if (!edp) {
                alert("Fill the required fields!");
                return;
            }

            $.post("/admin/add_studyload.php", {
                id: id,
                edp: edp,
            }, function (e) {
                data = JSON.parse(e);
                if (data.code == 200) {
                    alert("Success!");   
                    location.href = "/admin/studyload.php?id=<?php echo $user->id; ?>";
                    return;
                }

                alert("Failed!");
            })

        })
    </script>
</body>
</html>