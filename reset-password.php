<?php

session_start();

include 'db.php';

$message = "";

$token = $_GET['token'] ?? '';

if($token == ''){

    die("Invalid token.");

}

/* CHECK TOKEN */

$query = mysqli_query(

    $conn,

    "SELECT *

    FROM users

    WHERE reset_token='$token'"

);

if(mysqli_num_rows($query) == 0){

    die("Invalid or expired token.");

}

$user = mysqli_fetch_assoc($query);

/* RESET PASSWORD */

if(isset($_POST['update_password'])){

    $password = $_POST['password'];

    $confirm = $_POST['confirm_password'];

    if($password != $confirm){

        $message = "Passwords do not match.";

    }else{

        $hashed = password_hash(

            $password,

            PASSWORD_DEFAULT

        );

        mysqli_query(

            $conn,

            "UPDATE users

            SET 

            password='$hashed',

            reset_token=NULL

            WHERE user_id='".$user['user_id']."'"

        );

        header("Location: login.php?reset=success");
        exit();

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reset Password | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f5f5;
    font-family:Arial,sans-serif;

    min-height:100vh;

    display:flex;
    align-items:center;
    justify-content:center;
}

.card-box{
    width:420px;

    background:white;

    border-radius:24px;

    padding:40px;

    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

.logo{
    font-size:34px;
    font-weight:800;
    color:#9b59b6;
    text-align:center;
    margin-bottom:10px;
}

.subtitle{
    text-align:center;
    color:#777;
    margin-bottom:30px;
}

.form-control{
    height:54px;
    border-radius:14px;
    margin-bottom:20px;
}

.reset-btn{
    width:100%;
    border:none;
    background:#9b59b6;
    color:white;
    height:54px;
    border-radius:14px;
    font-weight:700;
}

.message-box{
    background:#fff0f0;
    color:#e53935;
    padding:12px;
    border-radius:12px;
    margin-bottom:20px;
    font-size:13px;
}

</style>

</head>

<body>

<div class="card-box">

<div class="logo">

TrustFund

</div>

<div class="subtitle">

Create a new password

</div>

<?php if($message != ""): ?>

<div class="message-box">

<?php echo $message; ?>

</div>

<?php endif; ?>

<form method="POST">

<input
type="password"
name="password"
class="form-control"
placeholder="New password"
required
>

<input
type="password"
name="confirm_password"
class="form-control"
placeholder="Confirm password"
required
>

<button
type="submit"
name="update_password"
class="reset-btn"
>

Update Password

</button>

</form>

</div>

</body>
</html>