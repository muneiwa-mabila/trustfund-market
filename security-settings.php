<?php

session_start();

include 'db.php';

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user_id'];

$message = "";
$error = "";

/* CHANGE PASSWORD */

if(isset($_POST['change_password'])){

    $currentPassword = $_POST['current_password'];

    $newPassword = $_POST['new_password'];

    $confirmPassword = $_POST['confirm_password'];

    /* GET USER */

    $query = mysqli_query(

        $conn,

        "SELECT * 

        FROM users 

        WHERE user_id='$user_id'"

    );

    $user = mysqli_fetch_assoc($query);

    /* VERIFY CURRENT PASSWORD */

    if(!password_verify($currentPassword, $user['password'])){

        $error = "Current password is incorrect.";

    }

    elseif($newPassword != $confirmPassword){

        $error = "New passwords do not match.";

    }

    elseif(strlen($newPassword) < 6){

        $error = "Password must be at least 6 characters.";

    }

    else{

        $hashedPassword = password_hash(

            $newPassword,

            PASSWORD_DEFAULT

        );

        mysqli_query(

            $conn,

            "UPDATE users

            SET password='$hashedPassword'

            WHERE user_id='$user_id'"

        );

        $message = "Password updated successfully.";

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Security Settings | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#f5f5f5;
    font-family:Arial,sans-serif;
}

/* PAGE */

.page-wrapper{
    max-width:700px;
    margin:60px auto;
    padding:20px;
}

/* CARD */

.settings-card{
    background:white;
    border-radius:24px;
    padding:40px;

    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

/* TITLE */

.page-title{
    font-size:34px;
    font-weight:800;
    margin-bottom:10px;
}

.page-subtitle{
    color:#777;
    margin-bottom:35px;
}

/* LABELS */

.form-label{
    font-weight:700;
    margin-bottom:8px;
    color:#333;
}

/* INPUTS */

.form-control{
    height:55px;
    border-radius:14px;
    margin-bottom:24px;
    border:1px solid #ddd;
    padding:0 18px;
}

.form-control:focus{
    border-color:#9b59b6;
    box-shadow:0 0 0 3px rgba(155,89,182,0.15);
}

/* BUTTON */

.save-btn{
    width:100%;
    height:56px;

    border:none;
    border-radius:14px;

    background:#9b59b6;
    color:white;

    font-size:15px;
    font-weight:700;

    transition:0.2s;
}

.save-btn:hover{
    background:#8747a1;
}

/* ALERTS */

.success-box{
    background:#ecfdf3;
    color:#0f9f5f;

    padding:14px 18px;

    border-radius:12px;

    margin-bottom:24px;

    font-size:14px;
    font-weight:600;
}

.error-box{
    background:#fff0f0;
    color:#e53935;

    padding:14px 18px;

    border-radius:12px;

    margin-bottom:24px;

    font-size:14px;
    font-weight:600;
}

/* BACK */

.back-link{
    display:inline-block;
    margin-top:22px;

    color:#777;
    text-decoration:none;
    font-size:14px;
}

</style>

</head>

<body>

<div class="page-wrapper">

<div class="settings-card">

<h1 class="page-title">

Security Settings

</h1>

<div class="page-subtitle">

Update your password and secure your account.

</div>

<?php if($message != ""): ?>

<div class="success-box">

<?php echo $message; ?>

</div>

<?php endif; ?>

<?php if($error != ""): ?>

<div class="error-box">

<?php echo $error; ?>

</div>

<?php endif; ?>

<form method="POST">

<label class="form-label">

Current Password

</label>

<input 
type="password"
name="current_password"
class="form-control"
required
>

<label class="form-label">

New Password

</label>

<input 
type="password"
name="new_password"
class="form-control"
required
>

<label class="form-label">

Confirm New Password

</label>

<input 
type="password"
name="confirm_password"
class="form-control"
required
>

<button 
type="submit"
name="change_password"
class="save-btn"
>

Save Changes

</button>

</form>

<a href="account.php" class="back-link">

← Back to My Account

</a>

</div>

</div>

</body>
</html>