<?php
// Database connection
require_once 'config/db.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize inputs
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        $response['message'] = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please enter a valid email address.';
    } else {
        try {
            // Insert into database
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $name, $email, $message);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Thank you for your message. We will get back to you soon!';
                
                // Optional: Send email notification
                $to = "mwakamule@gmail.com"; // Updated email address
                $subject = "New Contact Form Submission";
                $email_content = "Name: $name\n";
                $email_content .= "Email: $email\n\n";
                $email_content .= "Message:\n$message\n";
                
                // Headers
                $headers = "From: $email";
                
                // Send email (uncomment when ready to use)
                // mail($to, $subject, $email_content, $headers);
                
            } else {
                $response['message'] = 'Sorry, there was an error saving your message. Please try again.';
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $response['message'] = 'A system error occurred. Please try again later.';
            // Log the error (in a production environment)
            // error_log($e->getMessage());
        }
    }
}

// Return JSON response for AJAX requests
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// For non-AJAX requests, redirect back to the contact section with a status message
$status = $response['success'] ? 'success' : 'error';
$redirect = "index.php#contact?status=$status&message=" . urlencode($response['message']);
header("Location: $redirect");
exit;
?>