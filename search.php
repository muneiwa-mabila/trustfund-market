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

$query = $_GET['query'] ?? '';
$safe = mysqli_real_escape_string($conn, $query);

$sql = "SELECT * FROM seller_items 
        WHERE product_status='Approved'
        AND (
            product_name LIKE '%$safe%' 
            OR product_description LIKE '%$safe%' 
            OR product_category LIKE '%$safe%'
        )
        ORDER BY id DESC";

$result = mysqli_query($conn, $sql);
$count = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Search Results - TrustFund</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { background:#f5f5f5; font-family: system-ui, -apple-system, sans-serif; }

.navbar { background:#9b59b6; border-bottom:1px solid #ddd; padding:10px 0; }
.navbar-brand { font-weight:bold; font-size:1.8rem; color:white; }
.nav-link { color:white !important; font-weight:500; margin:0 10px; font-size:0.9rem; }
.search-bar { border-radius:25px; padding:8px 16px; border:1px solid #ddd; }
.search-bar:focus { border-color:#9b59b6; box-shadow:0 0 0 0.1rem rgba(155,89,182,0.25); }
.nav-btn { background:#ffffff; color:#9b59b6 !important; padding:4px 12px; border-radius:6px; font-weight:600; }
.nav-icon { position:relative; color:white !important; font-size:20px; }
.nav-icon span { position:absolute; top:-8px; right:-10px; background:white; color:#9b59b6; width:18px; height:18px; border-radius:50%; font-size:10px; font-weight:700; display:flex; align-items:center; justify-content:center; }

.page-wrapper { max-width:1152px; margin:40px auto; padding:0 20px; }

.results-header { margin-bottom:30px; }
.results-header h2 { font-size:28px; font-weight:700; }
.results-header span { color:#9b59b6; }
.results-count { font-size:14px; color:#777; margin-top:6px; }

.products-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:22px; }

.product-card { background:white; border-radius:14px; overflow:hidden; text-decoration:none; color:inherit; display:block; border:1px solid #eee; transition:transform 0.2s, box-shadow 0.2s; }
.product-card:hover { transform:translateY(-3px); box-shadow:0 8px 20px rgba(155,89,182,0.15); }

.product-image { width:100%; height:200px; background:#e5e5e5; overflow:hidden; }
.product-image img { width:100%; height:100%; object-fit:cover; }

.product-info { padding:14px; }
.product-category { font-size:11px; font-weight:700; color:#9b59b6; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; }
.product-name { font-size:14px; font-weight:600; margin-bottom:6px; line-height:1.3; }
.product-price { font-size:16px; font-weight:800; color:#111; }

.empty-state { text-align:center; padding:80px 20px; color:#999; grid-column:1/-1; }
.empty-state i { font-size:48px; margin-bottom:20px; color:#ddd; }
.empty-state h3 { font-size:22px; font-weight:700; color:#333; margin-bottom:10px; }

.search-again { margin-top:30px; }
.search-again form { display:flex; gap:12px; max-width:500px; }
.search-again input { flex:1; border-radius:25px; padding:10px 18px; border:1px solid #ddd; font-size:14px; }
.search-again button { background:#9b59b6; color:white; border:none; padding:10px 22px; border-radius:25px; font-weight:600; cursor:pointer; }

@media(max-width:900px){ .products-grid{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:500px){ .products-grid{ grid-template-columns:1fr; } }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="index.php">TrustFund</a>

    <form class="d-flex flex-grow-1 mx-5" method="GET" action="search.php">
      <input class="form-control search-bar" type="search" name="query"
        placeholder="Search for products, services, skills..."
        value="<?php echo htmlspecialchars($query); ?>" required>
    </form>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item"><a class="nav-link" href="#">Orders</a></li>
        <?php if(!isset($_SESSION['user_id'])): ?>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="account.php"><?php echo $username; ?></a></li>
        <li class="nav-item">
          <a class="nav-link nav-icon" href="wishlist.php">
            <i class="fa-regular fa-heart"></i>
            <span><?php echo $wishlistCount; ?></span>
          </a>
        </li>
        <li class="nav-item ms-3 me-3">
          <a class="nav-link nav-icon" href="cart.php">
            <i class="fa-solid fa-cart-shopping"></i>
            <span><?php echo $cartCount; ?></span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- PAGE CONTENT -->
<div class="page-wrapper">

  <div class="results-header">
    <h2>Results for: <span>"<?php echo htmlspecialchars($query); ?>"</span></h2>
    <p class="results-count"><?php echo $count; ?> product<?php echo $count != 1 ? 's' : ''; ?> found</p>
  </div>

  <div class="products-grid">

  <?php if($count == 0): ?>

    <div class="empty-state">
      <i class="fa-solid fa-magnifying-glass"></i>
      <h3>No results found for "<?php echo htmlspecialchars($query); ?>"</h3>
      <p>Try different keywords or browse our categories below.</p>
      <div class="search-again">
        <form method="GET" action="search.php">
          <input type="text" name="query" placeholder="Try another search...">
          <button type="submit">Search</button>
        </form>
      </div>
    </div>

  <?php else: ?>
  <?php while($p = mysqli_fetch_assoc($result)): ?>

    <a href="product.php?id=<?php echo $p['id']; ?>" class="product-card">
      <div class="product-image">
        <img src="<?php echo htmlspecialchars($p['product_image']); ?>"
             onerror="this.style.display='none'">
      </div>
      <div class="product-info">
        <div class="product-category"><?php echo htmlspecialchars($p['product_category']); ?></div>
        <div class="product-name"><?php echo htmlspecialchars($p['product_name']); ?></div>
        <div class="product-price">R<?php echo number_format((float)preg_replace('/[^0-9.]/','',$p['product_price'])); ?></div>
      </div>
    </a>

  <?php endwhile; ?>
  <?php endif; ?>

  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>