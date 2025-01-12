<?php

require 'mysql.php';
require 'mail.php';

function is_logon()
{
    if (empty($_SESSION['admin'])) {
        redirect("/admin/login.php");
    }
}

function generate_password()
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$&";
    $password = substr( str_shuffle( $chars ), 0, 8 );
    return $password;
}

function overview_data()
{
    $comments = 0;
    $total = 0;

    // Total Comments

    $query = "SELECT COUNT(id) as count FROM `teacher_feedback`";
    $comments += $GLOBALS['conn']->query($query)->fetch_assoc()['count'];
    
    $query = "SELECT COUNT(id) as count FROM `staff_feedback`";
    $comments += $GLOBALS['conn']->query($query)->fetch_assoc()['count'];

    $query = "SELECT COUNT(id) as count FROM `working_student_feedback`";
    $comments += $GLOBALS['conn']->query($query)->fetch_assoc()['count'];
    
    // Total Evaluated
    
    $query = "SELECT YEAR, semester, eval_id, user_id FROM `teacher_form` GROUP BY YEAR, semester, eval_id, user_id";
    $total += $GLOBALS['conn']->query($query)->num_rows;
    
    $query = "SELECT YEAR, semester, eval_id, user_id FROM `staff_form` GROUP BY YEAR, semester, eval_id, user_id";
    $total += $GLOBALS['conn']->query($query)->num_rows;
    
    $query = "SELECT YEAR, semester, eval_id, user_id FROM `working_student_form` GROUP BY YEAR, semester, eval_id, user_id";
    $total += $GLOBALS['conn']->query($query)->num_rows;

    return (object) array(
        "percentage" => 100,
        "comments" => $comments,
        "total" => $total
    );
}

function user_info($id)
{
    $query = "SELECT * FROM `user` WHERE id='$id'";
    return $GLOBALS['conn']->query($query);
}

function teacher_info($id)
{
    $query = "SELECT * FROM `teacher` WHERE id='$id'";
    return $GLOBALS['conn']->query($query);
}

function staff_info($id)
{
    $query = "SELECT * FROM `staff` WHERE id='$id'";
    return $GLOBALS['conn']->query($query);
}

function admin_info($id)
{
    $query = "SELECT * FROM `admin` WHERE id='$id' AND id <> '".$_SESSION['admin']->id."'";
    return $GLOBALS['conn']->query($query);
}

function get_users()
{
    $query = "SELECT user.id, user.school_id, user.fname, user.lname, course.acronym, user.type FROM `user` 
                INNER JOIN course ON user.course_id = course.id
                WHERE status='1'";

    return $GLOBALS['conn']->query($query);
}

function get_teachers()
{
    $query = "SELECT teacher.id, teacher.fname, teacher.lname, course.acronym FROM `teacher` 
                INNER JOIN course ON teacher.course_id = course.id
                WHERE status='1'";

    return $GLOBALS['conn']->query($query);
}

function get_staffs()
{
    $query = "SELECT id, fname, lname FROM `staff` 
                WHERE status='1'";

    return $GLOBALS['conn']->query($query);
}

function get_admins()
{
    $query = "SELECT id, fname, lname FROM `admin` 
                WHERE status='1' AND id <> ".$_SESSION['admin']->id;

    return $GLOBALS['conn']->query($query);
}

function post_user($data)
{
    try {
        $password = generate_password();

        $options = [
            'cost' => 12,
        ];
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, $options);

        $query = "INSERT INTO `user`
        (school_id, fname, mname, lname, email, is_working, type, course_id, password)
        VALUES (
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->school_id)."', 
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->fname)."', 
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->mname)."', 
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->lname)."', 
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->email)."', 
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->is_working)."', 
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->type)."', 
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->course)."',
        '$hashed_password')";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200,
                "password" => $password
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);    
}

