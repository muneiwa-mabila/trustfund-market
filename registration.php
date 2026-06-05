<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | TrustFund</title>

  <style>

    *{
      margin:0;
      padding:0;
      box-sizing:border-box;
      font-family:Arial, sans-serif;
    }

    body{
      background:#f5f5f5;
      height:100vh;
      display:flex;
      justify-content:center;
      align-items:center;
    }

    .container{
      background:white;
      width:900px;
      height:500px;
      display:flex;
      border-radius:12px;
      overflow:hidden;
      box-shadow:0 5px 20px rgba(0,0,0,0.08);
    }

    .left{
      width:45%;
      background:#d9d9d9;
      display:flex;
      justify-content:center;
      align-items:center;
      color:white;
      font-size:30px;
      font-weight:bold;
    }

    .right{
      width:55%;
      display:flex;
      justify-content:center;
      align-items:center;
    }

    .box{
      width:320px;
      text-align:center;
    }

    h1{
      font-size:32px;
      margin-bottom:10px;
    }

    p{
      color:#777;
      margin-bottom:35px;
    }

    .login-option{
      display:block;
      width:100%;
      padding:15px;
      margin-bottom:15px;
      border:none;
      border-radius:8px;
      font-size:16px;
      font-weight:600;
      cursor:pointer;
      text-decoration:none;
    }

    .seller{
      background:black;
      color:white;
    }

    .buyer{
      background:#ececec;
      color:black;
    }

    .login-option:hover{
      opacity:0.9;
    }

    @media(max-width:768px){

      .container{
        flex-direction:column;
        width:95%;
        height:auto;
      }

      .left{
        width:100%;
        height:180px;
      }

      .right{
        width:100%;
        padding:40px 20px;
      }

    }

  </style>
</head>

<body>

  <div class="container">

    <div class="left">
      TrustFund
    </div>

    <div class="right">

      <div class="box">

        <h1>Login</h1>

        <p>Choose how you want to continue</p>

        <a href="application.php" class="login-option seller">
          Register as Seller
        </a>

        <a href="register.php" class="login-option buyer">
          Register as Buyer
        </a>

      </div>

    </div>

  </div>

</body>
</html>