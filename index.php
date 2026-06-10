<?php
session_start();
include 'db.php';
include 'track_visitor.php';

/* UNREAD MESSAGES COUNT */
$unreadCount = 0;

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];

    $res = mysqli_query($conn, "
        SELECT COUNT(*) as total
        FROM messages
        WHERE receiver_id='$uid'
        AND is_read = 0
    ");

    $row = mysqli_fetch_assoc($res);
    $unreadCount = $row['total'] ?? 0;
}

/* LIVE USER DATA */
$currentUser = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $userQuery = mysqli_query($conn, "
        SELECT * FROM users
        WHERE user_id='$user_id'
    ");

    $currentUser = mysqli_fetch_assoc($userQuery);
}

/* CART */
$cartCount = 0;

if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}

/* WISHLIST */
$wishlistCount = 0;

if (isset($_SESSION['wishlist'])) {
    $wishlistCount = count($_SESSION['wishlist']);
}

/* USERNAME */
$username = $_SESSION['name'] ?? 'MyAccount';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>TrustFund – Kasi Marketplace</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <style>
    body {
      background: #ffffff;
      color: #000000;
      font-family: system-ui, -apple-system, sans-serif;
    }
    .navbar {
      background:  #9b59b6;
      border-bottom: 1px solid #dddddd;
      padding: 10px 0;
    }
	
    .navbar-brand {
      font-weight: bold;
      font-size: 1.8rem;
      color: white;
	   margin-left: 0; 
    }
	
    .nav-link, .nav-btn {
      color: white !important;
      font-weight: 500;
      margin: 0 10px;
      text-decoration: none;
      transition: color 0.2s;
      font-size: 0.9rem;
    }
    .nav-link:hover, .nav-btn:hover {
      color: #8e44ad !important;
    }
    .hero {
      padding: 100px 0 80px;
      text-align: left;
      background: #f9f9f9;
    }
    .hero h1 {
      font-size: 3.5rem;
      font-weight: 900;
      margin-bottom: 1rem;
    }
    .hero p {
      font-size: 1.4rem;
      color: #555555;
      margin-bottom: 2rem;
    }
    .btn-purple {
      background: #9b59b6;
      color: white;
      border: none;
      padding: 12px 32px;
      font-weight: 600;
    }
    .btn-purple:hover {
      background: #8e44ad;
    }
	.purple-boxes-wrapper {
      max-width: 1152px;
      margin: 0 auto;
    }

    .purple-card {
      background: linear-gradient(180deg, #9b59b6 0%, #8e44ad 100%);
      color: white;
      border-radius: 0;
      height: 80px;                    
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 16px rgba(155, 89, 182, 0.3);
      transition: all 0.3s ease;
      padding: 0 20px;
	  cursor: pointer;
    }

    .purple-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 24px rgba(155, 89, 182, 0.4);
    }

    .purple-card i {
      font-size: 32px;
      margin-right: 16px;
      flex-shrink: 0;
    }

    .purple-card .text-content {
      text-align: left;
      line-height: 1.2;
    }

    .purple-card .top-text {
      font-size: 16px;
      font-weight: 700;
      letter-spacing: 0.5px;
      margin-bottom: 2px;
    }

    .purple-card .bottom-text {
      font-size: 14px;
      font-weight: 300;
      opacity: 0.95;
    }
    .category-card {
      border: 1px solid #dddddd;
      padding: 30px 20px;
      text-align: center;
      transition: border-color 0.2s;
    }
    .category-card:hover {
      border-color: #9b59b6;
    }
    .category-card h5 {
      color: #9b59b6;
      margin-bottom: 10px;
    }
    footer {
      background: #f9f9f9;
      padding: 40px 0;
      text-align: center;
      margin-top: 60px;
      border-top: 1px solid #dddddd;
    }
.top-nav {
  background:  #ffffff;  
  width: 100%;
}
.top-nav {
  border-bottom: 1px solid #e5e5e5;
}
.top-nav .nav-link {
  font-size: 1.2rem;
  font-weight: 500;
}
.nav-btn {
  background: #9b59b6;        
  color: #ffffff !important;  
  padding: 4px 12px;
  border-radius: 6px;
  font-weight: 600;
}
.nav-btn:hover {
  background: #f2f2f2;
  color: #8e44ad !important;
}
.search-bar {
  border-radius: 25px;
  padding: 8px 16px;
  border: 1px solid #ddd;
}

.search-bar:focus {
  border-color: #9b59b6;
  box-shadow: 0 0 0 0.1rem rgba(155, 89, 182, 0.25);
}
.secondary-nav {
  background: #ffffff;  
  border-bottom: 1px solid #e5e5e5;
}

