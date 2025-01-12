<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/requests/user.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST")
{
    echo form_submission((object) $_POST);
    exit;
}

if (empty($_SESSION['evaluatee']) && empty($_SESSION['type']))
{
    redirect("/");
    exit;
}

if (is_evaluated())
{
    echo "<script>alert('You are already submitted the evaluation form for ".$_SESSION['evaluatee']->fname." ".$_SESSION['evaluatee']->lname.".');
    location.href = '/';</script>";
    exit;
}


?>

<html>
<head>
    <?php include "component/head.php"; ?>
</head>
<body class="bg-dark text-white">
    <?php include "component/nav.php"; ?>
    <div class="container m-5">
        <div class="m-5">
            <h1><?php echo $_SESSION['type'].' Evaluation Form for '.$_SESSION['evaluatee']->fname .'  '. $_SESSION['evaluatee']->lname; ?></h1>
        </div>
        <div class="px-5 py-3 bg-white text-dark" >
            <?php
                $response = get_questions($_SESSION['type']);
                $question_number = 0;
                while ($question = $response->fetch_assoc())
                {
                    $question_number += 1;
                    echo '<div class="mb-3 w-50">
                        <p class="fs-4 fw-bold">'.$question['question'].'</p>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="q'.$question_number.'" id="vs'.$question_number.'" value="4-'.$question['id'].'">
                            <label class="form-check-label" for="vs'.$question_number.'">
                                Very Satisfactory
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="q'.$question_number.'" id="s'.$question_number.'" value="3-'.$question['id'].'">
                            <label class="form-check-label" for="s'.$question_number.'">
                                Satisfactory
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="q'.$question_number.'" id="f'.$question_number.'" value="2-'.$question['id'].'">
                            <label class="form-check-label" for="f'.$question_number.'">
                                Fair
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="q'.$question_number.'" id="us'.$question_number.'" value="1-'.$question['id'].'">
                            <label class="form-check-label" for="us'.$question_number.'">
                                Unsatisfactory
                            </label>
                        </div>
                    </div>';
                }
            ?>
            <div class="mb-3">
                <label for="feedbackField" class="form-label fs-4 fw-bold">Feedback</label>
                <textarea class="form-control" id="feedbackField" rows="3"></textarea>
            </div>
            <a href="#" id="submitBtn" class="btn btn-primary">Submit</a>
        </div>
    </div>
    <?php include "component/script.php"; ?>
    <script>
        $("#submitBtn").click(function () {
            id = <?php echo $_SESSION['evaluatee']->id; ?>;
            <?php
                $validateVar = "";
                for ($i = 1; $i <= $question_number; $i++)
                {
                    $validateVar .= ' || !q'.$i;
                    echo 'q'.$i.' = $(\'input[name="q'.$i.'"]:checked\').val();';
                }
            ?>

            // basic validation
            if (!id <?php echo $validateVar;?>) {
                alert("Fill the required fields!");
                return;
            }

            $.post("/question.php", {
                eval_id: id,
                type: "<?php echo $_SESSION['type']; ?>",
                feedback: $("#feedbackField").val(),
                data: [<?php
                    for ($i = 1; $i <= $question_number; $i++)
                    {
                        echo 'q'.$i.'.split("-"),';
                    }
                ?>]
            }, function (e) {
                data = JSON.parse(e);
                console.log(data);
                if (data) {
                    alert("Your evaluation form has been submitted.");
                    location.href = "/";
                    return;
                }

                alert("Something went wrong. Please try again.");
            })

        })
    </script>
</body>
</html>