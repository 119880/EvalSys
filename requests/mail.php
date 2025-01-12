<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

function send_registration($email)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $GLOBALS['MAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $GLOBALS['MAIL_USER'];
        $mail->Password = $GLOBALS['MAIL_PASS'];
        $mail->SMTPSecure = $GLOBALS['MAIL_SECURE'];
        $mail->Port = $GLOBALS['MAIL_PORT'];
        $mail->SMTPOptions = array( 'ssl' => array( 'verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true ) );

        $password = create_account($email);
        if (!$password)
        {
            
            echo "<script>alert('Something went wrong!');</script>";
            return 0;
        }

        $mail->setFrom($GLOBALS['MAIL_USER'], $GLOBALS['MAIL_SENDER_NAME']);
        $mail->addAddress($email, '');

        $mail->isHTML(false);

        $mail->Subject = 'Evaluation System';
        $mail->Body    = "Welcome to Evaluation System
            \n\nHere's your new password: " . $password;

        // Attempt to send the email
        if (!$mail->send()) {
            echo '<script>alert("Email not sent. An error was encountered: test ' . $mail->ErrorInfo . '")</script>';
        } else {
            echo '<script>alert("Message has been sent. Please check your mailbox.");
                location.href = "/login.php";</script>';
        }

    } catch (Exception $ex) {
        // echo "<script>alert('Something went wrong! ".$ex."')</script>";
        var_dump($ex);
        exit;
    }
}

function send_form_update($email, $status, $message)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $GLOBALS['MAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $GLOBALS['MAIL_USER'];
        $mail->Password = $GLOBALS['MAIL_PASS'];
        $mail->SMTPSecure = $GLOBALS['MAIL_SECURE'];
        $mail->Port = $GLOBALS['MAIL_PORT'];
        $mail->SMTPOptions = array( 'ssl' => array( 'verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true ) );

        $mail->setFrom($GLOBALS['MAIL_USER'], $GLOBALS['MAIL_SENDER_NAME']);
        $mail->addAddress($email, '');

        $mail->isHTML(false);

        $mail->Subject = 'Evaluation System Verification';
        $mail->Body    = "Hi,
            \n$message";

        // Attempt to send the email
        return (!$mail->send()) ? 0 : 1;

    } catch (Exception $ex) {
        return 0;
    }
}