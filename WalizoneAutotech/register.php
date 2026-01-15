<?php
// Include database connection
require_once 'config/db.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is already logged in
if (isset($_SESSION['customer_id'])) {
    header("Location: customer-dashboard.php");
    exit;
}

// Create customers table if it doesn't exist
try {
    $checkTable = "SHOW TABLES LIKE 'customers'";
    $tableExists = $conn->query($checkTable);
    
    if ($tableExists->num_rows == 0) {
        // Create customers table
        $createTable = "CREATE TABLE customers (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            phone VARCHAR(20) NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at DATETIME NOT NULL,
            last_login DATETIME NULL,
            status ENUM('active', 'inactive') DEFAULT 'active'
        )";
        $conn->query($createTable);
        
        // Create vehicles table
        $createVehiclesTable = "CREATE TABLE IF NOT EXISTS vehicles (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            customer_id INT(11) UNSIGNED NOT NULL,
            make VARCHAR(50) NOT NULL,
            model VARCHAR(50) NOT NULL,
            year INT(4) NOT NULL,
            license_plate VARCHAR(20),
            vin VARCHAR(50),
            color VARCHAR(30),
            created_at DATETIME NOT NULL,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
        )";
        $conn->query($createVehiclesTable);
    }
} catch (Exception $e) {
    // Log error but don't display it
    error_log("Error checking/creating tables: " . $e->getMessage());
}

