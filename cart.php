<?php

session_start();

$cart = $_SESSION['cart'] ?? [];

$total = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Shopping Cart | TrustFund</title>

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
    color:#111;
}

/* PAGE */

.page-wrapper{
    max-width:1200px;
    margin:auto;
    padding:40px 20px 80px;
}

/* TITLE */

.page-title{
    font-size:32px;
    font-weight:800;
    margin-bottom:35px;
}

/* STEPS */

.steps{
    display:flex;
    align-items:center;
    justify-content:center;
    margin-bottom:45px;
}

.step{
    display:flex;
    flex-direction:column;
    align-items:center;
    font-size:11px;
    font-weight:700;
    color:#999;
}

.step.active{
    color:#9b59b6;
}

.step-circle{
    width:24px;
    height:24px;
    border-radius:50%;
    background:#9b59b6;
    color:white;

    display:flex;
    align-items:center;
    justify-content:center;

    font-size:11px;
    margin-bottom:8px;
}

.step-line{
    width:130px;
    height:4px;
    background:#d9b6ea;
    margin:0 12px;
    border-radius:999px;
}

/* CART */

.cart-layout{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:22px;
}

/* LEFT */

.cart-items{
    background:white;
    border:1px solid #eee;
}

/* ITEM */

.cart-item{
    display:grid;
    grid-template-columns:100px 1fr auto;
    gap:18px;

    padding:22px;

    border-bottom:1px solid #eee;
}

.cart-item:last-child{
    border-bottom:none;
}

/* IMAGE */

.cart-image{
    width:100px;
    height:100px;
    background:#eee;
    border-radius:8px;
    overflow:hidden;
}

.cart-image img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* INFO */

.cart-name{
    font-size:14px;
    font-weight:700;
    margin-bottom:6px;
}

.cart-desc{
    font-size:12px;
    color:#777;
    margin-bottom:14px;
}

.cart-select{
    padding:6px 10px;
    border:1px solid #ddd;
    font-size:12px;
    border-radius:4px;
    margin-bottom:18px;
}

/* ACTIONS */

.cart-actions{
    display:flex;
    gap:18px;
    font-size:11px;
    color:#666;
}

/* PRICE */

.cart-price{
    font-size:16px;
    font-weight:800;
}

.old-price{
    color:#999;
    text-decoration:line-through;
    font-size:11px;
    margin-left:5px;
}

/* SUMMARY */

.summary-box{
    background:white;
    border:1px solid #eee;
    padding:24px;
    height:fit-content;
}

.summary-title{
    font-size:16px;
    font-weight:800;
    margin-bottom:22px;
}

.summary-row{
    display:flex;
    justify-content:space-between;
    font-size:13px;
    margin-bottom:16px;
}

.summary-total{
    border-top:1px solid #eee;
    margin-top:18px;
    padding-top:18px;

    display:flex;
    justify-content:space-between;

    font-size:16px;
    font-weight:800;
}

/* COUPON */

.coupon-box{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    margin-top:18px;
    font-size:12px;
}

/* BUTTON */

.checkout-btn{
    width:100%;
    border:none;
    background:#9b59b6;
    color:white;
    padding:14px;
    margin-top:18px;
    font-size:12px;
    font-weight:700;
}

/* EMPTY */

.empty-cart{
    background:white;
    padding:80px;
    text-align:center;
    font-size:18px;
    border:1px solid #eee;
}

/* RESPONSIVE */

@media(max-width:900px){

    .cart-layout{
        grid-template-columns:1fr;
    }

}

</style>

</head>

<body>

<div class="page-wrapper">

<h1 class="page-title">
Shopping cart
</h1>

<!-- STEPS -->

<div class="steps">

<div class="step active">

<div class="step-circle">1</div>

<div>MY CART</div>

</div>

<div class="step-line"></div>

<div class="step">

<div class="step-circle">2</div>

<div>ADDRESS</div>

</div>

<div class="step-line"></div>

<div class="step">

<div class="step-circle">3</div>

<div>PAYMENT</div>

</div>

</div>

<?php if(empty($cart)){ ?>

<div class="empty-cart">

Your cart is empty.

</div>

<?php } else { ?>

<div class="cart-layout">

<!-- LEFT -->

<div class="cart-items">

<?php foreach($cart as $item){ 

$total += ((float)$item['price']) * $item['quantity'];

?>

<div class="cart-item">

<!-- IMAGE -->

<div class="cart-image">

<img 
src="<?php echo htmlspecialchars($item['image']); ?>"
alt="Product Image"
>

</div>

<!-- INFO -->

<div>

<div class="cart-name">

<?php echo $item['name']; ?>

</div>

<div class="cart-desc">

TrustFund product

</div>

<select class="cart-select">

<option>
Qty: <?php echo $item['quantity']; ?>
</option>

</select>

<div class="cart-actions">

<a href="remove-from-cart.php?id=<?php echo $item['id']; ?>">

Delete

</a>

<a href="move-to-wishlist.php?id=<?php echo $item['id']; ?>">

Move to Wishlist

</a>

</div>
</div>

<!-- PRICE -->

<div class="cart-price">

R<?php echo number_format(((float)$item['price']) * $item['quantity']); ?>

</div>

</div>

<?php } ?>

</div>

<!-- RIGHT -->

<div class="summary-box">

<div class="summary-title">
BILLING DETAILS
</div>

<div class="summary-row">

<div>Cart Total</div>

<div>
R<?php echo number_format($total); ?>
</div>

</div>

<div class="summary-row">

<div>Shipping Charges</div>

<div style="color:#9b59b6;">
Free
</div>

</div>

<div class="summary-row">

<div>Coupon Applied</div>

<div>R0.00</div>

</div>

<div class="summary-total">

<div>TOTAL</div>

<div>
R<?php echo number_format($total); ?>
</div>

</div>

<input 
type="text"
placeholder="Coupon Code"
class="coupon-box"
>
<a href="checkout-address.php">

<button class="checkout-btn">

Proceed to Checkout

</button>

</a>
</div>

</div>

<?php } ?>

</div>

</body>
</html>