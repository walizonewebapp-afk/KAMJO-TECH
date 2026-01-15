<?php
// Include database connection
require_once 'config/db.php';

// Check if tables exist
echo "<h1>Database Table Check</h1>";

// Function to check if a table exists
function tableExists($conn, $tableName) {
    $query = "SHOW TABLES LIKE '$tableName'";
    $result = $conn->query($query);
    return $result->num_rows > 0;
}

// Check customers table
if (tableExists($conn, 'customers')) {
    echo "<p style='color:green'>✓ Customers table exists</p>";
    
    // Count records
    $result = $conn->query("SELECT COUNT(*) as count FROM customers");
    $row = $result->fetch_assoc();
    echo "<p>Number of customers: {$row['count']}</p>";
} else {
    echo "<p style='color:red'>✗ Customers table does not exist</p>";
    
    // Try to create the table
    echo "<p>Attempting to create customers table...</p>";
    $createTable = "CREATE TABLE customers (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone VARCHAR(20) NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at DATETIME NOT NULL,
        last_login DATETIME NULL,
        status ENUM('active', 'inactive') DEFAULT 'active'
    )";
    
    if ($conn->query($createTable)) {
        echo "<p style='color:green'>✓ Customers table created successfully</p>";
    } else {
        echo "<p style='color:red'>✗ Failed to create customers table: " . $conn->error . "</p>";
    }
}

// Check vehicles table
if (tableExists($conn, 'vehicles')) {
    echo "<p style='color:green'>✓ Vehicles table exists</p>";
    
    // Count records
    $result = $conn->query("SELECT COUNT(*) as count FROM vehicles");
    $row = $result->fetch_assoc();
    echo "<p>Number of vehicles: {$row['count']}</p>";
} else {
    echo "<p style='color:red'>✗ Vehicles table does not exist</p>";
    
    // Try to create the table
    echo "<p>Attempting to create vehicles table...</p>";
    $createTable = "CREATE TABLE vehicles (
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
    
    if ($conn->query($createTable)) {
        echo "<p style='color:green'>✓ Vehicles table created successfully</p>";
    } else {
        echo "<p style='color:red'>✗ Failed to create vehicles table: " . $conn->error . "</p>";
    }
}

// Check appointments table
if (tableExists($conn, 'appointments')) {
    echo "<p style='color:green'>✓ Appointments table exists</p>";
    
    // Count records
    $result = $conn->query("SELECT COUNT(*) as count FROM appointments");
    $row = $result->fetch_assoc();
    echo "<p>Number of appointments: {$row['count']}</p>";
} else {
    echo "<p style='color:red'>✗ Appointments table does not exist</p>";
}

// Check services table
if (tableExists($conn, 'services')) {
    echo "<p style='color:green'>✓ Services table exists</p>";
    
    // Count records
    $result = $conn->query("SELECT COUNT(*) as count FROM services");
    $row = $result->fetch_assoc();
    echo "<p>Number of services: {$row['count']}</p>";
} else {
    echo "<p style='color:red'>✗ Services table does not exist</p>";
}

// Check users table
if (tableExists($conn, 'users')) {
    echo "<p style='color:green'>✓ Users table exists</p>";
    
    // Count records
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetch_assoc();
    echo "<p>Number of users: {$row['count']}</p>";
} else {
    echo "<p style='color:red'>✗ Users table does not exist</p>";
}

// Check contact_messages table
if (tableExists($conn, 'contact_messages')) {
    echo "<p style='color:green'>✓ Contact messages table exists</p>";
    
    // Count records
    $result = $conn->query("SELECT COUNT(*) as count FROM contact_messages");
    $row = $result->fetch_assoc();
    echo "<p>Number of contact messages: {$row['count']}</p>";
} else {
    echo "<p style='color:red'>✗ Contact messages table does not exist</p>";
}

echo "<p><a href='index.php'>Return to Homepage</a></p>";
?>