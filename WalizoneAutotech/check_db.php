<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'root';  // Default XAMPP username
$db_pass = '';      // Default XAMPP password (empty)
$db_name = 'walizone_autotech';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to MySQL server successfully.<br>";

// Check if database exists
$result = $conn->query("SHOW DATABASES LIKE '$db_name'");
if ($result->num_rows > 0) {
    echo "Database '$db_name' exists.<br>";
} else {
    echo "Database '$db_name' does not exist. Creating it now...<br>";
    if ($conn->query("CREATE DATABASE IF NOT EXISTS $db_name")) {
        echo "Database created successfully.<br>";
    } else {
        echo "Error creating database: " . $conn->error . "<br>";
    }
}

// Select the database
$conn->select_db($db_name);

// Check if tables exist
$tables = ['customers', 'vehicles', 'appointments', 'services', 'users', 'contact_messages'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "Table '$table' exists.<br>";
    } else {
        echo "Table '$table' does not exist.<br>";
    }
}

// Create customers table if it doesn't exist
$result = $conn->query("SHOW TABLES LIKE 'customers'");
if ($result->num_rows == 0) {
    echo "Creating customers table...<br>";
    $sql = "CREATE TABLE customers (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone VARCHAR(20) NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at DATETIME NOT NULL,
        last_login DATETIME NULL,
        status ENUM('active', 'inactive') DEFAULT 'active'
    )";
    
    if ($conn->query($sql)) {
        echo "Customers table created successfully.<br>";
    } else {
        echo "Error creating customers table: " . $conn->error . "<br>";
    }
}

// Create vehicles table if it doesn't exist
$result = $conn->query("SHOW TABLES LIKE 'vehicles'");
if ($result->num_rows == 0) {
    echo "Creating vehicles table...<br>";
    $sql = "CREATE TABLE vehicles (
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
    
    if ($conn->query($sql)) {
        echo "Vehicles table created successfully.<br>";
    } else {
        echo "Error creating vehicles table: " . $conn->error . "<br>";
    }
}

// Close connection
$conn->close();

echo "<br><a href='index.php'>Return to Homepage</a>";
?>