function put_user($data)
{
    try {
        $query = "UPDATE `user` SET
            school_id='".mysqli_real_escape_string($GLOBALS['conn'], $data->school_id)."',
            fname='".mysqli_real_escape_string($GLOBALS['conn'], $data->fname)."',
            mname='".mysqli_real_escape_string($GLOBALS['conn'], $data->mname)."',
            lname='".mysqli_real_escape_string($GLOBALS['conn'], $data->lname)."',
            email='".mysqli_real_escape_string($GLOBALS['conn'], $data->email)."',
            course_id='".mysqli_real_escape_string($GLOBALS['conn'], $data->course)."',
            type='".mysqli_real_escape_string($GLOBALS['conn'], $data->type)."',
            is_working='".mysqli_real_escape_string($GLOBALS['conn'], $data->is_working)."'
            WHERE id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."'";

        if ($GLOBALS['conn']->query($query))
        {
            if (isset($data->form_id) && isset($data->status))
            {
                $user = user_info(mysqli_real_escape_string($GLOBALS['conn'], $data->id));
                $user = (object) $user->fetch_assoc();

                if ($data->status == 'Verified' || $data->status == 'Rejected')
                {
                    $query = "UPDATE student_form SET
                        status = '$data->status'
                        WHERE id = '$data->form_id'";

                    if ($GLOBALS['conn']->query($query))
                    {
                        $message = ($data->status == 'Rejected') ? "Your application has been rejected.\n\nReason: $data->reason" 
                            : "You are verified. You may now login to proceed.";

                        send_form_update($user->email, $data->status, $message);
                    }
                    else {
                        $output = array(
                            "code" => 204
                        );
                    
                        return json_encode($output); 
                    }
                }
            }

            $output = array(
                "code" => 200
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);  
}

function delete_user($data)
{
    try {
        $query = "UPDATE `user` SET status='0' WHERE id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."'";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output); 
        } 

    } catch (Exception $ex) {

    }

    $output = array(
        "code" => 204
    );

    return json_encode($output);  
}

function post_teacher($data)
{
    try {
        $query = "INSERT INTO `teacher`
        (fname, mname, lname, course_id)
        VALUES (
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->fname)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->mname)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->lname)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->course)."')";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);    
}

function put_teacher($data)
{
    try {
        $query = "UPDATE `teacher` SET
            fname='".mysqli_real_escape_string($GLOBALS['conn'], $data->fname)."',
            mname='".mysqli_real_escape_string($GLOBALS['conn'], $data->mname)."',
            lname='".mysqli_real_escape_string($GLOBALS['conn'], $data->lname)."',
            course_id='".mysqli_real_escape_string($GLOBALS['conn'], $data->course)."'
            WHERE id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."'";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);  
}

function delete_teacher($data)
{
    try {
        $query = "UPDATE `teacher` SET status='0' WHERE id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."'";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output); 
        } 

    } catch (Exception $ex) {

    }

    $output = array(
        "code" => 204
    );

    return json_encode($output);  
}

function post_staff($data)
{
    try {
        $query = "INSERT INTO `staff`
        (fname, mname, lname)
        VALUES (
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->fname)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->mname)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->lname)."')";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);    
}

function put_staff($data)
{
    try {
        $query = "UPDATE `staff` SET
            fname='".mysqli_real_escape_string($GLOBALS['conn'], $data->fname)."',
            mname='".mysqli_real_escape_string($GLOBALS['conn'], $data->mname)."',
            lname='".mysqli_real_escape_string($GLOBALS['conn'], $data->lname)."'
            WHERE id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."'";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);  
}

function delete_staff($data)
{
    try {
        $query = "UPDATE `staff` SET status='0' WHERE id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."'";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output); 
        } 

    } catch (Exception $ex) {

    }

    $output = array(
        "code" => 204
    );

    return json_encode($output);  
}

function post_admin($data)
{
    try {
        $password = generate_password();

        $options = [
            'cost' => 12,
        ];
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, $options);

        $query = "INSERT INTO `admin`
        (fname, mname, lname, email, password)
        VALUES (
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->fname)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->mname)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->lname)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->email)."',
        '$hashed_password')";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200,
                "password" => $password
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);    
}

function put_admin($data)
{
    try {
        $query = "UPDATE `admin` SET
            fname='".mysqli_real_escape_string($GLOBALS['conn'], $data->fname)."',
            mname='".mysqli_real_escape_string($GLOBALS['conn'], $data->mname)."',
            lname='".mysqli_real_escape_string($GLOBALS['conn'], $data->lname)."',
            email='".mysqli_real_escape_string($GLOBALS['conn'], $data->email)."',
            WHERE id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."'";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);  
}

function delete_admin($data)
{
    try {
        $query = "UPDATE `admin` SET status='0' WHERE id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."'";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output); 
        } 

    } catch (Exception $ex) {

    }

    $output = array(
        "code" => 204
    );

    return json_encode($output);  
}

function course_list()
{
    $query = "SELECT * FROM `course`";

    return $GLOBALS['conn']->query($query);
}

