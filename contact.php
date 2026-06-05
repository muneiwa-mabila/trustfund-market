<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us - TrustFund</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      font-family: system-ui, -apple-system, sans-serif;
      background: #ffffff;
      color: #000;
    }

    .top-nav {
      background: #ffffff;
      border-bottom: 1px solid #e5e5e5;
      padding: 6px 0;
    }

    .nav-btn {
      background: #9b59b6;
      color: white !important;
      padding: 6px 14px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
    }

    .navbar {
      background: #ffffff;
      border-bottom: 1px solid #dddddd;
      padding: 10px 0;
    }

    .navbar-brand {
      font-weight: bold;
      font-size: 2rem;
      color: #9b59b6;
      text-decoration: none;
    }

    .nav-link {
      color: #9b59b6 !important;
      font-weight: 500;
      margin: 0 10px;
    }

    .page-header {
      max-width: 900px;
      margin: 40px auto 10px;
      padding: 0 20px;
    }

    .page-title {
      font-size: 28px;
      font-weight: 800;
      color: #000;
    }

    .page-subtitle {
      font-family: Arial, sans-serif;
      font-size: 14px;
      color: #777;
    }

    .page-header::after {
      content: "";
      display: block;
      height: 2px;
      width: 60px;
      background: #9b59b6;
      margin-top: 10px;
      border-radius: 10px;
    }

    .content {
      max-width: 900px;
      margin: 35px auto 60px;
      padding: 0 20px;
    }

    .contact-card {
      background: #ffffff;
      border: 1px solid #eeeeee;
      border-radius: 16px;
      padding: 28px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.04);
      margin-bottom: 22px;
    }

    .contact-card h2 {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 12px;
    }

    .contact-card p,
    .contact-card label {
      font-family: Arial, sans-serif;
      font-size: 16px;
      color: #555;
    }

    .form-control {
      border-radius: 10px;
      padding: 12px;
    }

    .form-control:focus {
      border-color: #9b59b6;
      box-shadow: 0 0 0 0.15rem rgba(155, 89, 182, 0.2);
    }

    .btn-purple {
      background: #9b59b6;
      color: white;
      border: none;
      border-radius: 10px;
      padding: 12px 20px;
      font-weight: 600;
    }

    .btn-purple:hover {
      background: #8e44ad;
      color: white;
    }

    .highlight {
      background: #faf7fc;
      border-left: 4px solid #9b59b6;
      border-radius: 10px;
      padding: 16px;
      color: #4a235a;
    }

    footer {
      background: #000;
      color: white;
      text-align: center;
      padding: 30px 0;
      margin-top: 60px;
    }

    footer span {
      color: #9b59b6;
      font-weight: 700;
    }
  </style>
</head>

<body>

<div class="top-nav">
  <div class="container d-flex justify-content-end">
    <a class="nav-btn" href="seller.html">Sell on TrustFund</a>
  </div>
</div>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="index.php">TrustFund</a>

    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="page-header">
  <h2 class="page-title">Contact Us</h2>
  <p class="page-subtitle">Need help? Send us a message and we’ll get back to you.</p>
</div>

<main class="content">

  <div class="contact-card">
    <h2>Get in Touch</h2>
    <p>
      Use the form below for support, seller applications, reporting suspicious listings, or general questions about TrustFund.
    </p>

    <form action="#" method="POST">
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Subject</label>
        <input type="text" name="subject" class="form-control" required>
      </div>

      <div class="mb-4">
        <label class="form-label">Message</label>
        <textarea name="message" rows="5" class="form-control" required></textarea>
      </div>

      <button type="submit" class="btn btn-purple">Send Message</button>
    </form>
  </div>

  <div class="contact-card">
    <h2>Contact Details</h2>
    <div class="highlight">
      Email: <strong>support@trustfund.co.za</strong><br>
      Security reports: <strong>security@trustfund.co.za</strong>
    </div>
  </div>

</main>

<footer>
  <p>© 2026 <span>TrustFund</span> – Proudly Kasi</p>
</footer>

</body>
</html>