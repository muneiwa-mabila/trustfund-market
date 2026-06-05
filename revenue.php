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

/* TOTAL REVENUE */

$revenueQuery = mysqli_query($conn, "
    SELECT 
        SUM(product_price * sales_count) AS total_revenue
    FROM seller_items
    WHERE seller_id = '$seller_id'
    AND product_status = 'Approved'
");

$totalRevenue = mysqli_fetch_assoc($revenueQuery)['total_revenue'] ?? 0;

/* TOTAL SALES */

$salesQuery = mysqli_query($conn, "
    SELECT 
        SUM(sales_count) AS total_sales
    FROM seller_items
    WHERE seller_id = '$seller_id'
");

$totalSales = mysqli_fetch_assoc($salesQuery)['total_sales'] ?? 0;

/* ACTIVE PRODUCTS */

$productsQuery = mysqli_query($conn, "
    SELECT COUNT(*) AS total_products
    FROM seller_items
    WHERE seller_id = '$seller_id'
    AND product_status = 'Approved'
");

$totalProducts = mysqli_fetch_assoc($productsQuery)['total_products'] ?? 0;

/* MONTHLY REVENUE */

$monthlyRevenueQuery = mysqli_query($conn, "
    SELECT 
        MONTHNAME(created_at) AS month_name,
        SUM(product_price * sales_count) AS revenue
    FROM seller_items
    WHERE seller_id = '$seller_id'
    GROUP BY MONTH(created_at), MONTHNAME(created_at)
    ORDER BY MONTH(created_at)
");

$months = [];
$revenues = [];

while($row = mysqli_fetch_assoc($monthlyRevenueQuery)){

    $months[] = $row['month_name'];
    $revenues[] = $row['revenue'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Revenue | TrustFund</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    flex-shrink:0;
}

.signout{
    text-decoration:none;
    color:white;
    padding:12px 14px;
    border-radius:10px;
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
    grid-template-columns:repeat(3,1fr);
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
    color:#10b981;
    font-size:13px;
}

/* CHART */

.chart-card{
    background:white;
    border:1px solid #ececec;
    border-radius:24px;
    padding:28px;
}

.chart-card h3{
    margin-bottom:20px;
}

.chart-wrapper{
    height:350px;
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

<a href="revenue.php" class="active">

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

<h1>Revenue</h1>

<p>
Track your earnings, product sales and monthly revenue performance
</p>

</div>

<!-- STATS -->

<div class="stats-grid">

<div class="stat-card">

<h4>Total Revenue</h4>

<h2>
R<?php echo number_format($totalRevenue, 2); ?>
</h2>

<p>
Live earnings from all approved products
</p>

</div>

<div class="stat-card">

<h4>Total Sales</h4>

<h2>
<?php echo number_format($totalSales); ?>
</h2>

<p>
Products sold on TrustFund
</p>

</div>

<div class="stat-card">

<h4>Active Products</h4>

<h2>
<?php echo $totalProducts; ?>
</h2>

<p>
Currently published products
</p>

</div>

</div>

<!-- CHART -->

<div class="chart-card">

<h3>Monthly Revenue</h3>

<div class="chart-wrapper">
<canvas id="revenueChart"></canvas>
</div>

</div>

</main>

</div>

<script>

const ctx = document.getElementById('revenueChart');

new Chart(ctx, {

    type:'line',

    data:{
        labels: <?php echo json_encode($months); ?>,

        datasets:[{
            label:'Revenue',
            data: <?php echo json_encode($revenues); ?>,
            borderWidth:3,
            tension:0.4
        }]
    },

    options:{
        responsive:true,
        maintainAspectRatio:false
    }

});

</script>

</body>
</html>