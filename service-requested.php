<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$seller_id  = $_GET['seller']  ?? 0;
$product_id = $_GET['product'] ?? 0;

// Insert quote if not already exists
$check = mysqli_query($conn, "SELECT id FROM service_quotes WHERE buyer_id='$current_user_id' AND product_id='$product_id' LIMIT 1");
if(mysqli_num_rows($check) == 0){
    mysqli_query($conn, "INSERT INTO service_quotes (seller_id, buyer_id, product_id, status)
        VALUES ('$seller_id', '$current_user_id', '$product_id', 'Pending')");
    $msg = mysqli_real_escape_string($conn, "Hi! I'm interested in this service. Can we discuss what's involved and agree on a price?");
    mysqli_query($conn, "INSERT INTO messages (sender_id, receiver_id, product_id, message)
        VALUES ('$current_user_id', '$seller_id', '$product_id', '$msg')");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Service Requested | TrustFund</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{ background:#f5f5f5; font-family:Arial,sans-serif; display:flex; justify-content:center; align-items:center; min-height:100vh; }
.box{ background:white; border-radius:22px; padding:50px 40px; text-align:center; max-width:480px; box-shadow:0 10px 30px rgba(0,0,0,0.08); }
.icon{ font-size:60px; margin-bottom:20px; }
h2{ font-weight:800; margin-bottom:12px; }
p{ color:#666; margin-bottom:28px; }
.btn-purple{ background:#9b59b6; color:white; padding:14px 30px; border-radius:12px; text-decoration:none; font-weight:700; display:inline-block; }
.btn-purple:hover{ background:#8e44ad; color:white; }
</style>
</head>
<body>
<div class="box">
    <div class="icon">✅</div>
    <h2>Request Sent!</h2>
    <p>Your service request has been sent to the seller. They will review it and get back to you with a price. You can chat with them directly using the button below.</p>
    <a href="chat.php?seller=<?php echo $seller_id; ?>&product=<?php echo $product_id; ?>" class="btn-purple">Open Chat with Seller</a>
</div>
</body>
</html>