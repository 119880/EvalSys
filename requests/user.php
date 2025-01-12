<?php

require 'mysql.php';

function is_logon()
{
    if (empty($_SESSION['user'])) {
        redirect("/login.php");
    }
}

function logout()
{
    $_SESSION['user'] = null;
    redirect("/login.php");
    exit;
}

function course_list()
{
    $query = "SELECT * FROM `course`";

    return $GLOBALS['conn']->query($query);
}

function is_form_open()
{
    $current = new DateTime();
    
    if ($GLOBALS['SETTINGS']->start > $current->format("Y-m-d")) {
        $_SESSION['user'] = null;
        echo "<script>alert('The evaluation form is not yet open.');
            location.href = '/login.php';</script>";
        exit;
    }

    if ($GLOBALS['SETTINGS']->end < $current->format("Y-m-d")) {
        $_SESSION['user'] = null;
        echo "<script>alert('The evaluation form is already closed.');
            location.href = '/login.php';</script>";
        exit;
    }

}

function is_evaluated()
{
    $table = null;

    if ($_SESSION['type'] == "Teacher")
    {
        $table = "teacher_form";
    }
    else if ($_SESSION['type'] == "Staff")
    {
        $table = "staff_form";
    }
    else if ($_SESSION['type'] == "Working Student")
    {
        $table = "working_student_form";
    }
    else {
        redirect("/");
        exit;
    }

    $query = "SELECT * FROM `$table`
        WHERE eval_id='".$_SESSION['evaluatee']->id."'
        AND year='".$GLOBALS['SETTINGS']->academicYear."'
        AND semester='".$GLOBALS['SETTINGS']->semester."'
        AND user_id = '".$_SESSION['user']->id."'";
    
    $response = $GLOBALS['conn']->query($query);
    return ($response->num_rows) ? true : 0;
}

function get_teachers($id)
{
    $query = "SELECT teacher.id, teacher.fname, teacher.lname, edp.edp_code as edp_code, edp.id as edp_id, subject.name FROM `teacher` 
                INNER JOIN edp ON teacher.id = edp.teacher_id
                INNER JOIN subject ON subject.id = edp.subject_id
                INNER JOIN study_load ON study_load.edp_id = edp.id
                INNER JOIN user ON study_load.user_id = user.id
                WHERE study_load.status='1'
                AND user.id='$id'
                AND edp.year = '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->academicYear)."'
                AND edp.semester = '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->semester)."'
                AND edp.id NOT IN (SELECT eval_id from teacher_form WHERE user_id='$id' AND YEAR = '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->academicYear)."' AND semester = '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->semester)."')";

    return $GLOBALS['conn']->query($query);
}

function get_staff($id)
{
    $query = "SELECT id, fname, lname FROM `staff` 
                WHERE status='1'
                AND id NOT IN (SELECT eval_id from staff_form WHERE user_id='$id' AND YEAR = '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->academicYear)."' AND semester = '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->semester)."')";

    return $GLOBALS['conn']->query($query);
}

function get_working_student($id)
{
    $query = "SELECT id, fname, lname FROM `user` 
                WHERE status='1' AND is_working='1' AND type='Student' AND course_id='".$_SESSION['user']->course_id."'
                AND id NOT IN (SELECT eval_id from working_student_form WHERE user_id='$id' AND YEAR = '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->academicYear)."' AND semester = '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->semester)."')";

    return $GLOBALS['conn']->query($query);
}

function set_evaluatee($data)
{
    try {
        $table = null;
        $query = null;

        if ($data->type == "Teacher")
        {
            $table = "teacher";
            $query = "SELECT edp.id as id, fname, lname FROM `teacher` 
                INNER JOIN edp ON edp.teacher_id = teacher.id
                WHERE edp.id='$data->id'";
        }
        else if ($data->type == "Staff")
        {
            $table = "staff";
            $query = "SELECT * FROM `$table` WHERE id='$data->id'";
        }
        else if ($data->type == "Working Student")
        {
            $table = "user";
            $query = "SELECT * FROM `$table` WHERE id='$data->id'";
        }
        else {
            return 0;
        }

        $response = $GLOBALS['conn']->query($query);

        if ($response->num_rows)
        {
            $_SESSION['evaluatee'] = (object) $response->fetch_assoc();
            return True;
        }
    } catch (Exception $ex)
    {

    }

    return 0;
}


function get_questions($type)
{
    $table = null;
    if ($type == 'Teacher') {
        $table = 'teacher_form_question';
    }
    else if ($type == 'Staff') {
        $table = 'staff_form_question';
    }
    else if ($type == 'Working Student') {
        $table = 'working_student_form_question';
    }
    else {
        return 0;
    }
    $query = "SELECT * FROM `$table` WHERE is_deleted='0'";
    // $query = "SELECT * FROM `form_question` WHERE is_deleted='0' LIMIT 1";

    return $GLOBALS['conn']->query($query);
}

