<?php

session_start();

include 'db.php';

if(!isset($_SESSION['user_id'])){

    header("Location: index.php");
    exit();

}

$seller_id = $_SESSION['user_id'];

$product_id = $_GET['id'] ?? 0;

/* GET PRODUCT */

$query = mysqli_query(

    $conn,

    "SELECT *

    FROM seller_items

    WHERE id='$product_id'

    AND seller_id='$seller_id'"

);

$product = mysqli_fetch_assoc($query);

if(!$product){

    die("Product not found.");

}

/* UPDATE IMAGE */

if(isset($_POST['update_image'])){

    if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0){

        $imageName = time() . "_" . $_FILES['product_image']['name'];

        $tmpName = $_FILES['product_image']['tmp_name'];

        $folder = "uploads/" . $imageName;

        move_uploaded_file($tmpName, $folder);

        mysqli_query(

            $conn,

            "UPDATE seller_items

            SET product_image='$folder'

            WHERE id='$product_id'"

        );

        header("Location: released-products.php");
        exit();

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Product Image</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f5f5;
    font-family:Arial,sans-serif;
}

.wrapper{
    max-width:700px;
    margin:60px auto;
}

.card{
    background:white;
    border:none;
    border-radius:24px;
    padding:35px;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

.product-image{
    width:220px;
    height:220px;
    object-fit:cover;
    border-radius:18px;
    margin-bottom:25px;
    border:1px solid #eee;
}

.btn-save{
    background:#9b59b6;
    border:none;
    color:white;
    padding:14px 22px;
    border-radius:12px;
    font-weight:700;
}

.back-link{
    display:inline-block;
    margin-top:20px;
    color:#777;
    text-decoration:none;
}

</style>

</head>

<body>

<div class="wrapper">

<div class="card">

<h2 class="fw-bold mb-4">

Edit Product Picture

</h2>

<img 
src="<?php echo htmlspecialchars($product['product_image']); ?>"
class="product-image"
>

<form method="POST" enctype="multipart/form-data">

<div class="mb-4">

<label class="form-label fw-bold">

Upload New Picture

</label>

<input 
type="file"
name="product_image"
class="form-control"
required
>

</div>

<button 
type="submit"
name="update_image"
class="btn-save"
>

Save Changes

</button>

</form>

<a href="released-products.php" class="back-link">

← Back to Released Products

</a>

</div>

</div>

</body>
</html>