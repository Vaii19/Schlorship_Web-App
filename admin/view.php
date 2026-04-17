<?php
/**
 * CSI EduAid - Applicant View & Award Page
 * Updated: Rejection now properly saves rejection_reason
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

$stmt = $pdo->prepare("SELECT * FROM applications WHERE id = ?");
$stmt->execute([$id]);
$app = $stmt->fetch();

if (!$app) {
    header("Location: all-applicants.php");
    exit();
}

// Handle Award
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['award_action'])) {
    $amount = (float)($_POST['scholarship_amount'] ?? 0);
    $type   = trim($_POST['scholarship_type'] ?? 'Full');
    $year   = trim($_POST['award_year'] ?? date('Y'));

    if ($amount > 0) {
        $stmt = $pdo->prepare("
            UPDATE applications 
            SET status = 'approved', 
                scholarship_amount = ?, 
                scholarship_type = ?, 
                award_year = ?,
                rejection_reason = NULL 
            WHERE id = ?
        ");
        $stmt->execute([$amount, $type, $year, $id]);

        header("Location: view.php?id=$id&msg=awarded");
        exit();
    }
}

// Handle Reject with Reason
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_action'])) {
    $reason = trim($_POST['reject_reason'] ?? '');

    if (empty($reason)) {
        $reason = "Unfortunately, your application was not selected this cycle.";
    }

    $stmt = $pdo->prepare("
        UPDATE applications 
        SET status = 'rejected', 
            rejection_reason = ?,
            scholarship_amount = NULL,
            scholarship_type = NULL 
        WHERE id = ?
    ");
    $stmt->execute([$reason, $id]);

    header("Location: view.php?id=$id&msg=rejected");
    exit();
}

// Messages
$msg = $_GET['msg'] ?? '';
$alert = '';
if ($msg === 'awarded') {
    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> Scholarship has been awarded successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
} elseif ($msg === 'rejected') {
    $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Rejected!</strong> Application has been rejected and reason saved.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application #<?= $app['id'] ?> - CSI EduAid</title>
    <link rel="icon" type="image/png" href="../img/logoo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; color: #212529; }
        .top-nav {
            background-color: #41514a;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        .section-title {
            background-color: #41514a;
            color: white;
            padding: 12px 20px;
            border-radius: 8px 8px 0 0;
            font-weight: 600;
            margin-bottom: 0;
        }
        .card-detail {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
        }
        .photo-frame {
            max-width: 240px;
            margin: 0 auto 1.5rem;
        }
        .photo-frame img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            object-fit: cover;
        }
        .status-badge {
            font-size: 1.1rem;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
        }
        .award-box, .reject-box {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
        }
        .award-box { border-left: 5px solid #28a745; }
        .reject-box { border-left: 5px solid #dc3545; }
        .form-label { font-weight: 600; color: #41514a; }
    </style>
</head>
<body>

<!-- Top Navigation -->
<nav class="top-nav py-3">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold">CSI EduAid Admin</h4>
            <div>
                <a href="all-applicants.php" class="btn btn-outline-light me-2">← Back to List</a>
                <a href="../logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container my-5">

    <?= $alert ?>

    <div class="row g-5">
        <!-- Left column - Profile photo & basic info -->
        <div class="col-lg-4">
            <div class="card-detail bg-white p-4 text-center">
                <div class="photo-frame">
                    <img src="../<?= htmlspecialchars($app['photo_path'] ?? 'img/default-avatar.jpg') ?>" 
                         alt="Applicant Photo" class="img-fluid">
                </div>
                <h3 class="fw-bold mb-2"><?= htmlspecialchars($app['full_name']) ?></h3>
                <p class="text-muted mb-3">Application #<?= $app['id'] ?></p>
                
                <span class="status-badge fw-semibold px-4 py-2 
                    <?= $app['status'] === 'approved' ? 'bg-success text-white' : 
                        ($app['status'] === 'rejected' ? 'bg-danger text-white' : 
                        'bg-warning text-dark') ?>">
                    <?= ucfirst($app['status'] ?: 'Pending') ?>
                </span>

                <div class="mt-4 text-muted">
                    <small>Submitted: <?= date('d M Y', strtotime($app['created_at'])) ?></small>
                </div>
            </div>
        </div>

        <!-- Right column - Details & Actions -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="card-detail bg-white">
                <div class="section-title">Personal Information</div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>Date of Birth:</strong><br>
                            <?= htmlspecialchars($app['dob'] ?? '—') ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Chin Ethnicity:</strong><br>
                            <?= htmlspecialchars($app['chin_ethnicity'] ?? '—') ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Phone:</strong><br>
                            <?= htmlspecialchars($app['phone'] ?? '—') ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Email:</strong><br>
                            <?= htmlspecialchars($app['email'] ?? '—') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Essay -->
            <div class="card-detail bg-white mt-4">
                <div class="section-title">Biographical Essay</div>
                <div class="p-4">
                    <div style="white-space: pre-wrap; line-height: 1.7;">
                        <?= nl2br(htmlspecialchars($app['biographical_essay'] ?? 'No essay submitted.')) ?>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="card-detail bg-white mt-4">
                <div class="section-title">Submitted Documents</div>
                <div class="p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <strong>High School Marks</strong>
                            <div class="mt-2">
                                <a href="../<?= htmlspecialchars($app['high_school_marks_path'] ?? '#') ?>" 
                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> View / Download
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <strong>High School Certificate</strong>
                            <div class="mt-2">
                                <a href="../<?= htmlspecialchars($app['high_school_certificate_path'] ?? '#') ?>" 
                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> View / Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card-detail bg-white mt-4">
                <div class="section-title">Actions</div>
                <div class="p-4">
                    <?php if ($app['status'] === 'pending'): ?>
                        <div class="row g-4">
                            <!-- Award Form -->
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Award Scholarship</h5>
                                <form method="post" class="award-box">
                                    <input type="hidden" name="award_action" value="1">

                                    <div class="mb-3">
                                        <label class="form-label">Scholarship Amount ($)</label>
                                        <input type="number" name="scholarship_amount" class="form-control" 
                                               min="0" step="0.01" placeholder="e.g. 5000.00" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Scholarship Type</label>
                                        <select name="scholarship_type" class="form-select" required>
                                            <option value="Full">Full Scholarship</option>
                                            <option value="Half">Half Scholarship</option>
                                            <option value="Partial">Partial Scholarship</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Award Year</label>
                                        <select name="award_year" class="form-select" required>
                                            <option value="2026">2026</option>
                                            <option value="2027">2027</option>
                                            <option value="2028">2028</option>
                                            <option value="2029">2029</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check-circle me-2"></i> Award & Approve
                                    </button>
                                </form>
                            </div>

                            <!-- Reject Form -->
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3 text-danger">Reject Application</h5>
                                <form method="post" class="reject-box">
                                    <input type="hidden" name="reject_action" value="1">

                                    <div class="mb-3">
                                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                        <textarea name="reject_reason" class="form-control" rows="5" 
                                                  placeholder="e.g. Thank you for applying. Unfortunately, you do not meet the eligibility criteria this cycle. We encourage you to re-apply next year." 
                                                  required></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-danger w-100"
                                            onclick="return confirm('Are you sure you want to reject this application? The student will see this reason on their dashboard.');">
                                        <i class="bi bi-x-circle me-2"></i> Reject Application
                                    </button>
                                </form>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            <h4 class="mt-3 fw-bold">
                                This application is already <?= ucfirst($app['status']) ?>
                            </h4>

                            <?php if ($app['status'] === 'approved' && $app['scholarship_amount'] > 0): ?>
                                <div class="alert alert-success mt-4">
                                    <strong>Awarded:</strong> 
                                    $<?= number_format($app['scholarship_amount'], 2) ?> 
                                    (<?= htmlspecialchars($app['scholarship_type'] ?? 'Full') ?> Scholarship – 
                                    <?= htmlspecialchars($app['award_year'] ?? date('Y')) ?>)
                                </div>
                            <?php endif; ?>

                            <?php if ($app['status'] === 'rejected' && !empty($app['rejection_reason'])): ?>
                                <div class="alert alert-danger mt-4 text-start">
                                    <strong>Rejection Reason:</strong><br><br>
                                    <?= nl2br(htmlspecialchars($app['rejection_reason'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>