<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch admin name
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';

// Fetch applications (only latest 10 for dashboard)
$stmt = $pdo->query("SELECT * FROM applications ORDER BY created_at DESC LIMIT 10");
$applications = $stmt->fetchAll();

// Statistics
$total    = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
$pending  = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'")->fetchColumn();
$approved = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'approved'")->fetchColumn();
$rejected = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'rejected'")->fetchColumn();

// Unread messages count
$msg_stmt = $pdo->query("SELECT COUNT(*) as unread FROM contact_messages WHERE is_read = 0");
$unread_count = $msg_stmt->fetch()['unread'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CSI EduAid Admin</title>
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
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #f1f5f1;
            color: #41514a;
            font-weight: 600;
        }
        .nav-link.text-danger:hover { color: #dc3545 !important; }
        .stat-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .table thead { background-color: #41514a; color: white; }
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
        }
        .sidebar-title {
            margin-top: 15px;
            font-size: 1.4rem;
            font-weight: 700;
            color: #41514a;
        }
        .sidebar-subtitle { font-size: 0.9rem; color: #6c757d; }
        tr:hover { background-color: #f8f9fa; }
    </style>
</head>
<body>

<!-- Top Bar -->
<nav class="topbar py-3">
    <div class="container-fluid d-flex align-items-center px-4">
        <button id="sidebarToggle" class="btn btn-light me-3">
            <i class="bi bi-list fs-4"></i>
        </button>
        <h4 class="mb-0 fw-bold">CSI EduAid Admin Dashboard</h4>
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
            <a href="dashboard.php" class="nav-link active"><i class="bi bi-speedometer2 me-3"></i> Dashboard</a>
            <a href="all-applicants.php" class="nav-link"><i class="bi bi-people me-3"></i> All Applicants</a>
            <a href="selected-students.php" class="nav-link"><i class="bi bi-check-circle me-3"></i> Selected Students</a>
            <a href="messages.php" class="nav-link">
                <i class="bi bi-envelope me-3"></i> Messages
                <?php if ($unread_count > 0): ?>
                    <span class="badge bg-danger ms-2"><?= $unread_count ?></span>
                <?php endif; ?>
            </a>
            <a href="settings.php" class="nav-link"><i class="bi bi-gear me-3"></i> Settings</a>
            <a href="report.php" class="nav-link"><i class="bi bi-clipboard-data me-3"></i> Report</a>
        </div>

        <hr class="mx-3 my-3">
        <a href="../logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right me-3"></i> Logout
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content flex-grow-1" id="mainContent">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold" style="color: #41514a;">Welcome back, <?= htmlspecialchars($admin_name) ?> 👋</h1>
                <p class="text-muted mb-0">Here's what's happening with CSI EduAid today</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card bg-white p-4 text-center">
                    <i class="bi bi-file-earmark-text fs-1 text-primary mb-3"></i>
                    <h5 class="text-muted">Total Applications</h5>
                    <h2 class="fw-bold text-dark"><?= number_format($total) ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-white p-4 text-center">
                    <i class="bi bi-hourglass-split fs-1 text-warning mb-3"></i>
                    <h5 class="text-muted">Pending Review</h5>
                    <h2 class="fw-bold text-warning"><?= number_format($pending) ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-white p-4 text-center">
                    <i class="bi bi-check-circle fs-1 text-success mb-3"></i>
                    <h5 class="text-muted">Approved</h5>
                    <h2 class="fw-bold text-success"><?= number_format($approved) ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-white p-4 text-center">
                    <i class="bi bi-x-circle fs-1 text-danger mb-3"></i>
                    <h5 class="text-muted">Rejected</h5>
                    <h2 class="fw-bold text-danger"><?= number_format($rejected) ?></h2>
                </div>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #41514a; color: white;">
                <h5 class="mb-0">Recent Applications</h5>
                <a href="all-applicants.php" class="btn btn-light btn-sm">View All →</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Chin Ethnicity</th>
                                <th>Email</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($applications)): ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">No applications yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td>#<?= $app['id'] ?></td>
                                        <td><?= htmlspecialchars($app['full_name']) ?></td>
                                        <td><?= htmlspecialchars($app['chin_ethnicity'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($app['email']) ?></td>
                                        <td><?= date('d M Y', strtotime($app['created_at'])) ?></td>
                                        <td>
                                            <span class="badge <?= $app['status']==='approved'?'bg-success':($app['status']==='rejected'?'bg-danger':'bg-warning text-dark') ?>">
                                                <?= ucfirst($app['status'] ?: 'Pending') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="view.php?id=<?= $app['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
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