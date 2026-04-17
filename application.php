<?php
/**
 * CSI EduAid - Scholarship Application Form
 * Updated: Mobile Responsive Design + Header matches dashboard
 */

session_start();
require_once 'config/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Get student info for pre-fill
$stmt = $pdo->prepare("SELECT full_name, email, phone FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

// Check if already applied
$check = $pdo->prepare("SELECT id FROM applications WHERE student_id = ? LIMIT 1");
$check->execute([$student_id]);
if ($check->fetch()) {
    header("Location: student_dashboard.php");
    exit();
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dob = $_POST['dob'] ?? '';
    $chin_ethnicity = $_POST['chin_ethnicity'] ?? '';
    $biographical_essay = trim($_POST['biographical_essay'] ?? '');

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $photo = $_FILES['photo'] ?? null;
    $marks = $_FILES['marks'] ?? null;
    $certificate = $_FILES['certificate'] ?? null;

    if (empty($dob) || empty($chin_ethnicity) || empty($biographical_essay)) {
        $error = "Please fill all required fields.";
    } elseif (!$photo || $photo['error'] !== 0 || !$marks || $marks['error'] !== 0 || !$certificate || $certificate['error'] !== 0) {
        $error = "Please upload all required documents.";
    } else {
        $photo_path = $upload_dir . 'photo_' . time() . '_' . basename($photo['name']);
        $marks_path = $upload_dir . 'marks_' . time() . '_' . basename($marks['name']);
        $cert_path  = $upload_dir . 'certificate_' . time() . '_' . basename($certificate['name']);

        if (move_uploaded_file($photo['tmp_name'], $photo_path) &&
            move_uploaded_file($marks['tmp_name'], $marks_path) &&
            move_uploaded_file($certificate['tmp_name'], $cert_path)) {

            $sql = "INSERT INTO applications 
                    (student_id, full_name, dob, chin_ethnicity, phone, email, biographical_essay, 
                     photo_path, high_school_marks_path, high_school_certificate_path, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

            $pdo->prepare($sql)->execute([
                $student_id,
                $student['full_name'],
                $dob,
                $chin_ethnicity,
                $student['phone'],
                $student['email'],
                $biographical_essay,
                $photo_path,
                $marks_path,
                $cert_path
            ]);

            $success = true;
        } else {
            $error = "Failed to upload files. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Application - CSI EduAid</title>
    
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
        .form-card {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .section-title {
            background-color: #41514a;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .submit-btn {
            padding: 14px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        }

        /* Mobile Responsive Improvements */
        @media (max-width: 768px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
            .form-card {
                padding: 1.5rem !important;
            }
            .section-title {
                font-size: 1rem;
                padding: 10px 15px;
            }
            .row.g-3 > div {
                margin-bottom: 1rem;
            }
            .submit-btn {
                width: 100%;
                padding: 16px;
                font-size: 1.05rem;
            }
        }

        /* Document upload cards on mobile */
        .document-upload {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s;
        }
        .document-upload:hover {
            border-color: #41514a;
        }
    </style>
</head>
<body>

<!-- Top Navigation Bar - Same as student_dashboard.php -->
<nav class="topbar py-3">
    <div class="container-fluid d-flex align-items-center px-4">
        <h4 class="mb-0 fw-bold">CSI EduAid Student Dashboard</h4>
        
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="text-white-50 small d-none d-sm-inline">Welcome,</span>
            <strong><?= htmlspecialchars($student['full_name']) ?></strong>
            
            <!-- Dashboard Button -->
            <a href="student_dashboard.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-house-door-fill me-1"></i> 
                <span class="d-none d-md-inline">Dashboard</span>
            </a>
            
            <a href="student_logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">

            <!-- Page Title -->
            <h1 class="display-6 fw-bold mb-4 mb-md-5 text-center" style="color: #41514a;">
                Scholarship Application
            </h1>

            <?php if ($success): ?>
                <div class="alert alert-success text-center py-5">
                    <i class="bi bi-check-circle-fill fs-1 mb-3 d-block"></i>
                    <h4 class="mb-3">Application Submitted Successfully!</h4>
                    <p class="lead mb-4">Thank you. Your application is now under review.<br>
                    You can track its status from your dashboard.</p>
                    <a href="student_dashboard.php" class="btn btn-success btn-lg px-5">
                        Go to Dashboard
                    </a>
                </div>
            <?php else: ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="form-card card shadow p-4 p-md-5">
                    <form method="post" enctype="multipart/form-data">

                        <!-- Personal Information -->
                        <div class="section-title">Personal Information</div>
                        <div class="row g-3 mb-5">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" class="form-control" 
                                       value="<?= htmlspecialchars($student['full_name']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" 
                                       value="<?= htmlspecialchars($student['email']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="tel" class="form-control" 
                                       value="<?= htmlspecialchars($student['phone'] ?? '') ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="dob" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Chin Ethnicity <span class="text-danger">*</span></label>
                                <select name="chin_ethnicity" class="form-select" required>
                                    <option value="">Select your ethnicity</option>
                                    <option value="Hakha">Hakha</option>
                                    <option value="Falam">Falam</option>
                                    <option value="Matupi">Matupi</option>
                                    <option value="Mindat">Mindat</option>
                                    <option value="Kanpalat">Kanpalat</option>
                                    <option value="Tidim">Tidim</option>
                                    <option value="Paletwah">Paletwah</option>
                                    <option value="Thantlang">Thantlang</option>
                                    <option value="Tuanzam">Tuanzam</option>
                                    <option value="Surkhua">Surkhua</option>
                                    <option value="Mizo">Mizo</option>
                                    <option value="Other">Other Chin Sub-group</option>
                                </select>
                            </div>
                        </div>

                        <!-- Biographical Essay -->
                        <div class="section-title">Biographical Essay</div>
                        <div class="mb-5">
                            <textarea name="biographical_essay" class="form-control" rows="8" rows-md="10"
                                placeholder="Tell us about yourself, the challenges you have faced, your academic goals, and how this scholarship will help you contribute to the Chin community..." required></textarea>
                        </div>

                        <!-- Required Documents -->
                        <div class="section-title">Required Documents</div>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="document-upload">
                                    <label class="form-label fw-semibold d-block mb-2">Passport Photo <span class="text-danger">*</span></label>
                                    <input type="file" name="photo" class="form-control" accept="image/*" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="document-upload">
                                    <label class="form-label fw-semibold d-block mb-2">Marks Sheet <span class="text-danger">*</span></label>
                                    <input type="file" name="marks" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="document-upload">
                                    <label class="form-label fw-semibold d-block mb-2">Certificate <span class="text-danger">*</span></label>
                                    <input type="file" name="certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-success submit-btn px-5 py-3 fw-bold">
                                Submit Application
                            </button>
                        </div>
                    </form>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>