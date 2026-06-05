<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];
$product_id = mysqli_real_escape_string($conn, $_POST['product_id'] ?? 0);
$seller_id = mysqli_real_escape_string($conn, $_POST['seller_id'] ?? 0);

// Create quote record
$q1 = mysqli_query($conn, "INSERT INTO service_quotes (seller_id, buyer_id, product_id, status)
    VALUES ('$seller_id', '$buyer_id', '$product_id', 'Pending')");
if(!$q1) die("Quote failed: " . mysqli_error($conn));

// Send automatic first message
$auto_message = mysqli_real_escape_string($conn, "Hi! I'm interested in this service. Can we discuss what's involved and agree on a price?");
$q2 = mysqli_query($conn, "INSERT INTO messages (sender_id, receiver_id, product_id, message)
    VALUES ('$buyer_id', '$seller_id', '$product_id', '$auto_message')");
if(!$q2) die("Message failed: " . mysqli_error($conn));

header("Location: chat.php?seller=$seller_id&product=$product_id");
exit();
?>