function subject_list()
{
    $query = "SELECT * FROM `subject`";

    return $GLOBALS['conn']->query($query);
}

function edp_list()
{
    $query = "SELECT teacher.fname, teacher.lname, subject.name, subject.description, edp.id, edp.edp_code FROM `edp` 
                INNER JOIN teacher ON edp.teacher_id = teacher.id
                INNER JOIN subject ON edp.subject_id = subject.id
                WHERE edp.status='1'";

    return $GLOBALS['conn']->query($query);
}

function edp_list_current()
{
    $query = "SELECT teacher.fname, teacher.lname, subject.name, subject.description, edp.id, edp.edp_code FROM `edp` 
                INNER JOIN teacher ON edp.teacher_id = teacher.id
                INNER JOIN subject ON edp.subject_id = subject.id
                WHERE edp.status='1'
                AND edp.year = '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->academicYear)."'
                AND edp.semester = '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->semester)."'";

    return $GLOBALS['conn']->query($query);
}

function post_edp($data)
{
    try {
        $query = "INSERT INTO `edp`
        (edp_code, year_level, year, semester, subject_id, teacher_id)
        VALUES (
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->edpid)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->year_level)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->year)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->semester)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->subject)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->teacher)."')";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);
}

function put_edp($data)
{
    try {
        $query = "UPDATE `edp` SET
            edp_code='".mysqli_real_escape_string($GLOBALS['conn'], $data->edpid)."',
            year_level='".mysqli_real_escape_string($GLOBALS['conn'], $data->year_level)."',
            year='".mysqli_real_escape_string($GLOBALS['conn'], $data->year)."',
            semester='".mysqli_real_escape_string($GLOBALS['conn'], $data->semester)."',
            subject_id='".mysqli_real_escape_string($GLOBALS['conn'], $data->subject)."',
            teacher_id='".mysqli_real_escape_string($GLOBALS['conn'], $data->teacher)."'
            WHERE id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."'";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        echo ($ex);
        exit;
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);
}

function edp_info($id)
{
    $query = "SELECT * FROM `edp` WHERE id='$id'";
    return $GLOBALS['conn']->query($query);
}

function delete_edp($data)
{
    try {
        $query = "UPDATE `edp` SET status='0' WHERE id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."'";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output); 
        } 

    } catch (Exception $ex) {

    }

    $output = array(
        "code" => 204
    );

    return json_encode($output);  
}

function studyload($id)
{
    $query = "SELECT study_load.id, study_load.user_id, CONCAT(user.fname, ' ', user.lname) as student_name, teacher.fname, teacher.lname, subject.name, subject.description, study_load.edp_id, edp.edp_code FROM `study_load`
                INNER JOIN user ON study_load.user_id = user.id 
                INNER JOIN edp ON study_load.edp_id = edp.id
                INNER JOIN teacher ON edp.teacher_id = teacher.id
                INNER JOIN subject ON edp.subject_id = subject.id
                WHERE study_load.status='1'
                AND study_load.user_id='$id'
                AND edp.year = '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->academicYear)."'
                AND edp.semester = '".mysqli_real_escape_string($GLOBALS['conn'], $GLOBALS['SETTINGS']->semester)."'";

    return $GLOBALS['conn']->query($query);
}

function post_studyload($data)
{
    try {
        $query = "INSERT INTO `study_load`
        (user_id, edp_id)
        VALUES (
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->edp)."')";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);
}

function delete_studyload($data)
{
    try {
        $query = "UPDATE `study_load` SET status='0' WHERE user_id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."' AND edp_id='".mysqli_real_escape_string($GLOBALS['conn'], $data->edp)."'";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output); 
        } 

    } catch (Exception $ex) {

    }

    $output = array(
        "code" => 204
    );

    return json_encode($output);  
}

function question_list($type)
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

    return $GLOBALS['conn']->query($query);
}

function post_question($data)
{
    try {
        $table = null;
        if ($data->type == 'Teacher') {
            $table = 'teacher_form_question';
        }
        else if ($data->type == 'Staff') {
            $table = 'staff_form_question';
        }
        else if ($data->type == 'Working Student') {
            $table = 'working_student_form_question';
        }
        else {
            $output = array(
                "code" => 204
            );
        
            return json_encode($output);
        }

        $query = "INSERT INTO `$table`
        (question)
        VALUES ('".mysqli_real_escape_string($GLOBALS['conn'], $data->question)."')";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);
}

function get_question($type, $id)
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
        $output = array(
            "code" => 204
        );
    
        return json_encode($output);
    }

    $query = "SELECT * FROM `$table` WHERE id='$id'";
    return $GLOBALS['conn']->query($query);
}

