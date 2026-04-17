<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: all-applicants.php");
    exit();
}

$id = (int)$_GET['id'];

// Unread messages count for sidebar badge
$msg_stmt = $pdo->query("SELECT COUNT(*) as unread FROM contact_messages WHERE is_read = 0");
$unread_count = $msg_stmt->fetch()['unread'];

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("DELETE FROM applications WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: all-applicants.php");
    exit();
}

// Fetch applicant name for confirmation
$stmt = $pdo->prepare("SELECT full_name FROM applications WHERE id = ?");
$stmt->execute([$id]);
$app = $stmt->fetch();

if (!$app) {
    header("Location: all-applicants.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Application - CSI EduAid</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../img/logoo.jpg">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; }
        .topbar { 
            background-color: #41514a; 
            color: white; 
            position: sticky; 
            top: 0; 
            z-index: 1030; 
        }
        .sidebar {
            background-color: #ffffff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.08);
            height: 100vh;
            position: fixed;
            width: 260px;
            transition: all 0.3s;
            overflow-y: auto;
        }
        .sidebar.collapsed { margin-left: -260px; }
        .main-content { 
            margin-left: 260px; 
            transition: margin-left 0.3s; 
            padding: 80px 30px 30px; 
        }
        .main-content.collapsed { margin-left: 0; }
        .nav-link {
            color: #000000;
            padding: 12px 24px;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #f1f5f1;
            color: #41514a;
            font-weight: 600;
        }
        .sidebar-logo-container {
            padding: 30px 20px 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        .sidebar-logo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #41514a;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: transform 0.3s;
        }
        .sidebar-logo:hover {
            transform: scale(1.08);
        }
        .sidebar-title {
            margin-top: 15px;
            font-size: 1.4rem;
            font-weight: 700;
            color: #41514a;
        }
        .sidebar-subtitle {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }
    </style>
</head>
<body>

<!-- Top Bar (Sticky) -->
<nav class="topbar py-3">
    <div class="container-fluid d-flex align-items-center px-4">
        <button id="sidebarToggle" class="btn btn-light me-3">
            <i class="bi bi-list fs-4"></i>
        </button>
        <h4 class="mb-0 fw-bold">CSI EduAid Admin Dashboard</h4>
        <div class="ms-auto">
            <a href="../logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </div>
</nav>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-logo-container">
            <img src="../img/logoo.jpg" alt="CSI EduAid Logo" class="sidebar-logo">
            <h5 class="sidebar-title mt-3">CSI EduAid</h5>
            <p class="sidebar-subtitle">Scholarship Management System</p>
        </div>

        <div class="mt-3">
            <a href="dashboard.php" class="nav-link"><i class="bi bi-speedometer2 me-3"></i> Dashboard</a>
            <a href="all-applicants.php" class="nav-link active"><i class="bi bi-people me-3"></i> All Applicants</a>
            <a href="selected-students.php" class="nav-link"><i class="bi bi-check-circle me-3"></i> Selected Students</a>
            <a href="messages.php" class="nav-link">
                <i class="bi bi-envelope me-3"></i> Messages
                <?php if ($unread_count > 0): ?>
                    <span class="badge bg-danger ms-2"><?= $unread_count ?></span>
                <?php endif; ?>
            </a>
            <a href="settings.php" class="nav-link"><i class="bi bi-gear me-3"></i> Settings</a>
            <a href="#" class="nav-link"><i class="bi bi-info-circle me-3"></i> Student Info</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content flex-grow-1" id="mainContent">
        <h1 class="fw-bold mb-4" style="color: #41514a;">Delete Application</h1>

        <div class="card shadow">
            <div class="card-header text-white" style="background-color: #41514a;">
                <h5 class="mb-0">Confirm Deletion</h5>
            </div>
            <div class="card-body p-4">
                <p class="lead mb-4">Are you sure you want to delete this application? This action cannot be undone.</p>
                
                <div class="mb-4">
                    <strong>Applicant Name:</strong> 
                    <span class="fs-5"><?= htmlspecialchars($app['full_name']) ?></span>
                </div>

                <div class="d-flex gap-3">
                    <form method="post" class="d-inline">
                        <button type="submit" class="btn btn-danger btn-lg px-5">
                            <i class="bi bi-trash me-2"></i> Yes, Delete
                        </button>
                    </form>
                    
                    <a href="all-applicants.php" class="btn btn-secondary btn-lg px-5">
                        <i class="bi bi-x-circle me-2"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    });
</script>

</body>
</html>