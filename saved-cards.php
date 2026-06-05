<?php

session_start();

$cart = $_SESSION['cart'] ?? [];

$total = 0;

foreach($cart as $item){

    $total += ((float)$item['price']) * $item['quantity'];

}

/* CARD DETAILS */

$name = $_POST['name'] ?? '';

$card = $_POST['card_number'] ?? '';

$expiry = $_POST['expiry'] ?? '';

$year = $_POST['year'] ?? '';

$bank = $_POST['bank'] ?? '';

$last4 = substr(

    preg_replace('/\D/', '', $card),

    -4

);

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Saved Cards | TrustFund</title>

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

.payment-layout{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:24px;
}

/* LEFT */

.cards-section{
    background:white;
    border:1px solid #eee;
    padding:24px;
}

.section-title{
    font-size:16px;
    font-weight:800;
    margin-bottom:20px;
}

/* ADD CARD */

.add-card-btn{
    display:inline-block;
    background:#9b59b6;
    color:white;
    padding:10px 18px;
    border-radius:8px;
    font-size:12px;
    font-weight:700;
    text-decoration:none;
    margin-bottom:22px;
}

/* CARD */

.saved-card{
    border:2px solid #d7b2ea;
    background:#f7effc;
    border-radius:10px;
    padding:18px;
    margin-bottom:16px;

    display:flex;
    justify-content:space-between;
    align-items:center;

    cursor:pointer;
}

.saved-card.active{
    border-color:#9b59b6;
}

.card-left{
    display:flex;
    gap:14px;
}

.card-radio{
    margin-top:5px;
}

.card-bank{
    font-size:13px;
    font-weight:800;
    margin-bottom:6px;
}

.card-number{
    font-size:12px;
    color:#555;
}

.delete-card{
    color:#9b59b6;
    font-size:11px;
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

.pay-btn{
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

    .payment-layout{
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

<div class="payment-layout">

<!-- LEFT -->

<div class="cards-section">

<div class="section-title">
Pay with a saved card
</div>

<a href="payment.php" class="add-card-btn">

Add new card

</a>

<!-- CARD -->

<div class="saved-card active" onclick="selectCard(this)">

<div class="card-left">

<input 
type="radio"
checked
name="savedcard"
class="card-radio"
>

<div>

<div class="card-bank">

<?php echo $bank; ?>

</div>

<div class="card-number">

MasterCard ****<?php echo $last4; ?>

Expires <?php echo $expiry; ?>/<?php echo $year; ?>

</div>

</div>

</div>

<div 
class="delete-card"
onclick="deleteCard(this)"
>
DELETE
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
R35,00
</div>

</div>

<div class="summary-row">

<div>Coupon Applied</div>

<div>R0.00</div>

</div>

<div class="summary-total">

<div>TOTAL</div>

<div>
R<?php echo number_format($total + 35 ); ?>
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

<a href="authorising-payment.php">

<button class="pay-btn">

Pay now

</button>

</a>

</div>

</div>

</div>

<script>

function selectCard(element){

    document.querySelectorAll(".saved-card")

    .forEach(card => {

        card.classList.remove("active");

        card.querySelector("input").checked = false;

    });

    element.classList.add("active");

    element.querySelector("input").checked = true;

}

function deleteCard(element){

    event.stopPropagation();

    const card = element.closest(".saved-card");

    card.remove();

}

</script>

</body>
</html>