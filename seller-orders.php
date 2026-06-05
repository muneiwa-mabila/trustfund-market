<?php

session_start();

include 'db.php';

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
    exit();

}

$seller_id = $_SESSION['user_id'];

/* UPDATE DROPOFF */

if(isset($_POST['update_dropoff'])){

    $order_id = $_POST['order_id'];

    $dropoff = mysqli_real_escape_string(

        $conn,

        $_POST['seller_dropoff']

    );

    mysqli_query(

        $conn,

        "UPDATE orders

        SET 

        seller_dropoff_point='$dropoff',

        status='Seller Dropped Parcel'

        WHERE id='$order_id'

        AND seller_id='$seller_id'"

    );

}

/* UPDATE TRACKING */

if(isset($_POST['update_tracking'])){

    $order_id = $_POST['order_id'];

    $tracking_stage = $_POST['tracking_stage'];

    mysqli_query(

        $conn,

        "UPDATE orders

        SET tracking_stage='$tracking_stage'

        WHERE id='$order_id'

        AND seller_id='$seller_id'"

    );

    /* RELEASE PAYMENT WHEN COMPLETED */

    if($tracking_stage == 6){

        /* GET ORDER */

        $orderQuery = mysqli_query(

            $conn,

            "SELECT *

            FROM orders

            WHERE id='$order_id'"

        );

        $order = mysqli_fetch_assoc($orderQuery);

        $seller_id = $order['seller_id'];

        $amount = $order['total_price'];

        /* UPDATE SELLER WALLET */

        mysqli_query(

            $conn,

            "UPDATE users

            SET wallet_balance = wallet_balance + $amount

            WHERE user_id='$seller_id'"

        );

        /* UPDATE PAYMENT STATUS */

        mysqli_query(

            $conn,

            "UPDATE orders

            SET payment_status='Released'

            WHERE id='$order_id'"

        );

    }

}

/* GET ORDERS */

$query = mysqli_query(

    $conn,

    "SELECT 

    orders.*,

    users.name AS buyer_name,

    seller_items.product_name,

    seller_items.product_image,

    seller_items.product_price

    FROM orders

    INNER JOIN users

    ON orders.buyer_id = users.user_id

    INNER JOIN seller_items

    ON orders.product_id = seller_items.id

    WHERE orders.seller_id='$seller_id'

    ORDER BY orders.created_at DESC"

);

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Seller Orders | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f5f5;
    font-family:Arial,sans-serif;
}

/* PAGE */

.wrapper{
    max-width:1200px;
    margin:50px auto;
    padding:20px;
}

/* TITLE */

.page-title{
    font-size:36px;
    font-weight:800;
    margin-bottom:30px;
}

/* ORDER CARD */

.order-card{
    background:white;
    border-radius:24px;
    padding:24px;
    margin-bottom:24px;

    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

/* TOP */

.order-top{
    display:flex;
    gap:20px;
    margin-bottom:20px;
}

.order-image{
    width:140px;
    height:140px;
    border-radius:18px;
    overflow:hidden;
    background:#eee;
}

.order-image img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* INFO */

.order-title{
    font-size:22px;
    font-weight:800;
    margin-bottom:10px;
}

.order-meta{
    color:#666;
    margin-bottom:8px;
    font-size:14px;
}

/* STATUS */

.status-badge{
    display:inline-block;

    padding:10px 16px;

    border-radius:999px;

    background:#f3e8fb;
    color:#9b59b6;

    font-size:13px;
    font-weight:700;

    margin-top:12px;
}

/* FORM */

.form-label{
    font-weight:700;
    margin-bottom:8px;
}

.form-select{
    height:54px;
    border-radius:14px;
    margin-bottom:16px;
}

/* BUTTON */

.update-btn{
    border:none;
    background:#9b59b6;
    color:white;

    padding:14px 22px;

    border-radius:14px;

    font-weight:700;
}

/* EMPTY */

.empty-box{
    background:white;
    border-radius:24px;
    padding:60px;
    text-align:center;
    color:#777;
}

</style>

</head>

<body>

<div class="wrapper">

<h1 class="page-title">

Seller Orders

</h1>

<?php if(mysqli_num_rows($query) > 0): ?>

<?php while($order = mysqli_fetch_assoc($query)): ?>

<div class="order-card">

<div class="order-top">

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

Buyer:

<strong>

<?php echo htmlspecialchars($order['buyer_name']); ?>

</strong>

</div>

<div class="order-meta">

Quantity:

<?php echo $order['quantity']; ?>

</div>

<div class="order-meta">

Buyer Pickup Point:

<strong>

<?php echo htmlspecialchars($order['buyer_pickup_point']); ?>

</strong>

</div>

<div class="order-meta">

Price:

<strong>

R<?php echo number_format((float)$order['product_price']); ?>

</strong>

</div>

<div class="order-meta">

Payment Status:

<strong>

<?php echo htmlspecialchars($order['payment_status'] ?? 'Held'); ?>

</strong>

</div>

<div class="status-badge">

<?php echo htmlspecialchars($order['status']); ?>

</div>

</div>

</div>

<form method="POST">

<input 
type="hidden"
name="order_id"
value="<?php echo $order['id']; ?>"
>

<label class="form-label">

Select Seller Dropoff Point

</label>

<select 
name="seller_dropoff"
class="form-select seller-dropoff"
required
>

<option value="">

Choose dropoff point

</option>

</select>

<button 
type="submit"
name="update_dropoff"
class="update-btn"
style="margin-bottom:20px;"
>

Save Dropoff Point

</button>

</form>

<form method="POST">

<input 
type="hidden"
name="order_id"
value="<?php echo $order['id']; ?>"
>

<label class="form-label">

Update Tracking Stage

</label>

<select 
name="tracking_stage"
class="form-select"
required
>

<option value="1">

Order Confirmed

</option>

<option value="2">

Preparing Package

</option>

<option value="3">

Picked Up

</option>

<option value="4">

At Local Warehouse

</option>

<option value="5">

Ready To Be Picked Up

</option>

<option value="6">

Completed

</option>

</select>

<button 
type="submit"
name="update_tracking"
class="update-btn"
>

Update Order

</button>

</form>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="empty-box">

No orders yet.

</div>

<?php endif; ?>

</div>

<script src="pickup-locations.js"></script>

<script>

const sellerSelects = document.querySelectorAll(".seller-dropoff");

sellerSelects.forEach(select => {

    for(const province in locations){

        locations[province].forEach(location => {

            const option = document.createElement("option");

            option.value = `${location.name} - ${location.city}`;

            option.textContent = `${location.name} (${location.city})`;

            select.appendChild(option);

        });

    }

});

</script>

</body>
</html>