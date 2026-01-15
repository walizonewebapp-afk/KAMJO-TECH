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

// Get current settings
$settings = [];
$query = "SELECT * FROM settings";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_name']] = $row['setting_value'];
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update general settings
    if (isset($_POST['update_general'])) {
        $siteName = trim($_POST['site_name']);
        $siteDescription = trim($_POST['site_description']);
        $contactEmail = trim($_POST['contact_email']);
        $contactPhone = trim($_POST['contact_phone']);
        $address = trim($_POST['address']);
        $businessHours = trim($_POST['business_hours']);
        
        // Validate inputs
        if (empty($siteName) || empty($contactEmail)) {
            $message = 'Site name and contact email are required.';
            $messageType = 'error';
        } else {
            // Update settings
            $settingsToUpdate = [
                'site_name' => $siteName,
                'site_description' => $siteDescription,
                'contact_email' => $contactEmail,
                'contact_phone' => $contactPhone,
                'address' => $address,
                'business_hours' => $businessHours
            ];
            
            $success = true;
            foreach ($settingsToUpdate as $key => $value) {
                // Check if setting exists
                $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM settings WHERE setting_name = ?");
                $checkStmt->bind_param("s", $key);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                $exists = $checkResult->fetch_assoc()['count'] > 0;
                $checkStmt->close();
                
                if ($exists) {
                    // Update existing setting
                    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_name = ?");
                    $stmt->bind_param("ss", $value, $key);
                } else {
                    // Insert new setting
                    $stmt = $conn->prepare("INSERT INTO settings (setting_name, setting_value) VALUES (?, ?)");
                    $stmt->bind_param("ss", $key, $value);
                }
                
                if (!$stmt->execute()) {
                    $success = false;
                    $message = 'Error updating settings: ' . $conn->error;
                    $messageType = 'error';
                }
                $stmt->close();
            }
            
            if ($success) {
                $message = 'General settings updated successfully.';
                $messageType = 'success';
                
                // Refresh settings
                $result = $conn->query($query);
                if ($result && $result->num_rows > 0) {
                    $settings = [];
                    while ($row = $result->fetch_assoc()) {
                        $settings[$row['setting_name']] = $row['setting_value'];
                    }
                }
            }
        }
    }
    
    // Update feature settings
    if (isset($_POST['update_features'])) {
        $enableOnlineBooking = isset($_POST['enable_online_booking']) ? '1' : '0';
        $maintenanceMode = isset($_POST['maintenance_mode']) ? '1' : '0';
        
        // Update settings
        $settingsToUpdate = [
            'enable_online_booking' => $enableOnlineBooking,
            'maintenance_mode' => $maintenanceMode
        ];
        
        $success = true;
        foreach ($settingsToUpdate as $key => $value) {
            // Check if setting exists
            $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM settings WHERE setting_name = ?");
            $checkStmt->bind_param("s", $key);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $exists = $checkResult->fetch_assoc()['count'] > 0;
            $checkStmt->close();
            
            if ($exists) {
                // Update existing setting
                $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_name = ?");
                $stmt->bind_param("ss", $value, $key);
            } else {
                // Insert new setting
                $stmt = $conn->prepare("INSERT INTO settings (setting_name, setting_value) VALUES (?, ?)");
                $stmt->bind_param("ss", $key, $value);
            }
            
            if (!$stmt->execute()) {
                $success = false;
                $message = 'Error updating settings: ' . $conn->error;
                $messageType = 'error';
            }
            $stmt->close();
        }
        
        if ($success) {
            $message = 'Feature settings updated successfully.';
            $messageType = 'success';
            
            // Refresh settings
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                $settings = [];
                while ($row = $result->fetch_assoc()) {
                    $settings[$row['setting_name']] = $row['setting_value'];
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Walizone Autotech Admin</title>
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
        
        /* Settings Sections */
        .settings-section {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .settings-title {
            font-size: 1.2rem;
            color: #0d47a1;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
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
        
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .checkbox-group input {
            margin-right: 0.5rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }
        
        .btn-primary {
            background-color: #0d47a1;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1565c0;
        }
        
        .btn-secondary {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .btn-secondary:hover {
            background-color: #e0e0e0;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
    </style>
</head>
<body>
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
            <li><a href="customers.php"><i class="fas fa-users"></i> <span>Customers</span></a></li>
            <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Settings</h1>
            <div class="user-info">
                <img src="https://via.placeholder.com/40" alt="Admin">
                <span>Admin</span>
            </div>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="settings-section">
            <h2 class="settings-title">General Settings</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="site_name">Site Name</label>
                    <input type="text" id="site_name" name="site_name" class="form-control" value="<?php echo htmlspecialchars($settings['site_name'] ?? 'Walizone Autotech'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="site_description">Site Description</label>
                    <textarea id="site_description" name="site_description" class="form-control"><?php echo htmlspecialchars($settings['site_description'] ?? 'Professional Auto Repair and Maintenance Services'); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="contact_email">Contact Email</label>
                    <input type="email" id="contact_email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($settings['contact_email'] ?? 'info@walizoneautotech.com'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="contact_phone">Contact Phone</label>
                    <input type="text" id="contact_phone" name="contact_phone" class="form-control" value="<?php echo htmlspecialchars($settings['contact_phone'] ?? '+1234567890'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Business Address</label>
                    <textarea id="address" name="address" class="form-control"><?php echo htmlspecialchars($settings['address'] ?? '123 Auto Street, Mechanic City, MC 12345'); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="business_hours">Business Hours</label>
                    <textarea id="business_hours" name="business_hours" class="form-control"><?php echo htmlspecialchars($settings['business_hours'] ?? 'Monday-Friday: 8am-6pm, Saturday: 9am-3pm, Sunday: Closed'); ?></textarea>
                </div>
                
                <button type="submit" name="update_general" class="btn btn-primary">Save General Settings</button>
            </form>
        </div>
        
        <div class="settings-section">
            <h2 class="settings-title">Feature Settings</h2>
            <form method="POST" action="">
                <div class="checkbox-group">
                    <input type="checkbox" id="enable_online_booking" name="enable_online_booking" <?php echo (isset($settings['enable_online_booking']) && $settings['enable_online_booking'] == '1') ? 'checked' : ''; ?>>
                    <label for="enable_online_booking">Enable Online Booking</label>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="maintenance_mode" name="maintenance_mode" <?php echo (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == '1') ? 'checked' : ''; ?>>
                    <label for="maintenance_mode">Maintenance Mode</label>
                </div>
                
                <button type="submit" name="update_features" class="btn btn-primary">Save Feature Settings</button>
            </form>
        </div>
    </div>
    
    <script>
        // Add any JavaScript functionality here
    </script>
</body>
</html>