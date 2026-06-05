<?php
$type = $_GET['type'] ?? 'individual';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sell on TrustFund</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{
    background:#f7f7f7;
    font-family:Arial,sans-serif;
}
.form-card{
    max-width:700px;
    margin:60px auto;
    background:white;
    padding:45px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}
.form-title{
    font-size:34px;
    font-weight:800;
    color:#111;
    margin-bottom:10px;
    text-align:center;
}
.form-subtitle{
    text-align:center;
    color:#777;
    margin-bottom:40px;
    font-size:14px;
}
.section-title{
    font-size:20px;
    font-weight:700;
    margin-bottom:24px;
    color:#9b59b6;
}
.form-label{
    font-weight:600;
    margin-bottom:8px;
    color:#333;
}
.form-control,
.form-select{
    height:52px;
    border-radius:12px;
    border:1px solid #ddd;
    padding:12px 16px;
    margin-bottom:22px;
    font-size:14px;
}
.form-control:focus,
.form-select:focus{
    border-color:#9b59b6 !important;
    box-shadow:0 0 0 3px rgba(155,89,182,0.15) !important;
    outline:none;
}
.btn-purple{
    width:100%;
    height:54px;
    border:none;
    border-radius:12px;
    background:#9b59b6;
    color:white;
    font-size:15px;
    font-weight:700;
    transition:0.2s;
}
.btn-purple:hover{
    background:#8a47ab;
}
</style>
</head>
<body>
<div class="form-card">
<h1 class="form-title">Sell on TrustFund</h1>
<p class="form-subtitle">Apply as an individual seller</p>

<form method="POST" action="submit.php" enctype="multipart/form-data">

<h5 class="section-title">Seller Information</h5>

<!-- FULL NAME -->
<div class="mb-3">
<label class="form-label">Full Name</label>
<input type="text" name="name" class="form-control" required>
</div>

<!-- PHONE -->
<div class="mb-3">
<label class="form-label">Phone Number</label>
<input type="text" name="phone" class="form-control" required>
</div>

<!-- EMAIL -->
<div class="mb-3">
<label class="form-label">Email Address</label>
<input type="email" name="email" class="form-control" required>
</div>

<!-- ID NUMBER -->
<div class="mb-3">
<label class="form-label">South African ID Number</label>
<input type="text" name="id_number" class="form-control" maxlength="13" placeholder="0000000000000" required>
</div>

<!-- CATEGORY -->
<div class="mb-3">
<label class="form-label">Category</label>
<select name="category" class="form-select" required>
<option value="">Select category</option>
<option value="Products">Products</option>
<option value="Services">Services</option>
<option value="Skills">Skills</option>
<option value="Other">Other</option>
</select>
</div>

<!-- ID DOCUMENT UPLOAD -->
<div class="mb-3">
<label class="form-label">Upload ID Document</label>
<div style="border:2px dashed #d7b5e7; border-radius:12px; padding:28px; text-align:center; background:#faf5fd;">
    <div style="font-size:28px; margin-bottom:8px;">📄</div>
    <div style="font-size:13px; color:#888; margin-bottom:10px;">Upload a clear photo or scan of your SA ID (JPG, PNG or PDF)</div>
    <input type="file" name="id_document" accept="image/jpeg,image/png,application/pdf" required
           style="border:none; background:none; padding:0; width:100%;">
</div>
</div>

<!-- BUTTON -->
<button type="submit" class="btn-purple">Submit Application</button>

</form>
</div>
</body>
</html>