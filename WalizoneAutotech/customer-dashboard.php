<?php
// Include database connection
require_once 'config/db.php';

// Start session
session_start();

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

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

// Get customer information
$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['customer_name'];
$customer_email = $_SESSION['customer_email'];

// Get customer's appointments
$appointments = [];
$checkAppointmentsTable = "SHOW TABLES LIKE 'appointments'";
$tableExists = $conn->query($checkAppointmentsTable);

if ($tableExists->num_rows > 0) {
    try {
        $appointmentsQuery = "SELECT * FROM appointments WHERE email = ? ORDER BY appointment_date DESC, appointment_time DESC";
        $stmt = $conn->prepare($appointmentsQuery);
        $stmt->bind_param("s", $customer_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $appointments[] = $row;
            }
        }
        $stmt->close();
    } catch (Exception $e) {
        // If there's an error, just continue with empty appointments array
    }
}

// Get customer's vehicles (if vehicles table exists)
$vehicles = [];
$checkVehiclesTable = "SHOW TABLES LIKE 'vehicles'";
$tableExists = $conn->query($checkVehiclesTable);

if ($tableExists->num_rows > 0) {
    // Check if the customer has any vehicles
    try {
        $vehiclesQuery = "SELECT * FROM vehicles WHERE customer_id = ? ORDER BY year DESC";
        $stmt = $conn->prepare($vehiclesQuery);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $vehicles[] = $row;
            }
        }
        $stmt->close();
    } catch (Exception $e) {
        // If there's an error (like table doesn't exist), just continue with empty vehicles array
    }
}

