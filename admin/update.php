<?php
/**
 * CSI EduAid Admin - Update Application
 * Added Rejection Reason field (shows only when status = rejected)
 */

session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: all-applicants.php");
    exit();
}

// Fetch application
$stmt = $pdo->prepare("SELECT * FROM applications WHERE id = ?");
$stmt->execute([$id]);
$app = $stmt->fetch();

if (!$app) {
    header("Location: all-applicants.php");
    exit();
}

// Current status display
$current_status = $app['status'] ?: 'pending';
$status_label   = ucfirst($current_status);
$status_class   = match($current_status) {
    'approved' => 'bg-success',
    'rejected' => 'bg-danger',
    default    => 'bg-warning text-dark',
};

// ────────────────────────────────────────────────
// Handle form submission
// ────────────────────────────────────────────────
$success     = false;
$success_msg = '';
$errors      = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status         = trim($_POST['status'] ?? '');
    $rejection_reason   = trim($_POST['rejection_reason'] ?? '');
    $amount             = filter_var($_POST['scholarship_amount'] ?? 0, FILTER_VALIDATE_FLOAT) ?: 0;
    $type               = trim($_POST['scholarship_type'] ?? 'Full');

    $valid_statuses = ['pending', 'approved', 'rejected'];

    if (!in_array($new_status, $valid_statuses)) {
        $errors[] = "Invalid status selected.";
    }

    // Validation for Rejected status
    if ($new_status === 'rejected' && empty($rejection_reason)) {
        $errors[] = "Please provide a rejection reason when rejecting an application.";
    }

    // Validation for Approved status
    if ($new_status === 'approved') {
        if ($amount <= 0) {
            $errors[] = "Scholarship amount must be greater than 0.";
        }
        if (!in_array($type, ['Full', 'Half', 'Partial'])) {
            $errors[] = "Invalid scholarship type.";
        }
    }

    if (empty($errors)) {
        $sql    = "UPDATE applications SET status = ?, rejection_reason = ?";
        $params = [$new_status, ($new_status === 'rejected' ? $rejection_reason : null)];

        if ($new_status === 'approved') {
            $sql .= ", scholarship_amount = ?, scholarship_type = ?";
            $params[] = $amount;
            $params[] = $type;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        try {
            $pdo->prepare($sql)->execute($params);
            $success = true;

            if ($new_status === 'approved') {
                $success_msg = "<strong>Approved!</strong> Scholarship awarded: $" . number_format($amount, 2) . " (" . htmlspecialchars($type) . ")";
            } elseif ($new_status === 'rejected') {
                $success_msg = "<strong>Application Rejected.</strong><br>Reason: " . htmlspecialchars($rejection_reason);
            } else {
                $success_msg = "<strong>Updated:</strong> Status changed to " . ucfirst($new_status);
            }

            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM applications WHERE id = ?");
            $stmt->execute([$id]);
            $app = $stmt->fetch();

            $current_status = $app['status'] ?: 'pending';
            $status_label   = ucfirst($current_status);
            $status_class   = match($current_status) {
                'approved' => 'bg-success',
                'rejected' => 'bg-danger',
                default    => 'bg-warning text-dark',
            };
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Sidebar unread count
$msg_stmt = $pdo->query("SELECT COUNT(*) as unread FROM contact_messages WHERE is_read = 0");
$unread_count = $msg_stmt->fetch()['unread'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Application #<?= htmlspecialchars($app['id']) ?> - CSI EduAid</title>
    <link rel="icon" type="image/png" href="../img/logoo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; font-family: system-ui, -apple-system, sans-serif; }
        .topbar { background-color: #41514a; color: white; position: sticky; top: 0; z-index: 1030; }
        .sidebar { background-color: #ffffff; box-shadow: 2px 0 10px rgba(0,0,0,0.08); height: 100vh; position: fixed; width: 260px; transition: all 0.3s; overflow-y: auto; }
        .sidebar.collapsed { margin-left: -260px; }
        .main-content { margin-left: 260px; transition: margin-left 0.3s; padding: 80px 30px 30px; }
        .main-content.collapsed { margin-left: 0; }
        .nav-link { color: #000; padding: 12px 24px; transition: all 0.2s; }
        .nav-link:hover, .nav-link.active { background-color: #f1f5f1; color: #41514a; font-weight: 600; }
        .nav-link.text-danger:hover { color: #dc3545 !important; }
        .sidebar-logo-container { padding: 30px 20px 20px; text-align: center; border-bottom: 1px solid #eee; }
        .sidebar-logo { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 4px solid #41514a; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .sidebar-title { margin-top: 15px; font-size: 1.4rem; font-weight: 700; color: #41514a; }
        .sidebar-subtitle { font-size: 0.9rem; color: #6c757d; }
        .card { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
        .status-badge { font-size: 1.3rem; padding: 0.8rem 1.6rem; min-width: 160px; text-align: center; }
        .award-section, .rejection-section { display: none; }
        .award-section.active, .rejection-section.active { display: block; }
        .info-box { background: #f8f9fa; border-left: 5px solid #41514a; padding: 1.25rem; border-radius: 8px; }
        .rejection-box { background: #fff5f5; border-left: 5px solid #dc3545; }
    </style>
</head>
<body>

<!-- Top Bar -->
<nav class="topbar py-3">
    <div class="container-fluid d-flex align-items-center px-4">
        <button id="sidebarToggle" class="btn btn-light me-3"><i class="bi bi-list fs-4"></i></button>
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
                <?php if ($unread_count > 0): ?><span class="badge bg-danger ms-2"><?= $unread_count ?></span><?php endif; ?>
            </a>
            <a href="settings.php" class="nav-link"><i class="bi bi-gear me-3"></i> Settings</a>
            <a href="report.php" class="nav-link"><i class="bi bi-clipboard-data me-3"></i> Report</a>
        </div>
        <hr class="mx-3 my-4">
        <a href="../logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right me-3"></i> Logout
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content flex-grow-1" id="mainContent">
        <h1 class="fw-bold mb-4" style="color: #41514a;">
            Update Application #<?= htmlspecialchars($app['id']) ?>
        </h1>

        <!-- Current Status -->
        <div class="mb-5">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div>
                    <label class="form-label fw-bold fs-5 mb-1">Current Status</label>
                    <div class="status-badge <?= $status_class ?>">
                        <?= $status_label ?>
                    </div>
                </div>
                <div class="text-muted">
                    <small>Submitted: <?= date('d M Y • H:i', strtotime($app['created_at'])) ?></small><br>
                    <small>Update the status below</small>
                </div>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                <div><?= $success_msg ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger mb-4" role="alert">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-header text-white" style="background-color: #41514a;">
                <h5 class="mb-0"><?= htmlspecialchars($app['full_name']) ?></h5>
            </div>

            <div class="card-body p-4 p-md-5">
                <!-- Applicant Quick Info -->
                <div class="info-box mb-5">
                    <h6 class="fw-bold mb-3">Applicant Details</h6>
                    <div class="row g-3 small">
                        <div class="col-md-4">
                            <strong>Email:</strong><br>
                            <?= htmlspecialchars($app['email']) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Phone:</strong><br>
                            <?= htmlspecialchars($app['phone'] ?? '—') ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Ethnicity:</strong><br>
                            <?= htmlspecialchars($app['chin_ethnicity'] ?? '—') ?>
                        </div>
                    </div>
                </div>

                <form method="post" id="updateForm" class="needs-validation" novalidate>

                    <!-- Status -->
                    <div class="mb-5">
                        <label class="form-label fw-bold fs-5">Update Status To</label>
                        <select name="status" id="statusSelect" class="form-select form-select-lg" required>
                            <option value="pending"  <?= $app['status'] === 'pending'  ? 'selected' : '' ?>>Pending Review</option>
                            <option value="approved" <?= $app['status'] === 'approved' ? 'selected' : '' ?>>Approved / Awarded</option>
                            <option value="rejected" <?= $app['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>

                    <!-- Scholarship Award Section -->
                    <div class="award-section mb-5 <?= $app['status'] === 'approved' ? 'active' : '' ?>" id="awardSection">
                        <div class="award-box border border-primary-subtle bg-white p-4 rounded">
                            <h5 class="fw-bold text-primary mb-4">
                                <i class="bi bi-award-fill me-2"></i> Scholarship Award
                            </h5>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Amount (USD)</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="scholarship_amount" class="form-control"
                                               step="0.01" min="1" placeholder="e.g. 5000.00"
                                               value="<?= htmlspecialchars($app['scholarship_amount'] ?? '0.00') ?>"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Type</label>
                                    <select name="scholarship_type" class="form-select form-select-lg" required>
                                        <option value="Full"    <?= ($app['scholarship_type'] ?? '') === 'Full'    ? 'selected' : '' ?>>Full Scholarship</option>
                                        <option value="Half"    <?= ($app['scholarship_type'] ?? '') === 'Half'    ? 'selected' : '' ?>>Half Scholarship</option>
                                        <option value="Partial" <?= ($app['scholarship_type'] ?? '') === 'Partial' ? 'selected' : '' ?>>Partial Scholarship</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rejection Reason Section -->
                    <div class="rejection-section mb-5 <?= $app['status'] === 'rejected' ? 'active' : '' ?>" id="rejectionSection">
                        <div class="rejection-box border border-danger-subtle bg-white p-4 rounded">
                            <h5 class="fw-bold text-danger mb-4">
                                <i class="bi bi-x-circle-fill me-2"></i> Rejection Reason
                            </h5>
                            <p class="text-muted small mb-3">This message will be shown to the student on their dashboard.</p>
                            <textarea name="rejection_reason" id="rejectionReason" class="form-control" rows="5"
                                placeholder="e.g. Thank you for applying. Unfortunately, you do not meet the eligibility criteria this cycle. We encourage you to apply again next year."><?= htmlspecialchars($app['rejection_reason'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex flex-wrap gap-3 mt-5">
                        <button type="submit" class="btn btn-success btn-lg px-5 fw-bold">
                            <i class="bi bi-check-circle me-2"></i> Save Changes
                        </button>
                        <a href="all-applicants.php" class="btn btn-outline-secondary btn-lg px-5">
                            <i class="bi bi-arrow-left me-2"></i> Back to Applicants
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle
document.getElementById('sidebarToggle').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('collapsed');
    document.getElementById('mainContent').classList.toggle('collapsed');
});

// Dynamic sections
const statusSelect = document.getElementById('statusSelect');
const awardSection = document.getElementById('awardSection');
const rejectionSection = document.getElementById('rejectionSection');
const amountInput = document.querySelector('[name="scholarship_amount"]');
const typeSelect = document.querySelector('[name="scholarship_type"]');
const rejectionTextarea = document.getElementById('rejectionReason');

function toggleSections() {
    const status = statusSelect.value;

    // Award section
    const isApproved = status === 'approved';
    awardSection.classList.toggle('active', isApproved);
    amountInput.required = isApproved;
    typeSelect.required = isApproved;
    amountInput.disabled = !isApproved;
    typeSelect.disabled = !isApproved;
    if (!isApproved) amountInput.value = '0.00';

    // Rejection section
    const isRejected = status === 'rejected';
    rejectionSection.classList.toggle('active', isRejected);
    rejectionTextarea.required = isRejected;
}

statusSelect.addEventListener('change', toggleSections);
toggleSections();   // Initialize on load

// Form validation
document.getElementById('updateForm').addEventListener('submit', function(e) {
    if (!this.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }
    this.classList.add('was-validated');
});
</script>
</body>
</html>