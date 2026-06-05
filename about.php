<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About Us - TrustFund</title>
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

    .about-hero {
      background: #f4e8f8;
      padding: 80px 0;
      text-align: center;
    }

    .about-hero h1 {
      font-size: 48px;
      font-weight: 800;
      color: #9b59b6;
    }

    .about-hero p {
      color: #555;
      font-size: 18px;
      margin-top: 10px;
    }

    .about-content {
      max-width: 1000px;
      margin: 60px auto;
      padding: 0 20px;
    }

    .about-card {
      background: #ffffff;
      border: 1px solid #eeeeee;
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.04);
      margin-bottom: 24px;
      transition: 0.2s;
    }

    .about-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    }

    .about-card h2 {
      font-size: 30px;
      font-weight: 700;
      margin-bottom: 14px;
      color: #000;
    }

    .about-card p,
    .about-card li {
      font-family: Arial, sans-serif;
      font-size: 16px;
      line-height: 1.7;
      color: #555;
    }

    .about-card ul {
      margin-top: 10px;
      padding-left: 20px;
    }

    .values-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin-top: 20px;
    }

    .value-box {
      background: #f4e8f8;
      padding: 22px;
      border-radius: 14px;
      border-left: 5px solid #9b59b6;
    }

    .value-box h4 {
      color: #9b59b6;
      font-weight: 700;
      margin-bottom: 8px;
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

    @media (max-width: 768px) {
      .values-grid {
        grid-template-columns: 1fr;
      }

      .about-hero h1 {
        font-size: 36px;
      }
    }
	.page-header {
  max-width: 1000px;
  margin: 40px auto 10px;
  padding: 0 20px;
}

.page-title {
  font-size: 28px;
  font-weight: 800;
  color: #000;
  margin-bottom: 5px;
}

.page-subtitle {
  font-family: Arial, sans-serif;
  font-size: 14px;
  color: #777;
  margin-bottom: 20px;
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
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="page-header container">
  <h2 class="page-title">About TrustFund</h2>
  <p class="page-subtitle">Get to know who we are and what we’re building</p>
</div>

<section class="about-content">

  <div class="about-card">
    <h2>About TrustFund</h2>
    <p>
      TrustFund is a community-driven digital marketplace built to connect people with real opportunities within their local environment. 
      It brings together products, services, skills and opportunities into one simple, accessible platform designed for everyday users.
    </p>
    <p>
      Whether you're buying, selling, learning or looking for your next move, TrustFund exists to make local connections easier, safer and more structured.
    </p>
  </div>

  <div class="about-card">
    <h2>Why TrustFund Exists</h2>
    <p>
      In many communities, especially in township environments, people already have the skills, products and hustle needed to succeed. 
      What is often missing is visibility, structure and a trusted platform to connect with others.
    </p>
    <p>
      Many rely on WhatsApp, social media and word of mouth to trade, but these tools are not built for scalable or secure e-commerce. 
      TrustFund was created to bridge that gap by providing a platform that supports how people already operate, while improving trust, organisation and reach.
    </p>
  </div>

  <div class="about-card">
    <h2>The Meaning Behind the Name</h2>
    <p>
      The name “TrustFund” represents a shift in perspective. It is not about inherited wealth, but about recognising the value that already exists within people and communities.
    </p>
    <p>
      Inspired by township culture and aspiration, the name reflects self-reliance, growth and the idea that your skills, your network and your hustle are your real assets. 
      Trust is at the centre of everything: trust between buyers and sellers, and trust in local talent.
    </p>
  </div>

  <div class="about-card">
    <h2>What You Can Do on TrustFund</h2>
    <p>
      TrustFund is more than just a marketplace. It is a space where different forms of opportunity come together:
    </p>
    <ul>
      <li>Buy and sell products within your community</li>
      <li>Offer and discover local services</li>
      <li>Showcase skills and freelance work</li>
      <li>Find jobs, gigs, internships and opportunities</li>
    </ul>
    <p>
      Everything is designed to be simple, mobile-friendly and accessible, even in low-data environments.
    </p>
  </div>

  <div class="about-card">
    <h2>Our Approach</h2>
    <p>
      TrustFund is built around real user needs. The platform focuses on simplicity, trust and accessibility through features like user profiles, direct messaging, structured listings and community-based interactions.
    </p>
    <p>
      Instead of forcing users to adapt to complex systems, TrustFund adapts to how people already live, work and trade.
    </p>
  </div>

  <div class="about-card">
    <h2>Our Values</h2>

    <div class="values-grid">
      <div class="value-box">
        <h4>Trust</h4>
        <p>We believe safe and honest interactions are the foundation of every strong marketplace.</p>
      </div>

      <div class="value-box">
        <h4>Community</h4>
        <p>We are built for local people, local sellers, local talent and local opportunities.</p>
      </div>

      <div class="value-box">
        <h4>Accessibility</h4>
        <p>We aim to keep the platform simple, mobile-friendly and easy to use for everyday users.</p>
      </div>

      <div class="value-box">
        <h4>Opportunity</h4>
        <p>We want to help people turn their skills, products and services into real income and growth.</p>
      </div>
    </div>
  </div>

  <div class="about-card">
    <h2>Our Mission</h2>
    <p>
      To create a trusted digital space where people can turn their skills, products and services into real opportunities.
    </p>
  </div>

  <div class="about-card">
    <h2>Our Vision</h2>
    <p>
      To become a leading marketplace that drives local growth, supports entrepreneurship and expands access to opportunity across communities.
    </p>
  </div>

</section>

<footer>
  <p>© 2026 <span>TrustFund</span> – Proudly Kasi</p>
</footer>

</body>
</html>