<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once '../config/db.php';

// Initialize variables
$message = '';
$messageType = '';
$viewVehicle = isset($_GET['view']) && is_numeric($_GET['view']) ? (int)$_GET['view'] : 0;
$editVehicle = isset($_GET['edit']) && is_numeric($_GET['edit']) ? (int)$_GET['edit'] : 0;
$customerId = isset($_GET['customer']) && is_numeric($_GET['customer']) ? (int)$_GET['customer'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add service history
    if (isset($_POST['add_service'])) {
        $vehicleId = (int)$_POST['vehicle_id'];
        $serviceType = trim($_POST['service_type']);
        $serviceDate = trim($_POST['service_date']);
        $description = trim($_POST['description'] ?? '');
        $mileage = !empty($_POST['mileage']) ? (int)$_POST['mileage'] : null;
        $cost = !empty($_POST['cost']) ? (float)$_POST['cost'] : null;
        $technician = trim($_POST['technician'] ?? '');
        $status = trim($_POST['status']);
        $notes = trim($_POST['notes'] ?? '');
        
        // Validate inputs
        if (empty($vehicleId) || empty($serviceType) || empty($serviceDate) || empty($status)) {
            $message = 'Please fill in all required fields.';
            $messageType = 'error';
        } else {
            // Insert service history
            $stmt = $conn->prepare("INSERT INTO service_history (vehicle_id, service_type, service_date, description, mileage, cost, technician, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssidsss", $vehicleId, $serviceType, $serviceDate, $description, $mileage, $cost, $technician, $status, $notes);
            
            if ($stmt->execute()) {
                $message = 'Service history added successfully.';
                $messageType = 'success';
            } else {
                $message = 'Error adding service history: ' . $conn->error;
                $messageType = 'error';
            }
            $stmt->close();
        }
    }
    
    // Update service history
    if (isset($_POST['update_service'])) {
        $serviceId = (int)$_POST['service_id'];
        $serviceType = trim($_POST['service_type']);
        $serviceDate = trim($_POST['service_date']);
        $description = trim($_POST['description'] ?? '');
        $mileage = !empty($_POST['mileage']) ? (int)$_POST['mileage'] : null;
        $cost = !empty($_POST['cost']) ? (float)$_POST['cost'] : null;
        $technician = trim($_POST['technician'] ?? '');
        $status = trim($_POST['status']);
        $notes = trim($_POST['notes'] ?? '');
        
        // Validate inputs
        if (empty($serviceId) || empty($serviceType) || empty($serviceDate) || empty($status)) {
            $message = 'Please fill in all required fields.';
            $messageType = 'error';
        } else {
            // Update service history
            $stmt = $conn->prepare("UPDATE service_history SET service_type = ?, service_date = ?, description = ?, mileage = ?, cost = ?, technician = ?, status = ?, notes = ? WHERE id = ?");
            $stmt->bind_param("sssidsssi", $serviceType, $serviceDate, $description, $mileage, $cost, $technician, $status, $notes, $serviceId);
            
            if ($stmt->execute()) {
                $message = 'Service history updated successfully.';
                $messageType = 'success';
            } else {
                $message = 'Error updating service history: ' . $conn->error;
                $messageType = 'error';
            }
            $stmt->close();
        }
    }
    
    // Delete service history
    if (isset($_POST['delete_service'])) {
        $serviceId = (int)$_POST['service_id'];
        
        // Delete service history
        $stmt = $conn->prepare("DELETE FROM service_history WHERE id = ?");
        $stmt->bind_param("i", $serviceId);
        
        if ($stmt->execute()) {
            $message = 'Service history deleted successfully.';
            $messageType = 'success';
        } else {
            $message = 'Error deleting service history: ' . $conn->error;
            $messageType = 'error';
        }
        $stmt->close();
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
        $status = trim($_POST['status']);
        
        // Validate inputs
        if (empty($vehicleId) || empty($make) || empty($model) || empty($year) || empty($licensePlate)) {
            $message = 'Please fill in all required fields.';
            $messageType = 'error';
        } else {
            // Check if license plate already exists for other vehicles
            $stmt = $conn->prepare("SELECT id FROM vehicles WHERE license_plate = ? AND id != ?");
            $stmt->bind_param("si", $licensePlate, $vehicleId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $message = 'A vehicle with this license plate already exists.';
                $messageType = 'error';
            } else {
                // Update vehicle
                $stmt = $conn->prepare("UPDATE vehicles SET make = ?, model = ?, year = ?, license_plate = ?, vin = ?, color = ?, mileage = ?, engine_type = ?, transmission = ?, status = ? WHERE id = ?");
                $stmt->bind_param("ssisssisssi", $make, $model, $year, $licensePlate, $vin, $color, $mileage, $engineType, $transmission, $status, $vehicleId);
                
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
}

// Get vehicles
$vehicles = [];
$query = "
    SELECT v.*, u.full_name as customer_name 
    FROM vehicles v
    JOIN users u ON v.customer_id = u.id
";

// Add customer filter if specified
if ($customerId) {
    $query .= " WHERE v.customer_id = $customerId";
}

$query .= " ORDER BY v.created_at DESC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}

// Get vehicle details and service history if viewing a specific vehicle
$vehicleDetails = null;
$serviceHistory = [];
$editService = null;

if ($viewVehicle) {
    foreach ($vehicles as $vehicle) {
        if ($vehicle['id'] == $viewVehicle) {
            $vehicleDetails = $vehicle;
            break;
        }
    }
    
    if ($vehicleDetails) {
        // Get service history for this vehicle
        $stmt = $conn->prepare("SELECT * FROM service_history WHERE vehicle_id = ? ORDER BY service_date DESC");
        $stmt->bind_param("i", $viewVehicle);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $serviceHistory[] = $row;
            }
        }
        $stmt->close();
    }
}

// Get service details if editing
if (isset($_GET['edit_service']) && is_numeric($_GET['edit_service'])) {
    $serviceId = (int)$_GET['edit_service'];
    
    $stmt = $conn->prepare("SELECT * FROM service_history WHERE id = ?");
    $stmt->bind_param("i", $serviceId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $editService = $result->fetch_assoc();
    }
    $stmt->close();
}

// Get vehicle details if editing
$editVehicleDetails = null;
if ($editVehicle) {
    foreach ($vehicles as $vehicle) {
        if ($vehicle['id'] == $editVehicle) {
            $editVehicleDetails = $vehicle;
            break;
        }
    }
}

// Get customers for dropdown
$customers = [];
$result = $conn->query("SELECT id, full_name, email FROM users WHERE role = 'customer' ORDER BY full_name");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Management - Walizone Autotech Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            display: flex;
            background-color: #f0f0f0;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #0d47a1;
            color: white;
            height: 100vh;
            position: fixed;
            padding: 1rem;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 1rem 0;
            text-align: center;
            border-bottom: 1px solid #1565c0;
            margin-bottom: 1rem;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-menu a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 0.75rem;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: #1565c0;
        }
        
        .sidebar-menu i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 1rem;
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
        
        /* Filter Section */
        .filter-section {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .filter-title {
            font-size: 1.2rem;
            color: #0d47a1;
            margin-bottom: 1rem;
        }
        
        .filter-form {
            display: flex;
            gap: 1rem;
            align-items: flex-end;
        }
        
        .filter-group {
            flex: 1;
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
            border-color: #0d47a1;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: #0d47a1;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1565c0;
        }
        
        .btn-secondary {
            background-color: #757575;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #616161;
        }
        
        .btn-danger {
            background-color: #d32f2f;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #b71c1c;
        }
        
        /* Vehicles Table */
        .vehicles-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .vehicles-table th, .vehicles-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .vehicles-table th {
            background-color: #f5f5f5;
            font-weight: 600;
            color: #0d47a1;
        }
        
        .vehicles-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .vehicles-table .status-active {
            color: #388e3c;
            font-weight: 500;
        }
        
        .vehicles-table .status-inactive {
            color: #d32f2f;
            font-weight: 500;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-btn {
            padding: 0.5rem;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }
        
        .view-btn {
            background-color: #0d47a1;
        }
        
        .view-btn:hover {
            background-color: #1565c0;
        }
        
        .edit-btn {
            background-color: #ff6f00;
        }
        
        .edit-btn:hover {
            background-color: #ff8f00;
        }
        
        .delete-btn {
            background-color: #d32f2f;
            border: none;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.9rem;
        }
        
        .delete-btn:hover {
            background-color: #b71c1c;
        }
        
        /* Vehicle Details */
        .vehicle-details {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .detail-title {
            font-size: 1.5rem;
            color: #0d47a1;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #1976d2;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .detail-item {
            margin-bottom: 0.5rem;
        }
        
        .detail-label {
            font-weight: 500;
            color: #757575;
            margin-bottom: 0.25rem;
        }
        
        .detail-value {
            font-weight: 600;
        }
        
        /* Service History */
        .service-history {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .service-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .service-table th, .service-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .service-table th {
            background-color: #f5f5f5;
            font-weight: 600;
            color: #0d47a1;
        }
        
        .service-table tr:hover {
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
        
        /* Forms */
        .form-section {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .form-title {
            font-size: 1.5rem;
            color: #0d47a1;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #1976d2;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            border-color: #0d47a1;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
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
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #bbdefb;
            margin-bottom: 1rem;
        }
        
        .empty-state h2 {
            color: #0d47a1;
            margin-bottom: 1rem;
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                padding: 0.5rem;
            }
            
            .sidebar-header h2 {
                display: none;
            }
            
            .sidebar-menu a span {
                display: none;
            }
            
            .sidebar-menu i {
                margin-right: 0;
                font-size: 1.25rem;
            }
            
            .main-content {
                margin-left: 70px;
                padding: 1rem;
            }
            
            .filter-form {
                flex-direction: column;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .detail-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Walizone Admin</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a href="messages.php"><i class="fas fa-envelope"></i> <span>Messages</span></a></li>
            <li><a href="appointments.php"><i class="fas fa-calendar-check"></i> <span>Appointments</span></a></li>
            <li><a href="services.php"><i class="fas fa-wrench"></i> <span>Services</span></a></li>
            <li><a href="vehicles.php" class="active"><i class="fas fa-car"></i> <span>Vehicles</span></a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> <span>Customers</span></a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1><?php echo $viewVehicle ? 'Vehicle Details' : ($editVehicle ? 'Edit Vehicle' : 'Vehicle Management'); ?></h1>
            <div class="user-info">
                <img src="https://via.placeholder.com/40" alt="Admin">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$viewVehicle && !$editVehicle && !isset($_GET['edit_service'])): ?>
            <!-- Filter Section -->
            <div class="filter-section">
                <h2 class="filter-title">Filter Vehicles</h2>
                
                <form method="GET" action="vehicles.php" class="filter-form">
                    <div class="filter-group">
                        <label for="customer">Customer</label>
                        <select id="customer" name="customer" class="filter-control">
                            <option value="">All Customers</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>" <?php echo ($customerId == $customer['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($customer['full_name'] . ' (' . $customer['email'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filter
                    </button>
                    
                    <a href="vehicles.php" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </form>
            </div>
            
            <!-- Vehicles Table -->
            <?php if (empty($vehicles)): ?>
                <div class="empty-state">
                    <i class="fas fa-car"></i>
                    <h2>No Vehicles Found</h2>
                    <p>There are no vehicles in the system yet.</p>
                </div>
            <?php else: ?>
                <table class="vehicles-table">
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>License Plate</th>
                            <th>Customer</th>
                            <th>Added On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vehicle['year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['license_plate']); ?></td>
                                <td><?php echo htmlspecialchars($vehicle['customer_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($vehicle['created_at'])); ?></td>
                                <td class="status-<?php echo $vehicle['status']; ?>"><?php echo ucfirst($vehicle['status']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="vehicles.php?view=<?php echo $vehicle['id']; ?>" class="action-btn view-btn" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="vehicles.php?edit=<?php echo $vehicle['id']; ?>" class="action-btn edit-btn" title="Edit Vehicle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        
        <?php elseif ($viewVehicle && $vehicleDetails): ?>
            <!-- Vehicle Details -->
            <div class="vehicle-details">
                <h2 class="detail-title"><?php echo htmlspecialchars($vehicleDetails['year'] . ' ' . $vehicleDetails['make'] . ' ' . $vehicleDetails['model']); ?></h2>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">License Plate</div>
                        <div class="detail-value"><?php echo htmlspecialchars($vehicleDetails['license_plate']); ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Customer</div>
                        <div class="detail-value"><?php echo htmlspecialchars($vehicleDetails['customer_name']); ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value status-<?php echo $vehicleDetails['status']; ?>"><?php echo ucfirst($vehicleDetails['status']); ?></div>
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
                    
                    <div class="detail-item">
                        <div class="detail-label">Last Updated</div>
                        <div class="detail-value"><?php echo date('M d, Y', strtotime($vehicleDetails['updated_at'])); ?></div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="vehicles.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Vehicles
                    </a>
                    <a href="vehicles.php?edit=<?php echo $vehicleDetails['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Vehicle
                    </a>
                    <a href="customers.php?view=<?php echo $vehicleDetails['customer_id']; ?>" class="btn btn-primary">
                        <i class="fas fa-user"></i> View Customer
                    </a>
                </div>
            </div>
            
            <!-- Service History -->
            <div class="service-history">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 class="detail-title" style="margin-bottom: 0;">Service History</h2>
                    <a href="vehicles.php?view=<?php echo $viewVehicle; ?>&add_service=1" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Service Record
                    </a>
                </div>
                
                <?php if (isset($_GET['add_service']) || isset($_GET['edit_service'])): ?>
                    <!-- Service Form -->
                    <div class="form-section">
                        <h3 class="form-title"><?php echo isset($_GET['edit_service']) ? 'Edit Service Record' : 'Add Service Record'; ?></h3>
                        
                        <form method="POST" action="vehicles.php?view=<?php echo $viewVehicle; ?>">
                            <?php if (isset($_GET['edit_service'])): ?>
                                <input type="hidden" name="service_id" value="<?php echo $editService['id']; ?>">
                            <?php else: ?>
                                <input type="hidden" name="vehicle_id" value="<?php echo $viewVehicle; ?>">
                            <?php endif; ?>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="service_type" class="required-field">Service Type</label>
                                    <input type="text" id="service_type" name="service_type" class="form-control" value="<?php echo $editService ? htmlspecialchars($editService['service_type']) : ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="service_date" class="required-field">Service Date</label>
                                    <input type="date" id="service_date" name="service_date" class="form-control" value="<?php echo $editService ? htmlspecialchars($editService['service_date']) : date('Y-m-d'); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="status" class="required-field">Status</label>
                                    <select id="status" name="status" class="form-control" required>
                                        <option value="scheduled" <?php echo ($editService && $editService['status'] == 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                        <option value="in_progress" <?php echo ($editService && $editService['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="completed" <?php echo ($editService && $editService['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo ($editService && $editService['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="mileage">Mileage (km)</label>
                                    <input type="number" id="mileage" name="mileage" class="form-control" min="0" value="<?php echo $editService ? htmlspecialchars($editService['mileage']) : $vehicleDetails['mileage']; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="cost">Cost (K)</label>
                                    <input type="number" id="cost" name="cost" class="form-control" min="0" step="0.01" value="<?php echo $editService ? htmlspecialchars($editService['cost']) : ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="technician">Technician</label>
                                    <input type="text" id="technician" name="technician" class="form-control" value="<?php echo $editService ? htmlspecialchars($editService['technician']) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" class="form-control"><?php echo $editService ? htmlspecialchars($editService['description']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea id="notes" name="notes" class="form-control"><?php echo $editService ? htmlspecialchars($editService['notes']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <a href="vehicles.php?view=<?php echo $viewVehicle; ?>" class="btn btn-secondary">Cancel</a>
                                <button type="submit" name="<?php echo isset($_GET['edit_service']) ? 'update_service' : 'add_service'; ?>" class="btn btn-primary">
                                    <?php echo isset($_GET['edit_service']) ? 'Update Service Record' : 'Add Service Record'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($serviceHistory)): ?>
                    <div style="text-align: center; padding: 2rem; background-color: #f9f9f9; border-radius: 5px;">
                        <i class="fas fa-history" style="font-size: 3rem; color: #bbdefb; margin-bottom: 1rem;"></i>
                        <h3>No Service Records</h3>
                        <p>This vehicle doesn't have any service records yet.</p>
                    </div>
                <?php else: ?>
                    <table class="service-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Service Type</th>
                                <th>Status</th>
                                <th>Mileage</th>
                                <th>Cost</th>
                                <th>Technician</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($serviceHistory as $service): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($service['service_date'])); ?></td>
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
                                    <td><?php echo $service['technician'] ? htmlspecialchars($service['technician']) : 'N/A'; ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="vehicles.php?view=<?php echo $viewVehicle; ?>&edit_service=<?php echo $service['id']; ?>" class="action-btn edit-btn" title="Edit Service">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="vehicles.php?view=<?php echo $viewVehicle; ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this service record?');">
                                                <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                                <button type="submit" name="delete_service" class="action-btn delete-btn" title="Delete Service">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        
        <?php elseif ($editVehicle && $editVehicleDetails): ?>
            <!-- Edit Vehicle Form -->
            <div class="form-section">
                <h2 class="form-title">Edit Vehicle</h2>
                
                <form method="POST" action="vehicles.php">
                    <input type="hidden" name="vehicle_id" value="<?php echo $editVehicleDetails['id']; ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="make" class="required-field">Make</label>
                            <input type="text" id="make" name="make" class="form-control" value="<?php echo htmlspecialchars($editVehicleDetails['make']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="model" class="required-field">Model</label>
                            <input type="text" id="model" name="model" class="form-control" value="<?php echo htmlspecialchars($editVehicleDetails['model']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="year" class="required-field">Year</label>
                            <input type="number" id="year" name="year" class="form-control" min="1900" max="<?php echo date('Y') + 1; ?>" value="<?php echo htmlspecialchars($editVehicleDetails['year']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="license_plate" class="required-field">License Plate</label>
                            <input type="text" id="license_plate" name="license_plate" class="form-control" value="<?php echo htmlspecialchars($editVehicleDetails['license_plate']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="vin">VIN (Vehicle Identification Number)</label>
                            <input type="text" id="vin" name="vin" class="form-control" value="<?php echo htmlspecialchars($editVehicleDetails['vin']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="color">Color</label>
                            <input type="text" id="color" name="color" class="form-control" value="<?php echo htmlspecialchars($editVehicleDetails['color']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="mileage">Mileage (km)</label>
                            <input type="number" id="mileage" name="mileage" class="form-control" min="0" value="<?php echo htmlspecialchars($editVehicleDetails['mileage']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="engine_type">Engine Type</label>
                            <select id="engine_type" name="engine_type" class="form-control">
                                <option value="">Select Engine Type</option>
                                <option value="Petrol" <?php echo ($editVehicleDetails['engine_type'] == 'Petrol') ? 'selected' : ''; ?>>Petrol</option>
                                <option value="Diesel" <?php echo ($editVehicleDetails['engine_type'] == 'Diesel') ? 'selected' : ''; ?>>Diesel</option>
                                <option value="Hybrid" <?php echo ($editVehicleDetails['engine_type'] == 'Hybrid') ? 'selected' : ''; ?>>Hybrid</option>
                                <option value="Electric" <?php echo ($editVehicleDetails['engine_type'] == 'Electric') ? 'selected' : ''; ?>>Electric</option>
                                <option value="Other" <?php echo (!in_array($editVehicleDetails['engine_type'], ['', 'Petrol', 'Diesel', 'Hybrid', 'Electric'])) ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="transmission">Transmission</label>
                            <select id="transmission" name="transmission" class="form-control">
                                <option value="">Select Transmission</option>
                                <option value="Automatic" <?php echo ($editVehicleDetails['transmission'] == 'Automatic') ? 'selected' : ''; ?>>Automatic</option>
                                <option value="Manual" <?php echo ($editVehicleDetails['transmission'] == 'Manual') ? 'selected' : ''; ?>>Manual</option>
                                <option value="CVT" <?php echo ($editVehicleDetails['transmission'] == 'CVT') ? 'selected' : ''; ?>>CVT</option>
                                <option value="Semi-Automatic" <?php echo ($editVehicleDetails['transmission'] == 'Semi-Automatic') ? 'selected' : ''; ?>>Semi-Automatic</option>
                                <option value="Other" <?php echo (!in_array($editVehicleDetails['transmission'], ['', 'Automatic', 'Manual', 'CVT', 'Semi-Automatic'])) ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="status" class="required-field">Status</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="active" <?php echo ($editVehicleDetails['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($editVehicleDetails['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="<?php echo $viewVehicle ? "vehicles.php?view=$viewVehicle" : 'vehicles.php'; ?>" class="btn btn-secondary">Cancel</a>
                        <button type="submit" name="update_vehicle" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Vehicle
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>