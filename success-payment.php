<?php

session_start();

include 'db.php';

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user_id'];

$cart = $_SESSION['cart'] ?? [];

$pickup_point = $_SESSION['pickup_point'] ?? '';

/* PREVENT DUPLICATE ORDERS */

if(!isset($_SESSION['order_processed'])){

    foreach($cart as $item){

        $product_id = $item['id'];

        /* GET PRODUCT */

        $productQuery = mysqli_query(

            $conn,

            "SELECT *

            FROM seller_items

            WHERE id='$product_id'"

        );

        $product = mysqli_fetch_assoc($productQuery);

        /* INSERT ORDER */
      mysqli_query(

    $conn,

    "INSERT INTO orders (

    buyer_id,
    seller_id,
    product_id,
    quantity,
    buyer_pickup_point,
    delivery_fee,
    trustfund_fee

    )

    VALUES (

    '$user_id',
    '".$product['seller_id']."',
    '$product_id',
    '".$item['quantity']."',
    '$pickup_point',
    '35',
    '5'

    )"


        );

    }

    /* MARK PROCESSED */

    $_SESSION['order_processed'] = true;

    /* CLEAR CART */

    unset($_SESSION['cart']);

}
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Payment Success | TrustFund</title>

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

/* PAGE */

.success-wrapper{
    min-height:100vh;

    display:flex;
    align-items:center;
    justify-content:center;

    padding:40px 20px;
}

/* BOX */

.success-box{
    background:white;
    width:450px;
    padding:60px 40px;

    border-radius:18px;

    text-align:center;

    border:1px solid #eee;
}

/* ICON */

.success-icon{
    width:110px;
    height:110px;

    border-radius:50%;

    border:5px solid #35b66a;

    color:#35b66a;

    font-size:50px;

    display:flex;
    align-items:center;
    justify-content:center;

    margin:auto;
    margin-bottom:35px;

    box-shadow:0 0 0 8px rgba(53,182,106,0.1);
}

/* TITLE */

.success-title{
    font-size:28px;
    font-weight:800;
    margin-bottom:14px;
    color:#111;
}

/* TEXT */

.success-text{
    font-size:14px;
    color:#666;
    margin-bottom:30px;
    line-height:1.7;
}

/* BUTTON */

.success-btn{
    display:inline-block;

    background:#9b59b6;
    color:white;

    text-decoration:none;

    padding:14px 28px;

    border-radius:10px;

    font-size:13px;
    font-weight:700;

    transition:0.2s;
}

.success-btn:hover{
    background:#8a47ab;
    color:white;
}

</style>

</head>

<body>

<div class="success-wrapper">

<div class="success-box">

<div class="success-icon">

<i class="fa-solid fa-check"></i>

</div>

<div class="success-title">

Payment successful

</div>

<div class="success-text">

Your order has been placed successfully.

Thank you for shopping with TrustFund.

</div>

<a href="index.php" class="success-btn">

Go back to shopping

</a>

</div>

</div>
<?php

unset($_SESSION['order_processed']);

unset($_SESSION['pickup_point']);

?>
</body>
</html>