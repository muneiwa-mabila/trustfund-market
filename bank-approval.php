<?php

session_start();

$cart = $_SESSION['cart'] ?? [];

$total = 0;

foreach($cart as $item){

    $total += ((float)$item['price']) * $item['quantity'];

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Waiting For Approval | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

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

    overflow:hidden;
}

/* DESKTOP */

.desktop-page{
    width:100%;
    max-width:1200px;

    text-align:center;
}

/* LOADER */

.loader{
    width:90px;
    height:90px;

    border:6px solid #eee;
    border-top:6px solid #9b59b6;

    border-radius:50%;

    margin:auto;
    margin-bottom:30px;

    animation:spin 1s linear infinite;
}

@keyframes spin{

    100%{
        transform:rotate(360deg);
    }

}

/* TITLE */

.page-title{
    font-size:36px;
    font-weight:800;

    margin-bottom:18px;

    color:#111;
}

/* TEXT */

.page-text{
    font-size:15px;
    color:#666;

    line-height:1.8;
}

/* NOTIFICATION */

.notification{

    position:fixed;

    top:30px;
    right:-500px;

    width:360px;

    background:white;

    border-radius:18px;

    box-shadow:0 20px 50px rgba(0,0,0,0.15);

    padding:20px;

    display:flex;
    gap:16px;

    align-items:flex-start;

    transition:0.6s;

    z-index:999;
}

.notification.show{
    right:30px;
}

/* NOTIFICATION ICON */

.notif-icon{
    width:55px;
    height:55px;

    border-radius:16px;

    background:#9b59b6;

    color:white;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:24px;

    flex-shrink:0;
}

/* NOTIFICATION TEXT */

.notif-title{
    font-size:15px;
    font-weight:800;

    margin-bottom:6px;

    color:#111;
}

.notif-text{
    font-size:13px;
    color:#666;

    line-height:1.6;

    margin-bottom:16px;
}

/* OPEN APP BUTTON */

.open-app-btn{
    border:none;

    background:#9b59b6;

    color:white;

    padding:10px 16px;

    border-radius:10px;

    font-size:12px;
    font-weight:700;
}

/* BANK APP */

.bank-app{

    position:fixed;

    inset:0;

    background:rgba(0,0,0,0.55);

    display:none;

    align-items:center;
    justify-content:center;

    z-index:1000;
}

/* PHONE */

.phone{
    width:360px;
    height:720px;

    background:white;

    border-radius:40px;

    overflow:hidden;

    position:relative;

    box-shadow:0 20px 60px rgba(0,0,0,0.3);
}

/* STATUS BAR */

.status-bar{
    background:#9b59b6;

    color:white;

    padding:16px 24px;

    display:flex;
    justify-content:space-between;

    font-size:13px;
    font-weight:700;
}

/* HEADER */

.app-header{
    background:#9b59b6;

    color:white;

    text-align:center;

    padding:30px 20px;
}

.bank-title{
    font-size:28px;
    font-weight:800;

    margin-bottom:8px;
}

.bank-subtitle{
    font-size:13px;
    opacity:0.9;
}

/* APP CONTENT */

.app-content{
    padding:26px;
}

/* PAYMENT BOX */

.payment-box{
    background:#fafafa;

    border:1px solid #eee;

    border-radius:18px;

    padding:22px;

    margin-bottom:22px;
}

.label{
    font-size:12px;
    color:#777;

    margin-bottom:6px;
}

.value{
    font-size:22px;
    font-weight:800;

    margin-bottom:18px;
}

/* PASSWORD */

.password-input{
    width:100%;
    height:54px;

    border:1px solid #ddd;

    border-radius:14px;

    padding:0 16px;

    font-size:14px;

    margin-top:10px;
    margin-bottom:18px;

    outline:none;
}

.password-input:focus{
    border-color:#9b59b6;
}

/* BUTTONS */

.approve-btn{
    width:100%;
    height:54px;

    border:none;

    border-radius:14px;

    background:#9b59b6;

    color:white;

    font-size:15px;
    font-weight:700;

    margin-bottom:14px;
}

.decline-btn{
    width:100%;
    height:54px;

    border:none;

    border-radius:14px;

    background:#f2f2f2;

    color:#444;

    font-size:15px;
    font-weight:700;
}

</style>

</head>

<body>

<!-- DESKTOP -->

<div class="desktop-page">

<div class="loader"></div>

<div class="page-title">

Waiting for payment approval

</div>

<div class="page-text">

We sent a payment request to your banking app.

Please approve the payment to continue.

</div>

</div>

<!-- NOTIFICATION -->

<div class="notification" id="notification">

<div class="notif-icon">

<i class="fa-solid fa-building-columns"></i>

</div>

<div>

<div class="notif-title">

FNB App

</div>

<div class="notif-text">

TrustFund Marketplace requested payment approval for

<strong>

R<?php echo number_format($total); ?>

</strong>

</div>

<button class="open-app-btn" onclick="openBankApp()">

Open Banking App

</button>

</div>

</div>

<!-- BANK APP -->

<div class="bank-app" id="bankApp">

<div class="phone">

<!-- STATUS -->

<div class="status-bar">

<div>
09:41
</div>

<div>

<i class="fa-solid fa-signal"></i>

<i class="fa-solid fa-wifi"></i>

<i class="fa-solid fa-battery-full"></i>

</div>

</div>

<!-- HEADER -->

<div class="app-header">

<div class="bank-title">

Bank App

</div>

<div class="bank-subtitle">

Secure payment approval

</div>

</div>

<!-- LOGIN SCREEN -->

<div class="app-content" id="loginScreen">

<div class="payment-box">

<div class="label">

Secure Login

</div>

<div class="value">

Enter your banking password

</div>

<input
type="password"
id="bankPassword"
placeholder="Password"
class="password-input"
>

<button class="approve-btn" onclick="loginBankApp()">

Login

</button>

</div>

</div>

<!-- APPROVAL SCREEN -->

<div 
class="app-content"
id="approvalScreen"
style="display:none;"
>

<div class="payment-box">

<div class="label">

Merchant

</div>

<div class="value">

TrustFund

</div>

<div class="label">

Amount

</div>

<div class="value">

R<?php echo number_format($total); ?>

</div>

<div class="label">

Card

</div>

<div class="value">

•••• 4586

</div>

</div>

<a href="success-payment.php">

<button class="approve-btn">

Approve Payment

</button>

</a>

<a href="payment-failed.php">

<button class="decline-btn">

Decline

</button>

</a>

</div>

</div>

</div>

<script>

/* SHOW NOTIFICATION */

setTimeout(() => {

    document
    .getElementById("notification")
    .classList.add("show");

}, 2000);

/* OPEN APP */

function openBankApp(){

    document
    .getElementById("bankApp")
    .style.display = "flex";

}

/* LOGIN */

function loginBankApp(){

    const password = document
    .getElementById("bankPassword")
    .value;

    const strongPassword = 

    /^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,}$/;

    if(!strongPassword.test(password)){

        alert(

        "Incorrect banking password"

        );

        return;

    }

    document
    .getElementById("loginScreen")
    .style.display = "none";

    document
    .getElementById("approvalScreen")
    .style.display = "block";

}

</script>

</body>
</html>