<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];
$message = '';
$messageType = '';

// Get admin users for sending messages
$admins = [];
$stmt = $conn->prepare("SELECT id, full_name FROM users WHERE role = 'admin'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $admins[] = $row;
}
$stmt->close();

// Handle sending new message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiverId = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : null;
    $subject = trim($_POST['subject']);
    $messageContent = trim($_POST['message']);
    
    if (empty($subject) || empty($messageContent)) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } else {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $userId, $receiverId, $subject, $messageContent);
        
        if ($stmt->execute()) {
            $message = 'Message sent successfully.';
            $messageType = 'success';
        } else {
            $message = 'Error sending message: ' . $conn->error;
            $messageType = 'error';
        }
        $stmt->close();
    }
}

// Get user's messages (sent and received)
$messages = [];
$query = "
    SELECT m.*, 
           s.full_name as sender_name, 
           r.full_name as receiver_name
    FROM messages m
    LEFT JOIN users s ON m.sender_id = s.id
    LEFT JOIN users r ON m.receiver_id = r.id
    WHERE m.sender_id = ? OR m.receiver_id = ?
    ORDER BY m.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $userId, $userId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();

// Get unread message count
$unreadCount = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $unreadCount = $row['count'];
}
$stmt->close();

// Mark message as read
if (isset($_GET['read']) && is_numeric($_GET['read'])) {
    $messageId = (int)$_GET['read'];
    
    // Verify this message belongs to the current user
    $stmt = $conn->prepare("SELECT id FROM messages WHERE id = ? AND receiver_id = ?");
    $stmt->bind_param("ii", $messageId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $conn->query("UPDATE messages SET is_read = 1 WHERE id = $messageId");
    }
    $stmt->close();
}

