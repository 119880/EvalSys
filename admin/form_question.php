<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST")
{
    echo delete_question((object) $_POST);
    exit;
}

$type = null;

if (isset($_GET['type']))
{
    if ($_GET['type'] == 'Teacher' || $_GET['type'] == 'Staff' || $_GET['type'] == 'Working Student')
    {
        $type = $_GET['type'];
    }
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
                    <span class="text">Questions</span>
                </div>
                <div class="row mx-3">
                    <div class="col-4 mb-3">
                        <label for="typeField" class="form-label">Type</label>
                        <select id="typeField" class="form-select" aria-label="Default select example">
                            <?php echo (!empty($type))? '' : '<option selected disabled>--Select Type--</option>';?>
                            <option value="Teacher" <?php echo ($type == 'Teacher')? 'selected' : ''; ?>>Teacher</option>
                            <option value="Staff" <?php echo ($type == 'Staff')? 'selected' : ''; ?>>Staff</option>
                            <option value="Working Student" <?php echo ($type == 'Working Student')? 'selected' : ''; ?>>Working Student</option>
                        </select>
                    </div>
                    <a href="#" id="enterBtn" class="btn btn-primary">Enter</a>
                </div>
                <div class="mt-5">
                <?php echo (empty($type))? '' : '<a href="/admin/add_question.php?type='.$type.'" class="btn btn-dark acc-delete">Add Question</a>';?>
                    <table id="accountTable" class="display">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Question</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if (!empty($type))
                                {
                                    $questions = question_list($type);

                                    while ($data = $questions->fetch_assoc())
                                    {
                                        echo '<tr class="acc-data">
                                        <td class="acc-id">'.$data["id"].'</td>
                                        <td>'.$data["question"].'</td>
                                        <td>
                                            <a href="/admin/view_question.php?id='.$data["id"].'&type='.$type.'" class="btn btn-dark">Edit</a>
                                            <button type="button" class="btn btn-dark acc-delete" data-bs-toggle="modal" data-bs-target="#deleteBtn">Delete</button>
                                        </td> 
                                        </tr>';
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
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
                        <label>ID: <span id="acc-id-del"><span></label>
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
            $.post("/admin/form_question.php", {
                type: '<?php echo (empty($type)) ? '' : $type ;?>',
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

        $("#enterBtn").click(function () {
            type = $("#typeField").val();

            if (!type) {
                alert('Please select type.');
                return;
            }

            location.href = `/admin/form_question.php?type=${type}`;
        })
    </script>
</body>
</html>