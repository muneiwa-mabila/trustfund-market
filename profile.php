<?php

session_start();

include 'db.php';

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user_id'];

$message = "";

/* GET USER */

$query = mysqli_query(

    $conn,

    "SELECT *

    FROM users

    WHERE user_id='$user_id'"

);

$user = mysqli_fetch_assoc($query);

/* UPDATE */

if(isset($_POST['save_profile'])){

    $name = mysqli_real_escape_string($conn, $_POST['name']);

    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    mysqli_query(

        $conn,

        "UPDATE users

        SET 

        name='$name',

        email='$email',

        phone='$phone'

        WHERE user_id='$user_id'"

    );

    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;

    $message = "Profile updated successfully.";

    header("Refresh:1");

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Personal Details | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f5f5;
    font-family:Arial,sans-serif;
}

.wrapper{
    max-width:700px;
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

Personal Details

</h1>

<div class="page-subtitle">

Manage your account information.

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
name="name"
class="form-control"
value="<?php echo htmlspecialchars($user['name']); ?>"
required
>

<label class="form-label">

Email Address

</label>

<input 
type="email"
name="email"
class="form-control"
value="<?php echo htmlspecialchars($user['email']); ?>"
required
>

<label class="form-label">

Phone Number

</label>

<input 
type="text"
name="phone"
class="form-control"
value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
>

<button 
type="submit"
name="save_profile"
class="save-btn"
>

Save Changes

</button>

</form>

<a href="account.php" class="back-link">

← Back to My Account

</a>

</div>

</div>

</body>
</html>