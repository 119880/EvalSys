<?php

require 'mysql.php';

function profile_info()
{
    $query = "SELECT * FROM `admin` WHERE id='".$_SESSION['admin']->id."'";
    return $GLOBALS['conn']->query($query);
}

function update_profile($data)
{
    try {
        $query = "UPDATE `admin` SET
            fname='".mysqli_real_escape_string($GLOBALS['conn'], $data->fname)."',
            mname='".mysqli_real_escape_string($GLOBALS['conn'], $data->mname)."',
            lname='".mysqli_real_escape_string($GLOBALS['conn'], $data->lname)."',
            email='".mysqli_real_escape_string($GLOBALS['conn'], $data->email)."'
            WHERE id='".$_SESSION['admin']->id."'";

        if ($GLOBALS['conn']->query($query))
        {
            $_SESSION['admin']->fname = $data->fname;
            $_SESSION['admin']->mname = $data->mname;
            $_SESSION['admin']->lname = $data->lname;
            $_SESSION['admin']->email = $data->email;

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

function change_password($data)
{
    try
    {
        $response = profile_info();

        $user = (object) $response->fetch_assoc();

        if (password_verify($data->current, $user->password)) {
            
            if ($data->password === $data->cpassword)
            {
                $options = [
                    'cost' => 12,
                ];
                $hashed_password = password_hash($data->password, PASSWORD_BCRYPT, $options);

                $query = "UPDATE `admin` SET password = '$hashed_password' WHERE id = '".$_SESSION['admin']->id."'";

                if ($GLOBALS['conn']->query($query)) {
                    return json_encode(array(
                        "code" => 200
                    ));
                }
            }
        }
    } catch (Exception $ex)
    {

    }

    return json_encode(array(
        "code" => 204
    ));
}
