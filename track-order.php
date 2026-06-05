<?php

session_start();

include 'db.php';

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user_id'];

$order_id = $_GET['id'] ?? 0;

/* GET ORDER */

$query = mysqli_query(

    $conn,

    "SELECT 

    orders.*,

    seller_items.product_name,
    seller_items.product_image,
    seller_items.product_price,

    users.name AS seller_name

    FROM orders

    INNER JOIN seller_items

    ON orders.product_id = seller_items.id

    INNER JOIN users

    ON orders.seller_id = users.user_id

    WHERE orders.id='$order_id'

    AND orders.buyer_id='$user_id'"

);

$order = mysqli_fetch_assoc($query);

/* CHECK IF USER HAS ANY ORDERS */

$hasOrdersQuery = mysqli_query(

    $conn,

    "SELECT id

    FROM orders

    WHERE buyer_id='$user_id'

    LIMIT 1"

);

$hasOrders = mysqli_num_rows($hasOrdersQuery);

/* NO ORDERS AT ALL */

if(!$hasOrders){
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Active Orders | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f5f5;
    font-family:Arial,sans-serif;
    color:#111;
}

.top-header{
    background:white;
    border-bottom:1px solid #eee;
}

.header-container{
    max-width:1200px;
    margin:auto;

    padding:20px;

    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo{
    font-size:34px;
    font-weight:800;
    color:#9b59b6;
    text-decoration:none;
}

.nav-links{
    display:flex;
    gap:28px;
}

.nav-links a{
    text-decoration:none;
    color:#111;
    font-size:14px;
    font-weight:600;
}

.wrapper{
    max-width:1200px;
    margin:50px auto;
    padding:20px;
}

.page-title{
    font-size:38px;
    font-weight:800;
    margin-bottom:50px;
}

.empty-orders{
    background:white;
    border-radius:30px;

    padding:80px 40px;

    text-align:center;

    box-shadow:0 10px 30px rgba(0,0,0,0.04);
}

.empty-icon{
    width:120px;
    height:120px;

    border-radius:50%;

    border:10px solid #eee;

    margin:auto;
    margin-bottom:30px;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:50px;
}

.empty-title{
    font-size:34px;
    font-weight:800;
    margin-bottom:18px;
}

.empty-text{
    color:#777;
    font-size:16px;
    margin-bottom:30px;
}

.shop-btn{
    display:inline-block;

    background:#9b59b6;
    color:white;

    padding:16px 30px;

    border-radius:14px;

    text-decoration:none;
    font-weight:700;
}

</style>

</head>

<body>

<header class="top-header">

<div class="header-container">

<a href="index.php" class="logo">

TrustFund

</a>

<div class="nav-links">

<a href="orders.php">
Orders
</a>

<a href="account.php">
MyAccount
</a>

<a href="logout.php">
Logout
</a>

</div>

</div>

</header>

<div class="wrapper">

<h1 class="page-title">

Active Orders

</h1>

<div class="empty-orders">

<div class="empty-icon">

📦

</div>

<div class="empty-title">

No active orders

</div>

<div class="empty-text">

You currently have no active orders.

</div>

<a href="index.php" class="shop-btn">

Continue shopping

</a>

</div>

</div>

</body>
</html>

<?php
exit();
}

/* USER HAS ORDERS BUT INVALID ORDER ID */

if(!$order){

    $firstOrder = mysqli_fetch_assoc(

        mysqli_query(

            $conn,

            "SELECT id

            FROM orders

            WHERE buyer_id='$user_id'

            ORDER BY created_at DESC

            LIMIT 1"

        )

    );

    header(

        "Location: track-order.php?id=".$firstOrder['id']

    );

    exit();

}

/* TRACKING */

$tracking = [

    1 => "Order Confirmed",
    2 => "Preparing Package",
    3 => "Picked Up",
    4 => "At Local Warehouse",
    5 => "Ready To Be Picked Up",
    6 => "Completed"

];

$currentStage = $order['tracking_stage'] ?? 1;

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Track Order | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f5f5;
    font-family:Arial,sans-serif;
    color:#111;
}

.top-header{
    background:white;
    border-bottom:1px solid #eee;
}

