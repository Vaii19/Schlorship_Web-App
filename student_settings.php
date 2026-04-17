<?php
/**
 * CSI EduAid - Student Settings
 * Fixed Header + Mobile Responsive Design
 */

session_start();
require_once 'config/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student data
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    session_destroy();
    header("Location: student_login.php");
    exit();
}

$success_msg = '';
$error_msg   = '';

// ====================== CHANGE PASSWORD ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_pass = $_POST['current_password'] ?? '';
    $new_pass     = $_POST['new_password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
        $error_msg = "All fields are required.";
    } elseif (strlen($new_pass) < 6) {
        $error_msg = "New password must be at least 6 characters long.";
    } elseif ($new_pass !== $confirm_pass) {
        $error_msg = "New passwords do not match.";
    } else {
        if (password_verify($current_pass, $student['password'])) {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            
            $update = $pdo->prepare("UPDATE students SET password = ? WHERE id = ?");
            $update->execute([$hashed, $student_id]);

            $success_msg = "Your password has been updated successfully.";
            
            // Refresh student data
            $stmt->execute([$student_id]);
            $student = $stmt->fetch();
        } else {
            $error_msg = "Current password is incorrect.";
        }
    }
}

// ====================== DELETE ACCOUNT ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $confirm_text = strtoupper(trim($_POST['confirm_delete'] ?? ''));

    if ($confirm_text === "DELETE MY ACCOUNT") {
        try {
            $pdo->beginTransaction();

            // Delete application if exists
            $pdo->prepare("DELETE FROM applications WHERE student_id = ?")->execute([$student_id]);
            
            // Delete student account
            $pdo->prepare("DELETE FROM students WHERE id = ?")->execute([$student_id]);

            $pdo->commit();

            session_destroy();
            header("Location: student_login.php?msg=account_deleted");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_msg = "Failed to delete account. Please try again later.";
        }
    } else {
        $error_msg = "Please type exactly 'DELETE MY ACCOUNT' to confirm.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - CSI EduAid</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/logoo.jpg" sizes="32x32">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: system-ui, -apple-system, sans-serif; 
        }
        .topbar { 
            background-color: #41514a; 
            color: white; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-radius: 12px;
        }
        .section-title {
            background-color: #41514a;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .danger-zone {
            border: 2px solid #dc3545;
        }

        /* Mobile Responsive Improvements */
        @media (max-width: 768px) {
            .topbar h4 {
                font-size: 1.1rem;
            }
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
            .card-body {
                padding: 1.5rem !important;
            }
            .section-title {
                font-size: 1rem;
                padding: 10px 15px;
            }
            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>

<!-- Top Navigation Bar - Consistent with student_dashboard.php -->
<nav class="topbar py-3">
    <div class="container-fluid d-flex align-items-center px-4">
        <h4 class="mb-0 fw-bold">CSI EduAid Student Dashboard</h4>
        
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white-50 small d-none d-sm-inline">Welcome,</span>
            <strong class="text-truncate" style="max-width: 180px;">
                <?= htmlspecialchars($student['full_name']) ?>
            </strong>
            
            <!-- Dashboard Button -->
            <a href="student_dashboard.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-house-door-fill me-1"></i> 
                <span class="d-none d-md-inline">Dashboard</span>
            </a>
            
            <a href="student_logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-4 my-md-5">

    <h1 class="fw-bold mb-4 mb-md-5 text-center text-md-start" style="color: #41514a;">
        Account Settings
    </h1>

    <?php if ($success_msg): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($success_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($error_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Profile Information -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="section-title">Profile Information</div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <strong>Full Name</strong><br>
                        <?= htmlspecialchars($student['full_name']) ?>
                    </div>
                    <div class="mb-3">
                        <strong>Email Address</strong><br>
                        <?= htmlspecialchars($student['email']) ?>
                    </div>
                    <div class="mb-3">
                        <strong>Phone Number</strong><br>
                        <?= htmlspecialchars($student['phone'] ?? 'Not provided') ?>
                    </div>
                    <div>
                        <strong>Joined On</strong><br>
                        <?= date('F j, Y', strtotime($student['created_at'] ?? 'now')) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="section-title">Change Password</div>
                <div class="card-body p-4">
                    <form method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="change_password" value="1">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">New Password</label>
                            <input type="password" name="new_password" class="form-control" 
                                   minlength="6" required>
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" 
                                   minlength="6" required>
                        </div>

                        <button type="submit" class="btn px-5" 
                                style="background-color: #198754; color: white;">
                            <i class="bi bi-key-fill me-2"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Danger Zone - Delete Account -->
        <div class="col-12 mt-4">
            <div class="card danger-zone">
                <div class="section-title bg-danger">Danger Zone</div>
                <div class="card-body p-4">
                    <h5 class="text-danger fw-bold mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> 
                        Delete Account
                    </h5>
                    <p class="text-muted mb-4">
                        This action is <strong>permanent</strong> and cannot be undone. 
                        All your data including applications and documents will be permanently deleted.
                    </p>

                    <form method="post" onsubmit="return confirmDelete();">
                        <input type="hidden" name="delete_account" value="1">

                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger">
                                Type <span class="text-dark">"DELETE MY ACCOUNT"</span> to confirm
                            </label>
                            <input type="text" name="confirm_delete" class="form-control" 
                                   placeholder="DELETE MY ACCOUNT" autocomplete="off" required>
                        </div>

                        <button type="submit" class="btn btn-danger px-4">
                            <i class="bi bi-trash-fill me-2"></i> Permanently Delete My Account
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <div class="text-center mt-5">
        <a href="student_dashboard.php" class="btn btn-outline-secondary px-4">
            ← Back to Dashboard
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmDelete() {
    const input = document.querySelector('input[name="confirm_delete"]').value.trim();
    
    if (input !== "DELETE MY ACCOUNT") {
        alert("Please type exactly 'DELETE MY ACCOUNT' to proceed.");
        return false;
    }

    return confirm("⚠️ WARNING: This will permanently delete your entire account and all associated data.\n\nThis action cannot be undone.\n\nAre you absolutely sure?");
}
</script>
</body>
</html>