function put_question($data)
{
    try {
        $table = null;
        if ($data->type == 'Teacher') {
            $table = 'teacher_form_question';
        }
        else if ($data->type == 'Staff') {
            $table = 'staff_form_question';
        }
        else if ($data->type == 'Working Student') {
            $table = 'working_student_form_question';
        }
        else {
            $output = array(
                "code" => 204
            );
        
            return json_encode($output);
        }

        $query = "UPDATE `$table` SET
            question='".mysqli_real_escape_string($GLOBALS['conn'], $data->question)."'
            WHERE id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."'";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);
}

function delete_question($data)
{
    try {
        $table = null;
        if ($data->type == 'Teacher') {
            $table = 'teacher_form_question';
        }
        else if ($data->type == 'Staff') {
            $table = 'staff_form_question';
        }
        else if ($data->type == 'Working Student') {
            $table = 'working_student_form_question';
        }
        else {
            $output = array(
                "code" => 204
            );
        
            return json_encode($output);
        }
        
        $query = "UPDATE `$table` SET is_deleted='1' WHERE id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."'";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output); 
        } 

    } catch (Exception $ex) {

    }

    $output = array(
        "code" => 204
    );

    return json_encode($output);  
}

function get_year_array($response)
{
    $array = [];
    while ($data = $response->fetch_assoc())
    {
        array_push($array, $data['year']);
    }

    return $array;
}

function get_result_academic_year()
{
    $table = null;
    
    $query = "SELECT year FROM teacher_form GROUP BY year";
    $teacher = get_year_array($GLOBALS['conn']->query($query));
    $query = "SELECT year FROM staff_form GROUP BY year";
    $staff = get_year_array($GLOBALS['conn']->query($query));
    $query = "SELECT year FROM working_student_form GROUP BY year";
    $working = get_year_array($GLOBALS['conn']->query($query));
    
    return array_unique(array_merge($teacher, $staff, $working));
}

function get_results($type, $year, $semester)
{
    try
    {
        if ($type == "Teacher")
        {
            $query = "SELECT teacher_form.year, teacher_form.semester, eval_id, edp.id as edp_id, edp.edp_code as edp_code, name as subject_name, fname, lname, MAX(teacher_form.created_at) AS updated_at FROM teacher_form 
            INNER JOIN edp ON teacher_form.eval_id = edp.id
            INNER JOIN subject ON edp.subject_id = subject.id
            INNER JOIN teacher ON edp.teacher_id = teacher.id
            WHERE teacher_form.year = '$year' AND teacher_form.semester='$semester'
            GROUP BY YEAR, semester, eval_id";
        }
        if ($type == "Staff")
        {
            $query = "SELECT staff_form.year, staff_form.semester, eval_id, fname, lname, MAX(staff_form.created_at) as updated_at FROM staff_form 
            INNER JOIN staff ON staff_form.eval_id = staff.id
            WHERE staff_form.year = '$year' AND staff_form.semester='$semester'
            GROUP BY YEAR, semester, eval_id";
        }
        if ($type == "Working Student")
        {
            $query = "SELECT working_student_form.year, working_student_form.semester, eval_id, fname, lname, MAX(working_student_form.created_at) as updated_at FROM working_student_form 
            INNER JOIN user ON working_student_form.eval_id = user.id
            WHERE working_student_form.year = '$year' AND working_student_form.semester='$semester'
            GROUP BY YEAR, semester, eval_id";
        }
        else {
            json_encode(array(
                "code" => 204,
            ));
        }

        $result = $GLOBALS['conn']->query($query);
        
        return json_encode(array(
            "code" => 200,
            "type" => $type,
            "data" => mysqli_fetch_all($result, MYSQLI_ASSOC)
        ));
    } catch (Exception $ex)
    {

    }

    return json_encode(array(
        "code" => 204,
    ));
}

