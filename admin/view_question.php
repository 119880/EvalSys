<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/admin.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
    $data = (object) $_POST;
    
    echo put_question($data);
    exit;
}

if (empty($_GET['id']) || empty($_GET['type']))
{
    echo 'Invalid Request';
    exit;
}

$type = null;

if (isset($_GET['type']))
{
    if ($_GET['type'] == 'Teacher' || $_GET['type'] == 'Staff' || $_GET['type'] == 'Working Student')
    {
        $type = $_GET['type'];
    }
    else {
        redirect("/admin/form_question.php");
        exit;
    }
}

$response = get_question($type, mysqli_real_escape_string($conn, $_GET['id']));

if (!$response->num_rows) {
    echo 'Question not found';
    exit;
}

$question = (object) $response->fetch_assoc();

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
                    <span class="text">Edit Question</span>
                </div>
                <div>
                    <div class="mb-3">
                        <label for="questionField" class="form-label">Question</label>
                        <!-- <input id="schoolField" type="text" class="form-control" id="schoolField" placeholder="School ID" required> -->
                        <textarea class="form-control" id="questionField" rows="3"><?php echo $question->question; ?></textarea>
                    </div>
                    <a href="#" id="updateBtn" class="btn btn-primary">Edit</a>
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
            question = $.trim($('#questionField').val());

            // basic validation
            if (!question) {
                alert("Fill the required fields!");
                return;
            }

            $.post("/admin/view_question.php", {
                type: '<?php echo $type; ?>',
                id: <?php echo $question->id ?>,
                question: question,
            }, function (e) {
                data = JSON.parse(e);
                if (data.code == 200) {
                    alert("Success!");   
                    location.href = "/admin/form_question.php?type=<?php echo $type; ?>";
                    return;
                }

                alert("Failed!");
            })

        })
    </script>
</body>
</html>