// Process logout
if (isset($_GET['logout'])) {
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Dashboard - Walizone Autotech</title>
  <meta name="description" content="Manage your vehicle service history and appointments with Walizone Autotech Enterprise.">
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
    
    .user-menu {
      position: relative;
    }
    
    .user-menu-btn {
      display: flex;
      align-items: center;
      gap: 8px;
      background: none;
      border: none;
      cursor: pointer;
      color: var(--text-dark);
      font-weight: 500;
      font-size: 1rem;
    }
    
    .user-menu-btn i {
      color: var(--primary-color);
    }
    
    .user-dropdown {
      position: absolute;
      top: 100%;
      right: 0;
      background-color: var(--background-white);
      box-shadow: var(--shadow);
      border-radius: 5px;
      width: 200px;
      z-index: 1000;
      display: none;
    }
    
    .user-dropdown.active {
      display: block;
    }
    
    .user-dropdown ul {
      list-style: none;
      padding: 10px 0;
    }
    
    .user-dropdown li {
      padding: 0;
    }
    
    .user-dropdown a {
      display: block;
      padding: 10px 20px;
      color: var(--text-dark);
      text-decoration: none;
      transition: var(--transition);
    }
    
    .user-dropdown a:hover {
      background-color: var(--background-light);
      color: var(--primary-color);
    }
    
    .user-dropdown a i {
      margin-right: 10px;
      width: 20px;
      text-align: center;
      color: var(--primary-color);
    }
    
    /* Dashboard Layout */
    .dashboard {
      display: flex;
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
      gap: 30px;
    }
    
    .dashboard-sidebar {
      width: 250px;
      flex-shrink: 0;
    }
    
    .dashboard-main {
      flex: 1;
    }
    
    .sidebar-card {
      background-color: var(--background-white);
      border-radius: 10px;
      box-shadow: var(--shadow);
      padding: 20px;
      margin-bottom: 20px;
    }
    
    .user-profile {
      text-align: center;
      padding-bottom: 20px;
      border-bottom: 1px solid #eee;
      margin-bottom: 20px;
    }
    
    .user-avatar {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background-color: var(--primary-light);
      color: var(--text-light);
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 2.5rem;
      margin: 0 auto 15px;
    }
    
    .user-name {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 5px;
    }
    
    .user-email {
      font-size: 0.9rem;
      color: #777;
    }
    
    .sidebar-menu {
      list-style: none;
    }
    
    .sidebar-menu li {
      margin-bottom: 5px;
    }
    
    .sidebar-menu a {
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 5px;
      text-decoration: none;
      color: var(--text-dark);
      transition: var(--transition);
    }
    
    .sidebar-menu a:hover, .sidebar-menu a.active {
      background-color: var(--primary-light);
      color: var(--text-light);
    }
    
    .sidebar-menu a i {
      margin-right: 10px;
      width: 20px;
      text-align: center;
    }
    
    .dashboard-card {
      background-color: var(--background-white);
      border-radius: 10px;
      box-shadow: var(--shadow);
      padding: 25px;
      margin-bottom: 30px;
    }
    
    .dashboard-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #eee;
    }
    
    .dashboard-card-title {
      font-size: 1.5rem;
      margin: 0;
    }
    
    .dashboard-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .stat-card {
      background-color: var(--background-white);
      border-radius: 10px;
      box-shadow: var(--shadow);
      padding: 20px;
      display: flex;
      align-items: center;
    }
    
    .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 10px;
      background-color: var(--primary-light);
      color: var(--text-light);
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 1.5rem;
      margin-right: 15px;
    }
    
    .stat-content h3 {
      font-size: 1.8rem;
      margin: 0 0 5px;
    }
    
    .stat-content p {
      margin: 0;
      color: #777;
      font-size: 0.9rem;
    }
    
    .appointment-list {
      width: 100%;
      border-collapse: collapse;
    }
    
    .appointment-list th, .appointment-list td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    
    .appointment-list th {
      background-color: var(--background-light);
      font-weight: 600;
    }
    
    .appointment-list tr:hover {
      background-color: #f9f9f9;
    }
    
    .status-badge {
      display: inline-block;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 500;
    }
    
    .status-pending {
      background-color: #fff8e1;
      color: #ffa000;
    }
    
    .status-confirmed {
      background-color: #e3f2fd;
      color: #1976d2;
    }
    
    .status-completed {
      background-color: #e8f5e9;
      color: #388e3c;
    }
    
    .status-cancelled {
      background-color: #ffebee;
      color: #d32f2f;
    }
    
    .btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: var(--primary-color);
      color: var(--text-light);
      text-decoration: none;
      border-radius: 5px;
      font-weight: 500;
      transition: var(--transition);
      border: none;
      cursor: pointer;
      font-size: 0.9rem;
    }
    
    .btn:hover {
      background-color: var(--primary-dark);
    }
    
    .btn-secondary {
      background-color: var(--secondary-color);
    }
    
    .btn-secondary:hover {
      background-color: var(--secondary-dark);
    }
    
    .btn-sm {
      padding: 5px 10px;
      font-size: 0.8rem;
    }
    
    .empty-state {
      text-align: center;
      padding: 30px;
    }
    
    .empty-state i {
      font-size: 3rem;
      color: #ccc;
      margin-bottom: 15px;
    }
    
    .empty-state h3 {
      margin-bottom: 10px;
    }
    
    .empty-state p {
      color: #777;
      margin-bottom: 20px;
    }
    
    /* Footer */
    .footer {
      background-color: var(--primary-dark);
      color: var(--text-light);
      padding: 50px 0 0;
      margin-top: 50px;
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
      .dashboard {
        flex-direction: column;
      }
      
      .dashboard-sidebar {
        width: 100%;
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
      
      .dashboard-stats {
        grid-template-columns: 1fr;
      }
      
      .appointment-list {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
      }
    }
    
    @media (max-width: 576px) {
      .top-contact {
        flex-direction: column;
        gap: 5px;
      }
      
      .dashboard-card {
        padding: 15px;
      }
      
      .dashboard-card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
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
      <div class="user-menu">
        <button class="user-menu-btn" onclick="toggleUserMenu()">
          <i class="fas fa-user-circle"></i>
          <?php echo htmlspecialchars($_SESSION['customer_name']); ?>
          <i class="fas fa-chevron-down"></i>
        </button>
        <div class="user-dropdown" id="userDropdown">
          <ul>
            <li><a href="customer-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <li><a href="my-vehicles.php"><i class="fas fa-car"></i> My Vehicles</a></li>
            <li><a href="my-appointments.php"><i class="fas fa-calendar-check"></i> My Appointments</a></li>
            <li><a href="customer-dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
          </ul>
        </div>
      </div>
      <button class="mobile-menu-btn">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </header>

  <!-- Dashboard -->
  <div class="dashboard">
    <!-- Sidebar -->
    <div class="dashboard-sidebar">
      <div class="sidebar-card">
        <div class="user-profile">
          <div class="user-avatar">
            <?php echo strtoupper(substr($customer_name, 0, 1)); ?>
          </div>
          <h3 class="user-name"><?php echo htmlspecialchars($customer_name); ?></h3>
          <p class="user-email"><?php echo htmlspecialchars($customer_email); ?></p>
        </div>
        <ul class="sidebar-menu">
          <li><a href="customer-dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
          <li><a href="my-vehicles.php"><i class="fas fa-car"></i> My Vehicles</a></li>
          <li><a href="my-appointments.php"><i class="fas fa-calendar-check"></i> My Appointments</a></li>
          <li><a href="service-history.php"><i class="fas fa-history"></i> Service History</a></li>
          <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
          <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
          <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
          <li><a href="customer-dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
      </div>
    </div>
    
    <!-- Main Content -->
    <div class="dashboard-main">
      <div class="dashboard-stats">
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="stat-content">
            <h3><?php echo count($appointments); ?></h3>
            <p>Total Appointments</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background-color: var(--secondary-color);">
            <i class="fas fa-car"></i>
          </div>
          <div class="stat-content">
            <h3><?php echo count($vehicles); ?></h3>
            <p>Registered Vehicles</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background-color: #388e3c;">
            <i class="fas fa-tools"></i>
          </div>
          <div class="stat-content">
            <h3>0</h3>
            <p>Completed Services</p>
          </div>
        </div>
      </div>
      
      <div class="dashboard-card">
        <div class="dashboard-card-header">
          <h2 class="dashboard-card-title">Upcoming Appointments</h2>
          <a href="booking.php" class="btn btn-secondary btn-sm">Book New Appointment</a>
        </div>
        
        <?php if (empty($appointments)): ?>
          <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h3>No Appointments Found</h3>
            <p>You don't have any upcoming appointments scheduled.</p>
            <a href="booking.php" class="btn btn-secondary">Book an Appointment</a>
          </div>
        <?php else: ?>
          <table class="appointment-list">
            <thead>
              <tr>
                <th>Service</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($appointments as $appointment): ?>
                <tr>
                  <td><?php echo htmlspecialchars($appointment['service']); ?></td>
                  <td><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></td>
                  <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                  <td>
                    <span class="status-badge status-<?php echo $appointment['status']; ?>">
                      <?php echo ucfirst($appointment['status']); ?>
                    </span>
                  </td>
                  <td>
                    <a href="view-appointment.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm">View</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
      
      <div class="dashboard-card">
        <div class="dashboard-card-header">
          <h2 class="dashboard-card-title">My Vehicles</h2>
          <a href="add-vehicle.php" class="btn btn-secondary btn-sm">Add Vehicle</a>
        </div>
        
        <?php if (empty($vehicles)): ?>
          <div class="empty-state">
            <i class="fas fa-car"></i>
            <h3>No Vehicles Found</h3>
            <p>You haven't added any vehicles to your account yet.</p>
            <a href="add-vehicle.php" class="btn btn-secondary">Add a Vehicle</a>
          </div>
        <?php else: ?>
          <div class="vehicle-grid">
            <?php foreach ($vehicles as $vehicle): ?>
              <div class="vehicle-card">
                <div class="vehicle-icon">
                  <i class="fas fa-car"></i>
                </div>
                <div class="vehicle-details">
                  <h3><?php echo htmlspecialchars($vehicle['year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model']); ?></h3>
                  <p>License: <?php echo htmlspecialchars($vehicle['license_plate']); ?></p>
                  <p>VIN: <?php echo htmlspecialchars($vehicle['vin']); ?></p>
                </div>
                <div class="vehicle-actions">
                  <a href="view-vehicle.php?id=<?php echo $vehicle['id']; ?>" class="btn btn-sm">View History</a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
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
    
    // User Menu Toggle
    function toggleUserMenu() {
      const dropdown = document.getElementById('userDropdown');
      dropdown.classList.toggle('active');
    }
    
    // Close dropdown when clicking outside
    window.addEventListener('click', function(event) {
      if (!event.target.matches('.user-menu-btn') && !event.target.matches('.user-menu-btn *')) {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown.classList.contains('active')) {
          dropdown.classList.remove('active');
        }
      }
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