<?php

session_start();

include 'db.php';

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user_id'];

/* GET ALL ORDERS */

$query = mysqli_query(

    $conn,

    "SELECT 

    orders.*,

    seller_items.product_name,
    seller_items.product_image,
    seller_items.product_price

    FROM orders

    INNER JOIN seller_items

    ON orders.product_id = seller_items.id

    WHERE orders.buyer_id='$user_id'

    ORDER BY orders.created_at DESC"

);

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Orders | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f5f5;
    font-family:Arial,sans-serif;
    color:#111;
}

/* HEADER */

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

/* CATEGORY NAV */

.category-nav{
    background:white;
    border-bottom:1px solid #eee;
}

.category-container{
    max-width:1200px;
    margin:auto;

    padding:14px 20px;

    display:flex;
    gap:28px;
}

.category-container a{
    text-decoration:none;
    color:#111;
    font-size:14px;
}

/* PAGE */

.wrapper{
    max-width:1200px;
    margin:50px auto;
    padding:20px;
}

.page-title{
    font-size:38px;
    font-weight:800;
    margin-bottom:40px;
}

/* ORDER CARD */

.order-card{
    background:white;

    border:2px solid #e5d4ef;

    border-radius:20px;

    padding:24px;

    margin-bottom:24px;

    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:30px;

    transition:0.2s;
}

.order-card:hover{
    border-color:#9b59b6;
}

/* LEFT */

.order-left{
    display:flex;
    align-items:center;
    gap:24px;
    flex:1;
}

.order-image{
    width:140px;
    height:140px;

    border-radius:16px;

    overflow:hidden;

    background:#eee;
}

.order-image img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* DETAILS */

.order-title{
    font-size:22px;
    font-weight:800;
    margin-bottom:10px;
}

.order-meta{
    color:#777;
    font-size:14px;
    margin-bottom:8px;
}

.qty-box{
    background:#f5f5f5;

    display:inline-block;

    padding:8px 14px;

    border-radius:10px;

    font-size:13px;

    margin-top:10px;
    margin-bottom:14px;
}

.order-price{
    font-size:28px;
    font-weight:800;
    color:#111;
}

.order-old-price{
    color:#aaa;
    text-decoration:line-through;
    font-size:14px;
    margin-left:8px;
}

/* RIGHT */

.order-right{
    text-align:right;
    min-width:220px;
}

.status-badge{
    display:inline-block;

    background:#f7effc;
    color:#9b59b6;

    padding:10px 16px;

    border-radius:999px;

    font-size:12px;
    font-weight:700;

    margin-bottom:24px;
}

.track-btn{
    display:inline-block;

    text-decoration:none;

    background:#9b59b6;
    color:white;

    padding:14px 24px;

    border-radius:12px;

    font-weight:700;

    transition:0.2s;
}

.track-btn:hover{
    background:#8a47ab;
}

/* EMPTY */

.empty-orders{
    background:white;
    border-radius:24px;

    padding:80px 40px;

    text-align:center;
}

.empty-icon{
    font-size:60px;
    margin-bottom:20px;
}

.empty-title{
    font-size:34px;
    font-weight:800;
    margin-bottom:16px;
}

.empty-text{
    color:#777;
    margin-bottom:30px;
}

.shop-btn{
    display:inline-block;

    background:#9b59b6;
    color:white;

    text-decoration:none;

    padding:14px 24px;

    border-radius:14px;

    font-weight:700;
}

/* MOBILE */

@media(max-width:900px){

    .order-card{
        flex-direction:column;
        align-items:flex-start;
    }

    .order-left{
        flex-direction:column;
        align-items:flex-start;
    }

    .order-right{
        width:100%;
        text-align:left;
    }

}

</style>

</head>

<body>

<!-- HEADER -->

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

<!-- CATEGORY NAV -->

<div class="category-nav">

<div class="category-container">

<a href="products.php">
Products
</a>

<a href="opportunities.php">
Opportunities
</a>

<a href="skills.php">
Skills
</a>

</div>

</div>

<!-- PAGE -->

<div class="wrapper">

<h1 class="page-title">

Active orders

</h1>

<?php if(mysqli_num_rows($query) > 0): ?>

<?php while($order = mysqli_fetch_assoc($query)): ?>

<?php

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

<div class="order-card">

<!-- LEFT -->

<div class="order-left">

<div class="order-image">

<img 
src="<?php echo htmlspecialchars($order['product_image']); ?>"
>

</div>

<div>

<div class="order-title">

<?php echo htmlspecialchars($order['product_name']); ?>

</div>

<div class="order-meta">

Pickup Point:
<?php echo htmlspecialchars($order['buyer_pickup_point']); ?>

</div>

<div class="qty-box">

Qty:
<?php echo $order['quantity']; ?>

</div>

<div class="order-price">

<?php echo htmlspecialchars($order['product_price']); ?>

</div>

</div>

</div>

<!-- RIGHT -->

<div class="order-right">

<div class="status-badge">

<?php echo $tracking[$currentStage]; ?>

</div>

<br>

<a 
href="track-order.php?id=<?php echo $order['id']; ?>"
class="track-btn"
>

Track My Order

</a>

</div>

</div>

<?php endwhile; ?>

<?php else: ?>

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

Continue Shopping

</a>

</div>

<?php endif; ?>

</div>

</body>
</html>