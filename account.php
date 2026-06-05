<?php

session_start();

include 'db.php';

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");

    exit();

}

$username = $_SESSION['name'] ?? 'User';

$cartCount = 0;

if(isset($_SESSION['cart'])){

    foreach($_SESSION['cart'] as $item){

        $cartCount += $item['quantity'];

    }

}

$wishlistCount = 0;

if(isset($_SESSION['wishlist'])){

    $wishlistCount = count($_SESSION['wishlist']);

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Account | TrustFund</title>

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
}

/* NAVBAR */

.navbar{
    background:#9b59b6;
    padding:14px 0;
}

.navbar-brand{
    font-size:30px;
    font-weight:800;
    color:white !important;
}

.nav-link{
    color:white !important;
    font-weight:500;
    margin:0 10px;
    font-size:15px;
}

.nav-link:hover{
    color:#f3d9ff !important;
}

/* ACCOUNT LINK */

.account-nav-name{

    text-decoration:none !important;

    color:white !important;

    font-weight:600;

}

.account-nav-name:hover{

    color:#f3d9ff !important;

}

/* SEARCH */

.search-bar{
    border-radius:30px;
    border:none;
    padding:10px 18px;
}

.search-bar:focus{
    box-shadow:none;
}

/* ICONS */

.nav-icon{
    position:relative;
    color:white !important;
    font-size:20px;
}

.nav-icon span{
    position:absolute;

    top:-8px;
    right:-10px;

    background:white;
    color:#9b59b6;

    width:18px;
    height:18px;

    border-radius:50%;

    font-size:10px;
    font-weight:700;

    display:flex;
    align-items:center;
    justify-content:center;
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

<!-- HEADER -->

<nav class="navbar navbar-expand-lg">

<div class="container">

<a class="navbar-brand" href="index.php">

TrustFund

</a>

<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">

<span class="navbar-toggler-icon"></span>

</button>

<form class="d-flex flex-grow-1 mx-5" method="GET" action="search.php">

<input 
class="form-control search-bar"
type="search"
name="query"
placeholder="Search for products, services, skills..."
required
>

</form>

<div class="collapse navbar-collapse justify-content-end" id="navbarNav">

<ul class="navbar-nav align-items-center">

<li class="nav-item">

<a class="nav-link" href="#">

Orders

</a>

</li>

<li class="nav-item">

<a class="nav-link account-nav-name" href="account.php">

<?php echo $username; ?>

</a>

</li>

<li class="nav-item">

<a class="nav-link nav-icon" href="wishlist.php">

<i class="fa-regular fa-heart"></i>

<span>

<?php echo $wishlistCount; ?>

</span>

</a>

</li>

<li class="nav-item ms-3 me-3">

<a class="nav-link nav-icon" href="cart.php">

<i class="fa-solid fa-cart-shopping"></i>

<span>

<?php echo $cartCount; ?>

</span>

</a>

</li>

</ul>

</div>

</div>

</nav>

<!-- PAGE -->

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

<a href="orders.php" class="account-link">

Orders

</a>

<a href="completed-orders.php" class="account-link">

Completed orders

</a>

</div>

<!-- PAYMENTS -->

<div class="account-card">

<div class="card-title">

Payments

</div>

<a href="refunds.php" class="account-link">

Refunds

</a>

<a href="pending-payments.php" class="account-link">

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

<a href="address-book.php" class="account-link">

Address book

</a>

<a href="security-settings.php" class="account-link">

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>