.secondary-nav-wrapper {
  max-width: 1152px;
  margin: 0 auto;
  padding: 10px 32px;
  display: flex;
  gap: 32px;
}

.secondary-nav a {
  text-decoration: none;
  color: #000;
  font-size: 14px;
  font-weight: 500;
  transition: color 0.2s;
}

.secondary-nav a:hover {
  color: #9b59b6;
}

/* TEXT BAR SECTION - ONE LINE ONLY */
.text-bar-wrapper {
    max-width: 1152px;
    margin: 88px auto 0;
}

.text-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: system-ui, -apple-system, sans-serif;
    min-height: 40px;
    padding: 0;
}

.text-bar .bold-text {
    font-size: 32px;
    font-weight: 700;
    color: #000000;          
}

.text-bar .regular-text {
    font-size: 24px;
    font-weight: 400;
    color: #A463BE;          
}
/* NEW: 6 PRODUCT CARDS SECTION - 24px gap under text bar */
.product-cards-wrapper {
    max-width: 1152px;
    margin: 24px auto 0;
}

.product-card {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    padding: 16px;
    text-align: center;
    height: 100%;
}

.gray-placeholder {
    background: #e5e5e5;
    width: 100%;
    height: 160px;
    border-radius: 8px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: #cccccc;
}

.badge-out {
    position: absolute;
    top: 16px;
    left: 16px;
    background: #000000;
    color: white;
    font-size: 12px;
    padding: 2px 10px;
    border-radius: 20px;
    font-weight: 500;
}

