<?php
session_start();
include '../db.php';

/* ADMIN PROTECTION */
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$user_id'");
if(mysqli_num_rows($query) == 0){ die("Admin account not found"); }
$user = mysqli_fetch_assoc($query);
if(trim(strtolower($user['role'])) != 'admin'){ die("You are not admin"); }

/* APPROVE PRODUCT */
if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    mysqli_query($conn, "UPDATE seller_items SET product_status='Approved' WHERE id='$id'");
    header("Location: listings.php");
    exit();
}

/* REJECT PRODUCT */
if(isset($_GET['reject'])){
    $id = $_GET['reject'];
    mysqli_query($conn, "UPDATE seller_items SET product_status='Rejected' WHERE id='$id'");
    header("Location: listings.php");
    exit();
}

/* GET PRODUCTS WITH SELLER NAME */
$listings = mysqli_query($conn,
    "SELECT seller_items.*, users.name AS seller_name
     FROM seller_items
     LEFT JOIN users ON seller_items.seller_id = users.user_id
     ORDER BY seller_items.id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Listings | TrustFund</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: #f7f7f7;
    font-family: Arial, sans-serif;
}
.wrapper {
    padding: 40px;
}
.page-title {
    font-size: 36px;
    font-weight: 800;
    margin-bottom: 25px;
    color: #111;
}
.card {
    background: white;
    border: none;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.05);
}
.table th {
    color: #9b59b6;
    font-weight: 700;
    white-space: nowrap;
}
.product-thumb {
    width: 64px;
    height: 64px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #eee;
    background: #f3f3f3;
}
.no-img {
    width: 64px;
    height: 64px;
    border-radius: 10px;
    background: #f0e8f8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #c9a0e0;
    border: 1px solid #e8d5f5;
}
.product-name {
    font-weight: 700;
    font-size: 14px;
    color: #111;
    margin-bottom: 3px;
}
.product-desc {
    font-size: 12px;
    color: #888;
    max-width: 220px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.status {
    padding: 5px 12px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 700;
    display: inline-block;
}
.pending  { background: #fff4cc; color: #8a6d00; }
.approved { background: #d4f8df; color: #0d7a32; }
.rejected { background: #ffd6d6; color: #b30000; }
.draft    { background: #e8e8e8; color: #555; }

.btn { border-radius: 10px; font-weight: 600; }

/* FILTER TABS */
.filter-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.filter-tab {
    padding: 7px 18px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    border: 2px solid #e0e0e0;
    background: white;
    color: #555;
    cursor: pointer;
    transition: 0.2s;
    text-decoration: none;
}
.filter-tab:hover, .filter-tab.active {
    background: #9b59b6;
    border-color: #9b59b6;
    color: white;
}
.seller-name {
    font-size: 13px;
    color: #555;
}
.seller-id {
    font-size: 11px;
    color: #bbb;
}
td { vertical-align: middle !important; }
</style>
</head>
<body>

<div class="wrapper">

    <h1 class="page-title">Marketplace Listings</h1>

    <!-- FILTER TABS -->
    <?php
    $filter = $_GET['filter'] ?? 'all';
    ?>
    <div class="filter-tabs">
        <a href="?filter=all"      class="filter-tab <?php echo $filter=='all'?'active':''; ?>">All</a>
        <a href="?filter=Pending"  class="filter-tab <?php echo $filter=='Pending'?'active':''; ?>">Pending</a>
        <a href="?filter=Approved" class="filter-tab <?php echo $filter=='Approved'?'active':''; ?>">Approved</a>
        <a href="?filter=Rejected" class="filter-tab <?php echo $filter=='Rejected'?'active':''; ?>">Rejected</a>
        <a href="?filter=Draft"    class="filter-tab <?php echo $filter=='Draft'?'active':''; ?>">Drafts</a>
    </div>

    <div class="card">
        <table class="table align-middle table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Seller</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            mysqli_data_seek($listings, 0);
            while($row = mysqli_fetch_assoc($listings)):
                if($filter != 'all' && $row['product_status'] != $filter) continue;
            ?>
            <tr>
                <td style="color:#bbb; font-size:13px;">#<?php echo $row['id']; ?></td>

                <!-- IMAGE -->
                <td>
                    <?php if(!empty($row['product_image'])): ?>
                        <img src="../<?php echo htmlspecialchars($row['product_image']); ?>"
                             class="product-thumb"
                             alt=""
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="no-img" style="display:none;">&#128247;</div>
                    <?php else: ?>
                        <div class="no-img">&#128247;</div>
                    <?php endif; ?>
                </td>

                <!-- PRODUCT NAME + DESCRIPTION -->
                <td>
                    <div class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></div>
                    <?php if(!empty($row['product_description'])): ?>
                    <div class="product-desc"><?php echo htmlspecialchars($row['product_description']); ?></div>
                    <?php endif; ?>
                </td>

                <!-- SELLER -->
                <td>
                    <div class="seller-name"><?php echo htmlspecialchars($row['seller_name'] ?? 'Unknown'); ?></div>
                    <div class="seller-id">ID: <?php echo $row['seller_id']; ?></div>
                </td>

                <td><?php echo htmlspecialchars($row['product_category']); ?></td>

                <td style="font-weight:700;">R<?php echo number_format($row['product_price'], 2); ?></td>

                <!-- STATUS -->
                <td>
                    <?php
                    $s = $row['product_status'];
                    $cls = strtolower($s);
                    echo "<span class='status $cls'>$s</span>";
                    ?>
                </td>

                <!-- ACTION -->
                <td>
                    <?php if($row['product_status'] == 'Pending'): ?>
                        <a href="?approve=<?php echo $row['id']; ?>&filter=<?php echo $filter; ?>"
                           class="btn btn-success btn-sm mb-1">Approve</a>
                        <a href="?reject=<?php echo $row['id']; ?>&filter=<?php echo $filter; ?>"
                           class="btn btn-danger btn-sm">Reject</a>
                    <?php elseif($row['product_status'] == 'Approved'): ?>
                        <a href="?reject=<?php echo $row['id']; ?>&filter=<?php echo $filter; ?>"
                           class="btn btn-outline-danger btn-sm">Revoke</a>
                    <?php elseif($row['product_status'] == 'Rejected'): ?>
                        <a href="?approve=<?php echo $row['id']; ?>&filter=<?php echo $filter; ?>"
                           class="btn btn-outline-success btn-sm">Re-approve</a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>