// Initialize variables
$full_name = $email = $phone = $password = $confirm_password = '';
$errors = [];
$success = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate full name
    if (empty($_POST['full_name'])) {
        $errors['full_name'] = 'Full name is required';
    } else {
        $full_name = htmlspecialchars(trim($_POST['full_name']));
    }
    
    // Validate email
    if (empty($_POST['email'])) {
        $errors['email'] = 'Email is required';
    } else {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        } else {
            try {
                // Check if customers table exists
                $checkTable = "SHOW TABLES LIKE 'customers'";
                $tableExists = $conn->query($checkTable);
                
                if ($tableExists->num_rows > 0) {
                    // Check if email already exists
                    $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        $errors['email'] = 'This email is already registered. Please login or use a different email.';
                    }
                    $stmt->close();
                }
            } catch (Exception $e) {
                // Log error but don't display it
                error_log("Error checking for existing email: " . $e->getMessage());
            }
        }
    }
    
    // Validate phone
    if (empty($_POST['phone'])) {
        $errors['phone'] = 'Phone number is required';
    } else {
        $phone = htmlspecialchars(trim($_POST['phone']));
        // Simple phone validation - can be enhanced based on country format
        if (!preg_match("/^[0-9]{10,15}$/", preg_replace("/[^0-9]/", "", $phone))) {
            $errors['phone'] = 'Please enter a valid phone number';
        }
    }
    
    // Validate password
    if (empty($_POST['password'])) {
        $errors['password'] = 'Password is required';
    } else {
        $password = $_POST['password'];
        // Password strength validation
        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long';
        } elseif (!preg_match("/[A-Z]/", $password)) {
            $errors['password'] = 'Password must contain at least one uppercase letter';
        } elseif (!preg_match("/[a-z]/", $password)) {
            $errors['password'] = 'Password must contain at least one lowercase letter';
        } elseif (!preg_match("/[0-9]/", $password)) {
            $errors['password'] = 'Password must contain at least one number';
        }
    }
    
    // Validate password confirmation
    if (empty($_POST['confirm_password'])) {
        $errors['confirm_password'] = 'Please confirm your password';
    } else {
        $confirm_password = $_POST['confirm_password'];
        if ($password !== $confirm_password) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
    }
    
    // If no errors, register the user
    if (empty($errors)) {
        // Check if customers table exists, if not create it
        $checkTable = "SHOW TABLES LIKE 'customers'";
        $tableExists = $conn->query($checkTable);
        
        if ($tableExists->num_rows == 0) {
            // Create customers table
            $createTable = "CREATE TABLE customers (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                phone VARCHAR(20) NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at DATETIME NOT NULL,
                last_login DATETIME NULL,
                status ENUM('active', 'inactive') DEFAULT 'active'
            )";
            $conn->query($createTable);
            
            // Create vehicles table
            $createVehiclesTable = "CREATE TABLE IF NOT EXISTS vehicles (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_id INT(11) UNSIGNED NOT NULL,
                make VARCHAR(50) NOT NULL,
                model VARCHAR(50) NOT NULL,
                year INT(4) NOT NULL,
                license_plate VARCHAR(20),
                vin VARCHAR(50),
                color VARCHAR(30),
                created_at DATETIME NOT NULL,
                FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
            )";
            $conn->query($createVehiclesTable);
        }
        
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user into database
        try {
            $stmt = $conn->prepare("INSERT INTO customers (full_name, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $full_name, $email, $phone, $hashed_password);
            
            if ($stmt->execute()) {
                $success = true;
                
                // Start session and log the user in
                session_start();
                $_SESSION['customer_id'] = $stmt->insert_id;
                $_SESSION['customer_name'] = $full_name;
                $_SESSION['customer_email'] = $email;
                
                // Redirect to dashboard after successful registration
                header("Location: customer-dashboard.php");
                exit;
            } else {
                $errors['general'] = 'Sorry, there was an error creating your account. Please try again.';
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $errors['general'] = 'A system error occurred. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - Walizone Autotech</title>
  <meta name="description" content="Create an account with Walizone Autotech Enterprise for easy appointment booking and service history tracking.">
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
    
    /* Page Header */
    .page-header {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center/cover;
      height: 40vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: var(--text-light);
      padding: 20px;
    }
    
    .page-header h1 {
      font-size: 3rem;
      margin-bottom: 10px;
      color: var(--text-light);
    }
    
    .page-header p {
      font-size: 1.2rem;
      max-width: 800px;
      margin: 0 auto;
    }
    
    /* Main Content */
    .main-content {
      padding: 60px 20px;
      max-width: 800px;
      margin: 0 auto;
    }
    
    .card {
      background-color: var(--background-white);
      border-radius: 10px;
      box-shadow: var(--shadow);
      padding: 40px;
      margin-bottom: 30px;
    }
    
    .section-title {
      font-size: 2rem;
      margin-bottom: 20px;
      text-align: center;
      position: relative;
    }
    
    .section-title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 3px;
      background-color: var(--secondary-color);
    }
    
    /* Form Styles */
    .form {
      margin-top: 30px;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: var(--primary-color);
    }
    
    .form-control {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 1rem;
      transition: var(--transition);
    }
    
    .form-control:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 2px rgba(13, 71, 161, 0.1);
    }
    
    .form-row {
      display: flex;
      gap: 20px;
    }
    
    .form-row .form-group {
      flex: 1;
    }
    
    .error {
      color: #d32f2f;
      font-size: 0.85rem;
      margin-top: 5px;
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
    
    /* Alert Messages */
    .alert {
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      text-align: center;
    }
    
    .alert-success {
      background-color: #e8f5e9;
      color: #2e7d32;
      border: 1px solid #c8e6c9;
    }
    
    .alert-error {
      background-color: #ffebee;
      color: #c62828;
      border: 1px solid #ffcdd2;
    }
    
    .password-toggle {
      position: relative;
    }
    
    .password-toggle .toggle-btn {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #777;
      cursor: pointer;
    }
    
    .auth-links {
      margin-top: 20px;
      text-align: center;
    }
    
    .auth-links a {
      color: var(--primary-color);
      text-decoration: none;
      transition: var(--transition);
    }
    
    .auth-links a:hover {
      color: var(--secondary-color);
      text-decoration: underline;
    }
    
    /* Footer */
    .footer {
      background-color: var(--primary-dark);
      color: var(--text-light);
      padding: 50px 0 0;
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
      .main-content {
        padding: 40px 20px;
      }
      
      .card {
        padding: 30px;
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
      
      .page-header h1 {
        font-size: 2.5rem;
      }
      
      .form-row {
        flex-direction: column;
        gap: 0;
      }
    }
    
    @media (max-width: 576px) {
      .top-contact {
        flex-direction: column;
        gap: 5px;
      }
      
      .page-header h1 {
        font-size: 2rem;
      }
      
      .card {
        padding: 20px;
      }
      
      .section-title {
        font-size: 1.8rem;
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
        </ul>
      </nav>
      <button class="mobile-menu-btn">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </header>

  <!-- Page Header -->
  <section class="page-header">
    <h1>Create an Account</h1>
    <p>Join Walizone Autotech for a seamless service experience</p>
  </section>

  <!-- Main Content -->
  <div class="main-content">
    <?php if (isset($errors['general'])): ?>
      <div class="alert alert-error">
        <?php echo $errors['general']; ?>
      </div>
    <?php endif; ?>
    
    <div class="card">
      <h2 class="section-title">Register</h2>
      <p>Create your account to manage your vehicle service history, book appointments, and receive exclusive offers.</p>
      
      <form class="form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <div class="form-group">
          <label for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($full_name); ?>" required>
          <?php if (isset($errors['full_name'])): ?>
            <div class="error"><?php echo $errors['full_name']; ?></div>
          <?php endif; ?>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
            <?php if (isset($errors['email'])): ?>
              <div class="error"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
          </div>
          
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($phone); ?>" required>
            <?php if (isset($errors['phone'])): ?>
              <div class="error"><?php echo $errors['phone']; ?></div>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="password">Password</label>
            <div class="password-toggle">
              <input type="password" id="password" name="password" class="form-control" required>
              <button type="button" class="toggle-btn" onclick="togglePassword('password')">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            <?php if (isset($errors['password'])): ?>
              <div class="error"><?php echo $errors['password']; ?></div>
            <?php endif; ?>
            <small>Password must be at least 8 characters with uppercase, lowercase, and numbers</small>
          </div>
          
          <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <div class="password-toggle">
              <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
              <button type="button" class="toggle-btn" onclick="togglePassword('confirm_password')">
                <i class="fas fa-eye"></i>
              </button>
            </div>
            <?php if (isset($errors['confirm_password'])): ?>
              <div class="error"><?php echo $errors['confirm_password']; ?></div>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="form-group" style="text-align: center; margin-top: 30px;">
          <button type="submit" class="btn btn-secondary">Create Account</button>
        </div>
        
        <div class="auth-links">
          <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
      </form>
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
          <li><a href="services/routine-maintenance.php">Routine Maintenance</a></li>
          <li><a href="services/computer-diagnostics.php">Computer Diagnostics</a></li>
          <li><a href="services/engine-transmission-repair.php">Engine & Transmission Repair</a></li>
          <li><a href="#">AC & Heating Services</a></li>
          <li><a href="#">Suspension & Steering</a></li>
          <li><a href="#">Panel Beating & Painting</a></li>
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
    
    // Password Toggle
    function togglePassword(id) {
      const passwordInput = document.getElementById(id);
      const icon = passwordInput.nextElementSibling.querySelector('i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }
    
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