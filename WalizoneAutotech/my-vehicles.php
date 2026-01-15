<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once 'config/db.php';

// Initialize variables
$message = '';
$messageType = '';
$customerId = $_SESSION['customer_id'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new vehicle
    if (isset($_POST['add_vehicle'])) {
        $make = trim($_POST['make']);
        $model = trim($_POST['model']);
        $year = (int)$_POST['year'];
        $licensePlate = trim($_POST['license_plate']);
        $vin = trim($_POST['vin'] ?? '');
        $color = trim($_POST['color'] ?? '');
        $mileage = !empty($_POST['mileage']) ? (int)$_POST['mileage'] : null;
        $engineType = trim($_POST['engine_type'] ?? '');
        $transmission = trim($_POST['transmission'] ?? '');
        
        // Validate inputs
        if (empty($make) || empty($model) || empty($year) || empty($licensePlate)) {
            $message = 'Please fill in all required fields.';
            $messageType = 'error';
        } else {
            // Check if license plate already exists
            $stmt = $conn->prepare("SELECT id FROM vehicles WHERE license_plate = ? AND customer_id != ?");
            $stmt->bind_param("si", $licensePlate, $customerId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $message = 'A vehicle with this license plate already exists.';
                $messageType = 'error';
            } else {
                // Insert new vehicle
                $stmt = $conn->prepare("INSERT INTO vehicles (customer_id, make, model, year, license_plate, vin, color, mileage, engine_type, transmission) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issississs", $customerId, $make, $model, $year, $licensePlate, $vin, $color, $mileage, $engineType, $transmission);
                
                if ($stmt->execute()) {
                    $message = 'Vehicle added successfully.';
                    $messageType = 'success';
                } else {
                    $message = 'Error adding vehicle: ' . $conn->error;
                    $messageType = 'error';
                }
            }
            $stmt->close();
        }
    }
    
    // Update vehicle
    if (isset($_POST['update_vehicle'])) {
        $vehicleId = (int)$_POST['vehicle_id'];
        $make = trim($_POST['make']);
        $model = trim($_POST['model']);
        $year = (int)$_POST['year'];
        $licensePlate = trim($_POST['license_plate']);
        $vin = trim($_POST['vin'] ?? '');
        $color = trim($_POST['color'] ?? '');
        $mileage = !empty($_POST['mileage']) ? (int)$_POST['mileage'] : null;
        $engineType = trim($_POST['engine_type'] ?? '');
        $transmission = trim($_POST['transmission'] ?? '');
        
        // Validate inputs
        if (empty($make) || empty($model) || empty($year) || empty($licensePlate)) {
            $message = 'Please fill in all required fields.';
            $messageType = 'error';
        } else {
            // Check if license plate already exists for other vehicles
            $stmt = $conn->prepare("SELECT id FROM vehicles WHERE license_plate = ? AND id != ? AND customer_id != ?");
            $stmt->bind_param("sii", $licensePlate, $vehicleId, $customerId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $message = 'A vehicle with this license plate already exists.';
                $messageType = 'error';
            } else {
                // Update vehicle
                $stmt = $conn->prepare("UPDATE vehicles SET make = ?, model = ?, year = ?, license_plate = ?, vin = ?, color = ?, mileage = ?, engine_type = ?, transmission = ? WHERE id = ? AND customer_id = ?");
                $stmt->bind_param("ssississii", $make, $model, $year, $licensePlate, $vin, $color, $mileage, $engineType, $transmission, $vehicleId, $customerId);
                
                if ($stmt->execute()) {
                    $message = 'Vehicle updated successfully.';
                    $messageType = 'success';
                } else {
                    $message = 'Error updating vehicle: ' . $conn->error;
                    $messageType = 'error';
                }
            }
            $stmt->close();
        }
    }
    
    // Delete vehicle
    if (isset($_POST['delete_vehicle'])) {
        $vehicleId = (int)$_POST['vehicle_id'];
        
        // Delete vehicle
        $stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ? AND customer_id = ?");
        $stmt->bind_param("ii", $vehicleId, $customerId);
        
        if ($stmt->execute()) {
            $message = 'Vehicle deleted successfully.';
            $messageType = 'success';
        } else {
            $message = 'Error deleting vehicle: ' . $conn->error;
            $messageType = 'error';
        }
        $stmt->close();
    }
}

