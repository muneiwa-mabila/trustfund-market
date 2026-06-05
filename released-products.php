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

if(isset($_GET['delete'])){

    $ids = explode(',', $_GET['delete']);

    foreach($ids as $id){

        mysqli_query($conn, "
            DELETE FROM seller_items
            WHERE id='$id'
            AND seller_id='$seller_id'
        ");
    }

    header("Location: released-products.php");
    exit();
}

if(isset($_GET['unpublish'])){

    $ids = explode(',', $_GET['unpublish']);

    foreach($ids as $id){

        mysqli_query($conn, "
            UPDATE seller_items
            SET product_status='Pending'
            WHERE id='$id'
            AND seller_id='$seller_id'
        ");
    }

    header("Location: released-products.php");
    exit();
}

$query = mysqli_query($conn, "
    SELECT *
    FROM seller_items
    WHERE seller_id = '$seller_id'
    AND product_status = 'Approved'
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Released Products | TrustFund</title>

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
    padding:12px 14px;
    border-radius:10px;
    font-size:13px;
    transition:0.2s;
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
    padding:10px 32px;
}

h1{
    font-size:34px;
    margin-bottom:20px;
}

.top-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.search-box{
    width:260px;
    padding:12px 14px;
    border:1px solid #ddd;
    border-radius:12px;
    font-size:13px;
}

/* PRODUCTS */

.products-card{
    border:1px solid #eee;
    border-radius:20px;
    padding:20px;
    background:white;
    overflow:visible;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    text-align:left;
    color:#888;
    font-size:12px;
    font-weight:600;
    padding:14px 10px;
    border-bottom:1px solid #eee;
}

td{
    padding:18px 10px;
    border-bottom:1px solid #f0f0f0;
    font-size:13px;
    vertical-align:middle;
}

.product-info{
    display:flex;
    align-items:center;
    gap:12px;
}

.product-img{
    width:58px;
    height:58px;
    border-radius:10px;
    object-fit:cover;
    background:#ddd;
}

.product-name{
    font-weight:700;
    margin-bottom:4px;
}

.category{
    color:#888;
    font-size:12px;
}

.price{
    background:#eef5ff;
    color:#1d4ed8;
    padding:5px 8px;
    border-radius:6px;
    font-weight:700;
    font-size:12px;
}

.status{
    background:#dcfce7;
    color:#16a34a;
    padding:5px 9px;
    border-radius:20px;
    font-size:12px;
    font-weight:700;
}

.rating{
    color:#f59e0b;
    font-weight:700;
}

.sales{
    font-weight:700;
}

/* ACTION MENU */

.sales-cell{
    position:relative;
    overflow:visible;
}

.row-actions{
    position:absolute;
    top:50%;
    right:0;
    transform:translateY(-50%);
    display:none;
    z-index:1000;
}

tr:hover .row-actions{
    display:block;
}

.action-trigger{
    width:34px;
    height:34px;
    border:none;
    border-radius:10px;
    background:white;
    cursor:pointer;
    font-size:18px;
    box-shadow:0 4px 14px rgba(0,0,0,0.08);
}

.action-menu{
    position:absolute;
    bottom:45px;
    right:0;
    width:220px;
    background:white;
    border:1px solid #ececec;
    border-radius:18px;
    padding:10px;
    box-shadow:0 10px 30px rgba(0,0,0,0.12);
    display:none;
    z-index:99999;
}

.action-menu a{
    display:flex;
    align-items:center;
    gap:10px;
    text-decoration:none;
    color:#333;
    padding:12px;
    border-radius:10px;
    font-size:13px;
    transition:0.2s;
}

.action-menu a:hover{
    background:#f5f5f5;
}

.delete-action{
    color:#dc2626 !important;
}

.show-menu{
    display:block;
}

/* FOOTER */

.table-footer{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:24px;
    padding-top:18px;
}

.selected-text{
    color:#666;
    font-size:13px;
}

.footer-actions{
    display:flex;
    align-items:center;
    gap:12px;
}

.nav-circle{
    width:38px;
    height:38px;
    border:none;
    border-radius:50%;
    background:#f1f1f1;
    cursor:pointer;
}

.delete-btn-footer{
    border:1px solid #ddd;
    background:white;
    padding:11px 18px;
    border-radius:12px;
    cursor:pointer;
    font-weight:600;
}

.unpublish-btn{
    background:#222;
    color:white;
    border:none;
    padding:11px 18px;
    border-radius:12px;
    cursor:pointer;
    font-weight:700;
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

<a href="released-products.php" class="active">

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

<h1>Released</h1>

<div class="top-row">

<h4>Products</h4>

<input 
type="text" 
class="search-box" 
id="searchInput"
placeholder="Search product"
onkeyup="searchProducts()"
>

</div>

<section class="products-card">

<table id="productsTable">

<thead>

<tr>
<th></th>
<th>Product</th>
<th>Price</th>
<th>Rating</th>
<th>Status</th>
<th>Sales</th>
</tr>

</thead>

<tbody>

<?php while ($item = mysqli_fetch_assoc($query)): ?>

<?php

$sales = $item['sales_count'] ?? 0;

/* REAL RATINGS */

$reviewQuery = mysqli_query(

    $conn,

    "SELECT 

    AVG(rating) AS avg_rating,

    COUNT(*) AS total_reviews

    FROM reviews

    WHERE product_id='".$item['id']."'"

);

$reviewData = mysqli_fetch_assoc($reviewQuery);

$rating = number_format($reviewData['avg_rating'] ?? 0, 1);

$totalReviews = $reviewData['total_reviews'] ?? 0;
?>

<tr>

<td>
<input 
type="checkbox"
class="product-checkbox"
value="<?php echo $item['id']; ?>"
>
</td>

<td>

<div class="product-info">

<?php if (!empty($item['product_image'])): ?>

<img 
src="<?php echo htmlspecialchars($item['product_image']); ?>"
class="product-img"
>

<?php else: ?>

<div class="product-img"></div>

<?php endif; ?>

<div>

<div class="product-name">
<?php echo htmlspecialchars($item['product_name']); ?>
</div>

<div class="category">
<?php echo htmlspecialchars($item['product_category']); ?>
</div>

</div>

</div>

</td>

<td>
<span class="price">
<?php

$price = $item['product_price'] ?? '';

if(is_numeric($price)){

    echo "R" . number_format((float)$price, 2);

}else{

    echo htmlspecialchars($price);

}

?>
</span>
</td>

<td>

<div class="rating">

★ <?php echo $rating; ?>

</div>

<div style="font-size:11px; color:#888; margin-top:4px;">

<?php echo $totalReviews; ?> reviews

</div>

</td>

<td>
<span class="status">Active</span>
</td>

<td class="sales-cell">

<span class="sales">
<?php echo number_format($sales); ?>
</span>

<div class="row-actions">

<button 
class="action-trigger"
onclick="toggleMenu(event, 'menu-<?php echo $item['id']; ?>')"
>
⋯
</button>

<div 
class="action-menu"
id="menu-<?php echo $item['id']; ?>"
>

<a href="edit-product.php?id=<?php echo $item['id']; ?>">

🖼 Edit picture

</a>

<a href="#">

✏️ Edit title & description

</a>

<a 
href="released-products.php?delete=<?php echo $item['id']; ?>"
class="delete-action"
onclick="return confirm('Delete this product?')"
>

🗑 Delete forever

</a>

</div>

</div>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

<!-- FOOTER -->

<div class="table-footer">

<div class="selected-text">
✓ <span id="selectedCount">0</span> products selected
</div>

<div class="footer-actions">

<button class="nav-circle">←</button>

<button class="nav-circle">→</button>

<button 
class="delete-btn-footer"
onclick="deleteSelectedProducts()"
>
Delete 🗑
</button>

<button 
class="unpublish-btn"
onclick="unpublishSelectedProducts()"
>
Unpublish
</button>

</div>

</div>

</section>

</main>

</div>

<script>

function searchProducts() {

    let input = document.getElementById("searchInput").value.toLowerCase();

    let rows = document.querySelectorAll("#productsTable tbody tr");

    rows.forEach(row => {

        let text = row.innerText.toLowerCase();

        row.style.display = text.includes(input) ? "" : "none";

    });
}

/* SELECTED COUNT */

const checkboxes = document.querySelectorAll('.product-checkbox');

const selectedCount = document.getElementById('selectedCount');

function updateSelectedCount() {

    let checked = document.querySelectorAll('.product-checkbox:checked');

    selectedCount.innerText = checked.length;
}

checkboxes.forEach(box => {

    box.addEventListener('change', updateSelectedCount);

});

/* GET SELECTED PRODUCTS */

function getSelectedProducts() {

    let selected = [];

    document.querySelectorAll('.product-checkbox:checked')
    .forEach(box => {

        selected.push(box.value);

    });

    return selected;
}

/* DELETE PRODUCTS */

function deleteSelectedProducts() {

    let selected = getSelectedProducts();

    if(selected.length === 0){

        alert("Select products first.");
        return;
    }

    if(confirm("Delete selected products?")){

        window.location.href =
        "released-products.php?delete=" + selected.join(',');

    }
}

/* UNPUBLISH PRODUCTS */

function unpublishSelectedProducts() {

    let selected = getSelectedProducts();

    if(selected.length === 0){

        alert("Select products first.");
        return;
    }

    if(confirm("Move selected products to pending?")){

        window.location.href =
        "released-products.php?unpublish=" + selected.join(',');

    }
}

/* ACTION MENU */

function toggleMenu(event, id){

    event.stopPropagation();

    document.querySelectorAll('.action-menu')
    .forEach(menu => {

        if(menu.id !== id){
            menu.classList.remove('show-menu');
        }

    });

    document.getElementById(id)
    .classList.toggle('show-menu');
}

document.addEventListener('click', function(){

    document.querySelectorAll('.action-menu')
    .forEach(menu => {
        menu.classList.remove('show-menu');
    });

});

</script>

</body>
</html>