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
$customerId = $_SESSION['customer_id'];
$customerName = $_SESSION['customer_name'];

// Get all service history for customer's vehicles
$serviceHistory = [];
$query = "
    SELECT sh.*, v.make, v.model, v.year, v.license_plate 
    FROM service_history sh
    JOIN vehicles v ON sh.vehicle_id = v.id
    WHERE v.customer_id = ?
    ORDER BY sh.service_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $serviceHistory[] = $row;
    }
}
$stmt->close();

// Get customer's vehicles for filter
$vehicles = [];
$stmt = $conn->prepare("SELECT id, make, model, year, license_plate FROM vehicles WHERE customer_id = ? ORDER BY make, model");
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}
$stmt->close();

// Apply filters if set
$selectedVehicle = isset($_GET['vehicle']) ? (int)$_GET['vehicle'] : 0;
$selectedStatus = isset($_GET['status']) ? $_GET['status'] : '';
$selectedDateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$selectedDateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Filter service history based on selections
if ($selectedVehicle || $selectedStatus || $selectedDateFrom || $selectedDateTo) {
    $filteredHistory = [];
    
    foreach ($serviceHistory as $service) {
        $includeService = true;
        
        // Filter by vehicle
        if ($selectedVehicle && $service['vehicle_id'] != $selectedVehicle) {
            $includeService = false;
        }
        
        // Filter by status
        if ($selectedStatus && $service['status'] != $selectedStatus) {
            $includeService = false;
        }
        
        // Filter by date range
        if ($selectedDateFrom && strtotime($service['service_date']) < strtotime($selectedDateFrom)) {
            $includeService = false;
        }
        
        if ($selectedDateTo && strtotime($service['service_date']) > strtotime($selectedDateTo)) {
            $includeService = false;
        }
        
        if ($includeService) {
            $filteredHistory[] = $service;
        }
    }
    
    $serviceHistory = $filteredHistory;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service History - Walizone Autotech</title>
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
        
        /* Filter Section */
        .filter-section {
            background-color: #f5f5f5;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .filter-title {
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .filter-group {
            margin-bottom: 1rem;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .filter-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .filter-control:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .filter-actions {
            display: flex;
            align-items: flex-end;
            gap: 1rem;
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
        
        /* Service History Table */
        .service-history {
            width: 100%;
            border-collapse: collapse;
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
            .filter-form {
                grid-template-columns: 1fr;
            }
            
            .service-history {
                font-size: 0.9rem;
            }
            
            .service-history th, .service-history td {
                padding: 0.5rem;
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
                    <li><a href="my-vehicles.php"><i class="fas fa-car"></i> My Vehicles</a></li>
                    <li><a href="my-appointments.php"><i class="fas fa-calendar-check"></i> My Appointments</a></li>
                    <li><a href="service-history.php" class="active"><i class="fas fa-history"></i> Service History</a></li>
                    <li><a href="booking.php"><i class="fas fa-tools"></i> Book a Service</a></li>
                    <li><a href="customer-dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <!-- Content Area -->
            <div class="content-area">
                <h1 class="page-title">Service History</h1>
                
                <!-- Filter Section -->
                <div class="filter-section">
                    <h2 class="filter-title">Filter Service Records</h2>
                    
                    <form method="GET" action="service-history.php" class="filter-form">
                        <div class="filter-group">
                            <label for="vehicle">Vehicle</label>
                            <select id="vehicle" name="vehicle" class="filter-control">
                                <option value="">All Vehicles</option>
                                <?php foreach ($vehicles as $vehicle): ?>
                                    <option value="<?php echo $vehicle['id']; ?>" <?php echo ($selectedVehicle == $vehicle['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($vehicle['year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model'] . ' (' . $vehicle['license_plate'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="filter-control">
                                <option value="">All Statuses</option>
                                <option value="scheduled" <?php echo ($selectedStatus == 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                <option value="in_progress" <?php echo ($selectedStatus == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                <option value="completed" <?php echo ($selectedStatus == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo ($selectedStatus == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="date_from">From Date</label>
                            <input type="date" id="date_from" name="date_from" class="filter-control" value="<?php echo $selectedDateFrom; ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label for="date_to">To Date</label>
                            <input type="date" id="date_to" name="date_to" class="filter-control" value="<?php echo $selectedDateTo; ?>">
                        </div>
                        
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                            <a href="service-history.php" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- Service History Table -->
                <?php if (empty($serviceHistory)): ?>
                    <div class="empty-state">
                        <i class="fas fa-history"></i>
                        <h2>No Service Records Found</h2>
                        <p>
                            <?php if ($selectedVehicle || $selectedStatus || $selectedDateFrom || $selectedDateTo): ?>
                                No service records match your filter criteria. Try adjusting your filters.
                            <?php else: ?>
                                You don't have any service records yet. Book a service for your vehicle to get started.
                            <?php endif; ?>
                        </p>
                        <?php if (empty($vehicles)): ?>
                            <a href="my-vehicles.php?add=1" class="btn btn-primary" style="margin-top: 1rem;">
                                <i class="fas fa-car"></i> Add a Vehicle
                            </a>
                        <?php else: ?>
                            <a href="booking.php" class="btn btn-primary" style="margin-top: 1rem;">
                                <i class="fas fa-tools"></i> Book a Service
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <table class="service-history">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Vehicle</th>
                                <th>Service Type</th>
                                <th>Status</th>
                                <th>Mileage</th>
                                <th>Cost</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($serviceHistory as $service): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($service['service_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($service['year'] . ' ' . $service['make'] . ' ' . $service['model']); ?></td>
                                    <td><?php echo htmlspecialchars($service['service_type']); ?></td>
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
                                    <td><?php echo $service['mileage'] ? number_format($service['mileage']) . ' km' : 'N/A'; ?></td>
                                    <td><?php echo $service['cost'] ? 'K' . number_format($service['cost'], 2) : 'N/A'; ?></td>
                                    <td>
                                        <a href="service-details.php?id=<?php echo $service['id']; ?>" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>