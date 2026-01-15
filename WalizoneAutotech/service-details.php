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
$serviceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get service details
$serviceDetails = null;
$vehicleDetails = null;

if ($serviceId) {
    $query = "
        SELECT sh.*, v.make, v.model, v.year, v.license_plate, v.color 
        FROM service_history sh
        JOIN vehicles v ON sh.vehicle_id = v.id
        WHERE sh.id = ? AND v.customer_id = ?
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $serviceId, $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $serviceDetails = $result->fetch_assoc();
        
        // Get vehicle details
        $vehicleId = $serviceDetails['vehicle_id'];
        $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ? AND customer_id = ?");
        $stmt->bind_param("ii", $vehicleId, $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $vehicleDetails = $result->fetch_assoc();
        }
    } else {
        // Service not found or doesn't belong to this customer
        header("Location: service-history.php");
        exit;
    }
    
    $stmt->close();
} else {
    // No service ID provided
    header("Location: service-history.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Details - Walizone Autotech</title>
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
        
        /* Service Details */
        .service-details {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .detail-section {
            background-color: var(--background-white);
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 1.5rem;
            border: 1px solid #eee;
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
        
        .description-box {
            background-color: #f9f9f9;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: var(--transition);
            text-decoration: none;
            text-align: center;
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
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
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
            .detail-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
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
                <h1 class="page-title">Service Details</h1>
                
                <div class="service-details">
                    <!-- Service Information -->
                    <div class="detail-section">
                        <h2 class="detail-title">Service Information</h2>
                        
                        <div class="detail-grid">
                            <div class="detail-item">
                                <div class="detail-label">Service Type</div>
                                <div class="detail-value"><?php echo htmlspecialchars($serviceDetails['service_type']); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Service Date</div>
                                <div class="detail-value"><?php echo date('F d, Y', strtotime($serviceDetails['service_date'])); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Status</div>
                                <div class="detail-value">
                                    <span class="status-badge status-<?php echo $serviceDetails['status']; ?>">
                                        <?php 
                                        $statusText = '';
                                        switch ($serviceDetails['status']) {
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
                                                $statusText = ucfirst($serviceDetails['status']);
                                        }
                                        echo $statusText;
                                        ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if (!empty($serviceDetails['mileage'])): ?>
                            <div class="detail-item">
                                <div class="detail-label">Mileage</div>
                                <div class="detail-value"><?php echo number_format($serviceDetails['mileage']) . ' km'; ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($serviceDetails['cost'])): ?>
                            <div class="detail-item">
                                <div class="detail-label">Cost</div>
                                <div class="detail-value">K<?php echo number_format($serviceDetails['cost'], 2); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($serviceDetails['technician'])): ?>
                            <div class="detail-item">
                                <div class="detail-label">Technician</div>
                                <div class="detail-value"><?php echo htmlspecialchars($serviceDetails['technician']); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($serviceDetails['description'])): ?>
                        <div class="detail-item" style="margin-top: 1rem;">
                            <div class="detail-label">Description</div>
                            <div class="description-box">
                                <?php echo nl2br(htmlspecialchars($serviceDetails['description'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($serviceDetails['notes'])): ?>
                        <div class="detail-item" style="margin-top: 1rem;">
                            <div class="detail-label">Notes</div>
                            <div class="description-box">
                                <?php echo nl2br(htmlspecialchars($serviceDetails['notes'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Vehicle Information -->
                    <div class="detail-section">
                        <h2 class="detail-title">Vehicle Information</h2>
                        
                        <div class="detail-grid">
                            <div class="detail-item">
                                <div class="detail-label">Vehicle</div>
                                <div class="detail-value"><?php echo htmlspecialchars($vehicleDetails['year'] . ' ' . $vehicleDetails['make'] . ' ' . $vehicleDetails['model']); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">License Plate</div>
                                <div class="detail-value"><?php echo htmlspecialchars($vehicleDetails['license_plate']); ?></div>
                            </div>
                            
                            <?php if (!empty($vehicleDetails['color'])): ?>
                            <div class="detail-item">
                                <div class="detail-label">Color</div>
                                <div class="detail-value"><?php echo htmlspecialchars($vehicleDetails['color']); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($vehicleDetails['vin'])): ?>
                            <div class="detail-item">
                                <div class="detail-label">VIN</div>
                                <div class="detail-value"><?php echo htmlspecialchars($vehicleDetails['vin']); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="service-history.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Service History
                        </a>
                        <a href="my-vehicles.php?view=<?php echo $vehicleDetails['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-car"></i> View Vehicle Details
                        </a>
                        <?php if ($serviceDetails['status'] === 'completed'): ?>
                        <a href="booking.php?vehicle=<?php echo $vehicleDetails['id']; ?>&service_type=<?php echo urlencode($serviceDetails['service_type']); ?>" class="btn btn-primary">
                            <i class="fas fa-redo"></i> Book Similar Service
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>