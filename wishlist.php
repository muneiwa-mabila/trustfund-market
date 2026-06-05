<?php

session_start();

$wishlist = $_SESSION['wishlist'] ?? [];

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Wishlist | TrustFund</title>

<style>

body{
    font-family:Arial,sans-serif;
    background:#f5f5f5;
}

.page{
    max-width:1200px;
    margin:auto;
    padding:40px 20px;
}

.title{
    font-size:32px;
    font-weight:800;
    margin-bottom:30px;
}

.item{
    background:white;
    padding:20px;
    margin-bottom:18px;
    border-radius:12px;
}

</style>

</head>

<body>

<div class="page">

<div class="title">
Wishlist
</div>

<?php if(empty($wishlist)){ ?>

<div class="item">
No wishlist items yet.
</div>

<?php } ?>

<?php foreach($wishlist as $item){ ?>

<div class="item">

<?php echo $item['name']; ?>

</div>

<?php } ?>

</div>

</body>
</html>