<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'root';  // Default XAMPP username
$db_pass = '';      // Default XAMPP password (empty)
$db_name = 'walizone_autotech';

// First, create a connection without selecting a database
$temp_conn = new mysqli($db_host, $db_user, $db_pass);

// Check connection
if ($temp_conn->connect_error) {
    die("Connection failed: " . $temp_conn->connect_error);
}

// Create the database if it doesn't exist
$temp_conn->query("CREATE DATABASE IF NOT EXISTS $db_name");

// Close the temporary connection
$temp_conn->close();

// Now create a connection with the database selected
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to ensure proper handling of special characters
$conn->set_charset("utf8mb4");

// Create tables if they don't exist
function createTables($conn) {
    // Contact messages table
    $contact_table = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at DATETIME NOT NULL,
        status ENUM('new', 'read', 'replied') DEFAULT 'new'
    )";
    
    // Services table
    $services_table = "CREATE TABLE IF NOT EXISTS services (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        icon VARCHAR(50),
        created_at DATETIME NOT NULL
    )";
    
    // Users table
    $users_table = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100),
        role ENUM('admin', 'staff') DEFAULT 'staff',
        created_at DATETIME NOT NULL,
        last_login DATETIME
    )";
    
    // Appointments table
    $appointments_table = "CREATE TABLE IF NOT EXISTS appointments (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        service VARCHAR(100) NOT NULL,
        appointment_date DATE NOT NULL,
        appointment_time TIME NOT NULL,
        message TEXT,
        status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
        created_at DATETIME NOT NULL
    )";
    
    // Customers table
    $customers_table = "CREATE TABLE IF NOT EXISTS customers (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone VARCHAR(20) NOT NULL,
        password VARCHAR(255) NOT NULL,
        address TEXT,
        city VARCHAR(50),
        created_at DATETIME NOT NULL,
        last_login DATETIME,
        status ENUM('active', 'inactive') DEFAULT 'active'
    )";
    
    // Vehicles table
    $vehicles_table = "CREATE TABLE IF NOT EXISTS vehicles (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        customer_id INT(11) UNSIGNED NOT NULL,
        make VARCHAR(50) NOT NULL,
        model VARCHAR(50) NOT NULL,
        year INT(4) NOT NULL,
        license_plate VARCHAR(20),
        vin VARCHAR(50),
        color VARCHAR(30),
        created_at DATETIME NOT NULL,
        FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
    )";
    
    // Execute queries
    $conn->query($contact_table);
    $conn->query($services_table);
    $conn->query($users_table);
    $conn->query($appointments_table);
    $conn->query($customers_table);
    $conn->query($vehicles_table);
    
    // Check if services table is empty, if so, add sample data
    $result = $conn->query("SELECT COUNT(*) as count FROM services");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        // Insert sample services
        $services_data = "INSERT INTO services (name, description, icon, created_at) VALUES
        ('Routine Maintenance', 'Oil changes, tire rotations, brake checks, and more.', 'wrench', NOW()),
        ('Computer Diagnostics', 'Advanced computerized vehicle diagnostics and repairs.', 'laptop', NOW()),
        ('Engine & Transmission Repair', 'Full service engine repairs, transmission fixes, and replacements.', 'cogs', NOW()),
        ('AC & Heating Services', 'AC recharge, heating system repairs, and diagnostics.', 'snowflake', NOW()),
        ('Suspension & Steering', 'Repairing shocks, struts, and ensuring a smooth ride.', 'car', NOW()),
        ('Panel Beating & Painting', 'High-performance panel beating and custom spray painting.', 'paint-brush', NOW()),
        ('Electrical Systems', 'Battery tests, alternator repairs, and full electrical diagnostics.', 'bolt', NOW()),
        ('Custom Modifications', 'Performance upgrades, body kits, and custom car designs.', 'tools', NOW())";
        
        $conn->query($services_data);
    }
    
    // Check if users table is empty, if so, add admin user
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        // Insert admin user (password: admin123)
        $admin_user = "INSERT INTO users (username, email, password, full_name, role, created_at) VALUES
        ('admin', 'mwakamule@gmail.com', '$2y$10$8KzO3LOgMxQQWJHXJM0YAuB5hPUMp.ZjQOQj5ggqPeD/4PEpRQOHi', 'System Administrator', 'admin', NOW())";
        
        $conn->query($admin_user);
    }
}

// Create tables automatically
try {
    createTables($conn);
    
    // Force creation of customers and vehicles tables if they don't exist
    $checkCustomersTable = "SHOW TABLES LIKE 'customers'";
    $customersTableExists = $conn->query($checkCustomersTable);
    
    if ($customersTableExists->num_rows == 0) {
        // Create customers table
        $customers_table = "CREATE TABLE IF NOT EXISTS customers (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            phone VARCHAR(20) NOT NULL,
            password VARCHAR(255) NOT NULL,
            address TEXT,
            city VARCHAR(50),
            created_at DATETIME NOT NULL,
            last_login DATETIME,
            status ENUM('active', 'inactive') DEFAULT 'active'
        )";
        $conn->query($customers_table);
        
        // Create vehicles table
        $vehicles_table = "CREATE TABLE IF NOT EXISTS vehicles (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            customer_id INT(11) UNSIGNED NOT NULL,
            make VARCHAR(50) NOT NULL,
            model VARCHAR(50) NOT NULL,
            year INT(4) NOT NULL,
            license_plate VARCHAR(20),
            vin VARCHAR(50),
            color VARCHAR(30),
            created_at DATETIME NOT NULL,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
        )";
        $conn->query($vehicles_table);
    }
} catch (Exception $e) {
    // Log error but don't display it
    error_log("Error creating tables: " . $e->getMessage());
}
?>