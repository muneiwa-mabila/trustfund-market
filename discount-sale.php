<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>20% Off Discount | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#ffffff;
    font-family:Arial,sans-serif;
    color:#111;
}

/* HEADER */

.top-header{
    background:#f5f5f5;
    border-bottom:1px solid #e5e5e5;
    padding:16px 40px;
}

.header-container{
    max-width:1200px;
    margin:auto;

    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo{
    font-size:28px;
    font-weight:800;
}

.header-nav{
    display:flex;
    gap:28px;
}

.header-nav a{
    text-decoration:none;
    color:#111;
    font-size:13px;
}

/* PAGE */

.page-wrapper{
    max-width:1350px;
    margin:auto;
    padding:28px 16px 60px;
}

/* BREADCRUMB */

.breadcrumb{
    font-size:13px;
    margin-bottom:25px;
}

.breadcrumb a{
    color:#666;
    text-decoration:none;
}

.breadcrumb span{
    color:#9b59b6;
}

/* TITLE */

.page-title{
    font-size:30px;
    font-weight:800;
    margin-bottom:22px;
}

/* GRID */

.products-grid{
    display:grid;
    grid-template-columns:repeat(6, 1fr);
    gap:16px;
}

/* CARD */

.product-link{
    text-decoration:none;
    color:inherit;
}

.product-card{
    transition:0.2s;
}

.product-card:hover{
    transform:translateY(-4px);
}

/* IMAGE */

.product-image{
    height:210px;
    background:#efefef;
    border-radius:10px;
    overflow:hidden;
    position:relative;
    margin-bottom:8px;
}

.product-image img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* BADGE */

.product-badge{
    position:absolute;
    top:10px;
    left:10px;

    background:#9b59b6;
    color:white;

    padding:5px 10px;
    border-radius:999px;

    font-size:10px;
    font-weight:700;
}

/* TEXT */

.product-name{
    font-size:11px;
    line-height:1.3;
    margin-bottom:3px;
}

.product-price{
    font-size:11px;
    font-weight:800;
}

.old-price{
    color:#999;
    text-decoration:line-through;
    margin-left:4px;
    font-weight:500;
}

/* RESPONSIVE */

@media(max-width:1000px){

    .products-grid{
        grid-template-columns:repeat(3,1fr);
    }

}

@media(max-width:650px){

    .products-grid{
        grid-template-columns:repeat(2,1fr);
    }

}

</style>

</head>

<body>

<header class="top-header">

<div class="header-container">

<div class="logo">
TrustFund
</div>

<nav class="header-nav">

<a href="#">Orders</a>
<a href="login.php">Login</a>
<a href="register.php">Register</a>
<a href="#">MyAccount</a>

</nav>

</div>

</header>

<div class="page-wrapper">

<div class="breadcrumb">

<a href="index.php">Home</a>

→

<span>20% Off Discount</span>

</div>

<h1 class="page-title">
20% Off Discount
</h1>

<div class="products-grid">

<!-- PRODUCT -->

<a href="product.php?id=1" class="product-link">

<div class="product-card">

<div class="product-image">

<div class="product-badge">
20% OFF
</div>

<img src="Images/winter1.jpg">

</div>

<div class="product-name">
Women Knit Sweater
</div>

<div class="product-price">
R799
<span class="old-price">R999</span>
</div>

</div>

</a>

<!-- PRODUCT -->

<a href="product.php?id=2" class="product-link">

<div class="product-card">

<div class="product-image">

<div class="product-badge">
20% OFF
</div>

<img src="Images/winter2.jpg">

</div>

<div class="product-name">
Winter Jacket
</div>

<div class="product-price">
R1,299
<span class="old-price">R1,599</span>
</div>

</div>

</a>

<!-- PRODUCT -->

<a href="product.php?id=3" class="product-link">

<div class="product-card">

<div class="product-image">

<div class="product-badge">
20% OFF
</div>

<img src="Images/winter3.jpg">

</div>

<div class="product-name">
Oversized Hoodie
</div>

<div class="product-price">
R699
<span class="old-price">R899</span>
</div>

</div>

</a>

<!-- PRODUCT -->

<a href="product.php?id=4" class="product-link">

<div class="product-card">

<div class="product-image">

<div class="product-badge">
20% OFF
</div>

<img src="Images/winter4.jpg">

</div>

<div class="product-name">
Women's Boots
</div>

<div class="product-price">
R1,099
<span class="old-price">R1,399</span>
</div>

</div>

</a>

<!-- PRODUCT -->

<a href="product.php?id=5" class="product-link">

<div class="product-card">

<div class="product-image">

<div class="product-badge">
20% OFF
</div>

<img src="Images/winter5.jpg">

</div>

<div class="product-name">
Brown Coat
</div>

<div class="product-price">
R899
<span class="old-price">R1,199</span>
</div>

</div>

</a>

<!-- PRODUCT -->

<a href="product.php?id=6" class="product-link">

<div class="product-card">

<div class="product-image">

<div class="product-badge">
20% OFF
</div>

<img src="Images/winter6.jpg">

</div>

<div class="product-name">
Winter Sweater
</div>

<div class="product-price">
R499
<span class="old-price">R699</span>
</div>

</div>

</a>

</div>

</div>

</body>
</html>