<?php

session_start();

include 'db.php';

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user_id'];

$cart = $_SESSION['cart'] ?? [];

if(empty($cart)){

    header("Location: cart.php");
    exit();

}

$pickup_point = $_POST['pickup_point'] ?? '';


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

<title>Review Order | TrustFund</title>

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

/* LAYOUT */

.review-layout{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:24px;
}

/* LEFT */

.review-section{
    background:white;
    border:1px solid #eee;
    padding:24px;
}

.section-title{
    font-size:18px;
    font-weight:800;
    margin-bottom:18px;
}

/* PICKUP */

.pickup-box{
    border:1px solid #ddd;
    padding:20px;
    margin-bottom:20px;
}

.pickup-label{
    font-size:12px;
    color:#777;
    margin-bottom:8px;
}

.pickup-title{
    font-size:24px;
    font-weight:800;
    margin-bottom:10px;
}

.pickup-address{
    font-size:13px;
    color:#666;
    line-height:1.6;
}

.change-btn{
    color:#9b59b6;
    font-size:12px;
    font-weight:700;
    float:right;
}

/* DELIVERY OPTION */

.delivery-option{
    border:1px solid #ddd;
    padding:18px;
    margin-bottom:14px;

    display:flex;
    justify-content:space-between;
    align-items:center;

    border-radius:8px;
}

.delivery-option.active{
    border:2px solid #9b59b6;
    background:#f7effc;
}

.delivery-left{
    display:flex;
    gap:14px;
}

.delivery-radio{
    margin-top:5px;
}

.delivery-date{
    font-size:14px;
    font-weight:800;
    margin-bottom:6px;
}

.delivery-text{
    font-size:12px;
    color:#666;
}

.delivery-badge{
    display:inline-block;
    background:#777;
    color:white;
    font-size:10px;
    padding:4px 8px;
    border-radius:999px;
    margin-top:6px;
}

.delivery-price{
    font-size:13px;
    font-weight:700;
}

/* RIGHT */

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

.estimated-date{
    margin-top:16px;
    font-size:12px;
    color:#777;
}

.coupon-box{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    margin-top:18px;
    font-size:12px;
}

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

@media(max-width:900px){

    .review-layout{
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

<div class="step active">

<div class="step-circle">2</div>

<div>ADDRESS</div>

</div>

<div class="step-line"></div>

<div class="step active">

<div class="step-circle">3</div>

<div>PAYMENT</div>

</div>

</div>

<div class="review-layout">

<!-- LEFT -->

<div class="review-section">

<div class="section-title">
Review
</div>

<!-- PICKUP -->

<div class="pickup-box">

<div class="pickup-label">
Pick up point
</div>

<a href="checkout-address.php" class="change-btn">
Change
</a>

<div class="pickup-title">
TrustFund Pickup Point
</div>

<div class="pickup-address">

1 First Place<br>

Kerk Street & Simmons Street<br>

Johannesburg, Johannesburg, 2000

</div>

</div>

<!-- OPTIONS -->

<div class="delivery-option active" onclick="selectDelivery(this)">

<div class="delivery-left">

<input type="radio" checked name="delivery" class="delivery-radio">

<div>

<div class="delivery-date">

Friday, <?php echo date("d M Y"); ?>

</div>

<div class="delivery-text">

Standard collect

</div>

<div class="delivery-badge">

1 day from today

</div>

</div>

</div>

<div class="delivery-price">
Free
</div>

</div>

<div class="delivery-option" onclick="selectDelivery(this)">

<div class="delivery-left">

<input type="radio" name="delivery" class="delivery-radio">

<div>

<div class="delivery-date">

Saturday, <?php echo date("d M Y", strtotime("+1 day")); ?>

</div>

<div class="delivery-text">

Standard collect

</div>

<div class="delivery-badge">

2 days from today

</div>

</div>

</div>

<div class="delivery-price">
Free
</div>

</div>

<div class="delivery-option" onclick="selectDelivery(this)">

<div class="delivery-left">

<input type="radio" name="delivery" class="delivery-radio">

<div>

<div class="delivery-date">

Sunday, <?php echo date("d M Y", strtotime("+2 day")); ?>

</div>

<div class="delivery-text">

Standard collect

</div>

<div class="delivery-badge">

3 days from today

</div>

</div>

</div>

<div class="delivery-price">
Free
</div>

</div>
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

<div class="estimated-date">

Estimated Delivery by<br>

<?php

echo date(

    "d M, Y",

    strtotime("+1 day")

);

?>

</div>

<input 
type="text"
placeholder="Coupon Code"
class="coupon-box"
>

<form method="POST" action="payment.php">

<input 
type="hidden"
name="pickup_point"
value="<?php echo htmlspecialchars($pickup_point); ?>"
>

<button class="checkout-btn">

Proceed to Checkout

</button>

</form>

</div>

</div>

</div>
<script>

function selectDelivery(element){

    document.querySelectorAll(".delivery-option")

    .forEach(option => {

        option.classList.remove("active");

        option.querySelector("input").checked = false;

    });

    element.classList.add("active");

    element.querySelector("input").checked = true;

}

</script>
</body>
</html>