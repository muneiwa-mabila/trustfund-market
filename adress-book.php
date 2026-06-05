<?php

session_start();

include 'db.php';

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user_id'];

$message = "";

/* SAVE ADDRESS */

if(isset($_POST['save_address'])){

    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);

    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $province = mysqli_real_escape_string($conn, $_POST['province']);

    $city = mysqli_real_escape_string($conn, $_POST['city']);

    $postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);

    $street_address = mysqli_real_escape_string($conn, $_POST['street_address']);

    /* CHECK EXISTING */

    $check = mysqli_query(

        $conn,

        "SELECT *

        FROM addresses

        WHERE user_id='$user_id'"

    );

    if(mysqli_num_rows($check) > 0){

        mysqli_query(

            $conn,

            "UPDATE addresses

            SET 

            full_name='$full_name',

            phone='$phone',

            province='$province',

            city='$city',

            postal_code='$postal_code',

            street_address='$street_address'

            WHERE user_id='$user_id'"

        );

    }else{

        mysqli_query(

            $conn,

            "INSERT INTO addresses (

            user_id,
            full_name,
            phone,
            province,
            city,
            postal_code,
            street_address

            )

            VALUES (

            '$user_id',
            '$full_name',
            '$phone',
            '$province',
            '$city',
            '$postal_code',
            '$street_address'

            )"

        );

    }

    $message = "Address saved successfully.";

}

/* GET ADDRESS */

$query = mysqli_query(

    $conn,

    "SELECT *

    FROM addresses

    WHERE user_id='$user_id'"

);

$address = mysqli_fetch_assoc($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Address Book | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f5f5;
    font-family:Arial,sans-serif;
}

.wrapper{
    max-width:800px;
    margin:60px auto;
    padding:20px;
}

.card{
    background:white;
    border-radius:24px;
    padding:40px;

    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

.page-title{
    font-size:34px;
    font-weight:800;
    margin-bottom:10px;
}

.page-subtitle{
    color:#777;
    margin-bottom:35px;
}

.form-label{
    font-weight:700;
    margin-bottom:8px;
}

.form-control{
    height:55px;
    border-radius:14px;
    margin-bottom:24px;
}

textarea.form-control{
    height:120px;
    padding-top:14px;
}

.save-btn{
    width:100%;
    height:56px;

    border:none;
    border-radius:14px;

    background:#9b59b6;
    color:white;

    font-weight:700;
}

.success-box{
    background:#ecfdf3;
    color:#0f9f5f;

    padding:14px;
    border-radius:12px;

    margin-bottom:20px;
}

.back-link{
    display:inline-block;
    margin-top:20px;

    text-decoration:none;
    color:#777;
}

</style>

</head>

<body>

<div class="wrapper">

<div class="card">

<h1 class="page-title">

Address Book

</h1>

<div class="page-subtitle">

Manage your delivery information.

</div>

<?php if($message != ""): ?>

<div class="success-box">

<?php echo $message; ?>

</div>

<?php endif; ?>

<form method="POST">

<label class="form-label">

Full Name

</label>

<input 
type="text"
name="full_name"
class="form-control"
value="<?php echo htmlspecialchars($address['full_name'] ?? ''); ?>"
required
>

<label class="form-label">

Phone Number

</label>

<input 
type="text"
name="phone"
class="form-control"
value="<?php echo htmlspecialchars($address['phone'] ?? ''); ?>"
required
>

<label class="form-label">

Province

</label>

<input 
type="text"
name="province"
class="form-control"
value="<?php echo htmlspecialchars($address['province'] ?? ''); ?>"
required
>

<label class="form-label">

City

</label>

<input 
type="text"
name="city"
class="form-control"
value="<?php echo htmlspecialchars($address['city'] ?? ''); ?>"
required
>

<label class="form-label">

Postal Code

</label>

<input 
type="text"
name="postal_code"
class="form-control"
value="<?php echo htmlspecialchars($address['postal_code'] ?? ''); ?>"
required
>

<label class="form-label">

Street Address

</label>

<textarea 
name="street_address"
class="form-control"
required
><?php echo htmlspecialchars($address['street_address'] ?? ''); ?></textarea>

<button 
type="submit"
name="save_address"
class="save-btn"
>

Save Address

</button>

</form>

<a href="account.php" class="back-link">

← Back to My Account

</a>

</div>

</div>

</body>
</html>