function eval_result($type, $id, $year, $semester)
{
    $table = null;
    $fb_table = null;
    $question = null;
    $data = null;

    try
    {
        if ($type == "Teacher") {
            $table = "teacher_form";
            $fb_table = "teacher_feedback";
            $question = "teacher_form_question";
            // $data = teacher_info(mysqli_real_escape_string($GLOBALS['conn'], $id));
            $query = "SELECT *, edp.id as edp_id, edp.edp_code as edp_code FROM `edp` 
                INNER JOIN teacher ON teacher.id = edp.teacher_id
                INNER JOIN subject ON subject.id = edp.subject_id
                WHERE edp.id='".mysqli_real_escape_string($GLOBALS['conn'], $id)."'";

            $data = $GLOBALS['conn']->query($query);
        }
        else if ($type == "Staff") {
            $table = "staff_form";
            $fb_table = "staff_feedback";
            $question = "staff_form_question";
            $data = staff_info(mysqli_real_escape_string($GLOBALS['conn'], $id));
        }
        else if ($type == "Working Student") {
            $table = "working_student_form";
            $fb_table = "working_student_feedback";
            $question = "working_student_form_question";
            $data = user_info(mysqli_real_escape_string($GLOBALS['conn'], $id));
        }
        else {
            return 0;
        }
    
        $result = "SELECT $table.question_id, $question.question, AVG(POINT) as average FROM $table
            INNER JOIN $question ON $table.question_id = $question.id
            WHERE year = '".mysqli_real_escape_string($GLOBALS['conn'], $year)."'
            AND SEMESTER = '".mysqli_real_escape_string($GLOBALS['conn'], $semester)."'
            AND eval_id = '".mysqli_real_escape_string($GLOBALS['conn'], $id)."'
            AND is_deleted='0'
            GROUP BY question_id;";

        $feedback = "SELECT * FROM $fb_table
            WHERE year = '".mysqli_real_escape_string($GLOBALS['conn'], $year)."'
            AND SEMESTER = '".mysqli_real_escape_string($GLOBALS['conn'], $semester)."'
            AND eval_id = '".mysqli_real_escape_string($GLOBALS['conn'], $id)."'";
    
        return (object) array(
            "info" => $data,
            "data" => $GLOBALS['conn']->query($result),
            "feedback" => $GLOBALS['conn']->query($feedback)
        );
        
    } catch (Exception $ex)
    {

    }

    return 0;
}

function remarks_enum($score)
{
    if ($score <= 1.74)
    {
        return "Unsatisfactory";
    }
    else if ($score <= 2.49)
    {
        return "Fair";
    }
    else if ($score <= 3.24)
    {
        return "Satisfactory";
    }
    else if ($score <= 4)
    {
        return "Very Satisfactory";
    }
}

function reset_password($table, $id)
{
    try {
        $password = generate_password();

        $options = [
            'cost' => 12,
        ];
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, $options);

        $query = "UPDATE `$table` SET password = '$hashed_password' WHERE id = '$id'";

        if ($GLOBALS['conn']->query($query)) 
        {
            return json_encode(array(
                "code" => 200,
                "password" => $password
            ));
        }
    } catch (Exception $ex)
    {

    }

    return json_encode(array(
        "code" => 204
    ));
}

function get_recent()
{
    $query = "SELECT * FROM history
    INNER JOIN user ON user.id = history.user_id";

    return $GLOBALS['conn']->query($query);
}

function get_pending_users()
{
    $query = "SELECT *, student_form.id as stu_form_id FROM user
    INNER JOIN student_form ON user.id = student_form.user_id
    WHERE student_form.status='Pending'
    AND year='".$GLOBALS['SETTINGS']->academicYear."'
    AND semester='".$GLOBALS['SETTINGS']->semester."'";

    return $GLOBALS['conn']->query($query);
}

function student_form_info($id)
{
    $query = "SELECT *, student_form.id as stu_form_id, student_form.user_id as user_id FROM user
    INNER JOIN student_form ON user.id = student_form.user_id
    WHERE student_form.status='Pending'
    AND year='".$GLOBALS['SETTINGS']->academicYear."'
    AND semester='".$GLOBALS['SETTINGS']->semester."'
    AND student_form.id = '$id'";
    
    return $GLOBALS['conn']->query($query);
}

function study_load_image($id)
{
    $query = "SELECT *, student_form.id as stu_form_id, student_form.user_id as user_id FROM user
    INNER JOIN student_form ON user.id = student_form.user_id
    WHERE year='".$GLOBALS['SETTINGS']->academicYear."'
    AND semester='".$GLOBALS['SETTINGS']->semester."'
    AND student_form.user_id = '$id'
    ORDER BY student_form.id desc
    LIMIT 1";
    
    return $GLOBALS['conn']->query($query);
}

