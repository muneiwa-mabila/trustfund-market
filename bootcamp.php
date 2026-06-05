<?php
session_start();
include 'db.php';
$currentUser = null;
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    $userQuery = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$user_id'");
    $currentUser = mysqli_fetch_assoc($userQuery);
}
$cartCount = 0;
if(isset($_SESSION['cart'])){ foreach($_SESSION['cart'] as $item){ $cartCount += $item['quantity']; } }
$wishlistCount = 0;
if(isset($_SESSION['wishlist'])){ $wishlistCount = count($_SESSION['wishlist']); }
$username = $_SESSION['name'] ?? 'MyAccount';
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Bootcamps | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#f5f5f5;
    font-family:Arial, sans-serif;
    color:#111;
}

/* HEADER */

.top-header{
    background:#e5e5e5;
    padding:18px 40px;
}

.header-container{
    max-width:1200px;
    margin:auto;

    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo{
    font-weight:800;
    font-size:22px;
}

.header-nav{
    display:flex;
    align-items:center;
    gap:30px;
}

.header-nav a{
    text-decoration:none;
    color:#111;
    font-size:13px;
    font-weight:500;
}

/* PAGE */

.page-wrapper{
    max-width:1200px;
    margin:auto;
    padding:30px 20px;
}

/* BREADCRUMB */

.breadcrumb-nav{
    font-size:13px;
    margin-bottom:25px;
}

.breadcrumb-nav a{
    text-decoration:none;
    color:#777;
}

.breadcrumb-nav span{
    color:#a65cc5;
    font-weight:600;
}

/* TITLE */

.page-title{
    font-size:38px;
    font-weight:800;
    margin-bottom:24px;
}

/* HERO */

.hero-section{
    height:320px;
    border-radius:22px;
    background:#d9d9d9;
    padding:50px;
    display:flex;
    flex-direction:column;
    justify-content:flex-end;
    margin-bottom:35px;
}

.hero-section h1{
    font-size:52px;
    font-weight:800;
    margin-bottom:10px;
}

.hero-section p{
    font-size:16px;
    color:#333;
    margin-bottom:22px;
}

.hero-btn{
    width:200px;
    border:none;
    background:#a65cc5;
    color:white;
    padding:14px 18px;
    border-radius:12px;
    font-size:13px;
    font-weight:700;
}

/* BOOTCAMPS */

.bootcamp-row{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:22px;
    margin-bottom:40px;
}

.bootcamp-card{
    background:white;
    border-radius:18px;
    overflow:hidden;
}

.bootcamp-image{
    width:100%;
    height:220px;
    background:#e2e2e2;
}

.bootcamp-info{
    padding:18px;
}

.bootcamp-title{
    font-size:18px;
    font-weight:700;
    margin-bottom:10px;
}

.bootcamp-desc{
    font-size:14px;
    color:#666;
    line-height:1.5;
    margin-bottom:14px;
}

.bootcamp-price{
    font-size:14px;
    font-weight:700;
}

/* BANNER */

.discount-banner{
    height:240px;
    border-radius:22px;
    background:#d9d9d9;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
    margin-bottom:40px;
}

.discount-banner h2{
    font-size:42px;
    font-weight:800;
    margin-bottom:10px;
}

.discount-banner p{
    margin-bottom:20px;
    color:#444;
}

.discount-btn{
    border:none;
    background:#a65cc5;
    color:white;
    padding:14px 22px;
    border-radius:12px;
    font-size:13px;
    font-weight:700;
}

/* CATEGORY */

.category-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:24px;
}

.category-box{
    height:360px;
    background:#d9d9d9;
    border-radius:22px;
    padding:34px;
    display:flex;
    align-items:flex-end;
}

.category-box h2{
    font-size:42px;
    font-weight:800;
}

/* RESPONSIVE */

@media(max-width:1000px){

    .bootcamp-row{
        grid-template-columns:repeat(2,1fr);
    }

}

