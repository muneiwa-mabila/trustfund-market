<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){

    header("Location: index.php");
    exit();

}

$seller_id = $_SESSION['user_id'];

$userQuery = mysqli_query(

    $conn,

    "SELECT * FROM users

    WHERE user_id='$seller_id'"

);

$currentUser = mysqli_fetch_assoc($userQuery);

if($currentUser['seller_status'] != 'approved'){

    header("Location: index.php");
    exit();

}

/* PENDING PRODUCTS COUNT */

$pendingCountQuery = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM seller_items
    WHERE seller_id = '$seller_id'
    AND product_status = 'Pending'
");

$pendingProducts = mysqli_fetch_assoc($pendingCountQuery)['total'] ?? 0;

/* GET PENDING PRODUCTS */

$query = mysqli_query($conn, "
    SELECT *
    FROM seller_items
    WHERE seller_id = '$seller_id'
    AND product_status = 'Pending'
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<title>Scheduled / Pending | TrustFund</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:Arial, sans-serif;
}

body{
  background:#f4f4f4;
  padding:28px;
  color:#222;
}

.dashboard-wrapper{
  background:white;
  min-height:90vh;
  display:flex;
  padding:18px;
  border-radius:24px;
}

/* SIDEBAR */

.sidebar{
  width:230px;
  background:#a65cc5;
  border-radius:20px;
  padding:24px 16px;
  color:white;
  display:flex;
  flex-direction:column;
  justify-content:space-between;
}

.logo{
  font-size:24px;
  font-weight:800;
  margin-bottom:25px;
}

.menu a{
  display:flex;
  justify-content:space-between;
  align-items:center;
  text-decoration:none;
  color:white;
  padding:12px 14px;
  border-radius:10px;
  font-size:13px;
  margin-bottom:6px;
  transition:0.2s;
  overflow:visible;
}

.menu a span{
  display:flex;
  align-items:center;
  gap:10px;
}

.menu a:hover{
  background:rgba(255,255,255,0.15);
}

.menu a.active{
  background:white;
  color:#333;
  font-weight:700;
}

.menu .sub{
  font-size:12px;
}

.menu .sub span{
  padding-left:14px;
}

.pending-count{
  width:20px;
  height:20px;
  border-radius:50%;
  background:#f4d35e;
  color:#333;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:11px;
  font-weight:700;
}

.signout{
  font-size:13px;
  color:white;
  text-decoration:none;
  padding:12px 14px;
}

.signout span{
  display:flex;
  align-items:center;
  gap:10px;
}

/* MAIN */

.main-content{
  flex:1;
  padding:10px 32px;
}

.page-header{
  margin-bottom:28px;
}

.page-header h1{
  font-size:34px;
  margin-bottom:8px;
}

.page-header p{
  color:#777;
  font-size:14px;
}

/* CARDS */

.pending-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));
  gap:22px;
}

.pending-card{
  background:white;
  border:1px solid #ededed;
  border-radius:22px;
  overflow:hidden;
  box-shadow:0 8px 25px rgba(0,0,0,0.03);
}

.product-image{
  width:100%;
  height:230px;
  object-fit:cover;
  background:#ddd;
}

.card-content{
  padding:24px;
}

.category{
  display:inline-block;
  background:#f3e8fa;
  color:#8e44ad;
  padding:6px 10px;
  border-radius:20px;
  font-size:12px;
  font-weight:700;
  margin-bottom:14px;
}

.product-name{
  font-size:21px;
  font-weight:800;
  margin-bottom:10px;
}

.description{
  color:#777;
  font-size:14px;
  line-height:1.6;
  margin-bottom:18px;
}

.price{
  font-size:26px;
  font-weight:800;
  margin-bottom:18px;
}

.status{
  display:inline-block;
  background:#fff4d6;
  color:#d97706;
  padding:8px 14px;
  border-radius:20px;
  font-size:12px;
  font-weight:700;
  margin-bottom:20px;
}

.timeline{
  background:#faf5fd;
  border:1px solid #ead8f5;
  border-radius:16px;
  padding:18px;
  margin-bottom:18px;
}

.timeline h4{
  font-size:14px;
  margin-bottom:10px;
}

.timeline p{
  color:#666;
  font-size:13px;
  line-height:1.6;
}

.progress-bar{
  width:100%;
  height:10px;
  background:#ececec;
  border-radius:20px;
  overflow:hidden;
  margin-top:14px;
}

.progress{
  width:70%;
  height:100%;
  background:#a65cc5;
  border-radius:20px;
}

.waiting-box{
  background:#f8fafc;
  border-radius:14px;
  padding:14px;
  font-size:13px;
  color:#555;
}

.empty-state{
  background:white;
  border:1px dashed #d7b5e7;
  border-radius:24px;
  padding:70px 30px;
  text-align:center;
}

.empty-state h2{
  margin-bottom:12px;
  font-size:28px;
}

