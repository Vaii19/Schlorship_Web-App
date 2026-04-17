<?php
/**
 * CSI EduAid - Student Dashboard
 * Clean version - Settings moved to header button
 */

session_start();
require_once 'config/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student information
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ? LIMIT 1");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    session_destroy();
    header("Location: student_login.php");
    exit();
}

// Fetch application (if exists)
$app_stmt = $pdo->prepare("SELECT * FROM applications WHERE student_id = ? LIMIT 1");
$app_stmt->execute([$student_id]);
$application = $app_stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - CSI EduAid</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/logoo.jpg" sizes="32x32">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; 
        }
        .topbar { 
            background-color: #41514a; 
            color: white; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .dashboard-header {
            background: linear-gradient(135deg, #41514a 0%, #2c3a32 100%);
            color: white;
            border-radius: 12px;
            padding: 2rem;
        }
        .status-card {
            border-left: 6px solid;
        }
        .status-pending  { border-color: #ffc107; }
        .status-approved { border-color: #28a745; }
        .status-rejected { border-color: #dc3545; }

        .timeline {
            position: relative;
            padding-left: 45px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 12px;
            bottom: 12px;
            width: 4px;
            background: #e9ecef;
            border-radius: 2px;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 32px;
        }
        .timeline-item:last-child { margin-bottom: 0; }
        .timeline-dot {
            position: absolute;
            left: -45px;
            width: 38px;
            height: 38px;
            background: white;
            border: 3px solid #41514a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .section-title {
            background-color: #41514a;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1.25rem;
        }

        .card {
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-radius: 12px;
        }

        .rejection-box {
            background-color: #fff5f5;
            border-left: 6px solid #dc3545;
            padding: 1.5rem;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<!-- Top Navigation Bar -->
<nav class="topbar py-3">
    <div class="container-fluid d-flex align-items-center px-4">
        <h4 class="mb-0 fw-bold">CSI EduAid Student Dashboard</h4>
        
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white-50 small">Welcome,</span>
            <strong><?= htmlspecialchars($student['full_name']) ?></strong>
            
            <!-- Settings Button -->
            <a href="student_settings.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-gear-fill me-1"></i> Settings
            </a>
            
            <a href="student_logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-5">

    <!-- Welcome Header -->
    <div class="dashboard-header mb-5">
        <h1 class="display-6 fw-bold mb-2">
            Hello, <?= htmlspecialchars(explode(' ', $student['full_name'])[0]) ?> 👋
        </h1>
        <p class="mb-0 opacity-90">
            Here's everything you need to know about your scholarship application.
        </p>
    </div>

    <?php if (!$application): ?>
        <!-- No Application Yet -->
        <div class="card shadow text-center p-5">
            <div class="mb-4">
                <i class="bi bi-clipboard-check fs-1 text-muted"></i>
            </div>
            <h3 class="mb-3">Ready to Apply for Scholarship?</h3>
            <p class="lead text-muted mb-4">
                Submit your application to be considered for financial aid, mentorship, and support from CSI EduAid.
            </p>
            <a href="application.php" class="btn btn-success btn-lg px-5 py-3 fw-bold">
                <i class="bi bi-rocket-takeoff-fill me-2"></i> 
                Submit Scholarship Application
            </a>
        </div>

    <?php else: ?>
        <!-- Has Application -->

        <div class="row g-4">

            <!-- Status Card -->
            <div class="col-lg-8">
                <div class="card status-card status-<?= $application['status'] === 'approved' ? 'approved' : ($application['status'] === 'rejected' ? 'rejected' : 'pending') ?>">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-muted mb-1">Current Status</h5>
                                <h3 class="fw-bold mb-0">
                                    <?= $application['status'] === 'pending' ? 'Under Review' : ucfirst($application['status']) ?>
                                </h3>
                            </div>
                            <span class="badge fs-5 px-4 py-2 bg-<?= 
                                $application['status']==='approved' ? 'success' : 
                                ($application['status']==='rejected' ? 'danger' : 'warning text-dark')
                            ?>">
                                <?= ucfirst($application['status'] ?: 'Pending') ?>
                            </span>
                        </div>
                        <p class="mt-3 mb-0">
                            <strong>Submitted on:</strong> 
                            <?= date('F j, Y', strtotime($application['created_at'])) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Award -->
            <?php if ($application['status'] === 'approved'): ?>
            <div class="col-lg-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-trophy-fill fs-1 mb-3"></i>
                        <h5 class="fw-bold">Awarded!</h5>
                        <a href="award_letter.php" class="btn btn-light mt-3">
                            View Award Letter →
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Rejection Reason -->
        <?php if ($application['status'] === 'rejected'): ?>
        <div class="mt-4">
            <div class="rejection-box">
                <h5 class="fw-bold text-danger mb-3">
                    <i class="bi bi-x-circle-fill me-2"></i> Application Not Approved
                </h5>
                <p class="mb-3">
                    Thank you for applying to CSI EduAid. After careful review by the selection committee, we regret to inform you that your application was not selected this cycle.
                </p>
                <?php if (!empty($application['rejection_reason'])): ?>
                    <div class="bg-white p-3 rounded border">
                        <strong>Reason from the committee:</strong><br>
                        <?= nl2br(htmlspecialchars($application['rejection_reason'])) ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">You may re-apply in the next scholarship cycle. We encourage you to keep pursuing your education goals.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Application Details -->
        <div class="mt-5">
            <div class="section-title">Application Details</div>
            <div class="card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <strong>Full Name</strong><br>
                            <?= htmlspecialchars($application['full_name']) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Date of Birth</strong><br>
                            <?= date('F j, Y', strtotime($application['dob'])) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Chin Ethnicity</strong><br>
                            <?= htmlspecialchars(ucfirst($application['chin_ethnicity'])) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Email Address</strong><br>
                            <?= htmlspecialchars($application['email']) ?>
                        </div>
                        
                        <?php if (!empty($application['scholarship_amount'])): ?>
                        <div class="col-md-6">
                            <strong>Scholarship Type</strong><br>
                            <span class="fw-bold"><?= ucfirst($application['scholarship_type'] ?? 'Full') ?></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Award Amount</strong><br>
                            <span class="fw-bold text-success">$<?= number_format($application['scholarship_amount'], 2) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Progress Timeline -->
        <div class="mt-5">
            <div class="section-title">Application Progress</div>
            <div class="card">
                <div class="card-body">
                    <div class="timeline">

                        <div class="timeline-item">
                            <div class="timeline-dot">
                                <i class="bi bi-check-circle-fill text-success"></i>
                            </div>
                            <strong>Application Submitted</strong>
                            <small class="text-muted d-block">
                                <?= date('F j, Y \a\t h:i A', strtotime($application['created_at'])) ?>
                            </small>
                        </div>

                        <?php if ($application['status'] === 'pending'): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot">
                                <i class="bi bi-hourglass-split text-warning"></i>
                            </div>
                            <strong>Under Review</strong>
                            <small class="text-muted d-block">Your application is being evaluated by the scholarship committee.</small>
                        </div>
                        <?php endif; ?>

                        <?php if (in_array($application['status'], ['approved', 'selected'])): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot">
                                <i class="bi bi-check-circle-fill text-success"></i>
                            </div>
                            <strong>Application Approved</strong>
                            <small class="text-muted d-block">Congratulations! You have been awarded a scholarship.</small>
                        </div>
                        <?php endif; ?>

                        <?php if ($application['status'] === 'rejected'): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot">
                                <i class="bi bi-x-circle-fill text-danger"></i>
                            </div>
                            <strong>Application Not Approved</strong>
                            <small class="text-muted d-block">Thank you for applying. You may re-apply in the next cycle.</small>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>

        <!-- Submitted Documents -->
        <div class="mt-5">
            <div class="section-title">Submitted Documents</div>
            <div class="card">
                <div class="card-body">
                    <div class="row g-4 text-center">
                        <div class="col-md-4">
                            <a href="<?= htmlspecialchars($application['photo_path'] ?? '#') ?>" target="_blank" class="text-decoration-none">
                                <i class="bi bi-image fs-1 text-primary d-block mb-2"></i>
                                <small class="fw-bold">Passport Photo</small>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= htmlspecialchars($application['high_school_marks_path'] ?? '#') ?>" target="_blank" class="text-decoration-none">
                                <i class="bi bi-file-earmark-text fs-1 text-primary d-block mb-2"></i>
                                <small class="fw-bold">High School Marks Sheet</small>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="<?= htmlspecialchars($application['high_school_certificate_path'] ?? '#') ?>" target="_blank" class="text-decoration-none">
                                <i class="bi bi-file-earmark-text fs-1 text-primary d-block mb-2"></i>
                                <small class="fw-bold">High School Certificate</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>