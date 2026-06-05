<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Payment Failed | TrustFund</title>

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

.failed-wrapper{
    min-height:100vh;

    display:flex;
    align-items:center;
    justify-content:center;

    padding:40px 20px;
}

/* BOX */

.failed-box{
    background:white;

    width:430px;

    padding:60px 40px;

    border-radius:18px;

    text-align:center;

    border:1px solid #eee;
}

/* ICON */

.failed-icon{
    width:120px;
    height:120px;

    border-radius:50%;

    border:5px solid #ef4444;

    color:#ef4444;

    font-size:52px;

    display:flex;
    align-items:center;
    justify-content:center;

    margin:auto;
    margin-bottom:35px;

    box-shadow:0 0 0 10px rgba(239,68,68,0.1);
}

/* TITLE */

.failed-title{
    font-size:30px;
    font-weight:800;

    margin-bottom:12px;

    color:#111;
}

/* TEXT */

.failed-text{
    font-size:14px;
    color:#666;

    line-height:1.7;

    margin-bottom:30px;
}

/* BUTTONS */

.failed-btn{
    display:block;

    width:100%;

    height:52px;

    border:none;

    border-radius:12px;

    font-size:14px;
    font-weight:700;

    margin-bottom:14px;

    transition:0.2s;
}

/* PRIMARY */

.primary-btn{
    background:#9b59b6;
    color:white;
}

.primary-btn:hover{
    background:#8a47ab;
}

/* SECONDARY */

.secondary-btn{
    background:white;

    border:1px solid #ddd;

    color:#444;
}

.secondary-btn:hover{
    background:#f7f7f7;
}
a{
    text-decoration:none;
}

</style>

</head>

<body>

<div class="failed-wrapper">

<div class="failed-box">

<div class="failed-icon">

<i class="fa-solid fa-exclamation"></i>

</div>

<div class="failed-title">

Payment failed

</div>

<div class="failed-text">

Not enough money on the card.

Please try another payment method.

</div>

<a href="saved-cards.php">

<button class="failed-btn primary-btn">

Pay with another card

</button>

</a>

<a href="index.php">

<button class="failed-btn secondary-btn">

End transaction

</button>

</a>

</div>

</div>

</body>
</html>