.product-card .regular {
    font-size: 12px;
    font-weight: 400;
    color: #222E37;
    height: 36px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.product-card {
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    min-height:280px;
}
.product-img{
    height:160px;
    width:100%;
    object-fit:cover;
    border-radius:8px;
}
/* CLOTHING HEADER - 88px under last section */
.clothing-header-wrapper {
    max-width: 1152px;
    margin: 24px auto 0;
}

.clothing-header {
    font-size: 32px;
    font-weight: 700;
    color: #000000;
    text-align: left;
}
.four-gray-cards-wrapper {
  max-width: 1150px;
  margin: auto;
}

.custom-row {
  display: flex;
  gap: 24px; /* spacing between cards */
  margin-bottom: 24px;
}

.gray-card {
  height: 340px;
  background: #eaeaea;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 32px;
}

/* Sizes */
.large {
  width: 664px;
}

.small {
  width: 462px;
}
@media (max-width: 768px) {
  .custom-row {
    flex-direction: column;
  }

  .large, .small {
    width: 100%;
  }
}
.section-spacing {
  margin-top: 10px;
}


.app-banner h1 {
  font-size: 48px;
  font-weight: bold;
  margin-bottom: 10px;
}

.sub-text {
  font-size: 20px;
  color: #767676;
}

.store-buttons img {
  height: 45px;
  margin: 0 8px;
  object-fit: contain;
}
.footer-spacing {
  margin-top: 30px;
}
.footer-custom {
  background: #000;
  padding: 50px 0;
  color: white;
}

.footer-title {
  color: #9b59b6;
  font-weight: bold;
  margin-bottom: 20px;
}

.footer-custom h6 {
  font-weight: bold;
  font-size: 20px;
}

.footer-custom p {
  color: #9b59b6;
  font-size: 20px;
  margin: 5px 0;
}
.hero {
  background: url("Images/hero.jpg") center center / cover no-repeat;
  color: black;
  padding: 120px 0 100px;
  position: relative;
}
.overlap-cards {
  position: relative;
  margin-top: -60px; /* controls overlap */
  z-index: 10;
}
.move-up {
  margin-top: -100px;
}
.product-img {
  width: 100%;
  height: 160px;
  object-fit: contain;   
  display: block;
  margin-bottom: 16px;
}
.gray-card {
  height: 340px;
  border-radius: 16px;
  display: flex;
  align-items: flex-end;
  justify-content: flex-start;
  padding: 20px;
  font-weight: bold;
  font-size: 32px;
  color: black;
  position: relative;
  overflow: hidden;
}

/* WOMEN IMAGE */
.women-card {
  background: url("Images/women.jpg") center/cover no-repeat;
}
.men-card {
  background: url("Images/men.jpg") center/cover no-repeat;
}
.hair-card {
  background: url("Images/Hair.jpg") center/cover no-repeat;
}
.tech-card {
  background: url("Images/Tech.jpg") center/cover no-repeat;
}

.bootcamps-card {
  background: url("Images/bootcamps.jpg") center/cover no-repeat;
}
.events-card {
  background: url("Images/event.jpg") center/cover no-repeat;
}
.app-banner-custom {
  background: #E7EBEF;
  border-radius: 12px;
  max-width: 1152px;
  margin: 0 auto;
  padding: 0 32px;
  overflow: hidden;
}

.app-flex {
  display: flex;
  align-items: center;
  gap: 24px;
  min-height: 260px;
}


/* PHONE IMAGE */
.app-image img {
  height: 300px;
  width: auto;
  display: block;
  margin-left: -10px;
  margin-bottom: -8px;
  object-fit: contain;
  mix-blend-mode: multiply;
}

/* RIGHT SIDE */
.app-content {
  flex: 1;
  padding: 24px 0;
}

.app-content h2 {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 8px;
  color: #000;
}

.app-content p {
  font-size: 14px;
  color: #767676;
  margin-bottom: 16px;
  line-height: 1.4;
}

.store-buttons {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.store-buttons img {
  height: 40px;
  width: auto;
  object-fit: contain;
}
.bold-text,
.about-title {
  font-family: system-ui, -apple-system, sans-serif;
  font-size: 32px;
  font-weight: 700;
  color: #000000;
  margin: 0;
   margin-bottom: 12px;
}
.about-wrapper {
  max-width: 1152px;
  margin: 0 auto;
  padding-left: 0;
}
.about-text {
  font-family: Arial, sans-serif;  
  font-size: 16px;
  color: #000000;
  line-height: 1.6;
}
.footer-custom a {
  display: block;
  color: #9b59b6;
  font-size: 20px;
  margin: 5px 0;
  text-decoration: none;
}

.footer-custom a:hover {
  text-decoration: underline;
}
.category-menu-section {
  display: none;
  background: #ffffff;
  border-radius: 0 0 18px 18px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.15);
  position: relative;
  z-index: 20;
  max-width: 1152px;   
  margin: 0 auto;  
  margin-top: -1px;
}

.category-menu-wrapper {
  max-width: 1152px;
  margin: 0 auto;
  padding: 28px 32px 36px;
}

.category-content {
  display: none;
  flex-direction: column;
  gap: 22px;
}

.category-content.active {
  display: flex;
}

.category-content a {
  color: #000;
  text-decoration: none;
  font-size: 14px;
}

.category-content a:hover {
  color: #9b59b6;
}
.secondary-nav a {
  position: relative;
  padding-bottom: 8px;
}

.secondary-nav a.active {
  color: #9b59b6;
}

.secondary-nav a.active::after {
  content: "";
  position: absolute;
  left: 0;
  bottom: -6px;
  width: 100%;
  height: 3px;
  background: #9b59b6;
}
.category-content.active {
  display: flex;
  flex-direction: row;
  gap: 0;
}

.category-left {
  width: 220px;
  background: #ffffff;
}

.sub-category {
  display: block;
  width: 100%;
  border: none;
  background: #ffffff;
  text-align: left;
  padding: 12px 16px;
  font-size: 14px;
  color: #000000;
  cursor: pointer;
}

.sub-category.active {
  background: #9b59b6;
  color: #ffffff;
  font-weight: 700;
}

.category-right {
  flex: 1;
  background: #9b59b6;
  color: #ffffff;
  padding: 24px 70px;
  min-height: 260px;
  
  border-radius: 0 18px 18px 0;
}

.sub-panel {
  display: none;
  gap: 90px;
}

.sub-panel.active {
  display: flex;
}

.sub-column {
  display: flex;
  flex-direction: column;
  gap: 18px;
  min-width: 180px;
}

.sub-column h6 {
  font-size: 14px;
  font-weight: 700;
  margin: 0 0 8px;
  color: #ffffff;
}

.sub-column a {
  color: #ffffff;
  text-decoration: none;
  font-size: 14px;
}

.sub-column a:hover {
  text-decoration: underline;
}
.category-link{
  text-decoration:none;
  color:inherit;
}
.deal-card-link{
    text-decoration:none;
    color:inherit;
    display:block;
}
/* NAV ICONS */

.nav-icon{
    position:relative;
    color:white !important;
    font-size:20px;
}

.nav-icon:hover{
    color:#e6d5f2 !important;
}

.nav-icon span{
    position:absolute;
    top:-8px;
    right:-10px;

    background:white;
    color:#9b59b6;

    width:18px;
    height:18px;

    border-radius:50%;

    font-size:10px;
    font-weight:700;

    display:flex;
    align-items:center;
    justify-content:center;
}
.account-nav-name{

    text-decoration:none !important;

    color:white !important;

    font-weight:600;

}

.account-nav-name:hover{

    color:#f3d9ff !important;

}
/* ACTIVE NAV */

.secondary-nav a.active{

    color:#9b59b6;

    font-weight:700;

    position:relative;

}

.secondary-nav a.active::after{

    content:"";

    position:absolute;

    left:0;
    bottom:-14px;

    width:100%;
    height:3px;

    background:#9b59b6;

    border-radius:999px;

}
.events-card {
  background-image: url("Images/event.jpg?test=1") !important;
  background-size: cover;
  background-position: center;
}
  </style>
</head>
<body>



<!-- TOP BAR -->
<div class="top-nav">
  <div class="container d-flex justify-content-between align-items-center py-1 px-3">
 
    <div></div>
    <!-- Right: Sell button -->
<?php if(isset($_SESSION['user_id'])): ?>

    <?php if($currentUser && $currentUser['seller_status'] == 'approved'): ?>

        <a href="seller-dashboard.php" class="nav-btn">

        Switch Profiles

        </a>

    <?php else: ?>

        <a href="application.php" class="nav-btn">

        Sell on TrustFund

        </a>

    <?php endif; ?>

<?php else: ?>

    <a href="login.php" class="nav-btn">

    Sell on TrustFund

    </a>

<?php endif; ?>
</div>
</div>

<!-- MAIN NAVBAR – move brand to left, links to right -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="#">TrustFund</a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
     <!-- SEARCH BAR -->
   <form class="d-flex flex-grow-1 mx-5" method="GET" action="search.php">
  <input 
    class="form-control search-bar" 
    type="search" 
    name="query"
    placeholder="Search for products, services, skills..." 
    required
  >
</form>

<div class="collapse navbar-collapse justify-content-end" id="navbarNav">

<ul class="navbar-nav align-items-center">

<li class="nav-item">

<?php if(isset($_SESSION['user_id'])): ?>

    <a class="nav-link" href="orders.php">
        Orders
    </a>

<?php else: ?>

    <a class="nav-link" href="login.php">
        Orders
    </a>

<?php endif; ?>
</li>

<?php if(!isset($_SESSION['user_id'])): ?>

<li class="nav-item">

<a class="nav-link" href="login.php">
Login
</a>

</li>

<li class="nav-item">

<a class="nav-link" href="register.php">
Register
</a>

</li>

<?php endif; ?>

<li class="nav-item">

<a class="nav-link account-nav-name" href="account.php">

<?php echo $username; ?>

</a>

</li>
<li class="nav-item">

<a class="nav-link nav-icon" href="wishlist.php">

<i class="fa-regular fa-heart"></i>

<span>

<?php echo $wishlistCount; ?>

</span>

</a>

</li>

<li class="nav-item ms-3 me-3">

<a class="nav-link nav-icon" href="cart.php">

<i class="fa-solid fa-cart-shopping"></i>

<span>

<?php echo $cartCount; ?>

</span>

</a>

</li>

<!-- 💬 Inbox  -->
<li class="nav-item ms-3">

<a class="nav-link nav-icon" href="inbox.php" style="position:relative;">

<i class="fa-solid fa-comment-dots"></i>

<?php if($unreadCount > 0): ?>
<span><?= $unreadCount ?></span>
<?php endif; ?>

</a>

</li>

</ul>

</div>

</nav>


<div onmouseleave="closeMenu()" onmouseenter="cancelClose()">

<!-- SECONDARY NAV -->
<div class="secondary-nav">
  <div class="secondary-nav-wrapper">
    <a href="#" onmouseenter="toggleMenu(event, 'products')">Products</a>
    <a href="#" onmouseenter="toggleMenu(event, 'skills')">Skills</a>
	<a href="#" onmouseenter="toggleMenu(event, 'other')">Other</a>
	
  </div>
</div>

<!-- CATEGORY NAV DROPDOWN SECTION -->
<section class="category-menu-section" id="categoryMenu">
  <div class="category-menu-wrapper">

    <div class="category-content active" id="products">

  <div class="category-left">
    <button class="sub-category active" onclick="showSubCategory(event, 'clothesPanel')">Clothes</button>
    <button class="sub-category" onclick="showSubCategory(event, 'phonesPanel')">Phones</button>
    <button class="sub-category" onclick="showSubCategory(event, 'shoesPanel')">Shoes</button>
    <button class="sub-category" onclick="showSubCategory(event, 'furniturePanel')">Furniture</button>
    <button class="sub-category" onclick="showSubCategory(event, 'artPanel')">Handmade and Art</button>
    <button class="sub-category" onclick="showSubCategory(event, 'beautyPanel')">Beauty and lifestyle</button>
  </div>

  <div class="category-right">

    <div class="sub-panel active" id="clothesPanel">
      <div class="sub-column">
        <h6>Women</h6>
        <a href="women.php">Shop all women's clothing</a>
        <a href="#">New In</a>
        <a href="#">Jeans</a>
        <a href="#">Dresses</a>
      </div>

      <div class="sub-column">
        <h6>Men</h6>
        <a href="men.php">Shop all men's clothing</a>
        <a href="#">New In</a>
        <a href="#">Hoodies & Sweats</a>
        <a href="#">T-Shirts & Vests</a>
      </div>
    </div>

    <div class="sub-panel" id="phonesPanel">
      <div class="sub-column">
        <h6>Phones</h6>
        <a href="#">Smartphones</a>
        <a href="#">iPhones</a>
        <a href="#">Samsung</a>
        <a href="#">Accessories</a>
      </div>
    </div>

    <div class="sub-panel" id="shoesPanel">
      <div class="sub-column">
        <h6>Shoes</h6>
        <a href="#">Sneakers</a>
        <a href="#">Formal shoes</a>
        <a href="#">Sandals</a>
        <a href="#">Kids shoes</a>
      </div>
    </div>

    <div class="sub-panel" id="furniturePanel">
      <div class="sub-column">
        <h6>Furniture</h6>
        <a href="#">Beds</a>
        <a href="#">Couches</a>
        <a href="#">Tables</a>
        <a href="#">Chairs</a>
      </div>
    </div>

    <div class="sub-panel" id="artPanel">
      <div class="sub-column">
        <h6>Handmade and Art</h6>
        <a href="#">Paintings</a>
        <a href="#">Crafts</a>
        <a href="#">Decor</a>
        <a href="#">Custom items</a>
      </div>
    </div>

    <div class="sub-panel" id="beautyPanel">
      <div class="sub-column">
        <h6>Beauty and lifestyle</h6>
        <a href="#">Makeup</a>
        <a href="#">Skincare</a>
        <a href="#">Fragrances</a>
        <a href="#">Lifestyle items</a>
      </div>
    </div>

  </div>

</div>

   

    <div class="category-content" id="skills">

  <div class="category-left">
    <button class="sub-category active" onclick="showSubCategory(event, 'designPanel')">Design</button>
    <button class="sub-category" onclick="showSubCategory(event, 'tutoringPanel')">Tutoring</button>
    <button class="sub-category" onclick="showSubCategory(event, 'musicPanel')">Music</button>
    <button class="sub-category" onclick="showSubCategory(event, 'codingPanel')">Coding</button>
    <button class="sub-category" onclick="showSubCategory(event, 'writingPanel')">Writing</button>
  </div>

  <div class="category-right">

    <!-- DESIGN -->
    <div class="sub-panel active" id="designPanel">
      <div class="sub-column">
        <h6>Design</h6>
        <a href="search.php?query=graphic+design">Graphic design</a>
        <a href="search.php?query=logo+design">Logo design</a>
        <a href="#">UI/UX design</a>
        <a href="#">Branding</a>
		<a href="#">Fashion design</a>
      </div>

      <div class="sub-column">
        <h6>Creative</h6>
        <a href="#">Poster design</a>
        <a href="#">Social media content</a>
        <a href="#">Flyers</a>
      </div>
    </div>

    <!-- TUTORING -->
    <div class="sub-panel" id="tutoringPanel">
      <div class="sub-column">
        <h6>Tutoring</h6>
        <a href="search.php?query=math+tutoring">Math tutoring</a>
        <a href="#">Science tutoring</a>
        <a href="#">Programming help</a>
        <a href="#">Exam prep</a>
      </div>
    </div>

    <!-- MUSIC -->
    <div class="sub-panel" id="musicPanel">
      <div class="sub-column">
        <h6>Music</h6>
        <a href="#">Music lessons</a>
        <a href="#">Production</a>
        <a href="#">DJ services</a>
        <a href="#">Recording</a>
      </div>
    </div>

    <!-- CODING -->
    <div class="sub-panel" id="codingPanel">
      <div class="sub-column">
        <h6>Coding</h6>
        <a href="search.php?query=web+development">Web development</a>
        <a href="#">App development</a>
        <a href="#">Fix bugs</a>
        <a href="#">Build websites</a>
      </div>

      <div class="sub-column">
        <h6>Tech Help</h6>
        <a href="#">Setup websites</a>
        <a href="#">Database help</a>
        <a href="#">System support</a>
      </div>
    </div>

    <!-- WRITING -->
    <div class="sub-panel" id="writingPanel">
      <div class="sub-column">
        <h6>Writing</h6>
        <a href="search.php?query=content+writing">Content writing</a>
        <a href="#">CV writing</a>
        <a href="#">Copywriting</a>
        <a href="#">Editing</a>
      </div>
    </div>
     
  </div>

</div>
<div class="category-content" id="other">

  <div class="category-left">

    <button class="sub-category active" onclick="showSubCategory(event, 'vehiclesPanel')">
      Vehicles
    </button>

    <button class="sub-category" onclick="showSubCategory(event, 'propertyPanel')">
      Property
    </button>

    <button class="sub-category" onclick="showSubCategory(event, 'petsPanel')">
      Pets
    </button>

    <button class="sub-category" onclick="showSubCategory(event, 'eventsPanel')">
      Events
    </button>

    <button class="sub-category" onclick="showSubCategory(event, 'gamingPanel')">
      Gaming
    </button>

  </div>

  <div class="category-right">

    <!-- VEHICLES -->
    <div class="sub-panel active" id="vehiclesPanel">

      <div class="sub-column">
        <h6>Vehicles</h6>

        <a href="#">Cars</a>
        <a href="#">Motorcycles</a>
        <a href="#">Trucks</a>
        <a href="#">Vehicle Parts</a>
      </div>

      <div class="sub-column">
        <h6>Popular</h6>

        <a href="#">BMW</a>
        <a href="#">VW</a>
        <a href="#">Toyota</a>
        <a href="#">Mercedes-Benz</a>
      </div>

    </div>

    <!-- PROPERTY -->
    <div class="sub-panel" id="propertyPanel">

      <div class="sub-column">
        <h6>Property</h6>

        <a href="#">Apartments</a>
        <a href="#">Rooms</a>
        <a href="#">Student Accommodation</a>
        <a href="#">Office Space</a>
      </div>

    </div>

    <!-- PETS -->
    <div class="sub-panel" id="petsPanel">

      <div class="sub-column">
        <h6>Pets</h6>

        <a href="#">Dogs</a>
        <a href="#">Cats</a>
        <a href="#">Pet Accessories</a>

      </div>

    </div>

    <!-- EVENTS -->
<div class="sub-panel" id="eventsPanel">
  <div class="sub-column">
    <h6>Events & Tickets</h6>
    <a href="events.php">Concerts</a>
    <a href="events.php">Markets</a>
    <a href="events.php">Parties</a>
    <a href="events.php">Expos</a>
  </div>
</div>

    <!-- GAMING -->
    <div class="sub-panel" id="gamingPanel">

      <div class="sub-column">
        <h6>Gaming</h6>

        <a href="#">Consoles</a>
        <a href="#">Gaming PCs</a>
        <a href="#">Accessories</a>
        <a href="#">Games</a>
      </div>

    </div>

  </div>
</div>
</div>
  </div>
</section>

  <!-- HERO SECTION -->
  <section class="hero">
    <div class="container">
      <h1>Buy and Sell Locally</h1>
      <p>Our first kasi marketplace for goods, skills & side hustles</p>
      <div class="d-grid gap-3 d-md-flex justify-content-md-start mt-4">
        <a href="start-selling.php" class="btn btn-purple btn-lg">Start Selling For Free</a>
      </div>
    </div>
  </section>

<section class="overlap-cards">
  <div class="purple-boxes-wrapper">
    <div class="row g-4">

      <!-- Card 1 -->
      <div class="col-lg-4 col-md-4 col-12">
        <div class="purple-card">
          <i class="fa-solid fa-truck"></i>
          <div class="text-content">
            <div class="top-text">Free delivery & collect</div>
            <div class="bottom-text">On orders within 15 KM radius*</div>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="col-lg-4 col-md-4 col-12">
        <div class="purple-card">
          <i class="fa-solid fa-mobile-screen-button"></i>
          <div class="text-content">
            <div class="top-text">Get the TrustFund app</div>
            <div class="bottom-text">The mall in your pocket</div>
          </div>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="col-lg-4 col-md-4 col-12">
        <div class="purple-card">
          <i class="fa-solid fa-mobile-screen-button"></i>
          <div class="text-content">
            <div class="top-text">Discover over 500 partners</div>
            <div class="bottom-text">Discover now!</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
 
<section class="pt-5 pb-5 bg-white">
  <div class="container about-wrapper">

    <h2 class="about-title">WELCOME TO TRUSTFUND</h2>

    <p class="about-text">
      TrustFund is a community-driven digital marketplace designed to connect people with opportunities within their local environment. 
      It enables individuals, small businesses and groups to buy, sell and promote products, services, skills and opportunities in a simple and accessible way.
    </p>

  </div>
  
</section>
<!-- TEXT BAR SECTION - ONE LINE ONLY -->
<section class="pt-0 pb-2 bg-white move-up">
  <div class="text-bar-wrapper">
    <div class="text-bar">
      <div class="bold-text">SHOP ALL PRODUCTS</div>
      <a href="top-deals.php" class="regular-text deals-link">
    VIEW ALL PRODUCTS
</a>
    </div>
  </div>
</section>
<!-- 6 CLEAN PRODUCT CARDS - 24px under text bar -->

<section class="pt-0 pb-5 bg-white">

<div class="product-cards-wrapper">

<div class="row g-4">
<?php
/* WISHLIST TOGGLE FOR INDEX */
$index_buyer_id = $_SESSION['user_id'] ?? null;
if(isset($_GET['wishlist']) && $index_buyer_id){
    if(!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
    $pid = (int)$_GET['wishlist'];
    if(in_array($pid, $_SESSION['wishlist'])){
        $_SESSION['wishlist'] = array_values(array_diff($_SESSION['wishlist'], [$pid]));
    } else {
        $_SESSION['wishlist'][] = $pid;
    }
    header("Location: index.php");
    exit();
}

$liveProducts = mysqli_query($conn,
    "SELECT seller_items.*,
        ROUND(AVG(reviews.rating),1) AS avg_rating,
        COUNT(reviews.id) AS review_count
    FROM seller_items
    LEFT JOIN reviews ON reviews.product_id = seller_items.id
    WHERE seller_items.product_status='Approved'
    GROUP BY seller_items.id
    ORDER BY seller_items.id DESC
    LIMIT 6"
);
?>

<?php while($product = mysqli_fetch_assoc($liveProducts)): ?>
<?php $inWishlist = isset($_SESSION['wishlist']) && in_array((int)$product['id'], $_SESSION['wishlist']); ?>

<div class="col-lg-2 col-md-4 col-6">
<div class="product-card" style="position:relative;">

    <!-- HEART -->
    <?php if($index_buyer_id && $index_buyer_id != $product['seller_id']): ?>
    <a href="index.php?wishlist=<?= $product['id']; ?>"
       style="position:absolute;top:10px;right:10px;width:30px;height:30px;background:white;
              border-radius:50%;display:flex;align-items:center;justify-content:center;
              box-shadow:0 2px 8px rgba(0,0,0,0.12);text-decoration:none;font-size:14px;z-index:10;
              color:<?= $inWishlist ? '#e74c3c' : '#aaa'; ?>;">
        <i class="fa-<?= $inWishlist ? 'solid' : 'regular'; ?> fa-heart"></i>
    </a>
    <?php endif; ?>

    <a href="product.php?id=<?php echo $product['id']; ?>" class="deal-card-link">
        <img src="<?php echo htmlspecialchars($product['product_image']); ?>" class="product-img">

        <div class="regular"><?php echo htmlspecialchars($product['product_name']); ?></div>

        <!-- STARS -->
        <div style="margin:4px 0 2px;font-size:11px;">
            <?php for($s=1;$s<=5;$s++): ?>
                <i class="fa-<?= $s <= round($product['avg_rating']) ? 'solid' : 'regular'; ?> fa-star"
                   style="color:<?= $s <= round($product['avg_rating']) ? '#f39c12' : '#ddd'; ?>;"></i>
            <?php endfor; ?>
            <span style="font-size:11px;color:#aaa;margin-left:3px;">
                (<?= $product['review_count']; ?>)
            </span>
        </div>

        <div class="bold mt-1">
            <?php
            $price = preg_replace('/[^0-9.]/', '', $product['product_price']);
            echo "R" . number_format((float)$price);
            ?>
        </div>
    </a>

</div>
</div>

<?php endwhile; ?>

</div>

</div>

</section>

<!-- CLOTHING HEADER - ONE LINE ONLY -->
<section class="pt-0 pb-5 bg-white">
  <div class="clothing-header-wrapper">
    <div class="clothing-header">WHAT WE OFFER</div>
  </div>
</section>

<!-- 4 GRAY CARDS SECTION - 24px spacing between cards -->
<section class="pt-0 pb-5 bg-white">
  <div class="four-gray-cards-wrapper">

    <!-- Row 1 -->
<div class="custom-row">

  <a href="women.php" class="category-link">
    <div class="gray-card large women-card"></div>
  </a>

  <a href="men.php" class="category-link">
    <div class="gray-card small men-card"></div>
  </a>

</div>
    <!-- Row 2 -->
    <div class="custom-row">
      <a href="hair.php" class="category-link">
  <div class="gray-card small hair-card"></div>
</a>
 <a href="tech.php" class="category-link">
  <div class="gray-card large tech-card"></div>
</a>
</div>

 <!-- Row 3-->
    <div class="custom-row">
	<a href="bootcamp.php" class="category-link">
  <div class="gray-card large bootcamps-card"></div>
</a>
 <a href="events.php" class="category-link">
  <div class="gray-card small events-card"></div>
</a>
    </div>

  </div>
</section>

<section class="app-section bg-white">
    <div class="app-banner-custom">

      <div class="app-flex">

        <!-- LEFT IMAGE -->
        <div class="app-image">
          <img src="Images/Transparent.png" alt="Transparent">
        </div>

        <!-- RIGHT CONTENT -->
        <div class="app-content">
          <h2>Get TrustFund app</h2>
          <p>
            Kasi's most loved brands, now on the go.<br>
            Available on your favourite app store.
          </p>

          <div class="store-buttons">
            <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg">
            <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg">
            <img src="https://7autospa.com.my/wp-content/uploads/2024/07/huawei-app-gallery-download-button-middle-1.png">
          </div>
        </div>

      </div>

    </div>
  </div>
</section>

<footer class="footer-spacing footer-custom">
  <div class="container text-center">

    <h3 class="footer-title">TrustFund</h3>

    <div class="row mt-4 text-start">

      <div class="col-md-3">
        <h6>Account</h6>
        <a href="account.php">My account</a>
        <a href="track-order.php">Track order</a>
        <a href="returns.php">Returns</a>
      </div>

      <div class="col-md-3">
        <h6>Help</h6>
        <a href="help.php">Help centre</a>
        <a href="contact.php">Contact us</a>
        <a href="returnsPolicy.php">Returns policy</a>
      </div>

      <div class="col-md-3">
        <h6>Company</h6>
        <a href="about.php">About us</a>
        <a href="application.php">Sell on TrustFund</a>
      </div>

      <div class="col-md-3">
        <h6>Terms and policies</h6>
        <a href="Disclosure.php">Responsible disclosure policy</a>
        <a href="advertising.php">Code of advertising practice</a>
      </div>

    </div>

  </div>
</footer>

  <!-- FOOTER -->
  <footer>
    <div class="container">
      <p>© 2026 TrustFund – Proudly Kasi</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  

<script>
let currentOpen = null;

function toggleMenu(event, categoryId) {
  event.preventDefault();

  const menu = document.getElementById("categoryMenu");

  // Close if clicking same
  if (currentOpen === categoryId) {
    menu.style.display = "none";
    currentOpen = null;
    return;
  }

  // Show menu
  menu.style.display = "block";

  // Reset everything
  document.querySelectorAll(".category-content").forEach(content => {
    content.classList.remove("active");
  });

  document.querySelectorAll(".sub-category").forEach(btn => {
    btn.classList.remove("active");
  });

  document.querySelectorAll(".sub-panel").forEach(panel => {
    panel.classList.remove("active");
  });

  // Activate category
  const activeCategory = document.getElementById(categoryId);
  activeCategory.classList.add("active");

  // AUTO-SELECT FIRST ITEM
  const firstButton = activeCategory.querySelector(".sub-category");
  const firstPanel = activeCategory.querySelector(".sub-panel");

  if (firstButton && firstPanel) {
    firstButton.classList.add("active");
    firstPanel.classList.add("active");
  }

  currentOpen = categoryId;
  /* ACTIVE NAV */

document
.querySelectorAll('.secondary-nav a')
.forEach(link => {

    link.classList.remove('active');

});

event.currentTarget.classList.add('active');
}
function closeMenu() {
  document.getElementById("categoryMenu").style.display = "none";
  document.querySelectorAll('.secondary-nav a').forEach(l => l.classList.remove('active'));
  currentOpen = null;
}
</script>


<script>
document.addEventListener("click", function(e) {
  const menu = document.getElementById("categoryMenu");
  const nav = document.querySelector(".secondary-nav");

  // If menu is open
  if (menu.style.display === "block") {

    // If click is NOT inside nav AND NOT inside dropdown
    if (!menu.contains(e.target) && !nav.contains(e.target)) {
      menu.style.display = "none";
	  /* REMOVE ACTIVE NAV */

document
.querySelectorAll('.secondary-nav a')
.forEach(link => {

    link.classList.remove('active');

});

      // remove active content
      document.querySelectorAll(".category-content").forEach(content => {
        content.classList.remove("active");
      });

      currentOpen = null;
    }
  }
});
</script>
<script>
function showSubCategory(event, panelId) {
  event.preventDefault();
  event.stopPropagation();

  document.querySelectorAll(".sub-category").forEach(btn => {
    btn.classList.remove("active");
  });

  document.querySelectorAll(".sub-panel").forEach(panel => {
    panel.classList.remove("active");
  });

  event.target.classList.add("active");
  document.getElementById(panelId).classList.add("active");
}
</script>

</body>
</html>
</body>
</html>