function form_submission($data)
{
    try {
        $form_table = null;
        $fb_table = null;

        if ($data->type == "Teacher") {
            $form_table = "teacher_form";
            $fb_table = "teacher_feedback";
        }
        else if ($data->type == "Staff") {
            $form_table = "staff_form";
            $fb_table = "staff_feedback";
        }
        else if ($data->type == "Working Student") {
            $form_table = "working_student_form";
            $fb_table = "working_student_feedback";
        }
        else {
            return 0;
        }

        $eval_id = $_SESSION['evaluatee']->id;

        foreach ($data->data as $question)
        {
            $query = "INSERT INTO `$form_table`
            (point, year, semester, question_id, user_id, eval_id)
            VALUES (
            '".mysqli_real_escape_string($GLOBALS['conn'], $question[0])."',
            '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->academicYear)."',
            '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->semester)."',
            '".mysqli_real_escape_string($GLOBALS['conn'], $question[1])."',
            '".$_SESSION['user']->id."',
            '".$eval_id."')";

            if (!$GLOBALS['conn']->query($query))
            {
                return 0;
            }
            
        }

        if (!empty($data->feedback)) {
            $query = "INSERT INTO `$fb_table`
            (feedback, year, semester, user_id, eval_id)
            VALUES (
            '".mysqli_real_escape_string($GLOBALS['conn'], $data->feedback)."',
            '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->academicYear)."',
            '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->semester)."',
            '".$_SESSION['user']->id."',
            '".$eval_id."')";

            if (!$GLOBALS['conn']->query($query))
            {
                return 0;
            }
        }

        $query = "INSERT INTO `history` (message, user_id)
        VALUES (
        'The user has been submitted their evaluation form.',
        '".$_SESSION['user']->id."')";

        if ($GLOBALS['conn']->query($query))
        {
            return true;
        }

    } catch (Exception $ex)
    {

    }
    
    return 0;
}

function post_form($file, $data)
{
    try {
        $filename = $data->school_id .'-'. date("Y-m-d") . '-' . rand(100, 999) . '.png';
        $tmp = $_FILES["image"]["tmp_name"];

        if (move_uploaded_file($tmp, $GLOBALS['EDP_IMAGE_PATH'] . $filename))
        {
            $query = "UPDATE `user` SET
                school_id='".mysqli_real_escape_string($GLOBALS['conn'], $data->school_id)."',
                fname='".mysqli_real_escape_string($GLOBALS['conn'], $data->fname)."',
                mname='".mysqli_real_escape_string($GLOBALS['conn'], $data->mname)."',
                lname='".mysqli_real_escape_string($GLOBALS['conn'], $data->lname)."',
                course_id='".mysqli_real_escape_string($GLOBALS['conn'], $data->course)."',
                is_working='".mysqli_real_escape_string($GLOBALS['conn'], $data->is_working)."'
                WHERE id='".$_SESSION['user']->id."'";

            if ($GLOBALS['conn']->query($query))
            {
                $query = "INSERT INTO student_form
                (image, year, semester, user_id) VALUES (
                '$filename',
                '".$GLOBALS['SETTINGS']->academicYear."',
                '".$GLOBALS['SETTINGS']->semester."',
                '".$_SESSION['user']->id."')";

                if ($GLOBALS['conn']->query($query))
                {
                    echo "<script>alert('Your information has been submitted successfully!');
                    location.href = '/login.php?logout=1';</script>";
                    return true;
                }
            }
        }
        else {
            echo "<script>alert('Failed to upload image to the server.');</script>";
        }


    } catch (Exception $ex) {

    }

    echo "<script>alert('Something went wrong!.');</script>";
}

function is_user_verified()
{
    
    $query = "SELECT * FROM student_form
        WHERE user_id='".$_SESSION['user']->id."'
        AND year='".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->academicYear)."'
        AND semester='".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->semester)."'
        AND status='Verified'
        ORDER BY id DESC LIMIT 1";

    $response = $GLOBALS['conn']->query($query);

    if (!$response->num_rows) {
        echo "<script>location.href = '/verification.php';</script>";
        exit;
    }

    $data = (object) $response->fetch_assoc();

    if ($data->status == 'Not Verified')
    {
        echo "<script>location.href = '/verification.php';</script>";
        exit;
    }

    if ($data->status != 'Verified')
    {
        echo "<script>alert('Your application is still in progress. You will be notified via email.');
        location.href='/login.php?logout=1';
        </script>";
        exit;
    }
}

function is_user_pending() {
    $query = "SELECT * FROM student_form
        WHERE user_id='".$_SESSION['user']->id."'
        AND year='".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->academicYear)."'
        AND semester='".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->semester)."'
        ORDER BY id DESC LIMIT 1";

    $response = $GLOBALS['conn']->query($query);

    if ($response->num_rows) {
        $data = (object) $response->fetch_assoc();

        if ($data->status == 'Verified') 
        {
            echo "<script>location.href='/';</script>";
            exit;
        }
    }
}

is_logon();