<?php

session_start();

include 'db.php';

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");

    exit();

}

$username = $_SESSION['name'] ?? 'User';

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Account | TrustFund</title>

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

.account-wrapper{
    max-width:1200px;

    margin:50px auto;

    padding:20px;
}

/* TITLE */

.page-title{
    font-size:38px;
    font-weight:800;

    margin-bottom:35px;

    color:#111;
}

/* GRID */

.account-grid{
    display:grid;

    grid-template-columns:1fr 1fr;

    gap:24px;
}

/* CARD */

.account-card{
    background:white;

    border-radius:18px;

    padding:30px;

    min-height:200px;

    box-shadow:0 8px 24px rgba(0,0,0,0.06);

    border:1px solid #eee;
}

/* CARD TITLE */

.card-title{
    font-size:20px;
    font-weight:800;

    margin-bottom:24px;

    color:#111;
}

/* LINKS */

.account-link{
    display:block;

    text-decoration:none;

    color:#b06bd8;

    font-size:15px;

    margin-bottom:18px;

    transition:0.2s;
}

.account-link:hover{
    color:#9b59b6;

    padding-left:5px;
}

/* USER */

.user-top{
    margin-bottom:35px;
}

.user-name{
    font-size:18px;
    font-weight:700;

    color:#9b59b6;
}

/* RESPONSIVE */

@media(max-width:900px){

    .account-grid{
        grid-template-columns:1fr;
    }

}

</style>

</head>

<body>

<div class="account-wrapper">

<div class="user-top">

<div class="user-name">

Welcome, <?php echo $username; ?>

</div>

</div>

<h1 class="page-title">

My account

</h1>

<div class="account-grid">

<!-- ORDERS -->

<div class="account-card">

<div class="card-title">

My orders

</div>

<a href="#" class="account-link">

Active orders

</a>

<a href="#" class="account-link">

Completed orders

</a>

</div>

<!-- PAYMENTS -->

<div class="account-card">

<div class="card-title">

Payments

</div>

<a href="#" class="account-link">

Refunds

</a>

<a href="#" class="account-link">

Pending payments

</a>

</div>

<!-- PROFILE -->

<div class="account-card">

<div class="card-title">

My profile

</div>

<a href="profile.php" class="account-link">

Personal details

</a>

<a href="#" class="account-link">

Address book

</a>

<a href="#" class="account-link">

Security settings

</a>

</div>

<!-- SUPPORT -->

<div class="account-card">

<div class="card-title">

Support

</div>

<a href="logout.php" class="account-link">

Log out

</a>

</div>

</div>

</div>

</body>
</html>