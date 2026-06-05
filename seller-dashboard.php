<?php

session_start();
include 'db.php';

/* PROTECT PAGE */

if(!isset($_SESSION['user_id'])){

    header("Location: ../index.php");
    exit();

}

$user_id = $_SESSION['user_id'];

$userQuery = mysqli_query(

    $conn,

    "SELECT * FROM users

    WHERE user_id='$user_id'"

);

$currentUser = mysqli_fetch_assoc($userQuery);

if($currentUser['seller_status'] != 'approved'){

    header("Location: ../index.php");
    exit();

}



/* USER DETAILS */

$user_id = $_SESSION['user_id'];

$seller_name = $_SESSION['name'] ?? 'Seller';


/* TOTAL PRODUCTS */

$totalProductsQuery = mysqli_query($conn, "

    SELECT COUNT(*) AS total

    FROM seller_items

    WHERE seller_id = '$user_id'

");

$totalProducts = mysqli_fetch_assoc($totalProductsQuery)['total'] ?? 0;

/* PENDING PRODUCTS */

$pendingQuery = mysqli_query($conn, "

    SELECT COUNT(*) AS total

    FROM seller_items

    WHERE seller_id = '$user_id'

    AND product_status = 'Pending'

");

$pendingProducts = mysqli_fetch_assoc($pendingQuery)['total'] ?? 0;

/* ACTIVE PRODUCTS */

$activeQuery = mysqli_query($conn, "

    SELECT COUNT(*) AS total

    FROM seller_items

    WHERE seller_id = '$user_id'

    AND product_status = 'Approved'

");

$activeProducts = mysqli_fetch_assoc($activeQuery)['total'] ?? 0;

/* PRODUCTS PER MONTH */

$chartQuery = mysqli_query($conn, "

    SELECT 

        MONTHNAME(created_at) AS month_name,

        COUNT(*) AS total

    FROM seller_items

    WHERE seller_id = '$user_id'

    GROUP BY MONTH(created_at), MONTHNAME(created_at)

    ORDER BY MONTH(created_at)

");

$months = [];

$totals = [];

while ($row = mysqli_fetch_assoc($chartQuery)) {

    $months[] = $row['month_name'];

    $totals[] = $row['total'];

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Seller Dashboard | TrustFund</title>

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

/* WRAPPER */

.dashboard-wrapper{
    background:white;
    min-height:90vh;
    display:flex;
    padding:18px;
    border-radius:18px;
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
    padding-left:6px;
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

.menu a i{
    font-size:12px;
    opacity:0.95;
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
    text-decoration:none;
    color:white;

    padding:14px 16px;
    border-radius:12px;

    font-size:15px;
    font-weight:700;

    transition:0.2s;

    display:flex;
    align-items:center;
}

.signout span{
    display:flex;
    align-items:center;
    gap:10px;
}

.signout:hover{
    background:rgba(255,255,255,0.15);
}

/* MAIN */

.main-content{
    flex:1;
    padding:10px 28px;
}

.top-area{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
}

.top-area h1{
    font-size:30px;
}

.welcome{
    color:#777;
    font-size:14px;
}

/* CARD */

.stats-card{
    border:1px solid #e5e5e5;
    border-radius:18px;
    padding:24px;
    width:100%;
    min-height:350px;
}

.card-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.card-top h4{
    font-size:15px;
}

select{
    border:1px solid #ddd;
    border-radius:8px;
    padding:8px 12px;
    background:white;
}

.big-number{
    font-size:28px;
    font-weight:800;
    margin-bottom:6px;
}

.growth{
    color:#10b981;
    font-size:13px;
    margin-bottom:25px;
}

.chart-box{
    width:100%;
    height:260px;
}

/* SUMMARY */

.summary-grid{
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    gap:18px;
    margin-top:25px;
}

.summary-box{
    border:1px solid #eee;
    border-radius:14px;
    padding:18px;
    background:white;
}

.summary-box h3{
    font-size:28px;
    margin-bottom:5px;
}

.summary-box p{
    color:#777;
    font-size:13px;
}

.empty-note{
    margin-top:15px;
    background:#f7f1fa;
    border-left:5px solid #a65cc5;
    color:#4a235a;
    padding:14px;
    border-radius:10px;
    font-size:14px;
}
.menu-link{

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

.menu-link span{

    display:flex;
    align-items:center;
    gap:10px;

}

.menu-link:hover{

    background:rgba(255,255,255,0.15);

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

    .summary-grid{
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

<a href="seller-dashboard.php" class="active">

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

<div class="top-area">

<div>

<h1>Dashboard</h1>

<p class="welcome">
Welcome back,
<?php echo htmlspecialchars($seller_name); ?>
</p>

</div>

</div>

<section class="stats-card">

<div class="card-top">

<h4>Product statistics</h4>

<select>

<option>All time</option>
<option>This month</option>
<option>This week</option>

</select>

</div>

<div class="big-number">
<?php echo $totalProducts; ?> products
</div>

<div class="growth">
Live statistics from your seller account
</div>

<div class="chart-box">
<canvas id="sellerChart"></canvas>
</div>

<?php if ($totalProducts == 0): ?>

<div class="empty-note">

No products have been added yet.
Once you add products, this dashboard updates automatically.

</div>

<?php endif; ?>

</section>

<section class="summary-grid">

<div class="summary-box">

<h3><?php echo $activeProducts; ?></h3>

<p>Active products</p>

</div>

<div class="summary-box">

<h3><?php echo $pendingProducts; ?></h3>

<p>Pending products</p>

</div>

<div class="summary-box">

<h3><?php echo $totalProducts; ?></h3>

<p>Total products</p>

</div>

<div class="summary-box">

<h3>

R<?php echo number_format($currentUser['wallet_balance'] ?? 0, 2); ?>

</h3>

<p>Wallet balance</p>

</div>

</section>

</main>

</div>

<script>

const ctx = document.getElementById('sellerChart');

new Chart(ctx, {

    type: 'line',

    data: {

        labels: <?php echo json_encode($months); ?>,

        datasets: [{

            label: 'Products Added',

            data: <?php echo json_encode($totals); ?>,

            borderWidth: 3,

            tension: 0.4

        }]
    },

    options: {

        responsive: true,

        maintainAspectRatio: false,

        plugins: {

            legend: {
                display: true
            }

        },

        scales: {

            y: {

                beginAtZero: true,

                ticks: {
                    precision: 0
                }

            }

        }

    }

});

</script>

</body>
</html>