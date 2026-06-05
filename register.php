<?php

session_start();

include 'db.php';

include 'mail-config.php';

$error = "";
$success = "";

if(isset($_POST['register'])){

    $name = mysqli_real_escape_string(
        $conn,
        $_POST['name']
    );

    $email = mysqli_real_escape_string(
        $conn,
        $_POST['email']
    );

    $phone = mysqli_real_escape_string(
        $conn,
        $_POST['phone']
    );

    $password = $_POST['password'];

    $confirm = $_POST['confirm_password'];

    /* CHECK PASSWORD */

    if($password != $confirm){

        $error = "Passwords do not match";

    } else {

        /* CHECK IF EMAIL EXISTS */

        $check = mysqli_query(

            $conn,

            "SELECT * FROM users WHERE email='$email'"

        );

        if(mysqli_num_rows($check) > 0){

            $error = "Email already exists";

        } else {

            /* HASH PASSWORD */

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            /* GENERATE OTP */

            $otp = rand(100000,999999);

            /* INSERT USER */

            mysqli_query(

                $conn,

                "INSERT INTO users(

                name,
                email,
                phone,
                password,
                otp,
                is_verified

                )

                VALUES(

                '$name',
                '$email',
                '$phone',
                '$hashedPassword',
                '$otp',
                0

                )"

            );

            /* SEND OTP EMAIL */

            sendOTP($email,$otp);

            /* REDIRECT */

            header(
                "Location: verify-otp.php?email=$email"
            );

            exit();

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Register | TrustFund</title>

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

    min-height:100vh;

    display:flex;
    align-items:center;
    justify-content:center;

    padding:20px;
}

/* CARD */

.register-card{
    width:480px;

    background:white;

    border-radius:22px;

    padding:45px;

    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

/* LOGO */

.logo{
    font-size:36px;
    font-weight:800;

    color:#9b59b6;

    text-align:center;

    margin-bottom:10px;
}

.subtitle{
    text-align:center;

    color:#777;

    margin-bottom:35px;

    font-size:14px;
}

/* LABELS */

.form-label{
    font-weight:700;

    margin-bottom:8px;

    color:#333;
}

/* INPUTS */

.form-control{
    height:54px;

    border-radius:14px;

    border:1px solid #ddd;

    margin-bottom:22px;

    padding:0 16px;

    font-size:14px;
}

.form-control:focus{

    border-color:#9b59b6 !important;

    box-shadow:0 0 0 3px rgba(155,89,182,0.15) !important;

}

/* BUTTON */

.register-btn{
    width:100%;

    height:54px;

    border:none;

    border-radius:14px;

    background:#9b59b6;

    color:white;

    font-size:15px;
    font-weight:700;

    transition:0.2s;
}

.register-btn:hover{
    background:#8a47ab;
}

/* ERROR */

.error-box{
    background:#fff0f0;

    color:#e53935;

    padding:12px 16px;

    border-radius:10px;

    margin-bottom:20px;

    font-size:13px;
    font-weight:600;
}

/* LINK */

.bottom-link{
    text-align:center;

    margin-top:22px;

    font-size:14px;
}

.bottom-link a{
    color:#9b59b6;

    text-decoration:none;

    font-weight:700;
}

</style>

</head>

<body>

<div class="register-card">

<div class="logo">

TrustFund

</div>

<div class="subtitle">

Create your account

</div>

<?php if($error != ""): ?>

<div class="error-box">

<?php echo $error; ?>

</div>

<?php endif; ?>

<form method="POST">

<label class="form-label">

Full Name

</label>

<input
type="text"
name="name"
class="form-control"
required
>

<label class="form-label">

Email Address

</label>

<input
type="email"
name="email"
class="form-control"
required
>

<label class="form-label">

Phone Number

</label>

<input
type="text"
name="phone"
class="form-control"
required
>

<label class="form-label">

Password

</label>

<input
type="password"
name="password"
class="form-control"
required
>

<label class="form-label">

Confirm Password

</label>

<input
type="password"
name="confirm_password"
class="form-control"
required
>

<button
type="submit"
name="register"
class="register-btn"
>

Create Account

</button>

</form>

<div class="bottom-link">

Already have an account?

<a href="login.php">

Login

</a>

</div>

</div>

</body>
</html>