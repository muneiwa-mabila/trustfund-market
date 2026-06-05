<?php

session_start();

include 'db.php';

$error = "";
$success = "";

$email = $_GET['email'] ?? '';

if(isset($_POST['verify'])){

    $email = mysqli_real_escape_string(
        $conn,
        $_POST['email']
    );

    $otp = mysqli_real_escape_string(
        $conn,
        $_POST['otp']
    );

    $query = mysqli_query(

        $conn,

        "SELECT * FROM users 

        WHERE email='$email'

        AND otp='$otp'

        LIMIT 1"

    );

    if(mysqli_num_rows($query) > 0){

        mysqli_query(

            $conn,

            "UPDATE users 

            SET 

            is_verified = 1,
            otp = NULL

            WHERE email='$email'"

        );

        $success = "Account verified successfully";

        header("refresh:2;url=login.php");

    } else {

        $error = "Invalid OTP code";

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Verify OTP | TrustFund</title>

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

.verify-card{
    width:460px;

    background:white;

    border-radius:22px;

    padding:45px;

    box-shadow:0 10px 30px rgba(0,0,0,0.05);

    text-align:center;
}

/* LOGO */

.logo{
    font-size:36px;
    font-weight:800;

    color:#9b59b6;

    margin-bottom:10px;
}

/* TEXT */

.subtitle{
    color:#777;

    margin-bottom:35px;

    font-size:14px;

    line-height:1.6;
}

/* INPUT */

.form-control{
    height:58px;

    border-radius:14px;

    border:1px solid #ddd;

    margin-bottom:22px;

    text-align:center;

    font-size:22px;

    letter-spacing:8px;

    font-weight:700;
}

.form-control:focus{

    border-color:#9b59b6 !important;

    box-shadow:0 0 0 3px rgba(155,89,182,0.15) !important;

}

/* BUTTON */

.verify-btn{
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

.verify-btn:hover{
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

/* SUCCESS */

.success-box{
    background:#eefaf1;

    color:#2e7d32;

    padding:12px 16px;

    border-radius:10px;

    margin-bottom:20px;

    font-size:13px;
    font-weight:600;
}

.email-text{
    color:#9b59b6;
    font-weight:700;
}

</style>

</head>

<body>

<div class="verify-card">

<div class="logo">

TrustFund

</div>

<div class="subtitle">

We sent a verification code to

<br><br>

<span class="email-text">

<?php echo $email; ?>

</span>

</div>

<?php if($error != ""): ?>

<div class="error-box">

<?php echo $error; ?>

</div>

<?php endif; ?>

<?php if($success != ""): ?>

<div class="success-box">

<?php echo $success; ?>

</div>

<?php endif; ?>

<form method="POST">

<input
type="hidden"
name="email"
value="<?php echo $email; ?>"
>

<input
type="text"
name="otp"
class="form-control"
maxlength="6"
placeholder="000000"
required
>

<button
type="submit"
name="verify"
class="verify-btn"
>

Verify Account

</button>

</form>

</div>

</body>
</html>