.empty-state p{
  color:#777;
  margin-bottom:22px;
}

.empty-btn{
  display:inline-block;
  background:#a65cc5;
  color:white;
  text-decoration:none;
  padding:14px 22px;
  border-radius:14px;
  font-weight:700;
}

@media(max-width:900px){

  body{
    padding:12px;
  }

  .dashboard-wrapper{
    flex-direction:column;
  }

  .sidebar{
    width:100%;
    margin-bottom:20px;
  }

  .main-content{
    padding:10px;
  }

}

</style>
</head>

<body>

<div class="dashboard-wrapper">

<!-- SIDEBAR -->

<aside class="sidebar">

<div>

<div class="logo">TrustFund</div>

<nav class="menu">

<a href="seller-dashboard.php" class="sub">

<span>
<i class="fa-solid fa-house"></i>
Dashboard
</span>

<i class="fa-solid fa-chevron-down"></i>

</a>

<a href="#">

<span>
<i class="fa-solid fa-tag"></i>
Product
</span>

<i class="fa-solid fa-chevron-up"></i>

</a>

<a href="add-product.php" class="sub">

<span>
<i class="fa-solid fa-plus"></i>
Add new product(s)
</span>

<i class="fa-regular fa-circle-dot"></i>

</a>

<a href="analytics.php" class="sub">

<span>
<i class="fa-solid fa-chart-line"></i>
Analytics
</span>

</a>

<a href="drafts.php" class="sub">

<span>
<i class="fa-regular fa-file-lines"></i>
Drafts
</span>

</a>

<a href="released-products.php" class="sub">

<span>
<i class="fa-solid fa-box-open"></i>
Released
</span>

</a>

<a href="pending-products.php" class="active">

<span>
<i class="fa-regular fa-clock"></i>
Scheduled/Pending
</span>

<span class="pending-count">
<?php echo $pendingProducts; ?>
</span>

</a>

<a href="revenue.php">

<span>
<i class="fa-solid fa-wallet"></i>
Revenue
</span>

<i class="fa-solid fa-chevron-down"></i>

</a>

<a href="refunds.php">

<span>
<i class="fa-solid fa-receipt"></i>
Refunds
</span>

<i class="fa-solid fa-chevron-down"></i>

</a>
<a href="seller-messages.php">

<span>

<i class="fa-solid fa-comments"></i>

Messages

</span>

</a>
<a href="seller-profile.php?id=<?php echo $_SESSION['user_id']; ?>">

<span>

<i class="fa-solid fa-user"></i>

Public Profile

</span>

</a>
<a href="seller-orders.php" class="dashboard-card">

<div class="card-icon">
📦
</div>

<div class="card-title">
Seller Orders
</div>

<div class="card-text">

Manage orders

</div>

</a>

</nav>

<a href="index.php" class="signout">

<span>
<i class="fa-solid fa-store"></i>
Switch Profile
</span>

</a>

<a href="logout.php" class="signout">

<span>
<i class="fa-solid fa-right-from-bracket"></i>
Sign out
</span>

</a>

</div>


</aside>


<!-- MAIN -->

<main class="main-content">

<div class="page-header">

<h1>Scheduled / Pending</h1>

<p>
Products currently under review or waiting to be published
</p>

</div>

<?php if (mysqli_num_rows($query) > 0): ?>

<div class="pending-grid">

<?php while ($item = mysqli_fetch_assoc($query)): ?>

<div class="pending-card">

<?php if (!empty($item['product_image'])): ?>

<img 
src="uploads/<?php echo htmlspecialchars($item['product_image']); ?>"
class="product-image"
>

<?php else: ?>

<div class="product-image"></div>

<?php endif; ?>

<div class="card-content">

<div class="category">
<?php echo htmlspecialchars($item['product_category']); ?>
</div>

<div class="product-name">
<?php echo htmlspecialchars($item['product_name']); ?>
</div>

<div class="description">
<?php echo htmlspecialchars($item['product_description']); ?>
</div>

<div class="price">
R<?php echo number_format($item['product_price'], 2); ?>
</div>

<div class="status">
Pending Review
</div>

<div class="timeline">

<h4>Publishing Progress</h4>

<p>
Your product is currently being reviewed before appearing publicly on TrustFund.
</p>

<div class="progress-bar">
<div class="progress"></div>
</div>

</div>

<div class="waiting-box">

Submitted on:

<strong>
<?php echo date("d M Y", strtotime($item['created_at'])); ?>
</strong>

</div>

</div>

</div>

<?php endwhile; ?>

</div>

<?php else: ?>

<div class="empty-state">

<h2>No Pending Products</h2>

<p>
Products awaiting approval will appear here.
</p>

<a href="add-product.php" class="empty-btn">
Add Product
</a>

</div>

<?php endif; ?>

</main>

</div>

</body>
</html>