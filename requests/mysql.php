<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = mysqli_connect($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASS, $MYSQL_DB);

if (!$conn){
	die("Connection error: " . mysqli_connect_error());	
}

function get_admin($email)
{
    $query = "SELECT * FROM `admin` WHERE email='$email'";

    return $GLOBALS['conn']->query($query);
}

function get_user($email)
{
    $query = "SELECT * FROM `user` WHERE email='$email'";

    return $GLOBALS['conn']->query($query);
}

function create_account($email)
{
    try {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$&";
        $password = substr( str_shuffle( $chars ), 0, 8 );
        // echo'<script>alert(' .$password .')></script>';
        $options = [
            'cost' => 12,
        ];
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, $options);
        echo'<script>alert(' .$hashed_password .')></script>';
        $query = "INSERT INTO `user`
        (email, password)
        VALUES (
        '".mysqli_real_escape_string($GLOBALS['conn'], $email)."', 
        '$hashed_password')";
        echo'<script>alert(' .$password .')></script>';

        if (!$GLOBALS['conn']->query($query))
        {
            return 0;
        }
    } catch (Exception $ex) {
        
    }
    echo'<script>alert(' .$password .')></script>';
    return $password;
}