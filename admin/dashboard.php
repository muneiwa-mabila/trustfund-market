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

$applications = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM applications")
);

$users = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM users")
);

$listings = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM seller_items")
);

$approvedSellers = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM users WHERE seller_status='approved'")
);

$isSuperAdmin = $_SESSION['role'] === 'super_admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#f4f4f4;
    font-family:Arial,sans-serif;
}

.admin-sidebar{
    width:270px;
    min-height:100vh;
    background:linear-gradient(180deg,#9b59b6 0%,#7d3fa0 100%);
    position:fixed;
    left:0;
    top:0;
    padding:28px 20px;
    color:white;
    border-top-right-radius:30px;
    border-bottom-right-radius:30px;
    box-shadow:0 10px 35px rgba(155,89,182,0.25);
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}

.logo{
    font-size:32px;
    font-weight:900;
    color:white;
    margin-bottom:40px;
    padding-left:10px;
    letter-spacing:0.5px;
}

.sidebar-menu{
    display:flex;
    flex-direction:column;
    gap:10px;
}

.admin-sidebar a{
    display:flex;
    align-items:center;
    gap:14px;
    color:white;
    text-decoration:none;
    padding:15px 18px;
    border-radius:18px;
    transition:0.25s;
    font-size:15px;
    font-weight:600;
}

.admin-sidebar a i{
    width:22px;
    text-align:center;
}

.admin-sidebar a:hover{
    background:rgba(255,255,255,0.16);
    transform:translateX(4px);
}

.admin-sidebar a.active{
    background:white;
    color:#9b59b6;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

.sidebar-bottom{
    margin-top:40px;
}

/* ROLE BADGE in sidebar */
.role-badge{
    display:inline-block;
    margin-left:10px;
    padding:2px 8px;
    border-radius:20px;
    font-size:11px;
    font-weight:700;
    background:rgba(255,255,255,0.2);
    color:white;
    vertical-align:middle;
}

.admin-main{
    margin-left:290px;
    padding:35px;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:35px;
}

.dashboard-title{
    font-size:38px;
    font-weight:800;
    color:#111;
}

.dashboard-subtitle{
    color:#777;
    margin-top:5px;
}

.admin-profile{
    background:white;
    padding:12px 18px;
    border-radius:14px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
    font-weight:700;
}

.admin-profile .role-tag{
    font-size:12px;
    color:#9b59b6;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:0.5px;
    display:block;
    margin-top:2px;
}

.stats-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:22px;
    margin-bottom:30px;
}

.stat-card{
    background:white;
    border-radius:22px;
    padding:28px;
    box-shadow:0 8px 24px rgba(0,0,0,0.05);
    transition:0.2s;
    border:1px solid #eee;
    position:relative;
    overflow:hidden;
}

.stat-card:hover{
    transform:translateY(-5px);
}

.card-icon{
    width:55px;
    height:55px;
    border-radius:14px;
    background:#f3e8fb;
    color:#9b59b6;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
    margin-bottom:18px;
}

.stat-card h2{
    font-size:34px;
    font-weight:800;
    margin-bottom:8px;
    color:#111;
}

.stat-card p{
    color:#777;
    margin:0;
}

.panel{
    background:white;
    border-radius:22px;
    padding:28px;
    box-shadow:0 8px 24px rgba(0,0,0,0.05);
    margin-bottom:24px;
}

.panel-title{
    font-size:20px;
    font-weight:800;
    margin-bottom:22px;
    color:#111;
}

.quick-actions{
    display:flex;
    flex-wrap:wrap;
    gap:14px;
}

.quick-btn{
    background:#9b59b6;
    color:white;
    text-decoration:none;
    padding:14px 18px;
    border-radius:14px;
    font-size:14px;
    font-weight:700;
    transition:0.2s;
}

.quick-btn:hover{
    background:#8a47ab;
}

.quick-btn.super-only{
    background:#2c3e50;
}

.quick-btn.super-only:hover{
    background:#1a252f;
}

@media(max-width:1100px){
    .stats-grid{ grid-template-columns:repeat(2,1fr); }
}

@media(max-width:700px){
    .admin-sidebar{ position:relative; width:100%; min-height:auto; border-radius:0; }
    .admin-main{ margin-left:0; }
    .stats-grid{ grid-template-columns:1fr; }
    .topbar{ flex-direction:column; align-items:flex-start; gap:18px; }
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="admin-sidebar">

    <div>

        <div class="logo">TrustFund</div>

        <div class="sidebar-menu">

            <a href="dashboard.php" class="active">
                <i class="fa-solid fa-chart-line"></i>
                Dashboard
            </a>

            <a href="applications.php">
                <i class="fa-solid fa-file-circle-check"></i>
                Seller Applications
            </a>

            <a href="listings.php">
                <i class="fa-solid fa-box"></i>
                Listings
            </a>

            <a href="Messages-admin.php">
                <i class="fa-solid fa-comments"></i>
                Messages
            </a>

            <?php if($isSuperAdmin): ?>
            <a href="users.php">
                <i class="fa-solid fa-users"></i>
                Users
            </a>

            <a href="manage_admins.php">
                <i class="fa-solid fa-user-shield"></i>
                Admin Panel
            </a>
            <?php endif; ?>

        </div>

    </div>

    <div class="sidebar-bottom">
        <a href="../index.php">
            <i class="fa-solid fa-globe"></i>
            View Website
        </a>
    </div>

</div>

<!-- MAIN -->
<div class="admin-main">

    <!-- TOPBAR -->
    <div class="topbar">

        <div>
            <div class="dashboard-title">Admin Dashboard</div>
            <div class="dashboard-subtitle">Monitor TrustFund marketplace activity</div>
        </div>

        <div class="admin-profile">
            Admin Panel
            <span class="role-tag">
                <?php echo $isSuperAdmin ? 'Super Admin' : 'Mini Admin'; ?>
            </span>
        </div>

    </div>

    <!-- STATS -->
    <div class="stats-grid">

        <div class="stat-card">
            <div class="card-icon"><i class="fa-solid fa-file-circle-check"></i></div>
            <h2><?php echo $applications; ?></h2>
            <p>Seller Applications</p>
        </div>

        <div class="stat-card">
            <div class="card-icon"><i class="fa-solid fa-users"></i></div>
            <h2><?php echo $users; ?></h2>
            <p>Registered Users</p>
        </div>

        <div class="stat-card">
            <div class="card-icon"><i class="fa-solid fa-box"></i></div>
            <h2><?php echo $listings; ?></h2>
            <p>Marketplace Listings</p>
        </div>

        <div class="stat-card">
            <div class="card-icon"><i class="fa-solid fa-store"></i></div>
            <h2><?php echo $approvedSellers; ?></h2>
            <p>Approved Sellers</p>
        </div>

    </div>

    <!-- QUICK ACTIONS -->
    <div class="panel">

        <div class="panel-title">Quick Actions</div>

        <div class="quick-actions">

            <a href="applications.php" class="quick-btn">Review Applications</a>
            <a href="listings.php" class="quick-btn">View Listings</a>
            <a href="../index.php" class="quick-btn">Open Website</a>

            <?php if($isSuperAdmin): ?>
            <a href="users.php" class="quick-btn super-only">
                <i class="fa-solid fa-users"></i> Manage Users
            </a>
            <a href="manage_admins.php" class="quick-btn super-only">
                <i class="fa-solid fa-user-shield"></i> Admin Panel
            </a>
            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>