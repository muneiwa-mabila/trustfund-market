<?php

session_start();

$id = $_GET['id'];

if(isset($_SESSION['cart'][$id])){

    $item = $_SESSION['cart'][$id];

    if(!isset($_SESSION['wishlist'])){
        $_SESSION['wishlist'] = [];
    }

    $_SESSION['wishlist'][$id] = $item;

    unset($_SESSION['cart'][$id]);

}

header("Location: wishlist.php");

?>