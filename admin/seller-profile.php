<?php

include 'db.php';

$seller_id = $_GET['id'] ?? 0;

/* SELLER */

$sellerQuery = mysqli_query(

    $conn,

    "SELECT *

    FROM users

    WHERE user_id='$seller_id'"

);

$seller = mysqli_fetch_assoc($sellerQuery);

if(!$seller){

    die("Seller not found.");

}

/* SELLER PRODUCTS */

$products = mysqli_query(

    $conn,

    "SELECT *

    FROM seller_items

    WHERE seller_id='$seller_id'

    AND product_status='Approved'

    ORDER BY created_at DESC"

);

/* TOTAL PRODUCTS */

$totalProducts = mysqli_num_rows($products);

/* RESET POINTER */

mysqli_data_seek($products, 0);

/* SELLER RATINGS */

$ratingsQuery = mysqli_query(

    $conn,

    "SELECT 

    AVG(reviews.rating) AS avg_rating,

    COUNT(reviews.id) AS total_reviews

    FROM reviews

    INNER JOIN seller_items

    ON reviews.product_id = seller_items.id

    WHERE seller_items.seller_id='$seller_id'"

);

$ratings = mysqli_fetch_assoc($ratingsQuery);

$avgRating = number_format($ratings['avg_rating'] ?? 0, 1);

$totalReviews = $ratings['total_reviews'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>

<?php echo htmlspecialchars($seller['name']); ?>

| TrustFund Seller

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#f5f5f5;
    font-family:Arial,sans-serif;
    color:#111;
}

/* HEADER */

.top-header{
    background:white;
    border-bottom:1px solid #eee;
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
    font-size:30px;
    font-weight:800;
    color:#9b59b6;
}

.header-nav{
    display:flex;
    gap:25px;
}

.header-nav a{
    text-decoration:none;
    color:#111;
    font-size:14px;
    font-weight:600;
}

/* PAGE */

.page-wrapper{
    max-width:1200px;
    margin:auto;
    padding:50px 20px;
}

/* SELLER CARD */

.seller-card{
    background:white;
    border-radius:24px;
    padding:40px;
    margin-bottom:40px;

    box-shadow:0 10px 30px rgba(0,0,0,0.05);

    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:20px;
}

.seller-name{
    font-size:42px;
    font-weight:800;
    margin-bottom:10px;
}

.seller-meta{
    color:#666;
    font-size:15px;
    margin-bottom:8px;
}

.message-btn{
    background:#9b59b6;
    color:white;
    text-decoration:none;
    padding:15px 24px;
    border-radius:14px;
    font-weight:700;
    transition:0.2s;
}

.message-btn:hover{
    background:#8747a1;
}

/* SECTION TITLE */

.section-title{
    font-size:28px;
    font-weight:800;
    margin-bottom:28px;
}

/* GRID */

.products-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:24px;
}

/* CARD */

.product-card{
    background:white;
    border-radius:20px;
    overflow:hidden;
    text-decoration:none;
    color:#111;

    box-shadow:0 8px 24px rgba(0,0,0,0.05);

    transition:0.2s;
}

.product-card:hover{
    transform:translateY(-4px);
}

/* IMAGE */

.product-image{
    width:100%;
    height:260px;
    overflow:hidden;
    background:#eee;
}

.product-image img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* INFO */

.product-info{
    padding:18px;
}

.product-name{
    font-size:16px;
    font-weight:700;
    margin-bottom:10px;
}

.product-price{
    color:#9b59b6;
    font-size:18px;
    font-weight:800;
}

/* EMPTY */

.empty-products{
    background:white;
    padding:60px;
    border-radius:24px;
    text-align:center;
    color:#777;
    font-size:18px;
}

/* RESPONSIVE */

@media(max-width:1000px){

    .products-grid{
        grid-template-columns:repeat(2,1fr);
    }

}

@media(max-width:650px){

    .products-grid{
        grid-template-columns:1fr;
    }

    .seller-card{
        flex-direction:column;
        align-items:flex-start;
    }

    .seller-name{
        font-size:32px;
    }

}

</style>

</head>

<body>

<!-- HEADER -->

<header class="top-header">

<div class="header-container">

<div class="logo">
TrustFund
</div>

<nav class="header-nav">

<a href="index.php">Home</a>

<a href="top-deals.php">Top Deals</a>

<a href="cart.php">Cart</a>

</nav>

</div>

</header>

<!-- PAGE -->

<div class="page-wrapper">

<!-- SELLER -->

<div class="seller-card">

<div>

<div class="seller-name">

<?php echo htmlspecialchars($seller['name']); ?>

</div>

<div class="seller-meta">

⭐ <?php echo $avgRating; ?>

(<?php echo $totalReviews; ?> reviews)

</div>

<div class="seller-meta">

<?php echo $totalProducts; ?> active listings

</div>

</div>

<a 
href="chat.php?seller=<?php echo $seller['user_id']; ?>"
class="message-btn"
>

Message Seller

</a>

</div>

<!-- PRODUCTS -->

<h2 class="section-title">

Seller Listings

</h2>

<?php if(mysqli_num_rows($products) > 0): ?>

<div class="products-grid">

<?php while($item = mysqli_fetch_assoc($products)): ?>

<a 
href="product.php?id=<?php echo $item['id']; ?>"
class="product-card"
>

<div class="product-image">

<img 
src="<?php echo htmlspecialchars($item['product_image']); ?>"
>

</div>

<div class="product-info">

<div class="product-name">

<?php echo htmlspecialchars($item['product_name']); ?>

</div>

<div class="product-price">

<?php echo htmlspecialchars($item['product_price']); ?>

</div>

</div>

</a>

<?php endwhile; ?>

</div>

<?php else: ?>

<div class="empty-products">

This seller has no active listings yet.

</div>

<?php endif; ?>

</div>

</body>
</html>