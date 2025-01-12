<?php

require 'mysql.php';
require 'mail.php';

function admin_login($email, $pass)
{
    $data = get_admin($email);

    if ($data->num_rows)
    {
        $user = $data->fetch_assoc();

        if (password_verify($pass, $user['password'])) {
            $_SESSION['admin'] = (object) $user;
            return true;
        }
    }
    
    return false;
}

function user_login($email, $pass)
{
    $data = get_user($email);

    if ($data->num_rows)
    {
        $user = $data->fetch_assoc();

        if (password_verify($pass, $user['password'])) {
            $_SESSION['user'] = (object) $user;
            return true;
        }
    }
    
    return 0;
}

function signup($email)
{
    $query = "SELECT * FROM user
    WHERE email='$email'
    AND status = '1'";

    $response = $GLOBALS['conn']->query($query);

    if (!$response->num_rows)
    {
        send_registration($email);
    }

    echo '<script>alert("Email not sent. An error was encountered: Email already existed. ");
                location.href = "/signup.php";</script>';
}
