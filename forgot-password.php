<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';
require_once 'mail-config.php';

$message = "";

if(isset($_POST['reset'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    
    if(mysqli_num_rows($query) > 0){
        $token = bin2hex(random_bytes(32));
        
        mysqli_query($conn, "UPDATE users SET reset_token='$token' WHERE email='$email'");
        
        $reset_link = "http://trustfundmarket.iceiy.com/reset-password.php?token=$token";
        
        $body = "
        <h2>Reset Password</h2>
        <p>Click below to reset your password:</p>
        <a href='$reset_link'>Reset Password</a>
        <p>This link expires in 1 hour.</p>
        <p>If you didn't request this, ignore this email.</p>
        ";
        
        if(sendEmail($email, 'Reset Your Password', $body)){
            $message = "Reset link sent successfully.";
        } else {
            $message = "Email could not be sent.";
        }
    } else {
        $message = "No account found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password | TrustFund</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f5f5f5;font-family:Arial,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;}
.card-box{width:420px;background:white;border-radius:24px;padding:40px;box-shadow:0 10px 30px rgba(0,0,0,0.05);}
.logo{font-size:34px;font-weight:800;color:#9b59b6;text-align:center;margin-bottom:10px;}
.subtitle{text-align:center;color:#777;margin-bottom:30px;}
.form-control{height:54px;border-radius:14px;margin-bottom:20px;}
.reset-btn{width:100%;border:none;background:#9b59b6;color:white;height:54px;border-radius:14px;font-weight:700;}
.message-box{background:#f7effc;color:#9b59b6;padding:12px;border-radius:12px;margin-bottom:20px;font-size:13px;}
</style>
</head>
<body>
<div class="card-box">
<div class="logo">TrustFund</div>
<div class="subtitle">Reset your password</div>
<?php if($message != ""): ?>
<div class="message-box"><?php echo $message; ?></div>
<?php endif; ?>
<form method="POST">
<input type="email" name="email" class="form-control" placeholder="Enter your email" required>
<button type="submit" name="reset" class="reset-btn">Send Reset Link</button>
</form>
</div>
</body>
</html>