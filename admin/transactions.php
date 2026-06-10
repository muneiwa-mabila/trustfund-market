<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

if(!in_array($_SESSION['role'], ['super_admin', 'mini_admin'])){
    header("Location: ../index.php");
    exit();
}

include '../db.php';

$isSuperAdmin = $_SESSION['role'] === 'super_admin';

// Filters
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$paymentFilter = isset($_GET['payment']) ? $_GET['payment'] : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$where = [];
if($statusFilter) $where[] = "o.status = '" . mysqli_real_escape_string($conn, $statusFilter) . "'";
if($paymentFilter) $where[] = "o.payment_status = '" . mysqli_real_escape_string($conn, $paymentFilter) . "'";
if($search) $where[] = "(buyer.name LIKE '%$search%' OR seller.name LIKE '%$search%' OR p.product_name LIKE '%$search%')";
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$ordersResult = mysqli_query($conn, "
    SELECT 
        o.id,
        o.created_at,
        o.status,
        o.payment_status,
        o.quantity,
        o.delivery_fee,
        o.trustfund_fee,
        o.buyer_pickup_point,
        buyer.name AS buyer_name,
        buyer.email AS buyer_email,
        seller.name AS seller_name,
        seller.email AS seller_email,
        p.product_name,
        p.product_price
    FROM orders o
    LEFT JOIN users buyer ON o.buyer_id = buyer.user_id
    LEFT JOIN users seller ON o.seller_id = seller.user_id
    LEFT JOIN products p ON o.product_id = p.id
    $whereSQL
    ORDER BY o.created_at DESC
");

// Summary stats
$totalOrders = mysqli_num_rows($ordersResult);
$completedOrders = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM orders WHERE status='Completed'"));
$pendingOrders   = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM orders WHERE payment_status='Held'"));
$releasedOrders  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM orders WHERE payment_status='Released'"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transactions | TrustFund Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{ margin:0; padding:0; box-sizing:border-box; }
body{ background:#f4f4f4; font-family:Arial,sans-serif; }

.admin-sidebar{
    width:270px; min-height:100vh;
    background:linear-gradient(180deg,#9b59b6 0%,#7d3fa0 100%);
    position:fixed; left:0; top:0; padding:28px 20px; color:white;
    border-top-right-radius:30px; border-bottom-right-radius:30px;
    box-shadow:0 10px 35px rgba(155,89,182,0.25);
    display:flex; flex-direction:column; justify-content:space-between;
}
.logo{ font-size:32px; font-weight:900; color:white; margin-bottom:40px; padding-left:10px; }
.sidebar-menu{ display:flex; flex-direction:column; gap:10px; }
.admin-sidebar a{
    display:flex; align-items:center; gap:14px; color:white; text-decoration:none;
    padding:15px 18px; border-radius:18px; transition:0.25s; font-size:15px; font-weight:600;
}
.admin-sidebar a i{ width:22px; text-align:center; }
.admin-sidebar a:hover{ background:rgba(255,255,255,0.16); transform:translateX(4px); }
.admin-sidebar a.active{ background:white; color:#9b59b6; box-shadow:0 10px 25px rgba(0,0,0,0.08); }
.sidebar-bottom{ margin-top:40px; }

.admin-main{ margin-left:290px; padding:35px; }
.topbar{ display:flex; justify-content:space-between; align-items:center; margin-bottom:35px; }
.page-title{ font-size:38px; font-weight:800; color:#111; }
.page-subtitle{ color:#777; margin-top:5px; }

.stats-grid{ display:grid; grid-template-columns:repeat(4,1fr); gap:22px; margin-bottom:30px; }
.stat-card{
    background:white; border-radius:22px; padding:24px;
    box-shadow:0 8px 24px rgba(0,0,0,0.05); border:1px solid #eee;
    transition:0.2s;
}
.stat-card:hover{ transform:translateY(-4px); }
.card-icon{
    width:50px; height:50px; border-radius:14px; background:#f3e8fb;
    color:#9b59b6; display:flex; align-items:center; justify-content:center;
    font-size:20px; margin-bottom:14px;
}
.stat-card h2{ font-size:30px; font-weight:800; margin-bottom:6px; color:#111; }
.stat-card p{ color:#777; margin:0; font-size:14px; }

.panel{ background:white; border-radius:22px; padding:28px; box-shadow:0 8px 24px rgba(0,0,0,0.05); }
.panel-title{ font-size:20px; font-weight:800; margin-bottom:20px; color:#111; }

.filters{ display:flex; gap:12px; flex-wrap:wrap; margin-bottom:22px; }
.filters select, .filters input{
    padding:10px 16px; border-radius:12px; border:1px solid #ddd;
    font-size:14px; background:white; color:#333;
}
.filters input{ min-width:220px; }
.filter-btn{
    background:#9b59b6; color:white; border:none; padding:10px 20px;
    border-radius:12px; font-weight:700; font-size:14px; cursor:pointer;
}
.filter-btn:hover{ background:#8a47ab; }
.reset-btn{
    background:#eee; color:#555; border:none; padding:10px 16px;
    border-radius:12px; font-weight:700; font-size:14px; cursor:pointer;
    text-decoration:none;
}

table{ width:100%; border-collapse:collapse; font-size:14px; }
thead th{ background:#f8f2fd; color:#9b59b6; padding:14px 12px; text-align:left; font-weight:700; }
tbody tr{ border-bottom:1px solid #f0f0f0; transition:0.15s; }
tbody tr:hover{ background:#fdf7ff; }
tbody td{ padding:13px 12px; color:#333; vertical-align:middle; }

.badge-status{
    padding:5px 12px; border-radius:20px; font-size:12px; font-weight:700; white-space:nowrap;
}
.badge-held{ background:#fff3cd; color:#856404; }
.badge-released{ background:#d1e7dd; color:#0a5c36; }
.badge-refunded{ background:#f8d7da; color:#842029; }

.badge-order{
    padding:5px 12px; border-radius:20px; font-size:12px; font-weight:700; white-space:nowrap;
}
.badge-awaiting{ background:#cce5ff; color:#004085; }
.badge-completed{ background:#d4edda; color:#155724; }
.badge-cancelled{ background:#f8d7da; color:#721c24; }
.badge-other{ background:#e2e3e5; color:#383d41; }

.no-data{ text-align:center; padding:50px; color:#999; font-size:16px; }

@media(max-width:1100px){ .stats-grid{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:700px){
    .admin-sidebar{ position:relative; width:100%; min-height:auto; border-radius:0; }
    .admin-main{ margin-left:0; }
    .stats-grid{ grid-template-columns:1fr; }
    .topbar{ flex-direction:column; align-items:flex-start; gap:12px; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="admin-sidebar">
    <div>
        <div class="logo">TrustFund</div>
        <div class="sidebar-menu">
            <a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="applications.php"><i class="fa-solid fa-file-circle-check"></i> Seller Applications</a>
            <a href="listings.php"><i class="fa-solid fa-box"></i> Listings</a>
            <a href="Messages-admin.php"><i class="fa-solid fa-comments"></i> Messages</a>
            <a href="transactions.php" class="active"><i class="fa-solid fa-receipt"></i> Transactions</a>
            <?php if($isSuperAdmin): ?>
            <a href="users.php"><i class="fa-solid fa-users"></i> Users</a>
            <a href="manage_admins.php"><i class="fa-solid fa-user-shield"></i> Admin Panel</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="sidebar-bottom">
        <a href="../index.php"><i class="fa-solid fa-globe"></i> View Website</a>
    </div>
</div>

<!-- MAIN -->
<div class="admin-main">

    <div class="topbar">
        <div>
            <div class="page-title">Transactions</div>
            <div class="page-subtitle">All marketplace orders and payment activity</div>
        </div>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="card-icon"><i class="fa-solid fa-receipt"></i></div>
            <h2><?php echo $totalOrders; ?></h2>
            <p>Total Orders</p>
        </div>
        <div class="stat-card">
            <div class="card-icon"><i class="fa-solid fa-circle-check"></i></div>
            <h2><?php echo $completedOrders; ?></h2>
            <p>Completed</p>
        </div>
        <div class="stat-card">
            <div class="card-icon"><i class="fa-solid fa-clock"></i></div>
            <h2><?php echo $pendingOrders; ?></h2>
            <p>Payment Held</p>
        </div>
        <div class="stat-card">
            <div class="card-icon"><i class="fa-solid fa-money-bill-transfer"></i></div>
            <h2><?php echo $releasedOrders; ?></h2>
            <p>Payment Released</p>
        </div>
    </div>

    <!-- TABLE -->
    <div class="panel">
        <div class="panel-title">Order History</div>

        <!-- FILTERS -->
        <form method="GET" action="">
            <div class="filters">
                <input type="text" name="search" placeholder="Search buyer, seller or product..." value="<?php echo htmlspecialchars($search); ?>">

                <select name="status">
                    <option value="">All Statuses</option>
                    <option value="Awaiting Seller Dropoff" <?php if($statusFilter=='Awaiting Seller Dropoff') echo 'selected'; ?>>Awaiting Dropoff</option>
                    <option value="In Transit" <?php if($statusFilter=='In Transit') echo 'selected'; ?>>In Transit</option>
                    <option value="Delivered" <?php if($statusFilter=='Delivered') echo 'selected'; ?>>Delivered</option>
                    <option value="Completed" <?php if($statusFilter=='Completed') echo 'selected'; ?>>Completed</option>
                    <option value="Cancelled" <?php if($statusFilter=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>

                <select name="payment">
                    <option value="">All Payments</option>
                    <option value="Held" <?php if($paymentFilter=='Held') echo 'selected'; ?>>Held</option>
                    <option value="Released" <?php if($paymentFilter=='Released') echo 'selected'; ?>>Released</option>
                    <option value="Refunded" <?php if($paymentFilter=='Refunded') echo 'selected'; ?>>Refunded</option>
                </select>

                <button type="submit" class="filter-btn"><i class="fa-solid fa-filter"></i> Filter</button>
                <a href="transactions.php" class="reset-btn">Reset</a>
            </div>
        </form>

        <?php if(mysqli_num_rows($ordersResult) == 0): ?>
            <div class="no-data"><i class="fa-solid fa-inbox" style="font-size:40px;margin-bottom:14px;display:block;"></i>No transactions found.</div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Buyer</th>
                    <th>Seller</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Delivery Fee</th>
                    <th>TrustFund Fee</th>
                    <th>Pickup Point</th>
                    <th>Order Status</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($ordersResult)): ?>
                <?php
                // Order status badge
                $status = $row['status'];
                if(stripos($status,'Awaiting') !== false || stripos($status,'Transit') !== false || stripos($status,'Delivered') !== false){
                    $sBadge = 'badge-awaiting';
                } elseif(strtolower($status) == 'completed'){
                    $sBadge = 'badge-completed';
                } elseif(strtolower($status) == 'cancelled'){
                    $sBadge = 'badge-cancelled';
                } else {
                    $sBadge = 'badge-other';
                }

                // Payment badge
                $payment = $row['payment_status'];
                if($payment == 'Held') $pBadge = 'badge-held';
                elseif($payment == 'Released') $pBadge = 'badge-released';
                else $pBadge = 'badge-refunded';
                ?>
                <tr>
                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                    <td><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['buyer_name'] ?? 'N/A'); ?></strong><br>
                        <small style="color:#999;"><?php echo htmlspecialchars($row['buyer_email'] ?? ''); ?></small>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['seller_name'] ?? 'N/A'); ?></strong><br>
                        <small style="color:#999;"><?php echo htmlspecialchars($row['seller_email'] ?? ''); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($row['product_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['product_price'] ?? 'N/A'); ?></td>
                    <td>R<?php echo number_format($row['delivery_fee'], 2); ?></td>
                    <td>R<?php echo number_format($row['trustfund_fee'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['buyer_pickup_point'] ?? '—'); ?></td>
                    <td><span class="badge-status <?php echo $sBadge; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                    <td><span class="badge-status <?php echo $pBadge; ?>"><?php echo htmlspecialchars($payment); ?></span></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

</div>
</body>
</html>