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

/* DELETE DRAFT */
if (isset($_GET['delete'])) {

    $delete_id = $_GET['delete'];

    mysqli_query($conn, "
        DELETE FROM seller_items
        WHERE id = '$delete_id'
        AND seller_id = '$seller_id'
        AND product_status = 'Draft'
    ");

    header("Location: drafts.php");
    exit();
}

/* PUBLISH DRAFT */
if (isset($_GET['publish'])) {

    $publish_id = $_GET['publish'];

    mysqli_query($conn, "
        UPDATE seller_items
        SET product_status = 'Approved'
        WHERE id = '$publish_id'
        AND seller_id = '$seller_id'
    ");

    header("Location: released-products.php");
    exit();
}

/* GET DRAFTS */
$query = mysqli_query($conn, "
    SELECT *
    FROM seller_items
    WHERE seller_id = '$seller_id'
    AND product_status = 'Draft'
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Drafts | TrustFund</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    }

    .menu a{
      display:block;
      text-decoration:none;
      color:white;
      padding:12px 14px;
      border-radius:10px;
      font-size:14px;
      margin-bottom:6px;
      transition:0.2s;
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
      padding-left:28px;
      font-size:13px;
    }

    .signout{
      font-size:13px;
      color:white;
      text-decoration:none;
      padding:12px 14px;
    }

    .main-content{
      flex:1;
      padding:10px 32px;
    }

    .page-header{
      margin-bottom:28px;
    }

    .page-header h1{
      font-size:34px;
      margin-bottom:8px;
    }

    .page-header p{
      color:#777;
      font-size:14px;
    }

    .drafts-grid{
      display:grid;
      grid-template-columns:repeat(auto-fill, minmax(300px, 1fr));
      gap:22px;
    }

    .draft-card{
      background:white;
      border:1px solid #ededed;
      border-radius:22px;
      overflow:hidden;
      box-shadow:0 8px 25px rgba(0,0,0,0.03);
    }

    .draft-image{
      width:100%;
      height:220px;
      object-fit:cover;
      background:#ddd;
    }

    .draft-content{
      padding:22px;
    }

    .category{
      display:inline-block;
      background:#f3e8fa;
      color:#8e44ad;
      padding:6px 10px;
      border-radius:20px;
      font-size:12px;
      font-weight:700;
      margin-bottom:14px;
    }

    .product-name{
      font-size:20px;
      font-weight:800;
      margin-bottom:10px;
    }

    .description{
      color:#777;
      font-size:14px;
      line-height:1.5;
      margin-bottom:18px;
    }

    .price{
      font-size:24px;
      font-weight:800;
      margin-bottom:20px;
    }

    .draft-status{
      display:inline-block;
      background:#fff4d6;
      color:#d97706;
      padding:7px 12px;
      border-radius:20px;
      font-size:12px;
      font-weight:700;
      margin-bottom:18px;
    }

    .btn-row{
      display:flex;
      gap:10px;
    }

    .publish-btn{
      flex:1;
      background:#a65cc5;
      color:white;
      text-decoration:none;
      text-align:center;
      padding:12px;
      border-radius:12px;
      font-size:13px;
      font-weight:700;
      transition:0.2s;
    }

    .publish-btn:hover{
      background:#914ab0;
    }

    .delete-btn{
      flex:1;
      background:#f3f3f3;
      color:#444;
      text-decoration:none;
      text-align:center;
      padding:12px;
      border-radius:12px;
      font-size:13px;
      font-weight:700;
      transition:0.2s;
    }

    .delete-btn:hover{
      background:#e2e2e2;
    }

    .empty-state{
      background:white;
      border:1px dashed #d7b5e7;
      border-radius:24px;
      padding:70px 30px;
      text-align:center;
    }

    .empty-state h2{
      margin-bottom:12px;
      font-size:28px;
    }

    .empty-state p{
      color:#777;
      margin-bottom:22px;
    }

    .empty-btn{
      display:inline-block;
      background:#a65cc5;
      color:white;
      text-decoration:none;
      padding:14px 22px;
      border-radius:14px;
      font-weight:700;
    }

    @media(max-width:900px){

      body{
        padding:12px;
      }

      .dashboard-wrapper{
        flex-direction:column;
      }

      .sidebar{
        width:100%;
        margin-bottom:20px;
      }

      .main-content{
        padding:10px;
      }

    }

  </style>
</head>

<body>

<div class="dashboard-wrapper">

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

<a href="drafts.php" class="active">

<span>
<i class="fa-regular fa-file-lines"></i>
Drafts
</span>

</a>

<a href="released-products.php" class="sub">

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


  <main class="main-content">

    <div class="page-header">
      <h1>Drafts</h1>
      <p>Manage unpublished products and prepare listings before release</p>
    </div>

    <?php if (mysqli_num_rows($query) > 0): ?>

      <div class="drafts-grid">

        <?php while ($item = mysqli_fetch_assoc($query)): ?>

          <div class="draft-card">

            <?php if (!empty($item['product_image'])): ?>

              <img 
                src="uploads/<?php echo htmlspecialchars($item['product_image']); ?>"
                class="draft-image"
              >

            <?php else: ?>

              <div class="draft-image"></div>

            <?php endif; ?>

            <div class="draft-content">

              <div class="category">
                <?php echo htmlspecialchars($item['product_category']); ?>
              </div>

              <div class="product-name">
                <?php echo htmlspecialchars($item['product_name']); ?>
              </div>

              <div class="description">
                <?php echo htmlspecialchars($item['product_description']); ?>
              </div>

              <div class="price">
                R<?php echo number_format($item['product_price'], 2); ?>
              </div>

              <div class="draft-status">
                Draft
              </div>

              <div class="btn-row">

                <a 
                  href="drafts.php?publish=<?php echo $item['id']; ?>"
                  class="publish-btn"
                >
                  Publish
                </a>

                <a 
                  href="drafts.php?delete=<?php echo $item['id']; ?>"
                  class="delete-btn"
                  onclick="return confirm('Delete this draft?')"
                >
                  Delete
                </a>

              </div>

            </div>

          </div>

        <?php endwhile; ?>

      </div>

    <?php else: ?>

      <div class="empty-state">

        <h2>No Drafts Yet</h2>

        <p>
          Your saved drafts will appear here before they are published.
        </p>

        <a href="add-product.php" class="empty-btn">
          Create Draft
        </a>

      </div>

    <?php endif; ?>

  </main>

</div>

</body>
</html>