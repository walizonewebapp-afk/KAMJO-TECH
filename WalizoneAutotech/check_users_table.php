<?php
// Include database connection
require_once 'config/db.php';

// Check if users table exists
$tableExists = $conn->query("SHOW TABLES LIKE 'users'")->num_rows > 0;

if (!$tableExists) {
    echo "The users table does not exist.";
    exit;
}

// Get table structure
$result = $conn->query("DESCRIBE users");

if ($result) {
    echo "<h2>Users Table Structure:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . ($row['Default'] === NULL ? 'NULL' : $row['Default']) . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "Error: " . $conn->error;
}

// Get sample data
$result = $conn->query("SELECT * FROM users LIMIT 1");

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<h2>Sample User Data (Column Names Only):</h2>";
    echo "<ul>";
    foreach ($row as $column => $value) {
        echo "<li>" . $column . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No users found in the table or error: " . $conn->error . "</p>";
}
?>