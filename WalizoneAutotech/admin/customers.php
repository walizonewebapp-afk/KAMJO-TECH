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
$viewCustomer = null;
$customerVehicles = [];

// Handle customer actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update customer status
    if (isset($_POST['action']) && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        
        if ($_POST['action'] === 'activate') {
            $stmt = $conn->prepare("UPDATE customers SET status = 'active' WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $message = 'Customer activated successfully';
                $messageType = 'success';
            } else {
                $message = 'Error updating customer: ' . $conn->error;
                $messageType = 'error';
            }
            
            $stmt->close();
        } elseif ($_POST['action'] === 'deactivate') {
            $stmt = $conn->prepare("UPDATE customers SET status = 'inactive' WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $message = 'Customer deactivated successfully';
                $messageType = 'success';
            } else {
                $message = 'Error updating customer: ' . $conn->error;
                $messageType = 'error';
            }
            
            $stmt->close();
        } elseif ($_POST['action'] === 'reset_password') {
            // Generate a random password
            $newPassword = bin2hex(random_bytes(4)); // 8 characters
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("UPDATE customers SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $id);
            
            if ($stmt->execute()) {
                $message = 'Password reset successfully. New password: ' . $newPassword;
                $messageType = 'success';
            } else {
                $message = 'Error resetting password: ' . $conn->error;
                $messageType = 'error';
            }
            
            $stmt->close();
        }
    }
}

// Get customer details if viewing a specific customer
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $id = (int)$_GET['view'];
    $stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $viewCustomer = $result->fetch_assoc();
        
        // Get customer vehicles
        $vehicleStmt = $conn->prepare("SELECT * FROM vehicles WHERE customer_id = ? ORDER BY created_at DESC");
        $vehicleStmt->bind_param("i", $id);
        $vehicleStmt->execute();
        $vehicleResult = $vehicleStmt->get_result();
        
        if ($vehicleResult->num_rows > 0) {
            while ($vehicle = $vehicleResult->fetch_assoc()) {
                $customerVehicles[] = $vehicle;
            }
        }
        
        $vehicleStmt->close();
        
        // Get customer appointments
        $appointmentStmt = $conn->prepare("
            SELECT a.* 
            FROM appointments a
            JOIN customers c ON a.email = c.email
            WHERE c.id = ?
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ");
        $appointmentStmt->bind_param("i", $id);
        $appointmentStmt->execute();
        $appointmentResult = $appointmentStmt->get_result();
        
        $customerAppointments = [];
        if ($appointmentResult->num_rows > 0) {
            while ($appointment = $appointmentResult->fetch_assoc()) {
                $customerAppointments[] = $appointment;
            }
        }
        
        $appointmentStmt->close();
    }
    
    $stmt->close();
}

// Get customers with filters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM customers WHERE 1=1";
$params = [];
$types = "";

if (!empty($status_filter)) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($search_query)) {
    $search_query = "%$search_query%";
    $query .= " AND (full_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $params[] = $search_query;
    $params[] = $search_query;
    $params[] = $search_query;
    $types .= "sss";
}

$query .= " ORDER BY created_at DESC";

$customers = [];
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}

$stmt->close();

// Count customers by status
$activeCount = 0;
$inactiveCount = 0;

$countQuery = "SELECT status, COUNT(*) as count FROM customers GROUP BY status";
$countResult = $conn->query($countQuery);

if ($countResult && $countResult->num_rows > 0) {
    while ($row = $countResult->fetch_assoc()) {
        if ($row['status'] === 'active') {
            $activeCount = $row['count'];
        } elseif ($row['status'] === 'inactive') {
            $inactiveCount = $row['count'];
        }
    }
}

// Count total vehicles
$totalVehicles = 0;
$vehicleQuery = "SELECT COUNT(*) as count FROM vehicles";
$vehicleResult = $conn->query($vehicleQuery);

