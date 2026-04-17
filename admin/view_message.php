<?php
/**
 * CSI EduAid - View Full Message
 * New Modern & Professional Style
 */

session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: messages.php");
    exit();
}

$id = (int)$_GET['id'];

// Fetch message
$stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$message = $stmt->fetch();

if (!$message) {
    header("Location: messages.php");
    exit();
}

// Mark as read
if ($message['is_read'] == 0) {
    $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")
        ->execute([$id]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message - CSI EduAid</title>
    <link rel="icon" type="image/png" href="../img/logoo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
        }
        .message-wrapper {
            max-width: 820px;
            margin: 60px auto;
        }
        .message-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }
        .message-header {
            background: linear-gradient(135deg, #2c3a32, #41514a);
            color: white;
            padding: 1.8rem 2rem;
        }
        .message-body {
            padding: 2.2rem 2.5rem;
            background: white;
            line-height: 1.75;
            font-size: 1.07rem;
            color: #333;
        }
        .meta {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }
        .back-button {
            transition: all 0.3s ease;
        }
        .back-button:hover {
            transform: translateX(-5px);
        }
    </style>
</head>
<body>

<div class="message-wrapper">
    <div class="mb-4">
        <a href="messages.php" class="btn btn-outline-secondary back-button">
            ← Back to Messages List
        </a>
    </div>

    <div class="message-card card">
        <!-- Header -->
        <div class="message-header">
            <h4 class="mb-2">Message from <?= htmlspecialchars($message['full_name']) ?></h4>
            <div style="opacity: 0.9; font-size: 0.95rem;">
                <?= date('F j, Y • g:i A', strtotime($message['created_at'])) ?>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="meta">
            <strong>Email:</strong> <?= htmlspecialchars($message['email']) ?><br>
            <strong>Phone:</strong> <?= htmlspecialchars($message['phone'] ?? 'Not provided') ?>
        </div>

        <!-- Message Content -->
        <div class="message-body">
            <?= nl2br(htmlspecialchars($message['message'])) ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>