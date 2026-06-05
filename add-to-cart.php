<?php

session_start();
include 'db.php';

$product_id = $_POST['product_id'];
$quantity = (int)$_POST['quantity'];

/* GET PRODUCT */
$query = "
SELECT *
FROM seller_items
WHERE id = '$product_id'
AND product_status='Approved'
";

$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if(!$product){
    die("Product not found");
}

/* BLOCK BUYING OWN PRODUCT */
if(isset($_SESSION['user_id'])){
    if($_SESSION['user_id'] == $product['seller_id']){
        $_SESSION['cart_error'] = "You cannot buy your own product.";
        header("Location: product.php?id=$product_id");
        exit();
    }
}

/* CREATE CART */
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

/* CHECK IF EXISTS */
if(isset($_SESSION['cart'][$product_id])){

    $_SESSION['cart'][$product_id]['quantity'] += $quantity;

}else{

    $_SESSION['cart'][$product_id] = [

        'id' => $product['id'],
        'name' => $product['product_name'],
        'price' => $product['product_price'],
        'image' => $product['product_image'],
        'quantity' => $quantity,

        /* ADDED (IMPORTANT) */
        'seller_id' => $product['seller_id'],
        'product_type' => $product['product_type'],
        'category' => $product['product_category']
    ];
}

/* GO TO CART INSTEAD OF CHECKOUT */
header("Location: cart.php");
exit();
?>