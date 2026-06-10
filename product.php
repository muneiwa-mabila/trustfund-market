<?php
session_start();
include 'db.php';

$product_id = $_GET['id'] ?? 0;
$buyer_id   = $_SESSION['user_id'] ?? null;

$query = "
SELECT 
seller_items.*,
users.name AS seller_name,
users.email AS seller_email,
users.user_id AS seller_user_id
FROM seller_items
INNER JOIN users ON seller_items.seller_id = users.user_id
WHERE seller_items.id = '$product_id'
AND seller_items.product_status='Approved'
";
$result  = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) { die("Product not found."); }

/* WISHLIST TOGGLE */
if(isset($_GET['wishlist']) && $buyer_id){
    if(!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
    $pid = (int)$_GET['wishlist'];
    if(in_array($pid, $_SESSION['wishlist'])){
        $_SESSION['wishlist'] = array_values(array_diff($_SESSION['wishlist'], [$pid]));
    } else {
        $_SESSION['wishlist'][] = $pid;
    }
    header("Location: product.php?id=$product_id");
    exit();
}

/* SUBMIT REVIEW */
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review']) && $buyer_id){
    $rating  = (int)($_POST['rating'] ?? 5);
    $comment = mysqli_real_escape_string($conn, trim($_POST['comment'] ?? ''));
    $rating  = max(1, min(5, $rating));

    $exists = mysqli_query($conn, "SELECT id FROM reviews WHERE product_id='$product_id' AND user_id='$buyer_id'");
    if(mysqli_num_rows($exists) == 0 && !empty($comment)){
        mysqli_query($conn, "INSERT INTO reviews (product_id, user_id, rating, review, created_at)
            VALUES ('$product_id','$buyer_id','$rating','$comment', NOW())");
    }
    header("Location: product.php?id=$product_id#reviews");
    exit();
}

/* SUBMIT REPORT */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_report']) && $buyer_id) {
    $report_type   = mysqli_real_escape_string($conn, $_POST['report_type'] ?? '');
    $report_detail = mysqli_real_escape_string($conn, trim($_POST['report_detail'] ?? ''));
    $valid_types   = ['Counterfeit product','Prohibited item','Misleading description','Fraudulent seller','Inappropriate content','Other'];
    if (in_array($report_type, $valid_types)) {
        mysqli_query($conn, "INSERT INTO reports (reporter_id, product_id, seller_id, report_type, report_detail, created_at)
            VALUES ('$buyer_id','$product_id','{$product['seller_user_id']}','$report_type','$report_detail', NOW())");
    }
    header("Location: product.php?id=$product_id&reported=1");
    exit();
}
$justReported = isset($_GET['reported']);

/* SKILLS QUOTE CHECK */
$existingQuote = null;
if ($buyer_id && $product['product_type'] == 'Skills') {
    $qr = mysqli_query($conn, "SELECT * FROM service_quotes 
        WHERE buyer_id='$buyer_id' AND product_id='$product_id'
        ORDER BY id DESC LIMIT 1");
    $existingQuote = mysqli_fetch_assoc($qr);
}

/* REVIEWS */
$reviewsQuery = mysqli_query($conn, "
    SELECT reviews.*, users.name AS reviewer_name
    FROM reviews
    LEFT JOIN users ON reviews.user_id = users.user_id
    WHERE reviews.product_id='$product_id'
    ORDER BY reviews.created_at DESC
");
$reviewCount = mysqli_num_rows($reviewsQuery);

$avgRating = 0;
if($reviewCount > 0){
    $sumQ = mysqli_query($conn, "SELECT AVG(rating) as avg FROM reviews WHERE product_id='$product_id'");
    $avgRating = round(mysqli_fetch_assoc($sumQ)['avg'], 1);
}

/* ALREADY REVIEWED? */
$alreadyReviewed = false;
if($buyer_id){
    $chk = mysqli_query($conn, "SELECT id FROM reviews WHERE product_id='$product_id' AND user_id='$buyer_id'");
    $alreadyReviewed = mysqli_num_rows($chk) > 0;
}

/* HAS PURCHASED? */
$hasPurchased = false;
if($buyer_id){
    $purchaseChk = mysqli_query($conn, "
        SELECT id FROM orders 
        WHERE buyer_id='$buyer_id' 
        AND product_id='$product_id'
        AND status='Delivered'
        LIMIT 1
    ");
    $hasPurchased = mysqli_num_rows($purchaseChk) > 0;
}

/* WISHLIST / CART COUNTS */
$cartCount     = 0;
$wishlistCount = 0;
$inWishlist    = false;

if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $item) $cartCount += $item['quantity'];
}
if(isset($_SESSION['wishlist']) && is_array($_SESSION['wishlist'])){
    $wishlistCount = count($_SESSION['wishlist']);
    $inWishlist    = in_array((int)$product_id, $_SESSION['wishlist']);
}

function stars($n){
    $out = '';
    for($i=1;$i<=5;$i++){
        $out .= $i <= $n
            ? '<i class="fa-solid fa-star" style="color:#f39c12;"></i>'
            : '<i class="fa-regular fa-star" style="color:#ddd;"></i>';
    }
    return $out;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($product['product_name']); ?> | TrustFund</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{ margin:0; padding:0; box-sizing:border-box; }
body{ background:#fff; font-family:Arial,sans-serif; color:#111; }

.top-header{ background:white;
 border-bottom:
 1px solid #eee; padding:18px 40px; }
.header-container{ max-width:1200px; margin:auto; display:flex; justify-content:space-between; align-items:center; }
.logo{ font-size:28px; font-weight:800; color:#9b59b6; text-decoration:none; }
.header-nav{ display:flex; gap:28px; align-items:center; }
.header-nav a{ text-decoration:none; color:#111; font-size:14px; font-weight:600; }
.header-nav a:hover{ color:#9b59b6; }
.nav-icons{ display:flex; gap:18px; align-items:center; }
.icon-link{ position:relative; color:#111; font-size:18px; text-decoration:none; }
.icon-link span{ position:absolute; top:-8px; right:-10px; background:#9b59b6; color:white;
    font-size:11px; width:18px; height:18px; display:flex; align-items:center;
    justify-content:center; border-radius:50%; }

.product-page{ max-width:1200px; margin:auto; padding:55px 20px; }
.breadcrumb-text{ font-size:13px; color:#999; margin-bottom:20px; }
.breadcrumb-text a{ text-decoration:none; color:#999; }
.breadcrumb-text span{ color:#9b59b6; }

.product-layout{ display:grid; grid-template-columns:1fr 1fr; gap:60px; align-items:start; }

.product-gallery{ width:100%; }
.main-image{ width:100%; height:420px; overflow:hidden; border-radius:18px; background:#f3f3f3; position:relative; }
.main-image img{ width:100%; height:100%; object-fit:cover; }

.wishlist-btn{
    position:absolute; top:16px; right:16px;
    width:42px; height:42px;
    background:white; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    text-decoration:none;
    box-shadow:0 2px 10px rgba(0,0,0,0.12);
    font-size:18px; transition:0.2s; z-index:10;
}
.wishlist-btn:hover{ transform:scale(1.1); }
.wishlist-btn.active{ color:#e74c3c; }
.wishlist-btn.inactive{ color:#aaa; }

.thumbnail-row{ display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-top:12px; }
.thumbnail{ height:80px; overflow:hidden; border-radius:10px; border:2px solid transparent; cursor:pointer; background:#f3f3f3; }
.thumbnail.active{ border-color:#9b59b6; }
.thumbnail img{ width:100%; height:100%; object-fit:cover; }

.product-info{ padding-top:0; }
.product-category{ font-size:13px; color:#999; margin-bottom:8px; }
.product-title{ font-size:38px; font-weight:800; margin-bottom:10px; }

.rating-summary{ display:flex; align-items:center; gap:10px; margin-bottom:18px; }
.rating-summary .avg{ font-size:22px; font-weight:800; color:#111; }
.rating-summary .count{ font-size:13px; color:#888; }

.product-price{ font-size:36px; font-weight:800; margin-bottom:24px; }

.purchase-row{ display:flex; gap:12px; margin-bottom:28px; }
.quantity{ width:65px; height:50px; border:1px solid #ddd; border-radius:8px; text-align:center; font-size:16px; }

.cart-btn{
    border:none; background:#9b59b6; color:white;
    padding:0 28px; font-size:13px; font-weight:700;
    border-radius:10px; transition:0.2s; cursor:pointer;
    text-decoration:none; display:inline-flex; align-items:center; height:50px;
}
.cart-btn:hover{ background:#8e44ad; color:white; }

.seller-section{ margin-bottom:28px; }
.seller-top{
    display:flex; justify-content:space-between; align-items:center;
    background:#f8f8f8; padding:18px 20px; border-radius:14px;
    flex-wrap:wrap; gap:12px;
}
.seller-top-left { display:flex; flex-direction:column; gap:6px; }
.seller-top-right { display:flex; flex-direction:column; align-items:flex-end; gap:8px; }
.sold-by{ font-size:12px; color:#777; margin-bottom:3px; }
.seller-name{ font-size:15px; font-weight:700; }

.report-btn {
    background: none; border: none; color: #bbb;
    font-size: 12px; cursor: pointer; padding: 0;
    text-decoration: underline; transition: color 0.2s;
    display: inline-flex; align-items: center; gap: 4px;
}
.report-btn:hover { color: #e74c3c; }

.details-title{ font-size:18px; font-weight:800; margin-bottom:12px; }
.product-description{ font-size:14px; line-height:1.8; color:#444; }

.info-box{
    background:#f8f0fc; border:2px solid #d7a8f0;
    border-radius:14px; padding:18px; margin-bottom:24px;
    font-size:14px; color:#444; line-height:1.6;
}
.info-box strong{ display:block; margin-bottom:6px; color:#111; }

/* ===== REVIEWS ===== */
.reviews-section{ max-width:1200px; margin:0 auto 60px; padding:0 20px; }
.reviews-header{
    display:flex; justify-content:space-between; align-items:center;
    margin-bottom:24px; padding-bottom:16px; border-bottom:1px solid #eee;
}
.reviews-header h2{ font-size:24px; font-weight:800; }

.overall-score{
    display:flex; align-items:center; gap:16px;
    background:#f8f0fc; border-radius:16px;
    padding:20px 28px; margin-bottom:28px;
    border:1px solid #e8d5f5;
}
.big-score{ font-size:52px; font-weight:900; color:#9b59b6; line-height:1; }
.score-right .total{ font-size:13px; color:#888; margin-top:4px; }

.review-card{
    background:#fafafa; border:1px solid #eee;
    border-radius:14px; padding:20px; margin-bottom:14px;
}
.review-top{
    display:flex; justify-content:space-between;
    align-items:flex-start; margin-bottom:10px;
}
.reviewer-name{ font-weight:700; font-size:14px; color:#111; }
.review-date{ font-size:12px; color:#bbb; }
.review-comment{ font-size:14px; color:#444; line-height:1.6; margin-top:8px; }

.write-review{
    background:white; border:1px solid #e8d5f5;
    border-radius:16px; padding:28px; margin-top:28px;
}
.write-review h3{ font-size:18px; font-weight:800; margin-bottom:20px; }
.star-picker{ display:flex; gap:8px; margin-bottom:16px; }
.star-picker i{ font-size:28px; color:#ddd; cursor:pointer; transition:0.15s; }
.review-textarea{
    width:100%; padding:14px 16px;
    border:1px solid #ddd; border-radius:12px;
    font-size:14px; resize:vertical; min-height:100px;
    font-family:Arial,sans-serif; margin-bottom:14px;
}
.review-textarea:focus{ border-color:#9b59b6; outline:none; box-shadow:0 0 0 3px rgba(155,89,182,0.1); }
.submit-review-btn{
    background:#9b59b6; color:white; border:none;
    padding:12px 28px; border-radius:10px;
    font-weight:700; font-size:14px; cursor:pointer; transition:0.2s;
}
.submit-review-btn:hover{ background:#8e44ad; }
.no-reviews{ text-align:center; color:#aaa; font-size:14px; padding:30px 0; }
.verified-badge{
    display:inline-flex; align-items:center; gap:5px;
    background:#e8f8ee; color:#1b9c5a;
    font-size:11px; font-weight:700;
    padding:3px 10px; border-radius:20px; margin-top:4px;
}

/* ===== REPORT MODAL ===== */
.modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.45); z-index: 1000;
    align-items: center; justify-content: center;
}
.modal-overlay.open { display: flex; }
.modal-box {
    background: white; border-radius: 18px;
    padding: 36px; max-width: 460px; width: 90%;
    box-shadow: 0 8px 40px rgba(0,0,0,0.18);
    animation: popIn 0.2s ease;
}
@keyframes popIn {
    from { transform: scale(0.92); opacity: 0; }
    to   { transform: scale(1);    opacity: 1; }
}
.modal-box h3 { font-size: 20px; font-weight: 800; margin-bottom: 6px; }
.modal-box .modal-subtitle { font-size: 13px; color: #888; margin-bottom: 22px; }
.report-reasons { display: flex; flex-direction: column; gap: 10px; margin-bottom: 18px; }
.report-reason-label {
    display: flex; align-items: center; gap: 10px;
    border: 1px solid #eee; border-radius: 10px; padding: 12px 14px;
    cursor: pointer; font-size: 14px; transition: 0.15s;
}
.report-reason-label:hover { border-color: #9b59b6; background: #faf5ff; }
.report-reason-label input { accent-color: #9b59b6; }
.report-details {
    width: 100%; padding: 12px 14px; border: 1px solid #ddd;
    border-radius: 10px; font-size: 13px; resize: vertical;
    min-height: 80px; font-family: Arial,sans-serif; margin-bottom: 18px;
}
.report-details:focus { border-color: #9b59b6; outline: none; box-shadow: 0 0 0 3px rgba(155,89,182,0.1); }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; }
.modal-cancel {
    background: #f4f4f4; border: none; padding: 11px 22px;
    border-radius: 9px; font-weight: 700; font-size: 13px;
    cursor: pointer; color: #555; transition: 0.2s;
}
.modal-cancel:hover { background: #eee; }
.modal-submit {
    background: #e74c3c; color: white; border: none;
    padding: 11px 22px; border-radius: 9px;
    font-weight: 700; font-size: 13px; cursor: pointer; transition: 0.2s;
    display: inline-flex; align-items: center; gap: 6px;
}
.modal-submit:hover { background: #c0392b; }
.report-success {
    text-align: center; padding: 10px 0;
}
.report-success i { font-size: 40px; color: #1b9c5a; display: block; margin-bottom: 14px; }
.report-success strong { font-size: 17px; display: block; margin-bottom: 8px; }
.report-success p { font-size: 13px; color: #888; }
</style>
</head>
<body>

<header class="top-header">
<div class="header-container">
  <a href="index.php" class="logo">TrustFund</a>
  <nav class="header-nav">
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
    <a href="contact.php">Contact</a>
    <div class="nav-icons">
      <a href="wishlist.php" class="icon-link">
        <i class="fa-regular fa-heart"></i>
        <?php if($wishlistCount > 0): ?><span><?= $wishlistCount ?></span><?php endif; ?>
      </a>
      <a href="cart.php" class="icon-link">
        <i class="fa-solid fa-cart-shopping"></i>
        <?php if($cartCount > 0): ?><span><?= $cartCount ?></span><?php endif; ?>
      </a>
    </div>
  </nav>
</div>
</header>

<div class="product-page">
<div class="product-layout">

  <!-- GALLERY -->
  <div class="product-gallery">
    <div class="main-image">
      <img id="mainImg" src="<?= htmlspecialchars($product['product_image']); ?>" alt="">

      <?php if($buyer_id && $buyer_id != $product['seller_user_id']): ?>
      <a href="product.php?id=<?= $product_id; ?>&wishlist=<?= $product_id; ?>"
         class="wishlist-btn <?= $inWishlist ? 'active' : 'inactive'; ?>">
        <i class="fa-<?= $inWishlist ? 'solid' : 'regular'; ?> fa-heart"></i>
      </a>
      <?php endif; ?>
    </div>
  </div>

  <!-- INFO -->
  <div class="product-info">

    <div class="breadcrumb-text">
      <a href="index.php">Home</a> /
      <span><?= htmlspecialchars($product['product_category']); ?></span>
    </div>

    <div class="product-category"><?= htmlspecialchars($product['product_category']); ?></div>

    <h1 class="product-title"><?= htmlspecialchars($product['product_name']); ?></h1>

    <div class="rating-summary">
      <span class="avg"><?= $avgRating ?: '-'; ?></span>
      <span><?= stars((int)round($avgRating)); ?></span>
      <span class="count">(<?= $reviewCount; ?> review<?= $reviewCount != 1 ? 's' : ''; ?>)</span>
    </div>

    <div class="product-price">
      R<?= number_format((float)$product['product_price'], 2); ?>
    </div>

    <?php if(strtolower(trim($product['product_type'])) == 'skills' || strtolower(trim($product['product_type'])) == 'service'): ?>

      <?php if(!$buyer_id): ?>
        <div class="info-box"><strong>Service Listing</strong>Login to chat with seller.</div>
      <?php elseif($buyer_id == $product['seller_user_id']): ?>
        <div class="info-box"><strong>Your Listing</strong>This is your service.</div>
      <?php elseif($existingQuote && $existingQuote['status'] == 'Quoted'): ?>
        <div class="info-box" style="border-color:#2ecc71;">
          <strong>Agreed Price</strong>
          R<?= number_format($existingQuote['agreed_price'], 2); ?>
          <form action="add-to-cart.php" method="POST" style="margin-top:10px;">
            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
            <input type="hidden" name="quantity" value="1">
            <input type="hidden" name="agreed_price" value="<?= $existingQuote['agreed_price']; ?>">
            <button type="submit" class="cart-btn">ADD TO CART</button>
          </form>
        </div>
      <?php elseif($existingQuote): ?>
        <div class="info-box" style="border-color:#f1c40f;">
          <strong>Waiting for Seller</strong>Request pending.<br><br>
          <a href="conversation.php?seller=<?= $product['seller_user_id']; ?>&product=<?= $product['id']; ?>" class="cart-btn">OPEN CONVERSATION</a>
        </div>
      <?php else: ?>
        <div class="info-box">
          <strong>Service Info</strong>Chat before purchase.<br><br>
          <a href="conversation.php?seller=<?= $product['seller_user_id']; ?>&product=<?= $product['id']; ?>" class="cart-btn">MESSAGE SELLER</a>
        </div>
      <?php endif; ?>

    <?php else: ?>

      <form action="add-to-cart.php" method="POST" class="purchase-row">
        <input type="number" name="quantity" class="quantity" value="1" min="1">
        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
        <?php if(!$buyer_id || $buyer_id != $product['seller_user_id']): ?>
          <button type="submit" class="cart-btn">ADD TO CART</button>
        <?php else: ?>
          <button type="button" class="cart-btn" style="background:#999;" disabled>Your Product</button>
        <?php endif; ?>
      </form>

    <?php endif; ?>

    <div class="seller-section">
      <div class="seller-top">
        <div class="seller-top-left">
          <div>
            <div class="sold-by">Sold by:</div>
           <div class="seller-name">
    <a href="seller-profile.php?id=<?= $product['seller_user_id']; ?>" style="color:inherit;text-decoration:none;">
        <?= htmlspecialchars($product['seller_name']); ?>
    </a>
        </div>
          </div>
          <?php if($buyer_id && $buyer_id != $product['seller_user_id']): ?>
          <button class="report-btn" onclick="document.getElementById('reportModal').classList.add('open')">
            <i class="fa-solid fa-flag"></i> Report this listing
          </button>
          <?php endif; ?>
        </div>
        <div class="seller-top-right">
          <a href="conversation.php?seller=<?= $product['seller_user_id']; ?>&product=<?= $product['id']; ?>" class="cart-btn">
            Message seller
          </a>
        </div>
      </div>
    </div>

    <div class="details-title">Product Details</div>
    <div class="product-description"><?= nl2br(htmlspecialchars($product['product_description'])); ?></div>

  </div>
</div>
</div>

<!-- ===== REVIEWS ===== -->
<div class="reviews-section" id="reviews">

  <div class="reviews-header">
    <h2>Customer Reviews</h2>
  </div>

  <?php if($reviewCount > 0): ?>
  <div class="overall-score">
    <div class="big-score"><?= $avgRating; ?></div>
    <div class="score-right">
      <div><?= stars((int)round($avgRating)); ?></div>
      <div class="total"><?= $reviewCount; ?> review<?= $reviewCount != 1 ? 's' : ''; ?></div>
    </div>
  </div>
  <?php endif; ?>

  <?php if($reviewCount > 0): ?>
    <?php mysqli_data_seek($reviewsQuery, 0); ?>
    <?php while($review = mysqli_fetch_assoc($reviewsQuery)): ?>
    <div class="review-card">
      <div class="review-top">
        <div>
          <div class="reviewer-name"><?= htmlspecialchars($review['reviewer_name'] ?? 'Anonymous'); ?></div>
          <div style="margin-top:4px;"><?= stars($review['rating']); ?></div>
          <div class="verified-badge"><i class="fa-solid fa-circle-check"></i> Verified Purchase</div>
        </div>
        <div class="review-date"><?= date('d M Y', strtotime($review['created_at'])); ?></div>
      </div>
      <div class="review-comment"><?= nl2br(htmlspecialchars($review['review'])); ?></div>
    </div>
    <?php endwhile; ?>
  <?php else: ?>
    <div class="no-reviews">No reviews yet - be the first to leave one!</div>
  <?php endif; ?>

  <!-- WRITE REVIEW — only if purchased -->
  <?php if($buyer_id && $buyer_id != $product['seller_user_id'] && $hasPurchased): ?>
  <div class="write-review">
    <?php if($alreadyReviewed): ?>
      <p style="color:#888;font-size:14px;">&#10003; You have already reviewed this product.</p>
    <?php else: ?>
      <h3>Leave a Review</h3>
      <form method="POST" action="product.php?id=<?= $product_id; ?>#reviews">
        <div class="star-picker" id="starPicker">
          <?php for($i=1;$i<=5;$i++): ?>
            <i class="fa-regular fa-star" data-value="<?= $i; ?>" onclick="setRating(<?= $i; ?>)"></i>
          <?php endfor; ?>
        </div>
        <input type="hidden" name="rating" id="ratingInput" value="5">
        <textarea name="comment" class="review-textarea" placeholder="Share your experience..." required></textarea>
        <button type="submit" name="submit_review" class="submit-review-btn">Submit Review</button>
      </form>
    <?php endif; ?>
  </div>
  <?php endif; ?>

</div>

<!-- ===== REPORT MODAL ===== -->
<div class="modal-overlay" id="reportModal">
  <div class="modal-box">
    <?php if($justReported): ?>
      <div class="report-success">
        <i class="fa-solid fa-circle-check"></i>
        <strong>Report Submitted</strong>
        <p>Thank you for helping keep TrustFund safe. Our team will review your report shortly.</p>
      </div>
      <div class="modal-actions" style="margin-top:24px;">
        <button class="modal-cancel" onclick="document.getElementById('reportModal').classList.remove('open')">Close</button>
      </div>
    <?php else: ?>
      <h3><i class="fa-solid fa-flag" style="color:#e74c3c;margin-right:8px;"></i>Report this listing</h3>
      <p class="modal-subtitle">Help us keep TrustFund safe. Select the reason for your report.</p>
      <form method="POST" action="product.php?id=<?= $product_id; ?>">
        <div class="report-reasons">
          <?php
          $reasons = [
            'Counterfeit product',
            'Prohibited item',
            'Misleading description',
            'Fraudulent seller',
            'Inappropriate content',
            'Other'
          ];
          foreach($reasons as $r):
          ?>
          <label class="report-reason-label">
            <input type="radio" name="report_type" value="<?= htmlspecialchars($r); ?>" required>
            <?= htmlspecialchars($r); ?>
          </label>
          <?php endforeach; ?>
        </div>
        <textarea name="report_detail" class="report-details" placeholder="Additional details (optional)..."></textarea>
        <div class="modal-actions">
          <button type="button" class="modal-cancel" onclick="document.getElementById('reportModal').classList.remove('open')">Cancel</button>
          <button type="submit" name="submit_report" class="modal-submit">
            <i class="fa-solid fa-flag"></i> Submit Report
          </button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>

<script>
function switchImg(el, src){
    document.getElementById('mainImg').src = src;
    document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}

function setRating(val){
    document.getElementById('ratingInput').value = val;
    document.querySelectorAll('#starPicker i').forEach((star, i) => {
        star.className = i < val ? 'fa-solid fa-star' : 'fa-regular fa-star';
        star.style.color = i < val ? '#f39c12' : '#ddd';
    });
}

setRating(5);

// Auto-open confirmation modal if redirected after report
<?php if($justReported): ?>
document.getElementById('reportModal').classList.add('open');
<?php endif; ?>

// Close modal when clicking the dark overlay
document.getElementById('reportModal').addEventListener('click', function(e){
    if(e.target === this) this.classList.remove('open');
});
</script>

</body>
</html>