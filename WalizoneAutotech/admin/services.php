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
$editId = null;
$editName = '';
$editDescription = '';
$editIcon = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add or update service
    if (isset($_POST['action'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $icon = trim($_POST['icon']);
        
        // Validate inputs
        if (empty($name)) {
            $message = 'Service name is required';
            $messageType = 'error';
        } else {
            if ($_POST['action'] === 'add') {
                // Add new service
                $stmt = $conn->prepare("INSERT INTO services (name, description, icon, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("sss", $name, $description, $icon);
                
                if ($stmt->execute()) {
                    $message = 'Service added successfully';
                    $messageType = 'success';
                } else {
                    $message = 'Error adding service: ' . $conn->error;
                    $messageType = 'error';
                }
                
                $stmt->close();
            } elseif ($_POST['action'] === 'update' && isset($_POST['id'])) {
                // Update existing service
                $id = (int)$_POST['id'];
                $stmt = $conn->prepare("UPDATE services SET name = ?, description = ?, icon = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $description, $icon, $id);
                
                if ($stmt->execute()) {
                    $message = 'Service updated successfully';
                    $messageType = 'success';
                } else {
                    $message = 'Error updating service: ' . $conn->error;
                    $messageType = 'error';
                }
                
                $stmt->close();
            }
        }
    }
    
    // Delete service
    if (isset($_POST['delete']) && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        
        // Check if service is used in appointments
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM appointments WHERE service = (SELECT name FROM services WHERE id = ?)");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();
        $checkStmt->close();
        
        if ($row['count'] > 0) {
            $message = 'Cannot delete service: It is used in ' . $row['count'] . ' appointment(s)';
            $messageType = 'error';
        } else {
            $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $message = 'Service deleted successfully';
                $messageType = 'success';
            } else {
                $message = 'Error deleting service: ' . $conn->error;
                $messageType = 'error';
            }
            
            $stmt->close();
        }
    }
}

// Handle edit request
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT id, name, description, icon FROM services WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $service = $result->fetch_assoc();
        $editName = $service['name'];
        $editDescription = $service['description'];
        $editIcon = $service['icon'];
    }
    
    $stmt->close();
}

// Get all services
$services = [];
$query = "SELECT * FROM services ORDER BY name ASC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

// Get available Font Awesome icons for services
$icons = [
    'wrench', 'tools', 'cogs', 'car', 'oil-can', 'car-battery', 'car-crash', 'car-side',
    'bolt', 'tachometer-alt', 'snowflake', 'fan', 'temperature-high', 'paint-brush',
    'spray-can', 'screwdriver', 'hammer', 'toolbox', 'laptop', 'microchip', 'plug',
    'lightbulb', 'gas-pump', 'tint', 'filter', 'tire', 'steering-wheel', 'key'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Management - Walizone Autotech Admin</title>
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
        
        /* Service Form */
        .service-form {
            background-color: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .form-title {
            margin-bottom: 1.5rem;
            color: #0d47a1;
            font-size: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
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
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .icon-selector {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            gap: 0.5rem;
            margin-top: 0.5rem;
            max-height: 200px;
            overflow-y: auto;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .icon-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .icon-option:hover {
            background-color: #f5f5f5;
        }
        
        .icon-option.selected {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
        }
        
        .icon-option i {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
            color: #0d47a1;
        }
        
        .icon-option span {
            font-size: 0.7rem;
            text-align: center;
            word-break: break-all;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
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
        }
        
        .btn-secondary:hover {
            background-color: #e0e0e0;
        }
        
        /* Services Table */
        .services-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .services-table th,
        .services-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .services-table th {
            background-color: #f5f5f5;
            color: #0d47a1;
            font-weight: bold;
        }
        
        .services-table tr:last-child td {
            border-bottom: none;
        }
        
        .services-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .service-icon {
            font-size: 1.5rem;
            color: #0d47a1;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e3f2fd;
            border-radius: 50%;
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
        
        .edit-btn {
            background-color: #1976d2;
        }
        
        .delete-btn {
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
        
        /* Responsive Styles */
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
            
            .icon-selector {
                grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
            }
            
            .services-table {
                font-size: 0.85rem;
            }
            
            .services-table th,
            .services-table td {
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
            <li><a href="services.php" class="active"><i class="fas fa-wrench"></i> <span>Services</span></a></li>
            <li><a href="vehicles.php"><i class="fas fa-car"></i> <span>Vehicles</span></a></li>
            <li><a href="customers.php"><i class="fas fa-users"></i> <span>Customers</span></a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Services Management</h1>
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
        
        <!-- Service Form -->
        <div class="service-form">
            <h2 class="form-title"><?php echo $editId ? 'Edit Service' : 'Add New Service'; ?></h2>
            
            <form method="POST" action="services.php">
                <input type="hidden" name="action" value="<?php echo $editId ? 'update' : 'add'; ?>">
                <?php if ($editId): ?>
                    <input type="hidden" name="id" value="<?php echo $editId; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="name">Service Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($editName); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars($editDescription); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="icon">Icon</label>
                    <input type="text" id="icon" name="icon" class="form-control" value="<?php echo htmlspecialchars($editIcon); ?>" readonly>
                    
                    <div class="icon-selector">
                        <?php foreach ($icons as $icon): ?>
                            <div class="icon-option <?php echo $icon === $editIcon ? 'selected' : ''; ?>" data-icon="<?php echo $icon; ?>">
                                <i class="fas fa-<?php echo $icon; ?>"></i>
                                <span><?php echo $icon; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editId ? 'Update Service' : 'Add Service'; ?>
                    </button>
                    
                    <?php if ($editId): ?>
                        <a href="services.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <?php if (empty($services)): ?>
            <div class="empty-state">
                <i class="fas fa-wrench"></i>
                <h2>No Services Found</h2>
                <p>Start by adding your first service using the form above.</p>
            </div>
        <?php else: ?>
            <!-- Services Table -->
            <table class="services-table">
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td>
                                <div class="service-icon">
                                    <i class="fas fa-<?php echo htmlspecialchars($service['icon']); ?>"></i>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($service['name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($service['description'], 0, 100)) . (strlen($service['description']) > 100 ? '...' : ''); ?></td>
                            <td><?php echo date('M d, Y', strtotime($service['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="services.php?edit=<?php echo $service['id']; ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="POST" action="services.php" onsubmit="return confirm('Are you sure you want to delete this service?');" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                        <button type="submit" name="delete" class="action-btn delete-btn">
                                            <i class="fas fa-trash"></i> Delete
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
    
    <script>
        // Icon selector functionality
        document.addEventListener('DOMContentLoaded', function() {
            const iconOptions = document.querySelectorAll('.icon-option');
            const iconInput = document.getElementById('icon');
            
            iconOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selected class from all options
                    iconOptions.forEach(opt => opt.classList.remove('selected'));
                    
                    // Add selected class to clicked option
                    this.classList.add('selected');
                    
                    // Update input value
                    iconInput.value = this.dataset.icon;
                });
            });
        });
    </script>
</body>
</html>