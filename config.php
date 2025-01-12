<?php

session_start();

$TITLE = "Evaluation System";
$MYSQL_HOST = "localhost"; // your mysql host
$MYSQL_USER = "root"; // your mysql username
$MYSQL_PASS = ""; // your mysql password
$MYSQL_DB = "evaluation_system"; // your db name
$SETTINGS = (object) json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT']."/form_settings.json"), true);

$MAIL_HOST = "smtp.gmail.com";
$MAIL_SENDER_NAME = "Evaluation System";
$MAIL_USER = "casipongjhon63@gmail.com";
$MAIL_PASS = "dicvkstnilsrhhjy";
$MAIL_PORT = 587;
$MAIL_SECURE = "";

$EDP_IMAGE_PATH = "./requests/edp/";
$EDP_IMAGE_PATH_VIEW = "/requests/edp/";

$REJECT_REASON = array(
    "The image your uploaded is blurred.",
    "Invalid Image",
    "You are not currently enrolled."
);

function redirect($url)
{
    if (!headers_sent())
    {    
        header('Location: '.$url);
    }
    else
    {  
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>';
    }
    exit;
}

function update_settings($data)
{
    return (file_put_contents($_SERVER['DOCUMENT_ROOT']."/form_settings.json", json_encode($data))) ? true : false;    
}
