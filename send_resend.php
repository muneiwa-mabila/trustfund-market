<?php
include 'db.php';
include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$email = $_POST['email'] ?? '';

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: forgot_password.php?error=noaccount");
    exit();
}

$token = bin2hex(random_bytes(32));
$expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

$update = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
$update->bind_param("ss", $token, $email);
$update->execute();

$resetLink = BASE_URL . "reset-password.php?token=" . $token;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port = SMTP_PORT;

    $mail->setFrom(SMTP_USER, 'TrustFund');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Reset your TrustFund password';
    $mail->Body = "
      <h2>Reset your password</h2>
      <p>Click below to reset your password:</p>
      <p><a href='$resetLink'>$resetLink</a></p>
      <p>This link expires in 1 hour.</p>
    ";

    $mail->send();

    header("Location: forgot_password.php?success=sent");
    exit();

} catch (Exception $e) {
    echo "Email could not be sent.";
}
?>