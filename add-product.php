<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$seller_id = $_SESSION['user_id'];

$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$seller_id'");
$currentUser = mysqli_fetch_assoc($userQuery);

if($currentUser['seller_status'] != 'approved'){
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_name        = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);
    $product_price       = mysqli_real_escape_string($conn, $_POST['product_price']);
    $product_category    = mysqli_real_escape_string($conn, $_POST['product_category']);
    $product_status      = $_POST['product_status'];

    $image_name = '';

    if (!empty($_FILES['product_image']['name'])) {

        $allowed_exts  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        $ext  = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        $mime = mime_content_type($_FILES['product_image']['tmp_name']);

        if (in_array($ext, $allowed_exts) && in_array($mime, $allowed_mimes)) {
            $image_name = "uploads/" . time() . "_" . uniqid() . "." . $ext;
            move_uploaded_file($_FILES['product_image']['tmp_name'], $image_name);
        }
    }

    $sql = "INSERT INTO seller_items (
        seller_id,
        product_name,
        product_description,
        product_price,
        product_image,
        product_category,
        product_status
    ) VALUES (
        '$seller_id',
        '$product_name',
        '$product_description',
        '$product_price',
        '$image_name',
        '$product_category',
        '$product_status'
    )";

    mysqli_query($conn, $sql);

    if ($product_status == "Draft") {
        header("Location: drafts.php");
    } else {
        header("Location: released-products.php");
    }

    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Product | TrustFund</title>
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
    .form-card{
      background:white;
      border:1px solid #ededed;
      border-radius:24px;
      padding:34px;
      max-width:900px;
      box-shadow:0 8px 25px rgba(0,0,0,0.04);
    }
    .form-grid{
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:22px;
    }
    .full-width{
      grid-column:1 / -1;
    }
    .form-group{
      margin-bottom:6px;
    }
    label{
      display:block;
      margin-bottom:10px;
      font-size:14px;
      font-weight:700;
      color:#333;
    }
    input,
    textarea,
    select{
      width:100%;
      padding:15px;
      border:1px solid #ddd;
      border-radius:14px;
      font-size:14px;
      transition:0.2s;
      background:#fafafa;
    }
    input:focus,
    textarea:focus,
    select:focus{
      border-color:#a65cc5;
      outline:none;
      background:white;
      box-shadow:0 0 0 4px rgba(166,92,197,0.1);
    }
    textarea{
      min-height:150px;
      resize:vertical;
    }
    .file-upload{
      border:2px dashed #d7b5e7;
      padding:35px;
      border-radius:18px;
      text-align:center;
      background:#faf5fd;
      transition:0.2s;
    }
    .file-upload:hover{
      background:#f5ebfb;
    }
    .file-upload input{
      border:none;
      background:none;
      padding:0;
      margin-top:10px;
    }
    .upload-text{
      color:#7a7a7a;
      font-size:14px;
    }
    .preview-img {
      max-width: 100%;
      max-height: 160px;
      border-radius: 10px;
      margin-top: 12px;
      display: none;
      object-fit: cover;
    }
    .btn-row{
      display:flex;
      gap:14px;
      margin-top:30px;
    }
    .publish-btn{
      background:#a65cc5;
      color:white;
      border:none;
      padding:15px 26px;
      border-radius:14px;
      cursor:pointer;
      font-weight:700;
      font-size:14px;
      transition:0.2s;
    }
    .publish-btn:hover{
      background:#914ab0;
      transform:translateY(-1px);
    }
    .draft-btn{
      background:#efefef;
      color:#333;
      border:none;
      padding:15px 26px;
      border-radius:14px;
      cursor:pointer;
      font-weight:700;
      font-size:14px;
      transition:0.2s;
    }
    .draft-btn:hover{
      background:#ddd;
    }
    @media(max-width:900px){
      body{ padding:12px; }
      .dashboard-wrapper{ flex-direction:column; }
      .sidebar{ width:100%; margin-bottom:20px; }
      .main-content{ padding:10px; }
      .form-grid{ grid-template-columns:1fr; }
      .btn-row{ flex-direction:column; }
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
          <span><i class="fa-solid fa-house"></i> Dashboard</span>
          <i class="fa-solid fa-chevron-down"></i>
        </a>

        <a href="#">
          <span><i class="fa-solid fa-tag"></i> Product</span>
          <i class="fa-solid fa-chevron-up"></i>
        </a>

        <a href="add-product.php" class="active">
          <span><i class="fa-solid fa-plus"></i> Add new product(s)</span>
          <i class="fa-regular fa-circle-dot"></i>
        </a>

        <a href="analytics.php" class="sub">
          <span><i class="fa-solid fa-chart-line"></i> Analytics</span>
        </a>

        <a href="drafts.php" class="sub">
          <span><i class="fa-regular fa-file-lines"></i> Drafts</span>
        </a>

        <a href="released-products.php" class="sub">
          <span><i class="fa-solid fa-box-open"></i> Released</span>
        </a>

        <a href="pending-products.php" class="sub">
          <span><i class="fa-regular fa-clock"></i> Scheduled/Pending</span>
          <span class="pending-count"><?php echo $pendingProducts ?? ''; ?></span>
        </a>

        <a href="revenue.php">
          <span><i class="fa-solid fa-wallet"></i> Revenue</span>
          <i class="fa-solid fa-chevron-down"></i>
        </a>

        <a href="refunds.php">
          <span><i class="fa-solid fa-receipt"></i> Refunds</span>
          <i class="fa-solid fa-chevron-down"></i>
        </a>

        <a href="seller-messages.php">
          <span><i class="fa-solid fa-comments"></i> Messages</span>
        </a>

        <a href="seller-profile.php?id=<?php echo $_SESSION['user_id']; ?>">
          <span><i class="fa-solid fa-user"></i> Public Profile</span>
        </a>

        <a href="seller-orders.php">
          <span><i class="fa-solid fa-box"></i> Seller Orders</span>
        </a>

      </nav>

      <a href="index.php" class="signout">
        <span><i class="fa-solid fa-store"></i> Switch Profile</span>
      </a>

      <a href="logout.php" class="signout">
        <span><i class="fa-solid fa-right-from-bracket"></i> Sign out</span>
      </a>
    </div>
  </aside>

  <main class="main-content">

    <div class="page-header">
      <h1>Add Product</h1>
      <p>Create and publish a new listing on TrustFund</p>
    </div>

    <div class="form-card">

      <form method="POST" enctype="multipart/form-data">

        <div class="form-grid">

          <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="product_name" placeholder="Enter product name" required>
          </div>

          <div class="form-group">
            <label>Category</label>

            <!-- LISTING TYPE -->
            <div style="margin-bottom:20px;">
              <label style="font-weight:700; margin-bottom:8px; display:block;">Listing Type</label>
              <div style="display:flex; gap:12px;">

                <label style="flex:1; border:2px solid #ddd; border-radius:12px; padding:16px; cursor:pointer; text-align:center; transition:0.2s;" id="label_product">
                  <input type="radio" name="product_type" value="Product" checked onchange="toggleType()" style="display:none;">
                  <div style="font-size:22px; margin-bottom:6px;">&#128722;</div>
                  <div style="font-weight:700; font-size:14px;">Product</div>
                  <div style="font-size:12px; color:#777;">Physical or digital item</div>
                </label>

                <label style="flex:1; border:2px solid #ddd; border-radius:12px; padding:16px; cursor:pointer; text-align:center; transition:0.2s;" id="label_service">
                  <input type="radio" name="product_type" value="Service" onchange="toggleType()" style="display:none;">
                  <div style="font-size:22px; margin-bottom:6px;">&#9881;</div>
                  <div style="font-weight:700; font-size:14px;">Service</div>
                  <div style="font-size:12px; color:#777;">Skill or service offering</div>
                </label>

              </div>
              <p id="service_note" style="display:none; font-size:12px; color:#9b59b6; margin-top:8px; padding:10px; background:#f8f0fc; border-radius:8px;">
                &#128172; Buyers will message you first to discuss and agree on a price before checkout.
              </p>
            </div>

            <select name="product_category" required>
              <option value="">Select category</option>
              <optgroup label="Clothing &amp; Fashion">
                <option value="Women">Women</option>
                <option value="Men">Men</option>
              </optgroup>
              <optgroup label="Beauty &amp; Wellness">
                <option value="Hair">Hair</option>
                <option value="Nails">Nails</option>
                <option value="Skincare">Skincare</option>
              </optgroup>
              <optgroup label="Technology">
                <option value="Tech">Tech</option>
                <option value="Phones">Phones</option>
                <option value="Gaming">Gaming</option>
              </optgroup>
              <optgroup label="Skills &amp; Services">
                <option value="Skills">Skills</option>
                <option value="Design">Design</option>
                <option value="Tutoring">Tutoring</option>
                <option value="Coding">Coding</option>
                <option value="Music">Music</option>
                <option value="Writing">Writing</option>
              </optgroup>
              <optgroup label="Other">
                <option value="Furniture">Furniture</option>
                <option value="Shoes">Shoes</option>
                <option value="Handmade &amp; Art">Handmade &amp; Art</option>
                <option value="Vehicles">Vehicles</option>
                <option value="Property">Property</option>
                <option value="Pets">Pets</option>
                <option value="Events">Events &amp; Tickets</option>
                <option value="Bootcamp">Bootcamp</option>
                <option value="Opportunities">Opportunities</option>
              </optgroup>
            </select>
          </div>

          <div class="form-group full-width">
            <label>Description</label>
            <textarea name="product_description" placeholder="Write product description..." required></textarea>
          </div>

          <div class="form-group">
            <label>Price</label>
            <input type="number" step="0.01" name="product_price" placeholder="0.00" required>
          </div>

          <div class="form-group">
            <label>Upload Image</label>
            <div class="file-upload">
              <div class="upload-text">&#128247; Upload product image (JPG, PNG, WEBP, GIF)</div>
              <input type="file" name="product_image" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewImage(event)">
              <img id="imagePreview" class="preview-img" src="" alt="Preview">
            </div>
          </div>

        </div>

        <div class="btn-row">
          <button type="submit" name="product_status" value="Pending" class="publish-btn">
            Submit For Approval
          </button>
          <button type="submit" name="product_status" value="Draft" class="draft-btn">
            Save as Draft
          </button>
        </div>

      </form>
    </div>
  </main>
</div>

<script>
function toggleType(){
    const isService = document.querySelector('input[name="product_type"][value="Service"]').checked;
    document.getElementById('label_product').style.borderColor = isService ? '#ddd' : '#9b59b6';
    document.getElementById('label_service').style.borderColor = isService ? '#9b59b6' : '#ddd';
    document.getElementById('service_note').style.display = isService ? 'block' : 'none';
}
document.querySelector('input[name="product_type"][value="Product"]').checked = true;
toggleType();

function previewImage(event){
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    if(file){
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}
</script>

</body>
</html>