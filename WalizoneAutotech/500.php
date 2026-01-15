<?php
// Include database connection
require_once 'config/db.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['customer_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Server Error - Walizone Autotech</title>
  <meta name="description" content="We're experiencing some technical difficulties at Walizone Autotech.">
  <meta name="theme-color" content="#0d47a1">
  <link rel="icon" href="images/favicon.svg" type="image/svg+xml">
  <link rel="manifest" href="manifest.json">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #0d47a1;
      --primary-light: #5472d3;
      --primary-dark: #002171;
      --secondary-color: #ff6f00;
      --secondary-light: #ffa040;
      --secondary-dark: #c43e00;
      --text-light: #ffffff;
      --text-dark: #333333;
      --background-light: #f5f5f5;
      --background-white: #ffffff;
      --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      --transition: all 0.3s ease;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      scroll-behavior: smooth;
    }
    
    body {
      font-family: 'Roboto', sans-serif;
      background-color: var(--background-light);
      color: var(--text-dark);
      line-height: 1.6;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    
    h1, h2, h3, h4, h5, h6 {
      font-family: 'Montserrat', sans-serif;
      font-weight: 600;
      color: var(--primary-color);
    }
    
    /* Top Bar */
    .top-bar {
      background-color: var(--primary-dark);
      color: var(--text-light);
      padding: 10px 0;
      font-size: 0.9rem;
    }
    
    .top-bar-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }
    
    .top-contact {
      display: flex;
      gap: 20px;
    }
    
    .top-contact a {
      color: var(--text-light);
      text-decoration: none;
      display: flex;
      align-items: center;
    }
    
    .top-contact i {
      margin-right: 5px;
    }
    
    .social-icons {
      display: flex;
      gap: 15px;
    }
    
    .social-icons a {
      color: var(--text-light);
      font-size: 1.1rem;
      transition: var(--transition);
    }
    
    .social-icons a:hover {
      color: var(--secondary-color);
    }
    
    /* Header */
    .main-header {
      background-color: var(--background-white);
      box-shadow: var(--shadow);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    
    .header-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      padding: 15px 20px;
    }
    
    .logo {
      display: flex;
      align-items: center;
      text-decoration: none;
    }
    
    .logo img {
      height: 50px;
      margin-right: 10px;
    }
    
    .logo-text {
      display: flex;
      flex-direction: column;
    }
    
    .logo-name {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary-color);
    }
    
    .logo-tagline {
      font-size: 0.8rem;
      color: var(--text-dark);
    }
    
    .main-nav ul {
      display: flex;
      list-style: none;
      gap: 30px;
    }
    
    .main-nav a {
      text-decoration: none;
      color: var(--text-dark);
      font-weight: 500;
      font-size: 1rem;
      position: relative;
      padding: 5px 0;
      transition: var(--transition);
    }
    
    .main-nav a:hover {
      color: var(--primary-color);
    }
    
    .main-nav a::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 2px;
      background-color: var(--primary-color);
      transition: var(--transition);
    }
    
    .main-nav a:hover::after {
      width: 100%;
    }
    
    .mobile-menu-btn {
      display: none;
      background: none;
      border: none;
      font-size: 1.5rem;
      color: var(--primary-color);
      cursor: pointer;
    }
    
    /* Error Page Styles */
    .error-container {
      flex: 1;
      max-width: 800px;
      margin: 60px auto;
      padding: 0 20px;
      text-align: center;
    }
    
    .error-code {
      font-size: 8rem;
      font-weight: 700;
      color: var(--secondary-color);
      line-height: 1;
      margin-bottom: 20px;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .error-title {
      font-size: 2.5rem;
      margin-bottom: 20px;
      color: var(--primary-color);
    }
    
    .error-message {
      font-size: 1.2rem;
      margin-bottom: 30px;
      color: var(--text-dark);
    }
    
    .error-actions {
      display: flex;
      flex-direction: column;
      gap: 15px;
      align-items: center;
      margin-top: 30px;
    }
    
    .btn {
      display: inline-block;
      padding: 12px 25px;
      background-color: var(--primary-color);
      color: var(--text-light);
      text-decoration: none;
      border-radius: 5px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: var(--transition);
      border: none;
      cursor: pointer;
      font-size: 1rem;
    }
    
    .btn:hover {
      background-color: var(--primary-dark);
      transform: translateY(-3px);
    }
    
    .btn-secondary {
      background-color: var(--secondary-color);
    }
    
    .btn-secondary:hover {
      background-color: var(--secondary-dark);
    }
    
    .btn-outline {
      background-color: transparent;
      color: var(--primary-color);
      border: 2px solid var(--primary-color);
    }
    
    .btn-outline:hover {
      background-color: var(--primary-color);
      color: var(--text-light);
    }
    
    .error-links {
      margin-top: 40px;
    }
    
    .error-links h3 {
      margin-bottom: 15px;
      font-size: 1.3rem;
    }
    
    .quick-links {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 15px;
      margin-top: 20px;
    }
    
    .quick-links a {
      display: inline-block;
      padding: 10px 20px;
      background-color: var(--background-white);
      color: var(--primary-color);
      text-decoration: none;
      border-radius: 5px;
      font-weight: 500;
      transition: var(--transition);
      box-shadow: var(--shadow);
    }
    
    .quick-links a:hover {
      background-color: var(--primary-light);
      color: var(--text-light);
      transform: translateY(-3px);
    }
    
    .mechanic-animation {
      margin: 40px 0;
      position: relative;
      height: 150px;
    }
    
    .mechanic {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      bottom: 0;
      animation: fix 2s ease-in-out infinite;
    }
    
    @keyframes fix {
      0%, 100% {
        transform: translateX(-50%) translateY(0);
      }
      50% {
        transform: translateX(-50%) translateY(-10px);
      }
    }
    
    /* Footer */
    .footer {
      background-color: var(--primary-dark);
      color: var(--text-light);
      padding: 50px 0 0;
      margin-top: auto;
    }
    
    .footer-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }
    
    .footer-column h3 {
      color: var(--text-light);
      margin-bottom: 20px;
      position: relative;
      padding-bottom: 10px;
    }
    
    .footer-column h3::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 50px;
      height: 2px;
      background-color: var(--secondary-color);
    }
    
    .footer-links {
      list-style: none;
    }
    
    .footer-links li {
      margin-bottom: 10px;
    }
    
    .footer-links a {
      color: #bbb;
      text-decoration: none;
      transition: var(--transition);
    }
    
    .footer-links a:hover {
      color: var(--text-light);
      padding-left: 5px;
    }
    
    .footer-contact-item {
      display: flex;
      margin-bottom: 15px;
    }
    
    .footer-contact-icon {
      margin-right: 10px;
      color: var(--secondary-color);
    }
    
    .footer-social {
      display: flex;
      gap: 15px;
      margin-top: 20px;
    }
    
    .footer-social a {
      width: 40px;
      height: 40px;
      background-color: rgba(255, 255, 255, 0.1);
      color: var(--text-light);
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 50%;
      transition: var(--transition);
    }
    
    .footer-social a:hover {
      background-color: var(--secondary-color);
      transform: translateY(-5px);
    }
    
    .footer-bottom {
      background-color: rgba(0, 0, 0, 0.2);
      padding: 20px 0;
      margin-top: 50px;
      text-align: center;
    }
    
    /* Responsive Styles */
    @media (max-width: 992px) {
      .error-code {
        font-size: 6rem;
      }
      
      .error-title {
        font-size: 2rem;
      }
    }
    
    @media (max-width: 768px) {
      .top-bar-container {
        flex-direction: column;
        gap: 10px;
      }
      
      .main-nav {
        display: none;
      }
      
      .mobile-menu-btn {
        display: block;
      }
      
      .error-code {
        font-size: 5rem;
      }
      
      .error-title {
        font-size: 1.8rem;
      }
      
      .quick-links {
        flex-direction: column;
        align-items: center;
      }
      
      .quick-links a {
        width: 100%;
        text-align: center;
      }
      
      .error-actions {
        flex-direction: column;
      }
    }
    
    @media (max-width: 576px) {
      .top-contact {
        flex-direction: column;
        gap: 5px;
      }
      
      .error-code {
        font-size: 4rem;
      }
      
      .error-title {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <!-- Top Bar -->
  <div class="top-bar">
    <div class="top-bar-container">
      <div class="top-contact">
        <a href="tel:+260976664017"><i class="fas fa-phone"></i> 0976664017</a>
        <a href="mailto:mwakamule@gmail.com"><i class="fas fa-envelope"></i> mwakamule@gmail.com</a>
        <span><i class="fas fa-clock"></i> Mon-Sat: 8:00AM - 5:00PM</span>
      </div>
      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-linkedin-in"></i></a>
      </div>
    </div>
  </div>

  <!-- Header -->
  <header class="main-header">
    <div class="header-container">
      <a href="index.php" class="logo">
        <img src="images/logo.svg" alt="Walizone Autotech Logo">
        <div class="logo-text">
          <span class="logo-name">Walizone Autotech</span>
          <span class="logo-tagline">Driven by Quality, Powered by Trust</span>
        </div>
      </a>
      <nav class="main-nav">
        <ul>
          <li><a href="index.php#home">Home</a></li>
          <li><a href="index.php#about">About</a></li>
          <li><a href="index.php#services">Services</a></li>
          <li><a href="services.php">All Services</a></li>
          <li><a href="index.php#testimonials">Testimonials</a></li>
          <li><a href="index.php#contact">Contact</a></li>
          <?php if ($isLoggedIn): ?>
            <li><a href="customer-dashboard.php" class="nav-highlight"><i class="fas fa-user-circle"></i> My Account</a></li>
          <?php else: ?>
            <li><a href="login.php" class="nav-highlight"><i class="fas fa-sign-in-alt"></i> Login</a></li>
            <li><a href="register.php" class="nav-highlight"><i class="fas fa-user-plus"></i> Register</a></li>
          <?php endif; ?>
        </ul>
      </nav>
      <button class="mobile-menu-btn">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </header>

  <!-- Error Content -->
  <div class="error-container">
    <div class="error-code">500</div>
    <h1 class="error-title">Server Error</h1>
    <p class="error-message">Oops! Something went wrong on our end. Our team of mechanics is working to fix the issue. Please try again later or contact us if the problem persists.</p>
    
    <div class="mechanic-animation">
      <img src="https://cdn-icons-png.flaticon.com/512/2421/2421989.png" alt="Mechanic" class="mechanic" width="150">
    </div>
    
    <div class="error-actions">
      <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-home"></i> Back to Homepage
      </a>
      <a href="index.php#contact" class="btn">
        <i class="fas fa-envelope"></i> Contact Support
      </a>
    </div>
    
    <div class="error-links">
      <h3>While you wait, you might want to:</h3>
      <div class="quick-links">
        <a href="index.php#services"><i class="fas fa-wrench"></i> Browse Our Services</a>
        <a href="index.php#about"><i class="fas fa-info-circle"></i> Learn About Us</a>
        <a href="index.php#testimonials"><i class="fas fa-star"></i> Read Testimonials</a>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-column">
        <h3>About Us</h3>
        <p>Walizone Autotech Enterprise is a premier automotive service provider in Chinsali, offering comprehensive vehicle maintenance and repair services since 2003.</p>
        <div class="footer-social">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
      <div class="footer-column">
        <h3>Our Services</h3>
        <ul class="footer-links">
          <li><a href="services.php">Routine Maintenance</a></li>
          <li><a href="services.php">Computer Diagnostics</a></li>
          <li><a href="services.php">Engine & Transmission Repair</a></li>
          <li><a href="services.php">AC & Heating Services</a></li>
          <li><a href="services.php">Suspension & Steering</a></li>
          <li><a href="services.php">Panel Beating & Painting</a></li>
        </ul>
      </div>
      <div class="footer-column">
        <h3>Quick Links</h3>
        <ul class="footer-links">
          <li><a href="index.php#home">Home</a></li>
          <li><a href="index.php#about">About Us</a></li>
          <li><a href="index.php#services">Services</a></li>
          <li><a href="services.php">All Services</a></li>
          <li><a href="booking.php">Book an Appointment</a></li>
          <li><a href="index.php#contact">Contact Us</a></li>
          <?php if ($isLoggedIn): ?>
            <li><a href="customer-dashboard.php">My Dashboard</a></li>
          <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="footer-column">
        <h3>Contact Info</h3>
        <div class="footer-contact-item">
          <div class="footer-contact-icon">
            <i class="fas fa-map-marker-alt"></i>
          </div>
          <p>Chinsali, Shambalakale Road, opposite Jesims Lodge</p>
        </div>
        <div class="footer-contact-item">
          <div class="footer-contact-icon">
            <i class="fas fa-phone"></i>
          </div>
          <p>0976664017 / 0965595951</p>
        </div>
        <div class="footer-contact-item">
          <div class="footer-contact-icon">
            <i class="fas fa-envelope"></i>
          </div>
          <p>mwakamule@gmail.com</p>
        </div>
        <div class="footer-contact-item">
          <div class="footer-contact-icon">
            <i class="fas fa-clock"></i>
          </div>
          <p>Mon-Sat: 8:00AM - 5:00PM</p>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2025 Walizone Autotech Enterprise. All rights reserved.</p>
    </div>
  </footer>
  
  <!-- JavaScript -->
  <script>
    // Mobile Menu Toggle
    document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
      const nav = document.querySelector('.main-nav');
      nav.style.display = nav.style.display === 'block' ? 'none' : 'block';
    });
    
    // Service Worker Registration for PWA
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
          .then(registration => {
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
          })
          .catch(error => {
            console.log('ServiceWorker registration failed: ', error);
          });
      });
    }
  </script>
</body>
</html>