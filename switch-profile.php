<?php

session_start();

if($_SESSION['seller_status'] != 'approved'){

    header("Location:index.php");
    exit();

}

if($_SESSION['active_profile'] == 'buyer'){

    $_SESSION['active_profile'] = 'seller';

    header("Location:seller/dashboard.php");

} else {

    $_SESSION['active_profile'] = 'buyer';

    header("Location:index.php");

}

exit();

?>