.header-container{
    max-width:1200px;
    margin:auto;

    padding:20px;

    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo{
    font-size:34px;
    font-weight:800;
    color:#9b59b6;
    text-decoration:none;
}

.nav-links{
    display:flex;
    gap:28px;
}

.nav-links a{
    text-decoration:none;
    color:#111;
    font-size:14px;
    font-weight:600;
}

.wrapper{
    max-width:1200px;
    margin:40px auto;
    padding:20px;
}

.breadcrumbs{
    font-size:13px;
    color:#777;
    margin-bottom:18px;
}

.page-title{
    font-size:36px;
    font-weight:800;
    margin-bottom:28px;
}

.track-card{
    background:white;
    border-radius:24px;
    padding:32px;

    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

.order-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:40px;
    flex-wrap:wrap;
    gap:20px;
}

.order-number{
    font-size:26px;
    font-weight:800;
}

.order-meta{
    color:#777;
    font-size:14px;
    margin-top:10px;
}

.cancel-btn{
    background:#f7effc;
    color:#9b59b6;

    border:none;

    padding:14px 20px;

    border-radius:14px;

    font-weight:700;
}

.tracking-wrapper{
    margin-bottom:50px;
}

.tracking-line{
    display:flex;
    justify-content:space-between;
    align-items:center;
    position:relative;
}

.tracking-line::before{
    content:"";
    position:absolute;
    top:18px;
    left:0;
    right:0;
    height:4px;
    background:#e5d4ef;
    z-index:1;
}

.tracking-step{
    position:relative;
    z-index:2;
    text-align:center;
    width:16%;
}

.tracking-circle{
    width:38px;
    height:38px;
    border-radius:50%;
    background:#ddd;
    margin:auto;
    margin-bottom:12px;

    display:flex;
    align-items:center;
    justify-content:center;

    color:white;
    font-size:14px;
    font-weight:700;
}

.tracking-step.active .tracking-circle{
    background:#9b59b6;
}

.tracking-label{
    font-size:12px;
    font-weight:700;
}

.tracking-date{
    font-size:11px;
    color:#888;
    margin-top:6px;
}

.product-card{
    display:flex;
    gap:20px;
    align-items:center;

    border-top:1px solid #eee;
    padding-top:30px;
    margin-top:20px;
}

.product-image{
    width:120px;
    height:120px;
    border-radius:18px;
    overflow:hidden;
    background:#eee;
}

.product-image img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.product-title{
    font-size:22px;
    font-weight:800;
    margin-bottom:10px;
}

.product-meta{
    font-size:14px;
    color:#666;
    margin-bottom:8px;
}

.product-price{
    font-size:22px;
    font-weight:800;
    color:#9b59b6;
}

.info-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:24px;
    margin-top:40px;
}

.info-box{
    background:#fafafa;
    border-radius:20px;
    padding:24px;
}

.info-title{
    font-size:16px;
    font-weight:800;
    margin-bottom:16px;
}

.info-text{
    color:#666;
    line-height:1.8;
    font-size:14px;
}

.summary-row{
    display:flex;
    justify-content:space-between;
    margin-bottom:14px;
}

.summary-total{
    border-top:1px solid #ddd;
    margin-top:18px;
    padding-top:18px;

    display:flex;
    justify-content:space-between;

    font-size:18px;
    font-weight:800;
}

@media(max-width:900px){

    .tracking-line{
        overflow-x:auto;
        gap:30px;
    }

    .tracking-step{
        min-width:120px;
    }

    .info-grid{
        grid-template-columns:1fr;
    }

}

</style>

</head>

<body>

<header class="top-header">

<div class="header-container">

<a href="index.php" class="logo">

TrustFund

</a>

<div class="nav-links">

<a href="orders.php">
Orders
</a>

<a href="account.php">
MyAccount
</a>

<a href="logout.php">
Logout
</a>

</div>

</div>

</header>

<div class="wrapper">

<div class="breadcrumbs">

Home → My Account → Active Orders

</div>

<h1 class="page-title">

Track Order

</h1>

<div class="track-card">

<div class="order-header">

<div>

<div class="order-number">

Order #<?php echo $order['id']; ?>

</div>

<div class="order-meta">

Placed on:

<?php echo date("d M Y", strtotime($order['created_at'])); ?>

</div>

</div>

<button class="cancel-btn">

Cancel Order

</button>

</div>

<div class="tracking-wrapper">

<div class="tracking-line">

<?php foreach($tracking as $stage => $label): ?>

<div class="tracking-step <?php echo ($currentStage >= $stage) ? 'active' : ''; ?>">

<div class="tracking-circle">

<?php echo $stage; ?>

</div>

<div class="tracking-label">

<?php echo $label; ?>

</div>

<div class="tracking-date">

<?php echo date("D, d M", strtotime("+".$stage." day")); ?>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

<div class="product-card">

<div class="product-image">

<img 
src="<?php echo htmlspecialchars($order['product_image']); ?>"
>

</div>

<div style="flex:1;">

<div class="product-title">

<?php echo htmlspecialchars($order['product_name']); ?>

</div>

<div class="product-meta">

Sold by:

<?php echo htmlspecialchars($order['seller_name']); ?>

</div>

<div class="product-meta">

Quantity:

<?php echo $order['quantity']; ?>

</div>

</div>

<div class="product-price">

<?php echo htmlspecialchars($order['product_price']); ?>

</div>

</div>

<div class="info-grid">

<div class="info-box">

<div class="info-title">

Pickup Point

</div>

<div class="info-text">

<?php echo htmlspecialchars($order['buyer_pickup_point']); ?>

</div>

</div>

<div class="info-box">

<div class="info-title">

Order Summary

</div>

<div class="summary-row">

<div>Subtotal</div>

<div>

<?php echo htmlspecialchars($order['product_price']); ?>

</div>

</div>

<div class="summary-row">

<div>Delivery</div>

<div>

Free

</div>

</div>

<div class="summary-total">

<div>Total</div>

<div>

<?php echo htmlspecialchars($order['product_price']); ?>

</div>

</div>

</div>

</div>

</div>

</div>

</body>
</html>