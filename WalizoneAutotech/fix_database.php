<?php
// Include database connection
require_once 'config/db.php';

echo "<h1>Database Fix Script</h1>";

// Function to execute SQL and display results
function executeSql($conn, $sql, $description) {
    echo "<h3>$description</h3>";
    
    try {
        if ($conn->query($sql)) {
            echo "<p style='color:green'>✓ Success</p>";
        } else {
            echo "<p style='color:red'>✗ Error: " . $conn->error . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:orange'>⚠ Notice: " . $e->getMessage() . "</p>";
    }
    echo "<hr>";
}

// Fix vehicles table
executeSql($conn, "DESCRIBE vehicles", "Checking vehicles table structure");

// Add missing columns to vehicles table
$columns = [
    "mileage" => "INT NULL",
    "engine_type" => "VARCHAR(100) NULL",
    "transmission" => "VARCHAR(50) NULL",
    "status" => "VARCHAR(20) DEFAULT 'active'"
];

foreach ($columns as $column => $definition) {
    // Check if column exists
    $result = $conn->query("SHOW COLUMNS FROM vehicles LIKE '$column'");
    
    if ($result && $result->num_rows == 0) {
        // Column doesn't exist, add it
        executeSql($conn, "ALTER TABLE vehicles ADD COLUMN $column $definition", "Adding $column column to vehicles table");
    } else {
        echo "<p>Column '$column' already exists in vehicles table.</p>";
    }
}

// Create service_history table if it doesn't exist
$tableExists = $conn->query("SHOW TABLES LIKE 'service_history'")->num_rows > 0;

if (!$tableExists) {
    $sql = "
    CREATE TABLE service_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vehicle_id INT NOT NULL,
        service_date DATE NOT NULL,
        service_type VARCHAR(100) NOT NULL,
        description TEXT,
        mileage INT,
        cost DECIMAL(10,2),
        technician VARCHAR(100),
        status VARCHAR(20) DEFAULT 'scheduled',
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    executeSql($conn, $sql, "Creating service_history table");
    
    // Add foreign key separately
    $sql = "ALTER TABLE service_history ADD CONSTRAINT fk_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE";
    executeSql($conn, $sql, "Adding foreign key to service_history table");
} else {
    echo "<p>service_history table already exists.</p>";
}

// Fix the bind_param in my-vehicles.php
$myVehiclesFile = file_get_contents('my-vehicles.php');

// Fix the insert bind_param
$oldInsertBindParam = '$stmt->bind_param("issisissss", $customerId, $make, $model, $year, $licensePlate, $vin, $color, $mileage, $engineType, $transmission);';
$newInsertBindParam = '$stmt->bind_param("issississs", $customerId, $make, $model, $year, $licensePlate, $vin, $color, $mileage, $engineType, $transmission);';

if (strpos($myVehiclesFile, $oldInsertBindParam) !== false) {
    $myVehiclesFile = str_replace($oldInsertBindParam, $newInsertBindParam, $myVehiclesFile);
    echo "<p>Fixed insert bind_param in my-vehicles.php</p>";
} else {
    echo "<p>Insert bind_param already fixed or not found in my-vehicles.php</p>";
}

// Fix the update bind_param
$oldUpdateBindParam = '$stmt->bind_param("ssisssissii", $make, $model, $year, $licensePlate, $vin, $color, $mileage, $engineType, $transmission, $vehicleId, $customerId);';
$newUpdateBindParam = '$stmt->bind_param("ssississii", $make, $model, $year, $licensePlate, $vin, $color, $mileage, $engineType, $transmission, $vehicleId, $customerId);';

if (strpos($myVehiclesFile, $oldUpdateBindParam) !== false) {
    $myVehiclesFile = str_replace($oldUpdateBindParam, $newUpdateBindParam, $myVehiclesFile);
    echo "<p>Fixed update bind_param in my-vehicles.php</p>";
} else {
    echo "<p>Update bind_param already fixed or not found in my-vehicles.php</p>";
}

// Save the updated file
file_put_contents('my-vehicles.php', $myVehiclesFile);

echo "<h2>Database fix completed!</h2>";
echo "<p><a href='my-vehicles.php' style='padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Go to My Vehicles</a></p>";
?>