// Get customer's vehicles
$vehicles = [];
$stmt = $conn->prepare("SELECT * FROM vehicles WHERE customer_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}
$stmt->close();

// Get vehicle details if viewing a specific vehicle
$vehicleDetails = null;
$serviceHistory = [];

if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $vehicleId = (int)$_GET['view'];
    
    // Get vehicle details
    $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ? AND customer_id = ?");
    $stmt->bind_param("ii", $vehicleId, $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $vehicleDetails = $result->fetch_assoc();
        
        // Get service history for this vehicle
        $stmt = $conn->prepare("SELECT * FROM service_history WHERE vehicle_id = ? ORDER BY service_date DESC");
        $stmt->bind_param("i", $vehicleId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $serviceHistory[] = $row;
            }
        }
    }
    $stmt->close();
}

// Get customer name
$customerName = $_SESSION['customer_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Vehicles - Walizone Autotech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-light);
            color: var(--text-dark);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .header {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 1rem 0;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--text-light);
        }
        
        .logo img {
            height: 40px;
            margin-right: 10px;
        }
        
        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .user-nav {
            display: flex;
            align-items: center;
        }
        
        .user-nav a {
            color: var(--text-light);
            text-decoration: none;
            margin-left: 20px;
            transition: var(--transition);
        }
        
        .user-nav a:hover {
            color: var(--secondary-light);
        }
        
        /* Main Content */
        .main-content {
            display: flex;
            margin-top: 2rem;
            gap: 2rem;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: var(--background-white);
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 0.75rem;
            text-decoration: none;
            color: var(--text-dark);
            border-radius: 5px;
            transition: var(--transition);
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: var(--primary-light);
            color: var(--text-light);
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Content Area */
        .content-area {
            flex: 1;
            background-color: var(--background-white);
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
        }
        
        .page-title {
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            font-size: 1.8rem;
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 0.5rem;
        }
        
        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: #388e3c;
            border: 1px solid #c8e6c9;
        }
        
        .alert-error {
            background-color: #ffebee;
            color: #d32f2f;
            border: 1px solid #ffcdd2;
        }
        
        /* Vehicle Cards */
        .vehicle-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .vehicle-card {
            background-color: var(--background-white);
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
            border: 1px solid #eee;
        }
        
        .vehicle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .vehicle-header {
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .vehicle-title {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .vehicle-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .vehicle-actions a {
            color: var(--text-light);
            text-decoration: none;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            transition: var(--transition);
        }
        
        .vehicle-actions a:hover {
            background-color: rgba(255, 255, 255, 0.4);
        }
        
        .vehicle-body {
            padding: 1rem;
        }
        
        .vehicle-info {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .vehicle-info i {
            width: 25px;
            color: var(--primary-color);
            margin-right: 0.5rem;
        }
        
        .vehicle-footer {
            padding: 1rem;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }
        
        .vehicle-footer a {
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 500;
            transition: var(--transition);
        }
        
        .vehicle-footer a:hover {
            color: var(--primary-dark);
        }
        
        /* Add Vehicle Button */
        .add-vehicle-btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }
        
        .add-vehicle-btn:hover {
            background-color: var(--primary-dark);
        }
        
        .add-vehicle-btn i {
            margin-right: 0.5rem;
        }
        
        /* Vehicle Form */
        .vehicle-form {
            background-color: var(--background-white);
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .form-title {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 0.5rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .required-field::after {
            content: '*';
            color: #d32f2f;
            margin-left: 3px;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: var(--text-light);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-secondary {
            background-color: #757575;
            color: var(--text-light);
        }
        
        .btn-secondary:hover {
            background-color: #616161;
        }
        
        .btn-danger {
            background-color: #d32f2f;
            color: var(--text-light);
        }
        
        .btn-danger:hover {
            background-color: #b71c1c;
        }
        
        /* Vehicle Details */
        .vehicle-details {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .detail-section {
            background-color: var(--background-white);
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
        }
        
        .detail-title {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.3rem;
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 0.5rem;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .detail-item {
            margin-bottom: 0.5rem;
        }
        
        .detail-label {
            font-weight: 500;
            color: #757575;
        }
        
        .detail-value {
            font-weight: 600;
        }
        
        /* Service History */
        .service-history {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .service-history th, .service-history td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .service-history th {
            background-color: #f5f5f5;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .service-history tr:hover {
            background-color: #f9f9f9;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-scheduled {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .status-in-progress {
            background-color: #fff8e1;
            color: #ff8f00;
        }
        
        .status-completed {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        
        .status-cancelled {
            background-color: #ffebee;
            color: #d32f2f;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            background-color: #f9f9f9;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #bbdefb;
            margin-bottom: 1rem;
        }
        
        .empty-state h2 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .main-content {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }
        }
        
        @media (max-width: 768px) {
            .vehicle-cards {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container header-container">
            <a href="index.php" class="logo">
                <img src="images/logo.svg" alt="Walizone Autotech Logo" onerror="this.src='images/logo.png'; this.onerror=null;">
                <span class="logo-text">Walizone Autotech</span>
            </a>
            <div class="user-nav">
                <span>Welcome, <?php echo htmlspecialchars($customerName); ?></span>
                <a href="customer-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="customer-dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <div class="container">
        <div class="main-content">
            <!-- Sidebar -->
            <div class="sidebar">
                <ul class="sidebar-menu">
                    <li><a href="customer-dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                    <li><a href="my-vehicles.php" class="active"><i class="fas fa-car"></i> My Vehicles</a></li>
                    <li><a href="my-appointments.php"><i class="fas fa-calendar-check"></i> My Appointments</a></li>
                    <li><a href="service-history.php"><i class="fas fa-history"></i> Service History</a></li>
                    <li><a href="booking.php"><i class="fas fa-tools"></i> Book a Service</a></li>
                    <li><a href="customer-dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <!-- Content Area -->
            <div class="content-area">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['add']) || (isset($_GET['edit']) && is_numeric($_GET['edit']))): ?>
                    <?php
                    // Get vehicle data if editing
                    $editVehicle = null;
                    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
                        $editId = (int)$_GET['edit'];
                        foreach ($vehicles as $vehicle) {
                            if ($vehicle['id'] == $editId) {
                                $editVehicle = $vehicle;
                                break;
                            }
                        }
                        
                        // Redirect if vehicle not found
                        if (!$editVehicle) {
                            header("Location: my-vehicles.php");
                            exit;
                        }
                    }
                    ?>
                    
                    <!-- Vehicle Form -->
                    <div class="vehicle-form">
                        <h2 class="form-title"><?php echo $editVehicle ? 'Edit Vehicle' : 'Add New Vehicle'; ?></h2>
                        
                        <form method="POST" action="my-vehicles.php">
                            <?php if ($editVehicle): ?>
                                <input type="hidden" name="vehicle_id" value="<?php echo $editVehicle['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="make" class="required-field">Make</label>
                                    <input type="text" id="make" name="make" class="form-control" value="<?php echo $editVehicle ? htmlspecialchars($editVehicle['make']) : ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="model" class="required-field">Model</label>
                                    <input type="text" id="model" name="model" class="form-control" value="<?php echo $editVehicle ? htmlspecialchars($editVehicle['model']) : ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="year" class="required-field">Year</label>
                                    <input type="number" id="year" name="year" class="form-control" min="1900" max="<?php echo date('Y') + 1; ?>" value="<?php echo $editVehicle ? htmlspecialchars($editVehicle['year']) : date('Y'); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="license_plate" class="required-field">License Plate</label>
                                    <input type="text" id="license_plate" name="license_plate" class="form-control" value="<?php echo $editVehicle ? htmlspecialchars($editVehicle['license_plate']) : ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="vin">VIN (Vehicle Identification Number)</label>
                                    <input type="text" id="vin" name="vin" class="form-control" value="<?php echo $editVehicle ? htmlspecialchars($editVehicle['vin']) : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="color">Color</label>
                                    <input type="text" id="color" name="color" class="form-control" value="<?php echo $editVehicle ? htmlspecialchars($editVehicle['color']) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="mileage">Mileage (km)</label>
                                    <input type="number" id="mileage" name="mileage" class="form-control" min="0" value="<?php echo $editVehicle ? htmlspecialchars($editVehicle['mileage']) : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="engine_type">Engine Type</label>
                                    <select id="engine_type" name="engine_type" class="form-control">
                                        <option value="">Select Engine Type</option>
                                        <option value="Petrol" <?php echo ($editVehicle && $editVehicle['engine_type'] == 'Petrol') ? 'selected' : ''; ?>>Petrol</option>
                                        <option value="Diesel" <?php echo ($editVehicle && $editVehicle['engine_type'] == 'Diesel') ? 'selected' : ''; ?>>Diesel</option>
                                        <option value="Hybrid" <?php echo ($editVehicle && $editVehicle['engine_type'] == 'Hybrid') ? 'selected' : ''; ?>>Hybrid</option>
                                        <option value="Electric" <?php echo ($editVehicle && $editVehicle['engine_type'] == 'Electric') ? 'selected' : ''; ?>>Electric</option>
                                        <option value="Other" <?php echo ($editVehicle && !in_array($editVehicle['engine_type'], ['Petrol', 'Diesel', 'Hybrid', 'Electric'])) ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="transmission">Transmission</label>
                                    <select id="transmission" name="transmission" class="form-control">
                                        <option value="">Select Transmission</option>
                                        <option value="Automatic" <?php echo ($editVehicle && $editVehicle['transmission'] == 'Automatic') ? 'selected' : ''; ?>>Automatic</option>
                                        <option value="Manual" <?php echo ($editVehicle && $editVehicle['transmission'] == 'Manual') ? 'selected' : ''; ?>>Manual</option>
                                        <option value="CVT" <?php echo ($editVehicle && $editVehicle['transmission'] == 'CVT') ? 'selected' : ''; ?>>CVT</option>
                                        <option value="Semi-Automatic" <?php echo ($editVehicle && $editVehicle['transmission'] == 'Semi-Automatic') ? 'selected' : ''; ?>>Semi-Automatic</option>
                                        <option value="Other" <?php echo ($editVehicle && !in_array($editVehicle['transmission'], ['Automatic', 'Manual', 'CVT', 'Semi-Automatic'])) ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <a href="my-vehicles.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" name="<?php echo $editVehicle ? 'update_vehicle' : 'add_vehicle'; ?>" class="btn btn-primary">
                                    <i class="fas fa-<?php echo $editVehicle ? 'save' : 'plus'; ?>"></i> 
                                    <?php echo $editVehicle ? 'Update Vehicle' : 'Add Vehicle'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                
                <?php elseif (isset($_GET['view']) && is_numeric($_GET['view']) && $vehicleDetails): ?>
                    <!-- Vehicle Details -->
                    <h1 class="page-title">Vehicle Details</h1>
                    
                    <div class="vehicle-details">
                        <div class="detail-section">
                            <h2 class="detail-title"><?php echo htmlspecialchars($vehicleDetails['year'] . ' ' . $vehicleDetails['make'] . ' ' . $vehicleDetails['model']); ?></h2>
                            
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <div class="detail-label">License Plate</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($vehicleDetails['license_plate']); ?></div>
                                </div>
                                
                                <?php if (!empty($vehicleDetails['vin'])): ?>
                                <div class="detail-item">
                                    <div class="detail-label">VIN</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($vehicleDetails['vin']); ?></div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($vehicleDetails['color'])): ?>
                                <div class="detail-item">
                                    <div class="detail-label">Color</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($vehicleDetails['color']); ?></div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($vehicleDetails['mileage'])): ?>
                                <div class="detail-item">
                                    <div class="detail-label">Mileage</div>
                                    <div class="detail-value"><?php echo number_format($vehicleDetails['mileage']) . ' km'; ?></div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($vehicleDetails['engine_type'])): ?>
                                <div class="detail-item">
                                    <div class="detail-label">Engine Type</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($vehicleDetails['engine_type']); ?></div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($vehicleDetails['transmission'])): ?>
                                <div class="detail-item">
                                    <div class="detail-label">Transmission</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($vehicleDetails['transmission']); ?></div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="detail-item">
                                    <div class="detail-label">Added On</div>
                                    <div class="detail-value"><?php echo date('M d, Y', strtotime($vehicleDetails['created_at'])); ?></div>
                                </div>
                            </div>
                            
                            <div class="form-actions" style="margin-top: 1.5rem;">
                                <a href="my-vehicles.php" class="btn btn-secondary">Back to Vehicles</a>
                                <a href="my-vehicles.php?edit=<?php echo $vehicleDetails['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Vehicle
                                </a>
                                <a href="booking.php?vehicle=<?php echo $vehicleDetails['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-tools"></i> Book a Service
                                </a>
                            </div>
                        </div>
                        
                        <!-- Service History -->
                        <div class="detail-section">
                            <h2 class="detail-title">Service History</h2>
                            
                            <?php if (empty($serviceHistory)): ?>
                                <div class="empty-state" style="padding: 2rem;">
                                    <i class="fas fa-history" style="font-size: 3rem;"></i>
                                    <h3>No Service History</h3>
                                    <p>This vehicle doesn't have any service records yet.</p>
                                </div>
                            <?php else: ?>
                                <table class="service-history">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Service Type</th>
                                            <th>Mileage</th>
                                            <th>Status</th>
                                            <th>Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($serviceHistory as $service): ?>
                                            <tr>
                                                <td><?php echo date('M d, Y', strtotime($service['service_date'])); ?></td>
                                                <td><?php echo htmlspecialchars($service['service_type']); ?></td>
                                                <td><?php echo $service['mileage'] ? number_format($service['mileage']) . ' km' : 'N/A'; ?></td>
                                                <td>
                                                    <span class="status-badge status-<?php echo $service['status']; ?>">
                                                        <?php 
                                                        $statusText = '';
                                                        switch ($service['status']) {
                                                            case 'scheduled':
                                                                $statusText = 'Scheduled';
                                                                break;
                                                            case 'in_progress':
                                                                $statusText = 'In Progress';
                                                                break;
                                                            case 'completed':
                                                                $statusText = 'Completed';
                                                                break;
                                                            case 'cancelled':
                                                                $statusText = 'Cancelled';
                                                                break;
                                                            default:
                                                                $statusText = ucfirst($service['status']);
                                                        }
                                                        echo $statusText;
                                                        ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $service['cost'] ? 'K' . number_format($service['cost'], 2) : 'N/A'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                
                <?php else: ?>
                    <!-- Vehicles List -->
                    <h1 class="page-title">My Vehicles</h1>
                    
                    <a href="my-vehicles.php?add=1" class="add-vehicle-btn">
                        <i class="fas fa-plus"></i> Add New Vehicle
                    </a>
                    
                    <?php if (empty($vehicles)): ?>
                        <div class="empty-state">
                            <i class="fas fa-car"></i>
                            <h2>No Vehicles Added Yet</h2>
                            <p>Add your first vehicle to manage its service history and book appointments.</p>
                            <a href="my-vehicles.php?add=1" class="btn btn-primary" style="margin-top: 1rem;">
                                <i class="fas fa-plus"></i> Add a Vehicle
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="vehicle-cards">
                            <?php foreach ($vehicles as $vehicle): ?>
                                <div class="vehicle-card">
                                    <div class="vehicle-header">
                                        <div class="vehicle-title"><?php echo htmlspecialchars($vehicle['year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model']); ?></div>
                                        <div class="vehicle-actions">
                                            <a href="my-vehicles.php?edit=<?php echo $vehicle['id']; ?>" title="Edit Vehicle"><i class="fas fa-edit"></i></a>
                                            <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $vehicle['id']; ?>)" title="Delete Vehicle"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </div>
                                    <div class="vehicle-body">
                                        <div class="vehicle-info">
                                            <i class="fas fa-id-card"></i>
                                            <span><?php echo htmlspecialchars($vehicle['license_plate']); ?></span>
                                        </div>
                                        <?php if (!empty($vehicle['color'])): ?>
                                        <div class="vehicle-info">
                                            <i class="fas fa-palette"></i>
                                            <span><?php echo htmlspecialchars($vehicle['color']); ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($vehicle['mileage'])): ?>
                                        <div class="vehicle-info">
                                            <i class="fas fa-tachometer-alt"></i>
                                            <span><?php echo number_format($vehicle['mileage']) . ' km'; ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($vehicle['engine_type'])): ?>
                                        <div class="vehicle-info">
                                            <i class="fas fa-gas-pump"></i>
                                            <span><?php echo htmlspecialchars($vehicle['engine_type']); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="vehicle-footer">
                                        <a href="my-vehicles.php?view=<?php echo $vehicle['id']; ?>">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        <a href="booking.php?vehicle=<?php echo $vehicle['id']; ?>">
                                            <i class="fas fa-tools"></i> Book Service
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Form (Hidden) -->
    <form id="deleteForm" method="POST" action="my-vehicles.php" style="display: none;">
        <input type="hidden" id="deleteVehicleId" name="vehicle_id">
        <input type="hidden" name="delete_vehicle" value="1">
    </form>
    
    <script>
        // Delete confirmation
        function confirmDelete(vehicleId) {
            if (confirm('Are you sure you want to delete this vehicle? This action cannot be undone.')) {
                document.getElementById('deleteVehicleId').value = vehicleId;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</body>
</html>