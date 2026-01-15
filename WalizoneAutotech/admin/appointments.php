<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once '../config/db.php';

// Handle status updates
if (isset($_GET['action']) && $_GET['action'] === 'update' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    
    if (in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'])) {
        $updateQuery = "UPDATE appointments SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
        
        // Redirect to remove query parameters
        header("Location: appointments.php");
        exit;
    }
}

// Get appointments with optional filters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

$query = "SELECT * FROM appointments WHERE 1=1";
$params = [];
$types = "";

if (!empty($status_filter)) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($date_filter)) {
    $query .= " AND appointment_date = ?";
    $params[] = $date_filter;
    $types .= "s";
}

$query .= " ORDER BY appointment_date ASC, appointment_time ASC";

$appointments = [];
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Walizone Autotech Admin</title>
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
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #0d47a1;
            color: white;
            height: 100vh;
            position: fixed;
            padding: 1rem;
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
        
        h1 {
            margin-bottom: 1rem;
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
            font-weight: bold;
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
            font-weight: bold;
        }
        
        .apply-btn {
            background-color: #0d47a1;
            color: white;
        }
        
        .reset-btn {
            background-color: #f5f5f5;
            color: #333;
        }
        
        /* Appointments Table */
        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .appointments-table th,
        .appointments-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .appointments-table th {
            background-color: #f5f5f5;
            color: #0d47a1;
            font-weight: bold;
        }
        
        .appointments-table tr:last-child td {
            border-bottom: none;
        }
        
        .appointments-table tr:hover {
            background-color: #f9f9f9;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: bold;
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
        
        /* Action Buttons */
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
        
        .confirm-btn {
            background-color: #1976d2;
        }
        
        .complete-btn {
            background-color: #388e3c;
        }
        
        .cancel-btn {
            background-color: #d32f2f;
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
            
            .appointments-table {
                font-size: 0.85rem;
            }
            
            .appointments-table th,
            .appointments-table td {
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
            <li><a href="appointments.php" class="active"><i class="fas fa-calendar-check"></i> <span>Appointments</span></a></li>
            <li><a href="services.php"><i class="fas fa-wrench"></i> <span>Services</span></a></li>
            <li><a href="vehicles.php"><i class="fas fa-car"></i> <span>Vehicles</span></a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> <span>Customers</span></a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Appointments</h1>
            <div class="user-info">
                <img src="images/ceo2.jpg" alt="Admin">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters">
            <form action="appointments.php" method="GET" style="width: 100%; display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
                <div class="filter-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date_filter); ?>">
                </div>
                
                <div class="filter-buttons">
                    <button type="submit" class="filter-btn apply-btn">Apply Filters</button>
                    <a href="appointments.php" class="filter-btn reset-btn" style="text-decoration: none; text-align: center;">Reset</a>
                </div>
            </form>
        </div>
        
        <?php if (empty($appointments)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h2>No Appointments Found</h2>
                <p>There are no appointments matching your filters. Try changing your filters or check back later.</p>
            </div>
        <?php else: ?>
            <!-- Appointments Table -->
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Date & Time</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td>#<?php echo $appointment['id']; ?></td>
                            <td><?php echo htmlspecialchars($appointment['name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['service']); ?></td>
                            <td>
                                <?php 
                                    $date = new DateTime($appointment['appointment_date']);
                                    echo $date->format('M d, Y') . ' at ';
                                    
                                    $time = new DateTime($appointment['appointment_time']);
                                    echo $time->format('h:i A');
                                ?>
                            </td>
                            <td>
                                <div><?php echo htmlspecialchars($appointment['phone']); ?></div>
                                <div><?php echo htmlspecialchars($appointment['email']); ?></div>
                            </td>
                            <td>
                                <?php
                                    $statusClass = 'status-' . $appointment['status'];
                                    $statusText = ucfirst($appointment['status']);
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($appointment['status'] === 'pending'): ?>
                                        <a href="appointments.php?action=update&id=<?php echo $appointment['id']; ?>&status=confirmed" class="action-btn confirm-btn">Confirm</a>
                                        <a href="appointments.php?action=update&id=<?php echo $appointment['id']; ?>&status=cancelled" class="action-btn cancel-btn">Cancel</a>
                                    <?php elseif ($appointment['status'] === 'confirmed'): ?>
                                        <a href="appointments.php?action=update&id=<?php echo $appointment['id']; ?>&status=completed" class="action-btn complete-btn">Complete</a>
                                        <a href="appointments.php?action=update&id=<?php echo $appointment['id']; ?>&status=cancelled" class="action-btn cancel-btn">Cancel</a>
                                    <?php else: ?>
                                        <span style="color: #666; font-style: italic;">No actions available</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>