<?php
session_start();
include 'db.php';
$currentUser = null;
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    $userQuery = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$user_id'");
    $currentUser = mysqli_fetch_assoc($userQuery);
}
$cartCount = 0;
if(isset($_SESSION['cart'])){ foreach($_SESSION['cart'] as $item){ $cartCount += $item['quantity']; } }
$wishlistCount = 0;
if(isset($_SESSION['wishlist'])){ $wishlistCount = count($_SESSION['wishlist']); }
$username = $_SESSION['name'] ?? 'MyAccount';
$buyer_id = $_SESSION['user_id'] ?? null;

if(isset($_GET['wishlist']) && $buyer_id){
    if(!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
    $pid = (int)$_GET['wishlist'];
    if(in_array($pid, $_SESSION['wishlist'])){
        $_SESSION['wishlist'] = array_values(array_diff($_SESSION['wishlist'], [$pid]));
    } else {
        $_SESSION['wishlist'][] = $pid;
    }
    header("Location: events.php");
    exit();
}

$query = "
SELECT seller_items.*, users.name AS seller_name,
    ROUND(AVG(reviews.rating), 1) AS avg_rating,
    COUNT(reviews.id) AS review_count
FROM seller_items
INNER JOIN users ON seller_items.seller_id = users.user_id
LEFT JOIN reviews ON reviews.product_id = seller_items.id
WHERE seller_items.product_status='Approved' AND (seller_items.product_category='Events' OR seller_items.product_category='Events & Tickets')
GROUP BY seller_items.id
ORDER BY seller_items.id DESC
";
$result = mysqli_query($conn, $query);

function stars($n){
    $out = '';
    for($i=1;$i<=5;$i++){
        $out .= $i <= $n
            ? '<i class="fa-solid fa-star" style="color:#f39c12;font-size:11px;"></i>'
            : '<i class="fa-regular fa-star" style="color:#ddd;font-size:11px;"></i>';
    }
    return $out;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Events & Tickets | TrustFund</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{ margin:0; padding:0; box-sizing:border-box; }
body{ background:#f5f5f5; font-family:Arial,sans-serif; color:#111; }
.top-header{ background:#9b59b6; padding:18px 40px; }
.header-container{ max-width:1200px; margin:auto; display:flex; justify-content:space-between; align-items:center; }
.logo{ font-weight:800; font-size:22px; color:white; }
.header-nav{ display:flex; align-items:center; gap:30px; }
.header-nav a{ text-decoration:none; color:white; font-size:13px; font-weight:500; }
.page-wrapper{ max-width:1250px; margin:auto; padding:30px 20px; }
.breadcrumb-nav{ font-size:13px; margin-bottom:25px; }
.breadcrumb-nav a{ text-decoration:none; color:#777; }
.breadcrumb-nav span{ color:#a65cc5; font-weight:600; }
.title-row{ display:flex; justify-content:space-between; align-items:center; margin-bottom:35px; }
.title-row h1{ font-size:48px; font-weight:800; }
.deals-count{ color:#a65cc5; font-weight:700; }
.deals-grid{ display:grid; grid-template-columns:repeat(3,1fr); gap:22px; }
.deal-card{ background:white; border-radius:18px; overflow:hidden; text-decoration:none; color:#111; transition:0.2s; border:1px solid #eee; box-shadow:0 5px 15px rgba(0,0,0,0.03); position:relative; }
.deal-card:hover{ transform:translateY(-5px); box-shadow:0 10px 25px rgba(155,89,182,0.12); }
.deal-image{ height:220px; overflow:hidden; background:#f1f1f1; position:relative; }
.deal-image img{ width:100%; height:100%; object-fit:cover; }
.heart-btn{ position:absolute; top:10px; right:10px; width:34px; height:34px; background:white; border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none; font-size:15px; box-shadow:0 2px 8px rgba(0,0,0,0.12); transition:0.2s; z-index:10; }
.heart-btn:hover{ transform:scale(1.15); }
.heart-btn.active{ color:#e74c3c; }
.heart-btn.inactive{ color:#aaa; }
.deal-info{ padding:18px; }
.event-badge{ display:inline-block; background:#f0e6f6; color:#9b59b6; font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.5px; }
.deal-name{ font-size:15px; font-weight:700; margin-bottom:8px; height:42px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; }
.deal-desc{ font-size:13px; color:#666; line-height:1.5; margin-bottom:10px; }
.stars-row{ display:flex; align-items:center; gap:5px; margin-bottom:8px; }
.stars-row .review-count{ font-size:11px; color:#aaa; }
.deal-price{ font-size:18px; font-weight:800; color:#9b59b6; }
.seller-name{ margin-top:8px; font-size:13px; color:#777; }
.empty-box{ background:white; padding:70px 30px; text-align:center; border-radius:20px; grid-column:1/-1; }
.empty-box i{ font-size:50px; color:#a65cc5; margin-bottom:20px; }
.empty-box h2{ font-size:28px; font-weight:800; margin-bottom:10px; }
.empty-box p{ color:#777; }
@media(max-width:1000px){ .deals-grid{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:600px){ .deals-grid{ grid-template-columns:1fr; } .title-row{ flex-direction:column; align-items:flex-start; gap:10px; } .title-row h1{ font-size:38px; } .header-container{ flex-direction:column; gap:15px; } }
</style>
</head>
<body>
<header class="top-header">
<div class="header-container">
  <a href="index.php" class="logo" style="text-decoration:none;color:inherit;">TrustFund</a>
  <nav class="header-nav">
    <a href="#">Orders</a>
    <?php if(!isset($_SESSION['user_id'])): ?><a href="login.php">Login</a><a href="register.php">Register</a><?php endif; ?>
    <a href="account.php"><?php echo $username; ?></a>
    <a href="wishlist.php" style="position:relative;">&#9825;<?php if($wishlistCount > 0): ?><span style="position:absolute;top:-8px;right:-10px;background:white;color:#9b59b6;border-radius:50%;width:16px;height:16px;font-size:10px;display:flex;align-items:center;justify-content:center;font-weight:700;"><?php echo $wishlistCount; ?></span><?php endif; ?></a>
    <a href="cart.php" style="position:relative;">&#128722;<?php if($cartCount > 0): ?><span style="position:absolute;top:-8px;right:-10px;background:white;color:#9b59b6;border-radius:50%;width:16px;height:16px;font-size:10px;display:flex;align-items:center;justify-content:center;font-weight:700;"><?php echo $cartCount; ?></span><?php endif; ?></a>
  </nav>
</div>
</header>
<div class="page-wrapper">
  <div class="breadcrumb-nav"><a href="index.php">Home</a> → <span>Events & Tickets</span></div>
  <div class="title-row">
    <h1>Events & Tickets</h1>
    <div class="deals-count"><?php echo mysqli_num_rows($result); ?> Events</div>
  </div>
  <div class="deals-grid">
  <?php if(mysqli_num_rows($result) > 0): ?>
  <?php while($product = mysqli_fetch_assoc($result)): ?>
  <?php $inWishlist = isset($_SESSION['wishlist']) && in_array((int)$product['id'], $_SESSION['wishlist']); ?>
  <div class="deal-card">
    <div class="deal-image">
      <a href="product.php?id=<?php echo $product['id']; ?>"><img src="<?php echo htmlspecialchars($product['product_image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>"></a>
      <?php if($buyer_id && $buyer_id != $product['seller_id']): ?>
      <a href="events.php?wishlist=<?php echo $product['id']; ?>" class="heart-btn <?= $inWishlist ? 'active' : 'inactive'; ?>"><i class="fa-<?= $inWishlist ? 'solid' : 'regular'; ?> fa-heart"></i></a>
      <?php endif; ?>
    </div>
    <a href="product.php?id=<?php echo $product['id']; ?>" style="text-decoration:none;color:inherit;">
    <div class="deal-info">
      <span class="event-badge">Event</span>
      <div class="deal-name"><?php echo htmlspecialchars($product['product_name']); ?></div>
      <?php if(!empty($product['product_description'])): ?>
      <div class="deal-desc"><?php echo htmlspecialchars(substr($product['product_description'], 0, 80)) . (strlen($product['product_description']) > 80 ? '...' : ''); ?></div>
      <?php endif; ?>
      <div class="stars-row">
        <?php if($product['review_count'] > 0): ?>
          <?= stars((int)round($product['avg_rating'])); ?><span class="review-count">(<?= $product['review_count']; ?>)</span>
        <?php else: ?>
          <?= stars(0); ?><span class="review-count">No reviews</span>
        <?php endif; ?>
      </div>
      <div class="deal-price">R<?php echo number_format((float)preg_replace('/[^0-9.]/', '', $product['product_price'])); ?></div>
      <div class="seller-name">Sold by <?php echo htmlspecialchars($product['seller_name']); ?></div>
    </div>
    </a>
  </div>
  <?php endwhile; ?>
  <?php else: ?>
    <div class="empty-box"><i class="fa-solid fa-calendar-days"></i><h2>No Events Yet</h2><p>Check back soon<?php if(!$currentUser || $currentUser['seller_status'] != 'approved'): ?> or <a href="application.php" style="color:#a65cc5;">become a seller</a><?php endif; ?>.</p></div>
  <?php endif; ?>
  </div>
</div>
</body>
</html>