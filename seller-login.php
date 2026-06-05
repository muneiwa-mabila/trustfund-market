<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Seller Login | TrustFund</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>

    body{
      margin:0;
      background:#f5f5f5;
      font-family:Arial, sans-serif;
    }

    .seller-wrapper{
      min-height:100vh;
      display:flex;
    }

    /* LEFT SIDE */
    .seller-left{
      width:45%;
      background:#d9d9d9;
      display:flex;
      justify-content:center;
      align-items:center;
      position:relative;
    }

    .brand{
      color:white;
      font-size:42px;
      font-weight:800;
      letter-spacing:1px;
    }

    /* RIGHT SIDE */
    .seller-right{
      width:55%;
      background:white;
      display:flex;
      justify-content:center;
      align-items:center;
      padding:40px;
    }

    .login-box{
      width:100%;
      max-width:380px;
    }

    .login-title{
      font-size:36px;
      font-weight:800;
      margin-bottom:5px;
    }

    .login-subtitle{
      color:#777;
      margin-bottom:30px;
      font-size:14px;
    }

    .google-btn{
      width:100%;
      padding:12px;
      border:1px solid #ddd;
      border-radius:10px;
      background:white;
      font-weight:600;
      margin-bottom:25px;
      transition:0.3s;
    }

    .google-btn:hover{
      background:#f8f8f8;
    }

    .divider{
      text-align:center;
      position:relative;
      margin-bottom:25px;
      color:#999;
      font-size:13px;
    }

    .divider::before{
      content:"";
      position:absolute;
      left:0;
      top:50%;
      width:42%;
      height:1px;
      background:#ddd;
    }

    .divider::after{
      content:"";
      position:absolute;
      right:0;
      top:50%;
      width:42%;
      height:1px;
      background:#ddd;
    }

    .form-label{
      font-weight:600;
      font-size:14px;
      margin-bottom:8px;
    }

    .form-control{
      height:52px;
      border-radius:10px;
      margin-bottom:18px;
      border:1px solid #ddd;
    }

    .form-control:focus{
      box-shadow:none;
      border-color:#999;
    }

    .login-btn{
      width:100%;
      height:52px;
      border:none;
      border-radius:10px;
      background:black;
      color:white;
      font-weight:700;
      transition:0.3s;
      margin-top:10px;
    }

    .login-btn:hover{
      background:#222;
    }

    .bottom-text{
      text-align:center;
      margin-top:25px;
      font-size:14px;
      color:#777;
    }

    .bottom-text a{
      text-decoration:none;
      color:#7c3aed;
      font-weight:600;
    }

    .forgot{
      text-align:right;
      margin-top:-8px;
      margin-bottom:18px;
    }

    .forgot a{
      text-decoration:none;
      color:#777;
      font-size:13px;
    }

    .trustfund-alert{
      background:#f8d7da;
      color:#842029;
      padding:12px;
      border-radius:8px;
      margin-bottom:20px;
      font-size:14px;
    }

    @media(max-width:900px){

      .seller-wrapper{
        flex-direction:column;
      }

      .seller-left{
        width:100%;
        height:220px;
      }

      .seller-right{
        width:100%;
      }

    }

  </style>

</head>

<body>

<div class="seller-wrapper">

  <!-- LEFT -->
  <div class="seller-left">
    <div class="brand">
      TrustFund
    </div>
  </div>

  <!-- RIGHT -->
  <div class="seller-right">

    <div class="login-box">

      <?php if (isset($_GET['error'])): ?>
        <div class="trustfund-alert">

          <?php
            if ($_GET['error'] == 'invalid') {
              echo "Incorrect email or password.";
            }
            elseif ($_GET['error'] == 'noaccount') {
              echo "Seller account not found.";
            }
          ?>

        </div>
      <?php endif; ?>

      <h1 class="login-title">
        Sign In
      </h1>

      <p class="login-subtitle">
        Seller account login
      </p>

      <button class="google-btn">
        Continue with Google
      </button>

      <div class="divider">
        OR
      </div>

      <form method="POST" action="seller-login-submit.php">

        <label class="form-label">
          Email Address
        </label>

        <input 
          type="email"
          name="email"
          class="form-control"
          placeholder="name@example.com"
          required
        >

        <label class="form-label">
          Password
        </label>

        <input 
          type="password"
          name="password"
          class="form-control"
          placeholder="Enter password"
          required
        >

        <div class="forgot">
          <a href="forgot_password.php">
            Forgot password?
          </a>
        </div>

        <button type="submit" class="login-btn">
          Login as Seller
        </button>

      </form>

      <div class="bottom-text">
        Don't have a seller account?
        <a href="seller.html">
          Register
        </a>
      </div>

    </div>

  </div>

</div>

</body>
</html>