// Get message details if viewing a specific message
$viewMessage = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $messageId = (int)$_GET['view'];
    
    // Verify this message belongs to the current user
    $stmt = $conn->prepare("
        SELECT m.*, 
               s.full_name as sender_name, 
               r.full_name as receiver_name
        FROM messages m
        LEFT JOIN users s ON m.sender_id = s.id
        LEFT JOIN users r ON m.receiver_id = r.id
        WHERE m.id = ? AND (m.sender_id = ? OR m.receiver_id = ?)
    ");
    $stmt->bind_param("iii", $messageId, $userId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $viewMessage = $result->fetch_assoc();
        
        // Mark as read if user is the receiver
        if ($viewMessage['receiver_id'] == $userId && $viewMessage['is_read'] == 0) {
            $conn->query("UPDATE messages SET is_read = 1 WHERE id = $messageId");
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Walizone Autotech</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .messages-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        
        .message-sidebar {
            flex: 1;
            background-color: #f5f5f5;
            border-radius: 5px;
            padding: 15px;
        }
        
        .message-content {
            flex: 2;
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .message-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .message-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .message-item:hover {
            background-color: #f0f0f0;
        }
        
        .message-item.unread {
            background-color: #e3f2fd;
            font-weight: bold;
        }
        
        .message-item.active {
            background-color: #bbdefb;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .message-sender {
            font-weight: bold;
        }
        
        .message-date {
            color: #757575;
            font-size: 0.8rem;
        }
        
        .message-subject {
            margin-bottom: 5px;
        }
        
        .message-preview {
            color: #757575;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .message-view {
            margin-top: 20px;
        }
        
        .message-view-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .message-view-subject {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .message-view-info {
            display: flex;
            justify-content: space-between;
            color: #757575;
        }
        
        .message-view-body {
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .message-form {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        textarea.form-control {
            min-height: 150px;
        }
        
        .btn {
            padding: 10px 15px;
            background-color: #0d47a1;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: #1565c0;
        }
        
        .alert {
            padding: 10px 15px;
            border-radius: 4px;
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
        
        .badge {
            display: inline-block;
            background-color: #f44336;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            margin-left: 5px;
        }
        
        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        
        .tab {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }
        
        .tab.active {
            border-bottom-color: #0d47a1;
            color: #0d47a1;
            font-weight: bold;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .reply-form {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <h1>Messages <?php if ($unreadCount > 0): ?><span class="badge"><?php echo $unreadCount; ?></span><?php endif; ?></h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="tabs">
            <div class="tab active" data-tab="inbox">Inbox</div>
            <div class="tab" data-tab="compose">Compose New Message</div>
        </div>
        
        <div class="tab-content active" id="inbox">
            <?php if (empty($messages)): ?>
                <p>You have no messages.</p>
            <?php else: ?>
                <div class="messages-container">
                    <div class="message-sidebar">
                        <ul class="message-list">
                            <?php foreach ($messages as $msg): ?>
                                <?php 
                                $isUnread = $msg['is_read'] == 0 && $msg['receiver_id'] == $userId;
                                $isActive = isset($_GET['view']) && $_GET['view'] == $msg['id'];
                                ?>
                                <li class="message-item <?php echo $isUnread ? 'unread' : ''; ?> <?php echo $isActive ? 'active' : ''; ?>">
                                    <a href="messages.php?view=<?php echo $msg['id']; ?>" style="text-decoration: none; color: inherit; display: block;">
                                        <div class="message-header">
                                            <span class="message-sender">
                                                <?php 
                                                if ($msg['sender_id'] == $userId) {
                                                    echo 'To: ' . htmlspecialchars($msg['receiver_name']);
                                                } else {
                                                    echo 'From: ' . htmlspecialchars($msg['sender_name']);
                                                }
                                                ?>
                                            </span>
                                            <span class="message-date"><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></span>
                                        </div>
                                        <div class="message-subject"><?php echo htmlspecialchars($msg['subject']); ?></div>
                                        <div class="message-preview"><?php echo htmlspecialchars(substr($msg['message'], 0, 50)) . (strlen($msg['message']) > 50 ? '...' : ''); ?></div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div class="message-content">
                        <?php if ($viewMessage): ?>
                            <div class="message-view">
                                <div class="message-view-header">
                                    <div class="message-view-subject"><?php echo htmlspecialchars($viewMessage['subject']); ?></div>
                                    <div class="message-view-info">
                                        <div>
                                            <?php 
                                            if ($viewMessage['sender_id'] == $userId) {
                                                echo 'To: ' . htmlspecialchars($viewMessage['receiver_name']);
                                            } else {
                                                echo 'From: ' . htmlspecialchars($viewMessage['sender_name']);
                                            }
                                            ?>
                                        </div>
                                        <div><?php echo date('F d, Y h:i A', strtotime($viewMessage['created_at'])); ?></div>
                                    </div>
                                </div>
                                
                                <div class="message-view-body">
                                    <?php echo nl2br(htmlspecialchars($viewMessage['message'])); ?>
                                </div>
                                
                                <?php if ($viewMessage['sender_id'] != $userId): ?>
                                    <div class="reply-form">
                                        <h3>Reply</h3>
                                        <form method="POST" action="">
                                            <input type="hidden" name="receiver_id" value="<?php echo $viewMessage['sender_id']; ?>">
                                            <input type="hidden" name="subject" value="RE: <?php echo htmlspecialchars($viewMessage['subject']); ?>">
                                            
                                            <div class="form-group">
                                                <label for="message">Your Reply</label>
                                                <textarea name="message" id="message" class="form-control" required></textarea>
                                            </div>
                                            
                                            <button type="submit" name="send_message" class="btn">Send Reply</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <p>Select a message to view its contents.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="tab-content" id="compose">
            <div class="message-form">
                <h2>Compose New Message</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="receiver_id">To</label>
                        <select name="receiver_id" id="receiver_id" class="form-control" required>
                            <option value="">Select Recipient</option>
                            <?php foreach ($admins as $admin): ?>
                                <option value="<?php echo $admin['id']; ?>"><?php echo htmlspecialchars($admin['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" name="subject" id="subject" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea name="message" id="message" class="form-control" required></textarea>
                    </div>
                    
                    <button type="submit" name="send_message" class="btn">Send Message</button>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Tab functionality
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabId = tab.getAttribute('data-tab');
                
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding content
                tab.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
    </script>
</body>
</html>