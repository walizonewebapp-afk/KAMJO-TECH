<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once '../config/db.php';

// Get counts for dashboard
$messageCount = 0;
$serviceCount = 0;
$userCount = 0;
$appointmentCount = 0;

// Count messages
$msgQuery = "SELECT COUNT(*) as count FROM contact_messages";
$msgResult = $conn->query($msgQuery);
if ($msgResult && $msgResult->num_rows > 0) {
    $messageCount = $msgResult->fetch_assoc()['count'];
}

// Count services
$serviceQuery = "SELECT COUNT(*) as count FROM services";
$serviceResult = $conn->query($serviceQuery);
if ($serviceResult && $serviceResult->num_rows > 0) {
    $serviceCount = $serviceResult->fetch_assoc()['count'];
}

// Count users
$userQuery = "SELECT COUNT(*) as count FROM users";
$userResult = $conn->query($userQuery);
if ($userResult && $userResult->num_rows > 0) {
    $userCount = $userResult->fetch_assoc()['count'];
}

// Count appointments
$appointmentQuery = "SELECT COUNT(*) as count FROM appointments";
$appointmentResult = $conn->query($appointmentQuery);
if ($appointmentResult && $appointmentResult->num_rows > 0) {
    $appointmentCount = $appointmentResult->fetch_assoc()['count'];
}

// Get recent messages
$recentMessages = [];
$recentMsgQuery = "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5";
$recentMsgResult = $conn->query($recentMsgQuery);
if ($recentMsgResult && $recentMsgResult->num_rows > 0) {
    while ($row = $recentMsgResult->fetch_assoc()) {
        $recentMessages[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Walizone Autotech</title>
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
        
        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .card {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .card-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #0d47a1;
        }
        
        .card-title {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .card-value {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        /* Recent Messages */
        .recent-messages {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .recent-messages h2 {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
            color: #0d47a1;
        }
        
        .message-item {
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .message-item:last-child {
            border-bottom: none;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .message-sender {
            font-weight: bold;
        }
        
        .message-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .message-content {
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .message-actions a {
            color: #0d47a1;
            text-decoration: none;
            margin-right: 1rem;
            font-size: 0.9rem;
        }
        
        .message-actions a:hover {
            text-decoration: underline;
        }
        
        /* Logout Button */
        .logout-btn {
            display: inline-block;
            background-color: #0d47a1;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background-color: #1565c0;
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
            <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
            <li><a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
            <li><a href="services.php"><i class="fas fa-wrench"></i> Services</a></li>
            <li><a href="vehicles.php"><i class="fas fa-car"></i> Vehicles</a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Dashboard</h1>
            <div class="user-info">
                <img src="https://via.placeholder.com/40" alt="Admin">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>
        </div>
        
        <!-- Welcome Message -->
        <div style="background-color: #e3f2fd; padding: 20px; border-radius: 10px; margin-bottom: 30px; border-left: 4px solid #1976d2;">
            <h2 style="color: #0d47a1; margin-bottom: 10px; font-size: 1.5rem;">Welcome to Walizone Autotech Admin Panel</h2>
            <p style="margin-bottom: 15px;">This dashboard provides you with tools to manage your automotive business. Use the sidebar navigation to access different sections.</p>
            <p>Quick links:</p>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                <a href="services.php" style="background-color: #0d47a1; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 0.9rem;">
                    <i class="fas fa-wrench"></i> Manage Services
                </a>
                <a href="appointments.php" style="background-color: #0d47a1; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 0.9rem;">
                    <i class="fas fa-calendar-check"></i> View Appointments
                </a>
                <a href="messages.php" style="background-color: #0d47a1; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 0.9rem;">
                    <i class="fas fa-envelope"></i> Check Messages
                </a>
                <a href="customers.php" style="background-color: #0d47a1; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 0.9rem;">
                    <i class="fas fa-users"></i> Manage Customers
                </a>
            </div>
        </div>
        
        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <a href="messages.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <div class="card-icon"><i class="fas fa-envelope"></i></div>
                    <div class="card-title">Messages</div>
                    <div class="card-value"><?php echo $messageCount; ?></div>
                </div>
            </a>
            
            <a href="appointments.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="card-title">Appointments</div>
                    <div class="card-value"><?php echo $appointmentCount; ?></div>
                </div>
            </a>
            
            <a href="services.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <div class="card-icon"><i class="fas fa-wrench"></i></div>
                    <div class="card-title">Services</div>
                    <div class="card-value"><?php echo $serviceCount; ?></div>
                </div>
            </a>
            
            <a href="customers.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <div class="card-icon"><i class="fas fa-users"></i></div>
                    <div class="card-title">Customers</div>
                    <div class="card-value"><?php echo $userCount; ?></div>
                </div>
            </a>
        </div>
        
        <!-- Recent Messages -->
        <div class="recent-messages">
            <h2>Recent Messages</h2>
            
            <?php if (empty($recentMessages)): ?>
                <p>No messages yet.</p>
            <?php else: ?>
                <?php foreach ($recentMessages as $message): ?>
                    <div class="message-item">
                        <div class="message-header">
                            <div class="message-sender"><?php echo htmlspecialchars($message['name']); ?> (<?php echo htmlspecialchars($message['email']); ?>)</div>
                            <div class="message-date"><?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></div>
                        </div>
                        <div class="message-content"><?php echo htmlspecialchars(substr($message['message'], 0, 150)) . (strlen($message['message']) > 150 ? '...' : ''); ?></div>
                        <div class="message-actions">
                            <a href="messages.php?view=<?php echo $message['id']; ?>"><i class="fas fa-eye"></i> View</a>
                            <a href="messages.php?view=<?php echo $message['id']; ?>"><i class="fas fa-reply"></i> Reply</a>
                            <form method="POST" action="messages.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" style="background: none; border: none; color: #0d47a1; cursor: pointer; font-size: 0.9rem; padding: 0; text-decoration: none; font-family: inherit;">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>