@media(max-width:700px){

    .bootcamp-row{
        grid-template-columns:1fr;
    }

    .category-grid{
        grid-template-columns:1fr;
    }

    .hero-section{
        height:260px;
        padding:30px;
    }

    .hero-section h1{
        font-size:38px;
    }

    .header-container{
        flex-direction:column;
        gap:15px;
    }

}

</style>

</head>

<body>

<!-- HEADER -->

<header class="top-header">
<div class="header-container">
<a href="index.php" class="logo" style="text-decoration:none;color:inherit;">TrustFund</a>
<nav class="header-nav">
<a href="#">Orders</a>
<?php if(!isset($_SESSION['user_id'])): ?>
<a href="login.php">Login</a>
<a href="register.php">Register</a>
<?php endif; ?>
<a href="account.php"><?php echo $username; ?></a>
<a href="wishlist.php" style="position:relative;">&#9825;<?php if($wishlistCount > 0): ?><span style="position:absolute;top:-8px;right:-10px;background:#a65cc5;color:white;border-radius:50%;width:16px;height:16px;font-size:10px;display:flex;align-items:center;justify-content:center;"><?php echo $wishlistCount; ?></span><?php endif; ?></a>
<a href="cart.php" style="position:relative;">&#128722;<?php if($cartCount > 0): ?><span style="position:absolute;top:-8px;right:-10px;background:#a65cc5;color:white;border-radius:50%;width:16px;height:16px;font-size:10px;display:flex;align-items:center;justify-content:center;"><?php echo $cartCount; ?></span><?php endif; ?></a>
</nav>
</div>
</header>

<!-- PAGE -->

<div class="page-wrapper">

<!-- BREADCRUMB -->

<div class="breadcrumb-nav">

<a href="index.php">Home</a>

→

<span>Bootcamps</span>

</div>

<!-- TITLE -->

<h1 class="page-title">Bootcamps</h1>

<!-- HERO -->

<section class="hero-section">

<h1>Build real-world skills</h1>

<p>
Join practical bootcamps in tech, design and digital careers
</p>

<button class="hero-btn">
EXPLORE BOOTCAMPS
</button>

</section>

<!-- LIVE BOOTCAMPS FROM DATABASE -->

<section class="bootcamp-row">

<?php
$bootcamps = mysqli_query($conn,
    "SELECT * FROM seller_items WHERE product_category='Bootcamp' AND product_status='Approved' ORDER BY id DESC LIMIT 6"
);
$count = mysqli_num_rows($bootcamps);
if($count == 0): ?>

<div style="grid-column:1/-1; text-align:center; padding:60px 20px; color:#999;">
    <p style="font-size:18px; margin-bottom:10px;">No bootcamps listed yet.</p>
    <p style="font-size:14px;">Check back soon.
    <?php if(!$currentUser || $currentUser['seller_status'] != 'approved'): ?>
    or <a href="application.php" style="color:#a65cc5;">become a seller</a>
    <?php endif; ?>
    </p>
</div>

<?php else: ?>
<?php while($p = mysqli_fetch_assoc($bootcamps)): ?>

<a href="product.php?id=<?php echo $p['id']; ?>" style="text-decoration:none; color:inherit;">
<div class="bootcamp-card">
<div class="bootcamp-image">
<img src="uploads/<?php echo htmlspecialchars($p['product_image']); ?>" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none'">
</div>
<div class="bootcamp-info">
<div class="bootcamp-title"><?php echo htmlspecialchars($p['product_name']); ?></div>
<div class="bootcamp-desc"><?php echo htmlspecialchars(substr($p['product_description'],0,100)).(strlen($p['product_description'])>100?'...':''); ?></div>
<div class="bootcamp-price">R<?php echo number_format((float)preg_replace('/[^0-9.]/','',$p['product_price'])); ?></div>
</div>
</div>
</a>

<?php endwhile; ?>
<?php endif; ?>

</section>



</div>

</body>
</html>