function get_subjects()
{
    $query = "SELECT * FROM `subject` 
                WHERE status='1'";

    return $GLOBALS['conn']->query($query);
}

function post_subject($data)
{
    try {
        $query = "INSERT INTO `subject`
        (name, description)
        VALUES (
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->name)."',
        '".mysqli_real_escape_string($GLOBALS['conn'], $data->description)."')";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);    
}

function put_subject($data)
{
    try {
        $query = "UPDATE `subject` SET
            name='".mysqli_real_escape_string($GLOBALS['conn'], $data->name)."',
            description='".mysqli_real_escape_string($GLOBALS['conn'], $data->description)."'
            WHERE id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."'";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output);
        }
    } catch (Exception $ex) {
        
    }
    
    $output = array(
        "code" => 204
    );

    return json_encode($output);  
}

function delete_subject($data)
{
    try {
        $query = "UPDATE `subject` SET status='0' WHERE id='".mysqli_real_escape_string($GLOBALS['conn'], $data->id)."'";

        if ($GLOBALS['conn']->query($query))
        {
            $output = array(
                "code" => 200
            );
        
            return json_encode($output); 
        } 

    } catch (Exception $ex) {

    }

    $output = array(
        "code" => 204
    );

    return json_encode($output);  
}

function subject_info($id)
{
    $query = "SELECT * FROM `subject` WHERE id='$id'";
    return $GLOBALS['conn']->query($query);
}

function get_user_results($id, $type, $form_type, $year, $semester)
{
    try
    {
        if ($form_type == "Teacher")
        {
            $query = "SELECT study_load.id, study_load.user_id, CONCAT(user.fname, ' ', user.lname) as student_name, teacher.fname, teacher.lname, subject.name as subject_name, subject.description, study_load.edp_id, edp.edp_code,
                CASE 
                    WHEN study_load.edp_id IN (SELECT eval_id FROM teacher_form  WHERE user_id = '$id' AND YEAR = '$year' AND semester = '$semester') THEN 'Finished'
                    ELSE 'Not yet evaluated'
                END AS is_done,
                (SELECT MAX(created_at) FROM teacher_form WHERE teacher_form.eval_id = study_load.edp_id AND teacher_form.user_id = '$id' AND teacher_form.year = '$year' AND teacher_form.semester = '$semester') AS created_at
                FROM `study_load`
                INNER JOIN user ON study_load.user_id = user.id 
                INNER JOIN edp ON study_load.edp_id = edp.id
                INNER JOIN teacher ON edp.teacher_id = teacher.id
                INNER JOIN subject ON edp.subject_id = subject.id
                WHERE study_load.status='1'
                AND study_load.user_id='$id'
                AND edp.year = '$year'
                AND edp.semester = '$semester'";
        }
        if ($form_type == "Staff")
        {
            $query = "SELECT id, fname, lname,
            CASE 
                WHEN id IN (SELECT eval_id FROM staff_form  WHERE user_id = '$id' AND YEAR = '$year' AND semester = '$semester') THEN 'Finished'
                ELSE 'Not yet evaluated'
            END AS is_done,
            (SELECT MAX(created_at) FROM staff_form WHERE staff_form.eval_id = id AND staff_form.user_id = '$id' AND staff_form.year = '$year' AND staff_form.semester = '$semester') AS created_at
            FROM staff
            WHERE status='1'";
        }
        if ($form_type == "Working Student")
        {
            $query = "SELECT working_student_form.year, working_student_form.semester, eval_id, fname, lname, working_student_form.created_at,
            CASE 
                WHEN working_student_form.eval_id NOT IN (SELECT id FROM user) THEN 'Not yet evaluated'
                ELSE 'Finished'
            END AS is_done
            FROM working_student_form 
            INNER JOIN user ON working_student_form.eval_id = user.id
            WHERE working_student_form.year = '$year' AND working_student_form.semester='$semester'
            AND working_student_form.user_id = '$id'
            GROUP BY YEAR, semester, eval_id, fname, lname, working_student_form.created_at";
        }
        else {
            json_encode(array(
                "code" => 204,
            ));
        }

        $result = $GLOBALS['conn']->query($query);
        
        return json_encode(array(
            "code" => 200,
            "type" => $form_type,
            "data" => mysqli_fetch_all($result, MYSQLI_ASSOC)
        ));
    } catch (Exception $ex)
    {

    }

    return json_encode(array(
        "code" => 204,
    ));
}

is_logon();
