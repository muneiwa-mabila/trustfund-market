<?php
session_start();
include 'db.php';

if (!isset($_SESSION['seller_id'])) {
    header("Location: seller-login.php");
    exit();
}

$seller_id = $_SESSION['seller_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);
    $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);
    $product_category = mysqli_real_escape_string($conn, $_POST['product_category']);
    $product_status = $_POST['product_status'];

    $image_name = '';

    if (!empty($_FILES['product_image']['name'])) {

        $image_name = time() . "_" . $_FILES['product_image']['name'];

        $target = "uploads/" . $image_name;

        move_uploaded_file($_FILES['product_image']['tmp_name'], $target);
    }

    $sql = "INSERT INTO seller_items (
        seller_id,
        product_name,
        product_description,
        product_price,
        product_image,
        product_category,
        product_status
    )

    VALUES (

        '$seller_id',
        '$product_name',
        '$product_description',
        '$product_price',
        '$image_name',
        '$product_category',
        '$product_status'

    )";

    mysqli_query($conn, $sql);

    header("Location: released-products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Product | TrustFund</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
      border-radius:18px;
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
      border-radius:8px;
      font-size:14px;
      margin-bottom:6px;
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
      padding:10px 28px;
    }

    h1{
      font-size:30px;
      margin-bottom:25px;
    }

    .form-card{
      border:1px solid #eee;
      border-radius:18px;
      padding:28px;
      max-width:800px;
    }

    .form-group{
      margin-bottom:22px;
    }

    label{
      display:block;
      margin-bottom:8px;
      font-size:14px;
      font-weight:700;
    }

    input,
    textarea,
    select{
      width:100%;
      padding:14px;
      border:1px solid #ddd;
      border-radius:10px;
      font-size:14px;
    }

    textarea{
      min-height:140px;
      resize:vertical;
    }

    .btn-row{
      display:flex;
      gap:12px;
      margin-top:25px;
    }

    .publish-btn{
      background:#a65cc5;
      color:white;
      border:none;
      padding:14px 22px;
      border-radius:10px;
      cursor:pointer;
      font-weight:700;
    }

    .draft-btn{
      background:#eee;
      color:#333;
      border:none;
      padding:14px 22px;
      border-radius:10px;
      cursor:pointer;
      font-weight:700;
    }

    .publish-btn:hover{
      background:#914ab0;
    }

    .draft-btn:hover{
      background:#ddd;
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

      .btn-row{
        flex-direction:column;
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

        <a href="seller-dashboard.php">Dashboard</a>

        <a href="#">Product</a>

        <a href="add-product.php" class="sub active">
          Add new product(s)
        </a>

        <a href="analytics.php" class="sub">
          Analytics
        </a>

        <a href="drafts.php" class="sub">
          Drafts
        </a>

        <a href="released-products.php" class="sub">
          Released
        </a>

        <a href="pending-products.php" class="sub">
          Scheduled/Pending
        </a>

        <a href="customer-refunds.php">
          Customer refunds
        </a>

        <a href="revenue.php">
          Revenue
        </a>

        <a href="refunds.php">
          Refunds
        </a>

      </nav>

    </div>

    <a href="logout.php" class="signout">
      Sign out
    </a>

  </aside>

  <main class="main-content">

    <h1>Add Product</h1>

    <div class="form-card">

      <form method="POST" enctype="multipart/form-data">

        <div class="form-group">
          <label>Product Name</label>

          <input 
            type="text"
            name="product_name"
            required
          >
        </div>

        <div class="form-group">
          <label>Description</label>

          <textarea
            name="product_description"
            required
          ></textarea>
        </div>

        <div class="form-group">
          <label>Price</label>

          <input 
            type="number"
            step="0.01"
            name="product_price"
            required
          >
        </div>

        <div class="form-group">
          <label>Category</label>

          <select name="product_category" required>

            <option value="">Select category</option>

            <option value="Products">Products</option>
            <option value="Services">Services</option>
            <option value="Skills">Skills</option>
            <option value="Opportunities">Opportunities</option>

          </select>
        </div>

        <div class="form-group">
          <label>Product Image</label>

          <input 
            type="file"
            name="product_image"
            accept="image/*"
          >
        </div>

        <div class="btn-row">

          <button 
            type="submit"
            name="product_status"
            value="Approved"
            class="publish-btn"
          >
            Publish Product
          </button>

          <button 
            type="submit"
            name="product_status"
            value="Draft"
            class="draft-btn"
          >
            Save as Draft
          </button>

        </div>

      </form>

    </div>

  </main>

</div>

</body>
</html>