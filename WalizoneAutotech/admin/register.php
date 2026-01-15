<?php
session_start();

// Check if already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: index.php");
    exit;
}

// Include database connection
require_once '../config/db.php';

$error = '';
$success = '';

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $registrationCode = isset($_POST['registration_code']) ? trim($_POST['registration_code']) : '';
    
    // Validate inputs
    if (empty($username) || empty($password) || empty($confirmPassword) || empty($fullName) || empty($email) || empty($registrationCode)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // Check if registration code is valid
        // This should be a secure code that only authorized personnel know
        // In a real application, you might want to store this in a database or use a more secure method
        $validRegistrationCode = 'WALIZONE_ADMIN_2023'; // Example code
        
        if ($registrationCode !== $validRegistrationCode) {
            $error = 'Invalid registration code';
        } else {
            // Check if username already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = 'Username already exists';
            } else {
                // Check if email already exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $error = 'Email already exists';
                } else {
                    // Hash password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert new admin user
                    $stmt = $conn->prepare("INSERT INTO users (username, password, full_name, email, role, created_at) VALUES (?, ?, ?, ?, 'admin', NOW())");
                    $stmt->bind_param("ssss", $username, $hashedPassword, $fullName, $email);
                    
                    if ($stmt->execute()) {
                        $success = 'Registration successful! You can now login.';
                    } else {
                        $error = 'Registration failed: ' . $conn->error;
                    }
                }
            }
            
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - Walizone Autotech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #0d47a1, #1976d2);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .register-container {
            max-width: 500px;
            width: 100%;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .register-header {
            background-color: #0d47a1;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .register-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .register-header p {
            margin-top: 5px;
            opacity: 0.8;
            font-size: 0.9rem;
        }
        
        .register-form-container {
            padding: 30px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
        }
        
        .logo img {
            height: 60px;
            margin-right: 15px;
        }
        
        .logo-text {
            display: flex;
            flex-direction: column;
        }
        
        .logo-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0d47a1;
            line-height: 1.2;
        }
        
        .logo-tagline {
            font-size: 0.8rem;
            color: #666;
        }
        
        .error-message {
            background-color: #ffebee;
            color: #d32f2f;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border-left: 4px solid #f44336;
        }
        
        .success-message {
            background-color: #e8f5e9;
            color: #388e3c;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border-left: 4px solid #4caf50;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #1976d2;
            box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.2);
        }
        
        .register-form button {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #0d47a1;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            text-align: center;
        }
        
        .register-form button:hover {
            background-color: #1565c0;
        }
        
        .login-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #0d47a1;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .login-link i {
            margin-right: 5px;
        }
        
        .login-link:hover {
            text-decoration: underline;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #0d47a1;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .back-link i {
            margin-right: 5px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .form-note {
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Admin Registration</h1>
            <p>Create a new admin account</p>
        </div>
        
        <div class="register-form-container">
            <div class="logo">
                <img src="../images/logo.svg" alt="Walizone Autotech Logo" onerror="this.src='../images/logo.png'; this.onerror=null;">
                <div class="logo-text">
                    <span class="logo-name">Walizone Autotech</span>
                    <span class="logo-tagline">Admin Portal</span>
                </div>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form class="register-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" id="full_name" name="full_name" class="form-control" placeholder="Enter your full name" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-group">
                        <i class="fas fa-user-shield"></i>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Choose a username" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
                    </div>
                    <p class="form-note">Password must be at least 6 characters long</p>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="registration_code">Registration Code</label>
                    <div class="input-group">
                        <i class="fas fa-key"></i>
                        <input type="text" id="registration_code" name="registration_code" class="form-control" placeholder="Enter admin registration code" required>
                    </div>
                    <p class="form-note">This code is required for admin registration</p>
                </div>
                
                <button type="submit">
                    <i class="fas fa-user-plus"></i> Register
                </button>
            </form>
            
            <a href="login.php" class="login-link">
                <i class="fas fa-sign-in-alt"></i> Already have an account? Login
            </a>
            
            <a href="../index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Website
            </a>
        </div>
    </div>
</body>
</html>