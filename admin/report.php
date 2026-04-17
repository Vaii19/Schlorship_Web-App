<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch admin name
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';

// Fetch all approved students for report
$stmt = $pdo->query("
    SELECT id, full_name, dob, phone, email, created_at, 
           scholarship_amount, scholarship_type
    FROM applications 
    WHERE status = 'approved' 
    ORDER BY created_at DESC
");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistics
$total_students   = count($students);
$total_awarded    = array_sum(array_column($students, 'scholarship_amount')) ?: 0;
$avg_award        = $total_students > 0 ? $total_awarded / $total_students : 0;

$full    = count(array_filter($students, fn($s) => ($s['scholarship_type'] ?? '') === 'Full'));
$half    = count(array_filter($students, fn($s) => ($s['scholarship_type'] ?? '') === 'Half'));
$partial = $total_students - $full - $half;

// Unread messages count
$msg_stmt = $pdo->query("SELECT COUNT(*) as unread FROM contact_messages WHERE is_read = 0");
$unread_count = $msg_stmt->fetch()['unread'] ?? 0;

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="CSI_EduAid_Awarded_Students_'.date('Y-m-d').'.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
    
    fputcsv($output, ['ID', 'Full Name', 'DOB', 'Phone', 'Email', 'Awarded Date', 'Amount ($)', 'Type']);
    
    foreach ($students as $s) {
        fputcsv($output, [
            $s['id'],
            $s['full_name'],
            $s['dob'] ?? '',
            $s['phone'] ?? '',
            $s['email'] ?? '',
            date('d M Y', strtotime($s['created_at'])),
            $s['scholarship_amount'] ?? 0,
            $s['scholarship_type'] ?? 'Full'
        ]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report - CSI EduAid Admin</title>
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
        .type-badge { font-size: 0.85rem; padding: 0.45em 0.85em; }
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
            <a href="dashboard.php" class="nav-link"><i class="bi bi-speedometer2 me-3"></i> Dashboard</a>
            <a href="all-applicants.php" class="nav-link"><i class="bi bi-people me-3"></i> All Applicants</a>
            <a href="selected-students.php" class="nav-link"><i class="bi bi-check-circle me-3"></i> Selected Students</a>
            <a href="messages.php" class="nav-link">
                <i class="bi bi-envelope me-3"></i> Messages
                <?php if ($unread_count > 0): ?>
                    <span class="badge bg-danger ms-2"><?= $unread_count ?></span>
                <?php endif; ?>
            </a>
            <a href="settings.php" class="nav-link"><i class="bi bi-gear me-3"></i> Settings</a>
            <a href="report.php" class="nav-link active"><i class="bi bi-clipboard-data me-3"></i> Report</a>
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
                <h1 class="fw-bold" style="color: #41514a;">Report</h1>
                <p class="text-muted mb-0">Here's the scholarship award overview</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card bg-white p-4 text-center">
                    <i class="bi bi-people fs-1 text-primary mb-3"></i>
                    <h5 class="text-muted">Total Awarded</h5>
                    <h2 class="fw-bold text-dark"><?= number_format($total_students) ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-white p-4 text-center">
                    <i class="bi bi-currency-dollar fs-1 text-success mb-3"></i>
                    <h5 class="text-muted">Total Amount</h5>
                    <h2 class="fw-bold text-success">$<?= number_format($total_awarded, 2) ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-white p-4 text-center">
                    <i class="bi bi-calculator fs-1 text-info mb-3"></i>
                    <h5 class="text-muted">Average Award</h5>
                    <h2 class="fw-bold text-info">$<?= number_format($avg_award, 2) ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-white p-4 text-center">
                    <i class="bi bi-trophy fs-1 text-warning mb-3"></i>
                    <h5 class="text-muted">Full Scholarships</h5>
                    <h2 class="fw-bold text-warning"><?= number_format($full) ?></h2>
                </div>
            </div>
        </div>

        <!-- Export + Table -->
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #41514a; color: white;">
                <h5 class="mb-0">Awarded Students (<?= number_format($total_students) ?>)</h5>
                <a href="?export=csv" class="btn btn-light btn-sm">
                    <i class="bi bi-download me-1"></i> Export CSV
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($students)): ?>
                    <div class="text-center py-5 text-muted">
                        No scholarships awarded yet.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>DOB</th>
                                    <th>Phone</th>
                                    <th>Awarded</th>
                                    <th>Amount ($)</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $s): 
                                    $type = $s['scholarship_type'] ?? 'Full';
                                    $badgeClass = match(strtolower($type)) {
                                        'full'    => 'bg-success',
                                        'half'    => 'bg-info text-dark',
                                        'partial' => 'bg-warning text-dark',
                                        default   => 'bg-secondary text-white',
                                    };
                                ?>
                                    <tr>
                                        <td>#<?= $s['id'] ?></td>
                                        <td><?= htmlspecialchars($s['full_name']) ?></td>
                                        <td><?= htmlspecialchars($s['dob'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($s['phone'] ?? '—') ?></td>
                                        <td><?= date('d M Y', strtotime($s['created_at'])) ?></td>
                                        <td class="fw-bold">$<?= number_format($s['scholarship_amount'] ?? 0, 2) ?></td>
                                        <td>
                                            <span class="badge type-badge <?= $badgeClass ?>">
                                                <?= ucfirst($type) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
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