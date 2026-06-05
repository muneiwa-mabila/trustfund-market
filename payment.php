<?php

session_start();
$pickup_point = $_POST['pickup_point'] ?? '';

$_SESSION['pickup_point'] = $pickup_point;
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Payment | TrustFund</title>

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

.page-wrapper{
    max-width:500px;
    margin:auto;
    padding:80px 20px;
}

/* CARD */

.payment-card{
    background:white;
    padding:40px;
    border-radius:14px;
    border:1px solid #eee;
}

/* TITLE */

.payment-title{
    text-align:center;
    font-size:32px;
    font-weight:800;
    margin-bottom:40px;
    color:#111;
}

/* LABELS */

.form-label{
    font-size:12px;
    font-weight:700;
    color:#666;
    margin-bottom:8px;
}

/* INPUTS */

.form-control,
.form-select{
    height:50px;
    border-radius:10px;
    border:1px solid #ddd;
    margin-bottom:22px;
    font-size:14px;
}

.form-control:focus,
.form-select:focus{

    border-color:#9b59b6 !important;

    box-shadow:0 0 0 3px rgba(155,89,182,0.15) !important;

    outline:none !important;

}

/* ROW */

.row-gap-custom{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
}

/* BUTTON */

.pay-btn{
    width:100%;
    border:none;
    background:#9b59b6;
    color:white;
    height:52px;
    border-radius:10px;
    font-size:14px;
    font-weight:700;
    margin-top:10px;
    transition:0.2s;
}

.pay-btn:hover{
    background:#8a47ab;
}

/* SMALL TEXT */

.small-note{
    font-size:11px;
    color:#999;
    margin-top:-12px;
    margin-bottom:22px;
}


</style>

</head>

<body>

<div class="page-wrapper">

<div class="payment-card">

<h1 class="payment-title">
Add new payment card
</h1>

<form action="saved-cards.php" method="POST">
<input 
type="hidden"
name="pickup_point"
value="<?php echo htmlspecialchars($pickup_point); ?>"
>

<label class="form-label">
Name on card
</label>

<input 
type="text"
class="form-control"
placeholder="Muneiwa Mabila"
required
>

<label class="form-label">
Card number
</label>

<input 
type="text"
class="form-control"
placeholder="1234 5678 9012 3456"
maxlength="19"
required
>

<div class="row-gap-custom">

<div>

<label class="form-label">
Expiry date
</label>

<input 
type="text"
class="form-control"
placeholder="MM"
maxlength="2"
required
>

</div>

<div>

<label class="form-label">
Year
</label>

<input 
type="text"
class="form-control"
placeholder="YYYY"
maxlength="4"
required
>

</div>

</div>

<label class="form-label">
CVV
</label>

<input 
type="password"
class="form-control"
placeholder="123"
maxlength="3"
required
>

<label class="form-label">
Bank
</label>

<select class="form-select">

<option>
Choose Bank
</option>

<option>
FNB
</option>

<option>
Capitec
</option>

<option>
Nedbank
</option>

<option>
ABSA
</option>

<option>
Standard Bank
</option>

<option>
TymeBank
</option>

</select>

<div class="small-note">

South African issued cards only

</div>

<button type="submit" class="pay-btn">

Continue

</button>

</form>

</div>

</div>

</body>
</html>