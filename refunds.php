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

/* TOTAL REFUNDS */

$refundQuery = mysqli_query($conn, "
    SELECT COUNT(*) AS total_refunds
    FROM refunds
    WHERE seller_id = '$seller_id'
");

$totalRefunds = mysqli_fetch_assoc($refundQuery)['total_refunds'] ?? 0;

/* TOTAL REFUND AMOUNT */

$refundAmountQuery = mysqli_query($conn, "
    SELECT SUM(refund_amount) AS total_amount
    FROM refunds
    WHERE seller_id = '$seller_id'
");

$totalRefundAmount = mysqli_fetch_assoc($refundAmountQuery)['total_amount'] ?? 0;

/* REFUND LIST */

$refundsQuery = mysqli_query($conn, "
    SELECT *
    FROM refunds
    WHERE seller_id = '$seller_id'
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Refunds | TrustFund</title>

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
    width:22px;
    height:22px;
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
    text-decoration:none;
    color:white;
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
    margin-bottom:30px;
}

.page-header h1{
    font-size:34px;
    margin-bottom:8px;
}

.page-header p{
    color:#777;
}

/* STATS */

.stats-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:20px;
    margin-bottom:28px;
}

.stat-card{
    background:white;
    border:1px solid #ececec;
    border-radius:22px;
    padding:24px;
}

.stat-card h4{
    color:#777;
    font-size:14px;
    margin-bottom:14px;
}

.stat-card h2{
    font-size:34px;
    margin-bottom:8px;
}

.stat-card p{
    color:#ef4444;
    font-size:13px;
}

/* TABLE */

.refunds-card{
    background:white;
    border:1px solid #ececec;
    border-radius:24px;
    padding:24px;
}

.refunds-card h3{
    margin-bottom:20px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    text-align:left;
    padding:14px 10px;
    color:#888;
    font-size:12px;
    border-bottom:1px solid #eee;
}

td{
    padding:18px 10px;
    border-bottom:1px solid #f1f1f1;
    font-size:13px;
}

.status{
    display:inline-block;
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:700;
}

.completed{
    background:#dcfce7;
    color:#16a34a;
}

.pending{
    background:#fff4d6;
    color:#d97706;
}

.empty-state{
    text-align:center;
    padding:60px 20px;
    color:#777;
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

    .stats-grid{
        grid-template-columns:1fr;
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

<a href="pending-products.php" class="sub">

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

<a href="refunds.php" class="active">

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

<h1>Refunds</h1>

<p>
Manage customer refund activity and track refunded payments
</p>

</div>

<!-- STATS -->

<div class="stats-grid">

<div class="stat-card">

<h4>Total Refunds</h4>

<h2>
<?php echo $totalRefunds; ?>
</h2>

<p>
Refund requests received
</p>

</div>

<div class="stat-card">

<h4>Total Refunded Amount</h4>

<h2>
R<?php echo number_format($totalRefundAmount, 2); ?>
</h2>

<p>
Money refunded to customers
</p>

</div>

</div>

<!-- TABLE -->

<div class="refunds-card">

<h3>Refund Activity</h3>

<?php if(mysqli_num_rows($refundsQuery) > 0): ?>

<table>

<thead>

<tr>
<th>Customer</th>
<th>Product</th>
<th>Amount</th>
<th>Status</th>
<th>Date</th>
</tr>

</thead>

<tbody>

<?php while($refund = mysqli_fetch_assoc($refundsQuery)): ?>

<tr>

<td>
<?php echo htmlspecialchars($refund['customer_name']); ?>
</td>

<td>
<?php echo htmlspecialchars($refund['product_name']); ?>
</td>

<td>
R<?php echo number_format($refund['refund_amount'], 2); ?>
</td>

<td>

<span class="status <?php echo strtolower($refund['refund_status']); ?>">

<?php echo htmlspecialchars($refund['refund_status']); ?>

</span>

</td>

<td>
<?php echo date("d M Y", strtotime($refund['created_at'])); ?>
</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

<?php else: ?>

<div class="empty-state">

<h2>No Refunds Yet</h2>

<p>
Refund activity from customers will appear here.
</p>

</div>

<?php endif; ?>

</div>

</main>

</div>

</body>
</html>