if ($vehicleResult && $vehicleResult->num_rows > 0) {
    $totalVehicles = $vehicleResult->fetch_assoc()['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Walizone Autotech Admin</title>
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
        
        /* Customer Stats */
        .customer-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.5rem;
        }
        
        .stat-total .stat-icon {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        
        .stat-active .stat-icon {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        
        .stat-inactive .stat-icon {
            background-color: #ffebee;
            color: #d32f2f;
        }
        
        .stat-vehicles .stat-icon {
            background-color: #fff8e1;
            color: #ffa000;
        }
        
        .stat-info {
            flex: 1;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Filters */
        .filters {
            background-color: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #0d47a1;
        }
        
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .filter-buttons {
            display: flex;
            gap: 1rem;
        }
        
        .filter-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .apply-btn {
            background-color: #0d47a1;
            color: white;
        }
        
        .reset-btn {
            background-color: #f5f5f5;
            color: #333;
            text-decoration: none;
            text-align: center;
        }
        
        /* Customer Container */
        .customer-container {
            display: flex;
            gap: 2rem;
        }
        
        .customer-list {
            flex: 1;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .customer-list-header {
            padding: 1.5rem;
            background-color: #f5f5f5;
            border-bottom: 1px solid #eee;
        }
        
        .customer-list-header h2 {
            color: #0d47a1;
            margin-bottom: 0.5rem;
        }
        
        .customer-list-body {
            max-height: 600px;
            overflow-y: auto;
        }
        
        .customer-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .customer-table th,
        .customer-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .customer-table th {
            background-color: #f5f5f5;
            color: #0d47a1;
            font-weight: bold;
            position: sticky;
            top: 0;
        }
        
        .customer-table tr:last-child td {
            border-bottom: none;
        }
        
        .customer-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .customer-table tr.active {
            background-color: #e3f2fd;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        
        .status-active {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        
        .status-inactive {
            background-color: #ffebee;
            color: #d32f2f;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-btn {
            padding: 0.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            font-size: 0.85rem;
        }
        
        .view-btn {
            background-color: #0d47a1;
        }
        
        .activate-btn {
            background-color: #388e3c;
        }
        
        .deactivate-btn {
            background-color: #d32f2f;
        }
        
        .reset-pwd-btn {
            background-color: #ffa000;
        }
        
        /* Customer Detail */
        .customer-detail {
            flex: 1;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: <?php echo $viewCustomer ? 'block' : 'none'; ?>;
        }
        
        .customer-detail-header {
            padding: 1.5rem;
            background-color: #f5f5f5;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .customer-detail-title {
            color: #0d47a1;
            margin: 0;
        }
        
        .customer-detail-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .customer-detail-body {
            padding: 1.5rem;
        }
        
        .customer-info {
            margin-bottom: 2rem;
        }
        
        .customer-info-item {
            display: flex;
            margin-bottom: 0.5rem;
        }
        
        .customer-info-label {
            font-weight: bold;
            width: 150px;
        }
        
        .customer-vehicles {
            margin-bottom: 2rem;
        }
        
        .customer-vehicles h3 {
            color: #0d47a1;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        
        .vehicle-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        
        .vehicle-table th,
        .vehicle-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .vehicle-table th {
            background-color: #f5f5f5;
            color: #0d47a1;
            font-weight: bold;
        }
        
        .customer-appointments {
            margin-bottom: 2rem;
        }
        
        .customer-appointments h3 {
            color: #0d47a1;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        
        .appointment-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .appointment-table th,
        .appointment-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .appointment-table th {
            background-color: #f5f5f5;
            color: #0d47a1;
            font-weight: bold;
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
        
        .no-customer-selected {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .no-customer-selected i {
            font-size: 4rem;
            color: #bbdefb;
            margin-bottom: 1rem;
        }
        
        .no-data {
            text-align: center;
            padding: 1.5rem;
            color: #666;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .customer-container {
                flex-direction: column;
            }
            
            .customer-detail {
                display: <?php echo $viewCustomer ? 'block' : 'none'; ?>;
            }
        }
        
        @media (max-width: 768px) {
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
            
            .filters {
                flex-direction: column;
                gap: 1rem;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .customer-detail-actions {
                flex-wrap: wrap;
            }
            
            .customer-table {
                font-size: 0.85rem;
            }
            
            .customer-table th,
            .customer-table td {
                padding: 0.75rem 0.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
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
            <li><a href="vehicles.php"><i class="fas fa-car"></i> <span>Vehicles</span></a></li>
            <li><a href="customers.php" class="active"><i class="fas fa-users"></i> <span>Customers</span></a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Customers</h1>
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
        
        <!-- Customer Stats -->
        <div class="customer-stats">
            <div class="stat-card stat-total">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo count($customers); ?></div>
                    <div class="stat-label">Total Customers</div>
                </div>
            </div>
            
            <div class="stat-card stat-active">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $activeCount; ?></div>
                    <div class="stat-label">Active Customers</div>
                </div>
            </div>
            
            <div class="stat-card stat-inactive">
                <div class="stat-icon">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $inactiveCount; ?></div>
                    <div class="stat-label">Inactive Customers</div>
                </div>
            </div>
            
            <div class="stat-card stat-vehicles">
                <div class="stat-icon">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalVehicles; ?></div>
                    <div class="stat-label">Registered Vehicles</div>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters">
            <form action="customers.php" method="GET" style="width: 100%; display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
                <div class="filter-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="">All Customers</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" placeholder="Search by name, email or phone" value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                
                <div class="filter-buttons">
                    <button type="submit" class="filter-btn apply-btn">Apply Filters</button>
                    <a href="customers.php" class="filter-btn reset-btn">Reset</a>
                </div>
            </form>
        </div>
        
        <?php if (empty($customers)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h2>No Customers Found</h2>
                <p>There are no customers matching your filters. Try changing your filters or check back later.</p>
            </div>
        <?php else: ?>
            <!-- Customer Container -->
            <div class="customer-container">
                <!-- Customer List -->
                <div class="customer-list">
                    <div class="customer-list-header">
                        <h2>Customers (<?php echo count($customers); ?>)</h2>
                    </div>
                    <div class="customer-list-body">
                        <table class="customer-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $customer): ?>
                                    <tr class="<?php echo $viewCustomer && $viewCustomer['id'] === $customer['id'] ? 'active' : ''; ?>">
                                        <td><?php echo htmlspecialchars($customer['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $customer['status']; ?>">
                                                <?php echo ucfirst($customer['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="customers.php?view=<?php echo $customer['id']; ?>" class="action-btn view-btn">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                
                                                <?php if ($customer['status'] === 'inactive'): ?>
                                                    <form method="POST" action="customers.php?view=<?php echo $customer['id']; ?>" style="display: inline;">
                                                        <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                                                        <input type="hidden" name="action" value="activate">
                                                        <button type="submit" class="action-btn activate-btn">
                                                            <i class="fas fa-user-check"></i> Activate
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" action="customers.php?view=<?php echo $customer['id']; ?>" style="display: inline;">
                                                        <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                                                        <input type="hidden" name="action" value="deactivate">
                                                        <button type="submit" class="action-btn deactivate-btn">
                                                            <i class="fas fa-user-times"></i> Deactivate
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Customer Detail -->
                <div class="customer-detail" id="customer-detail">
                    <?php if ($viewCustomer): ?>
                        <div class="customer-detail-header">
                            <h2 class="customer-detail-title">Customer Details</h2>
                            <div class="customer-detail-actions">
                                <?php if ($viewCustomer['status'] === 'inactive'): ?>
                                    <form method="POST" action="customers.php?view=<?php echo $viewCustomer['id']; ?>" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $viewCustomer['id']; ?>">
                                        <input type="hidden" name="action" value="activate">
                                        <button type="submit" class="action-btn activate-btn">
                                            <i class="fas fa-user-check"></i> Activate
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="customers.php?view=<?php echo $viewCustomer['id']; ?>" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $viewCustomer['id']; ?>">
                                        <input type="hidden" name="action" value="deactivate">
                                        <button type="submit" class="action-btn deactivate-btn">
                                            <i class="fas fa-user-times"></i> Deactivate
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <form method="POST" action="customers.php?view=<?php echo $viewCustomer['id']; ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to reset this customer\'s password?');">
                                    <input type="hidden" name="id" value="<?php echo $viewCustomer['id']; ?>">
                                    <input type="hidden" name="action" value="reset_password">
                                    <button type="submit" class="action-btn reset-pwd-btn">
                                        <i class="fas fa-key"></i> Reset Password
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="customer-detail-body">
                            <div class="customer-info">
                                <div class="customer-info-item">
                                    <div class="customer-info-label">Full Name:</div>
                                    <div><?php echo htmlspecialchars($viewCustomer['full_name']); ?></div>
                                </div>
                                <div class="customer-info-item">
                                    <div class="customer-info-label">Email:</div>
                                    <div><a href="mailto:<?php echo htmlspecialchars($viewCustomer['email']); ?>"><?php echo htmlspecialchars($viewCustomer['email']); ?></a></div>
                                </div>
                                <div class="customer-info-item">
                                    <div class="customer-info-label">Phone:</div>
                                    <div><a href="tel:<?php echo htmlspecialchars($viewCustomer['phone']); ?>"><?php echo htmlspecialchars($viewCustomer['phone']); ?></a></div>
                                </div>
                                <div class="customer-info-item">
                                    <div class="customer-info-label">Status:</div>
                                    <div>
                                        <span class="status-badge status-<?php echo $viewCustomer['status']; ?>">
                                            <?php echo ucfirst($viewCustomer['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="customer-info-item">
                                    <div class="customer-info-label">Registered On:</div>
                                    <div><?php echo date('F d, Y H:i', strtotime($viewCustomer['created_at'])); ?></div>
                                </div>
                                <?php if ($viewCustomer['last_login']): ?>
                                    <div class="customer-info-item">
                                        <div class="customer-info-label">Last Login:</div>
                                        <div><?php echo date('F d, Y H:i', strtotime($viewCustomer['last_login'])); ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Customer Vehicles -->
                            <div class="customer-vehicles">
                                <h3>Vehicles (<?php echo count($customerVehicles); ?>)</h3>
                                
                                <?php if (empty($customerVehicles)): ?>
                                    <div class="no-data">
                                        <p>This customer has not registered any vehicles yet.</p>
                                    </div>
                                <?php else: ?>
                                    <table class="vehicle-table">
                                        <thead>
                                            <tr>
                                                <th>Make</th>
                                                <th>Model</th>
                                                <th>Year</th>
                                                <th>License Plate</th>
                                                <th>Color</th>
                                                <th>Added On</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($customerVehicles as $vehicle): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($vehicle['make']); ?></td>
                                                    <td><?php echo htmlspecialchars($vehicle['model']); ?></td>
                                                    <td><?php echo htmlspecialchars($vehicle['year']); ?></td>
                                                    <td><?php echo htmlspecialchars($vehicle['license_plate']); ?></td>
                                                    <td><?php echo htmlspecialchars($vehicle['color']); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($vehicle['created_at'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Customer Appointments -->
                            <div class="customer-appointments">
                                <h3>Appointments (<?php echo count($customerAppointments); ?>)</h3>
                                
                                <?php if (empty($customerAppointments)): ?>
                                    <div class="no-data">
                                        <p>This customer has not made any appointments yet.</p>
                                    </div>
                                <?php else: ?>
                                    <table class="appointment-table">
                                        <thead>
                                            <tr>
                                                <th>Service</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Status</th>
                                                <th>Booked On</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($customerAppointments as $appointment): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($appointment['service']); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></td>
                                                    <td><?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                                                    <td>
                                                        <span class="status-badge status-<?php echo $appointment['status']; ?>">
                                                            <?php echo ucfirst($appointment['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($appointment['created_at'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-customer-selected">
                            <i class="fas fa-user"></i>
                            <h2>No Customer Selected</h2>
                            <p>Select a customer from the list to view their details.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>