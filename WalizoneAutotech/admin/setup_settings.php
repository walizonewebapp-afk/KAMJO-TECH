<?php
// Include database connection
require_once '../config/db.php';

// Create settings table if it doesn't exist
$query = "
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(255) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
";

if ($conn->query($query)) {
    echo "Settings table created or already exists.<br>";
} else {
    echo "Error creating settings table: " . $conn->error . "<br>";
}

// Insert default settings if they don't exist
$defaultSettings = [
    'business_name' => 'Walizone Autotech Enterprise',
    'business_email' => 'mwakamule@gmail.com',
    'business_phone' => '0976664017',
    'business_address' => 'Chinsali, Shambalakale Road, opposite Jesims Lodge',
    'business_hours' => 'Mon-Sat: 8:00AM - 5:00PM',
    'about_us' => 'Established in 2003, Walizone Autotech Enterprise has been providing reliable, affordable, and high-quality automotive solutions to the Chinsali community and beyond.',
    'facebook_url' => '',
    'twitter_url' => '',
    'instagram_url' => '',
    'linkedin_url' => ''
];

foreach ($defaultSettings as $key => $value) {
    // Check if setting exists
    $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM settings WHERE setting_key = ?");
    $checkStmt->bind_param("s", $key);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $exists = $checkResult->fetch_assoc()['count'] > 0;
    $checkStmt->close();
    
    if (!$exists) {
        // Insert new setting
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->bind_param("ss", $key, $value);
        
        if ($stmt->execute()) {
            echo "Default setting '$key' inserted.<br>";
        } else {
            echo "Error inserting default setting '$key': " . $conn->error . "<br>";
        }
        
        $stmt->close();
    }
}

echo "<br>Settings setup complete. <a href='settings.php'>Go to Settings</a>";
?>