<?php

session_start();

include 'db.php';

/* IF ALREADY LOGGED IN */

if(isset($_SESSION['user_id'])){

    if($_SESSION['role'] == 'super_admin' || $_SESSION['role'] == 'mini_admin'){
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();

}

/* AUTO LOGIN */

if(!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user'])){

    $remember_id = $_COOKIE['remember_user'];

    $remember_query = mysqli_query(
        $conn,
        "SELECT * FROM users WHERE user_id='$remember_id'"
    );

    if(mysqli_num_rows($remember_query) > 0){

        $remember_user = mysqli_fetch_assoc($remember_query);

        $_SESSION['user_id']        = $remember_user['user_id'];
        $_SESSION['name']           = $remember_user['name'];
        $_SESSION['email']          = $remember_user['email'];
        $_SESSION['seller_status']  = $remember_user['seller_status'];
        $_SESSION['active_profile'] = $remember_user['active_profile'];
        $_SESSION['role']           = $remember_user['role'];

        if($remember_user['role'] == 'super_admin' || $remember_user['role'] == 'mini_admin'){
            header("Location: admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();

    }

}

$error = null;

/* LOGIN */

if(isset($_POST['login'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query(
        $conn,
        "SELECT * FROM users WHERE email='$email'"
    );

    if(mysqli_num_rows($query) > 0){

        $user = mysqli_fetch_assoc($query);

        if(password_verify($password, $user['password'])){

            /* CHECK OTP VERIFICATION */
            if($user['is_verified'] == 0){
                $error = "Please verify your email before logging in.";
            } else {

                session_regenerate_id(true);

                $_SESSION['user_id']        = $user['user_id'];
                $_SESSION['name']           = $user['name'];
                $_SESSION['email']          = $user['email'];
                $_SESSION['seller_status']  = $user['seller_status'];
                $_SESSION['active_profile'] = $user['active_profile'];
                $_SESSION['role']           = $user['role'];

                /* REMEMBER ME */
                if(isset($_POST['remember_me'])){
                    setcookie("remember_user", $user['user_id'], time() + (86400 * 30), "/");
                }

                /* REDIRECT BY ROLE */
                if($user['role'] == 'super_admin' || $user['role'] == 'mini_admin'){
                    header("Location: admin/dashboard.php");
                    exit();
                }

                /* CHECKOUT REDIRECT */
                if(isset($_SESSION['checkout_redirect'])){
                    unset($_SESSION['checkout_redirect']);
                    header("Location: checkout.php");
                } else {
                    header("Location: index.php");
                }
                exit();

            }

        } else {
            $error = "Incorrect password";
        }

    } else {
        $error = "Account not found";
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | TrustFund</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#f5f5f5;
    font-family:Arial,sans-serif;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
}

.login-card{
    width:450px;
    background:white;
    border-radius:22px;
    padding:45px;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}

.logo{
    font-size:36px;
    font-weight:800;
    color:#9b59b6;
    text-align:center;
    margin-bottom:10px;
}

.subtitle{
    text-align:center;
    color:#777;
    margin-bottom:35px;
    font-size:14px;
}

.form-label{
    font-weight:700;
    margin-bottom:8px;
    color:#333;
}

.form-control{
    height:54px;
    border-radius:14px;
    border:1px solid #ddd;
    margin-bottom:22px;
    padding:0 16px;
    font-size:14px;
}

.form-control:focus{
    border-color:#9b59b6 !important;
    box-shadow:0 0 0 3px rgba(155,89,182,0.15) !important;
}

.login-options{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:-6px;
    margin-bottom:24px;
}

.remember-me{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:13px;
    color:#555;
    cursor:pointer;
}

.remember-me input{
    accent-color:#9b59b6;
}

.forgot-password{
    text-decoration:none;
    font-size:13px;
    font-weight:700;
    color:#9b59b6;
}

.forgot-password:hover{
    text-decoration:underline;
}

.login-btn{
    width:100%;
    height:54px;
    border:none;
    border-radius:14px;
    background:#9b59b6;
    color:white;
    font-size:15px;
    font-weight:700;
    transition:0.2s;
}

.login-btn:hover{
    background:#8a47ab;
}

.error-box{
    background:#fff0f0;
    color:#e53935;
    padding:12px 16px;
    border-radius:10px;
    margin-bottom:20px;
    font-size:13px;
    font-weight:600;
}

.bottom-link{
    text-align:center;
    margin-top:22px;
    font-size:14px;
}

.bottom-link a{
    color:#9b59b6;
    text-decoration:none;
    font-weight:700;
}

</style>

</head>

<body>

<div class="login-card">

    <div class="logo">TrustFund</div>

    <div class="subtitle">Login to your account</div>

    <?php if(!empty($error)): ?>
    <div class="error-box"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">

        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" required>

        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>

        <div class="login-options">
            <label class="remember-me">
                <input type="checkbox" name="remember_me">
                <span>Remember me</span>
            </label>
            <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
        </div>

        <button type="submit" name="login" class="login-btn">Login</button>

    </form>

    <div class="bottom-link">
        Don't have an account? <a href="register.php">Register</a>
    </div>

</div>

</body>
</html>