<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Unread messages count for sidebar badge
$msg_stmt = $pdo->query("SELECT COUNT(*) as unread FROM contact_messages WHERE is_read = 0");
$unread_count = $msg_stmt->fetch()['unread'];

// Fetch admin name only (no last_login)
$stmt = $pdo->prepare("SELECT full_name FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();

$admin_name = $admin['full_name'] ?? 'Administrator';

// Handle password change
$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password']     ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $errors[] = "All password fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "New passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "New password must be at least 8 characters long.";
    } else {
        $stmt = $pdo->prepare("SELECT password_hash FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin_data = $stmt->fetch();

        if ($admin_data && password_verify($current_password, $admin_data['password_hash'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
            $update->execute([$new_hash, $_SESSION['admin_id']]);
            $success = true;
        } else {
            $errors[] = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - CSI EduAid Admin</title>
    <link rel="icon" type="image/png" href="../img/logoo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; font-family: system-ui, -apple-system, sans-serif; }
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
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            transition: transform 0.2s;
        }
        .card:hover { transform: translateY(-3px); }
        .profile-header {
            background: linear-gradient(135deg, #41514a 0%, #2c3e2f 100%);
            color: white;
            border-radius: 12px 12px 0 0;
        }
        .password-toggle { 
            cursor: pointer; 
            position: absolute; 
            top: 50%; 
            right: 15px; 
            transform: translateY(-50%); 
            z-index: 10; 
        }
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
            <a href="settings.php" class="nav-link active"><i class="bi bi-gear me-3"></i> Settings</a>
            <a href="report.php" class="nav-link"><i class="bi bi-clipboard-data me-3"></i> Report</a>
        </div>

        <!-- Logout moved here -->
        <hr class="mx-3 my-3">
        <a href="../logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right me-3"></i> Logout
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content flex-grow-1" id="mainContent">
        <h1 class="fw-bold mb-4" style="color: #41514a;">Account Settings</h1>

        <div class="row g-4">
            <!-- Profile Card -->
            <div class="col-lg-4">
                <div class="card shadow h-100">
                    <div class="profile-header p-4 text-center">
                        <i class="bi bi-person-circle fs-1 mb-3 d-block"></i>
                        <h5 class="mb-1"><?= htmlspecialchars($admin_name) ?></h5>
                        <small>Administrator Account</small>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Account Security</h6>
                        <ul class="list-unstyled small text-muted">
                            <li class="mb-2"><i class="bi bi-shield-lock-fill me-2 text-success"></i> Password protected</li>
                            <li class="mb-2"><i class="bi bi-clock-history me-2 text-info"></i> Active session</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Password Change Card -->
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header" style="background-color: #41514a; color: white;">
                        <h5 class="mb-0">Change Password</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($success): ?>
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                <div>
                                    <strong>Success!</strong> Your password has been updated successfully.<br>
                                    <small>Use the new password for your next login.</small>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0 ps-3">
                                    <?php foreach ($errors as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="post" class="needs-validation" novalidate>
                            <input type="hidden" name="change_password" value="1">

                            <div class="mb-4 position-relative">
                                <label class="form-label fw-bold">Current Password</label>
                                <input type="password" name="current_password" class="form-control form-control-lg pe-5" 
                                       required autocomplete="current-password" id="currentPass">
                                <i class="bi bi-eye password-toggle position-absolute top-50 end-0 translate-middle-y pe-3 text-muted" 
                                   onclick="togglePassword('currentPass', this)"></i>
                            </div>

                            <div class="mb-4 position-relative">
                                <label class="form-label fw-bold">New Password</label>
                                <input type="password" name="new_password" class="form-control form-control-lg pe-5" 
                                       required minlength="8" autocomplete="new-password" id="newPass">
                                <i class="bi bi-eye password-toggle position-absolute top-50 end-0 translate-middle-y pe-3 text-muted" 
                                   onclick="togglePassword('newPass', this)"></i>
                            </div>

                            <div class="mb-4 position-relative">
                                <label class="form-label fw-bold">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control form-control-lg pe-5" 
                                       required autocomplete="new-password" id="confirmPass">
                                <i class="bi bi-eye password-toggle position-absolute top-50 end-0 translate-middle-y pe-3 text-muted" 
                                   onclick="togglePassword('confirmPass', this)"></i>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg px-5 fw-bold">
                                <i class="bi bi-check-circle me-2"></i> Update Password
                            </button>
                        </form>

                        <hr class="my-5">

                        <h6 class="fw-bold mb-3">Security Recommendations</h6>
                        <ul class="small text-muted">
                            <li>Use a strong, unique password</li>
                            <li>Never share your credentials</li>
                            <li>Change your password regularly</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle
const toggleBtn = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');

toggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('collapsed');
});

// Password visibility toggle
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Bootstrap form validation
(() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>
</body>
</html>