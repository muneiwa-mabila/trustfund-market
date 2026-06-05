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
/* TOTAL PRODUCTS */
$totalProductsQuery = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM seller_items
    WHERE seller_id = '$seller_id'
");

$totalProducts = mysqli_fetch_assoc($totalProductsQuery)['total'] ?? 0;

/* ACTIVE PRODUCTS */
$activeProductsQuery = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM seller_items
    WHERE seller_id = '$seller_id'
    AND product_status = 'Approved'
");

$activeProducts = mysqli_fetch_assoc($activeProductsQuery)['total'] ?? 0;

/* TOTAL SALES */
$totalSalesQuery = mysqli_query($conn, "
    SELECT SUM(sales_count) AS total_sales
    FROM seller_items
    WHERE seller_id = '$seller_id'
");

$totalSales = mysqli_fetch_assoc($totalSalesQuery)['total_sales'] ?? 0;

/* TOTAL REVENUE */
$revenueQuery = mysqli_query($conn, "
    SELECT SUM(product_price * sales_count) AS revenue
    FROM seller_items
    WHERE seller_id = '$seller_id'
");

$totalRevenue = mysqli_fetch_assoc($revenueQuery)['revenue'] ?? 0;

/* TOP PRODUCT */
$topProductQuery = mysqli_query($conn, "
    SELECT product_name, sales_count
    FROM seller_items
    WHERE seller_id = '$seller_id'
    ORDER BY sales_count DESC
    LIMIT 1
");

$topProduct = mysqli_fetch_assoc($topProductQuery);

/* MONTHLY DATA */
$chartQuery = mysqli_query($conn, "
    SELECT 
        MONTHNAME(created_at) AS month_name,
        SUM(sales_count) AS monthly_sales
    FROM seller_items
    WHERE seller_id = '$seller_id'
    GROUP BY MONTH(created_at), MONTHNAME(created_at)
    ORDER BY MONTH(created_at)
");

$months = [];
$salesData = [];

while ($row = mysqli_fetch_assoc($chartQuery)) {
    $months[] = $row['month_name'];
    $salesData[] = $row['monthly_sales'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Analytics | TrustFund</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
      display:block;
      text-decoration:none;
      color:white;
      padding:12px 14px;
      border-radius:10px;
      font-size:14px;
      margin-bottom:6px;
      transition:0.2s;
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
      padding-left:28px;
      font-size:13px;
    }

    .signout{
      font-size:13px;
      color:white;
      text-decoration:none;
      padding:12px 14px;
    }

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

    .stats-grid{
      display:grid;
      grid-template-columns:repeat(4, 1fr);
      gap:18px;
      margin-bottom:28px;
    }

    .stat-card{
      background:white;
      border:1px solid #ededed;
      border-radius:20px;
      padding:24px;
      box-shadow:0 8px 25px rgba(0,0,0,0.03);
    }

    .stat-card h3{
      font-size:30px;
      margin-bottom:8px;
    }

    .stat-card p{
      color:#777;
      font-size:13px;
    }

    .chart-card{
      background:white;
      border:1px solid #ededed;
      border-radius:20px;
      padding:28px;
      box-shadow:0 8px 25px rgba(0,0,0,0.03);
    }

    .chart-header{
      margin-bottom:25px;
    }

    .chart-header h2{
      font-size:22px;
      margin-bottom:6px;
    }

    .chart-header p{
      color:#777;
      font-size:13px;
    }

    .top-product{
      margin-top:25px;
      padding:18px;
      border-radius:16px;
      background:#faf5fd;
      border:1px solid #ead8f5;
    }

    .top-product h4{
      margin-bottom:6px;
    }

    .top-product p{
      color:#666;
      font-size:14px;
    }

    @media(max-width:1100px){

      .stats-grid{
        grid-template-columns:repeat(2, 1fr);
      }

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

      .stats-grid{
        grid-template-columns:1fr;
      }

    }

  </style>
</head>

<body>

<div class="dashboard-wrapper">

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

<a href="analytics.php" class="active">

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

  <main class="main-content">

    <div class="page-header">
      <h1>Analytics</h1>
      <p>Track your product performance and marketplace growth</p>
    </div>

    <div class="stats-grid">

      <div class="stat-card">
        <h3><?php echo number_format($totalProducts); ?></h3>
        <p>Total Products</p>
      </div>

      <div class="stat-card">
        <h3><?php echo number_format($activeProducts); ?></h3>
        <p>Active Products</p>
      </div>

      <div class="stat-card">
        <h3><?php echo number_format($totalSales); ?></h3>
        <p>Total Sales</p>
      </div>

      <div class="stat-card">
        <h3>R<?php echo number_format($totalRevenue, 2); ?></h3>
        <p>Total Revenue</p>
      </div>

    </div>

    <div class="chart-card">

      <div class="chart-header">
        <h2>Monthly Sales Performance</h2>
        <p>Sales generated from your released products</p>
      </div>

      <canvas id="analyticsChart"></canvas>

      <?php if ($topProduct): ?>

        <div class="top-product">

          <h4>Top Performing Product</h4>

          <p>
            <?php echo htmlspecialchars($topProduct['product_name']); ?>
            •
            <?php echo number_format($topProduct['sales_count']); ?> sales
          </p>

        </div>

      <?php endif; ?>

    </div>

  </main>

</div>

<script>

const ctx = document.getElementById('analyticsChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Sales',
            data: <?php echo json_encode($salesData); ?>,
            borderWidth: 1,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
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