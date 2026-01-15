<?php
// Include database connection
require_once 'config/db.php';

// Get all tables
$result = $conn->query("SHOW TABLES");

if ($result) {
    echo "<h2>Database Tables:</h2>";
    echo "<ul>";
    
    while ($row = $result->fetch_row()) {
        echo "<li>" . $row[0] . "</li>";
    }
    
    echo "</ul>";
} else {
    echo "Error: " . $conn->error;
}
?>