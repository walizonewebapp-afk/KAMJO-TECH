<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once '../config/db.php';

$message = '';
$messageType = '';

// Check if status column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'status'");
$statusExists = $result->num_rows > 0;

if (!$statusExists) {
    // Add status column
    $query = "ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive', 'suspended') DEFAULT 'active' AFTER role";
    
    if ($conn->query($query)) {
        $message = "Status column added successfully to users table.";
        $messageType = "success";
    } else {
        $message = "Error adding status column: " . $conn->error;
        $messageType = "error";
    }
} else {
    $message = "Status column already exists in users table.";
    $messageType = "info";
}

// Check if last_login column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'last_login'");
$lastLoginExists = $result->num_rows > 0;

if (!$lastLoginExists) {
    // Add last_login column
    $query = "ALTER TABLE users ADD COLUMN last_login DATETIME NULL AFTER created_at";
    
    if ($conn->query($query)) {
        $message .= "<br>Last login column added successfully to users table.";
        $messageType = "success";
    } else {
        $message .= "<br>Error adding last_login column: " . $conn->error;
        $messageType = "error";
    }
} else {
    $message .= "<br>Last login column already exists in users table.";
    $messageType = "info";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Users Table - Walizone Autotech Admin</title>
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
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #0d47a1, #1976d2);
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            width: 100%;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .header {
            background-color: #0d47a1;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .content {
            padding: 30px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
        
        .alert-info {
            background-color: #e3f2fd;
            color: #1976d2;
            border: 1px solid #bbdefb;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0d47a1;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 500;
            margin-top: 20px;
        }
        
        .btn:hover {
            background-color: #1565c0;
        }
        
        .btn i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Update Users Table</h1>
        </div>
        
        <div class="content">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <p>This utility adds necessary columns to the users table for enhanced functionality.</p>
            
            <a href="index.php" class="btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>