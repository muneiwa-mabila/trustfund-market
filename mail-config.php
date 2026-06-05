<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';
require_once 'phpmailer/Exception.php';

function sendEmail($to, $subject, $body){
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.zoho.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '-';
        $mail->Password   = '-';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('no-reply@trustfund.store', 'TrustFund');
        $mail->addReplyTo('no-reply@trustfund.store', 'TrustFund');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->send();
        return true;
    } catch(Exception $e){
        return false;
    }
}

function sendOTP($email, $otp){
    $body = "
    <div style='font-family:Arial;padding:30px;'>
    <h2 style='color:#9b59b6;'>TrustFund Verification</h2>
    <p>Your OTP code is:</p>
    <h1 style='letter-spacing:5px;'>$otp</h1>
    <p>This code expires soon.</p>
    </div>";
    return sendEmail($email, 'TrustFund OTP Verification', $body);
}
?>