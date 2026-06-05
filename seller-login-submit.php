<?php

session_start();
include 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

/* CHECK SELLER ACCOUNT */

$sql = "SELECT * FROM sellers WHERE email='$email'";

$result = mysqli_query($conn, $sql);

/* IF ACCOUNT EXISTS */

if(mysqli_num_rows($result) > 0){

    $seller = mysqli_fetch_assoc($result);

    /* CHECK PASSWORD */

    if($password == $seller['password']){

        /* CREATE SESSION */

        $_SESSION['seller_id'] = $seller['id'];
        $_SESSION['seller_name'] = $seller['name'];

        /* REDIRECT TO DASHBOARD */

        header("Location: seller-dashboard.php");
        exit();

    } else {

        header("Location: seller-login.php?error=invalid");
        exit();

    }

} else {

    header("Location: seller-login.php?error=noaccount");
    exit();

}

?>