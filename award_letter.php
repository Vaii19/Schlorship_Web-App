<?php
/**
 * CSI EduAid - Scholarship Award Letter
 * Updated for new workflow (uses student_id from applications table)
 */

session_start();
require_once 'config/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch the student's application (using student_id)
$stmt = $pdo->prepare("SELECT * FROM applications WHERE student_id = ? LIMIT 1");
$stmt->execute([$student_id]);
$application = $stmt->fetch();

if (!$application || !in_array($application['status'], ['approved', 'selected'])) {
    header("Location: student_dashboard.php");
    exit();
}

// Default values if fields are empty
$amount = $application['scholarship_amount'] ?? 5000.00;
$type   = $application['scholarship_type'] ?? 'Full';
$year   = $application['award_year'] ?? date('Y');

// Award letter date
$letter_date = date('F d, Y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Award Letter - CSI EduAid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        @media print {
            body { font-size: 12pt; }
            .no-print { display: none !important; }
            .container { max-width: 100% !important; }
        }
        .letter-header {
            border-bottom: 3px solid #41514a;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .letter-content {
            font-family: 'Times New Roman', serif;
            line-height: 1.8;
            font-size: 1.05rem;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 220px;
            margin: 60px auto 0;
            text-align: center;
        }
        .award-amount {
            color: #41514a;
            font-size: 1.6rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <!-- Print Controls -->
            <div class="no-print text-center mb-4">
                <a href="student_dashboard.php" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
                <button onclick="window.print()" class="btn btn-success">
                    <i class="bi bi-printer me-2"></i> Print / Save as PDF
                </button>
            </div>

            <div class="card shadow border-0 letter-content">
                <div class="card-body p-5">

                    <!-- Letter Header -->
                    <div class="letter-header text-center">
                        <img src="img/logoo.jpg" alt="CSI EduAid Logo" class="mb-3" style="width: 85px; height: 85px; border-radius: 50%;">
                        <h3 class="fw-bold mb-1" style="color: #41514a;">Chin Student Initiative for Education & Advancement</h3>
                        <p class="mb-0 text-muted">Official Scholarship Award Notification</p>
                    </div>

                    <!-- Date -->
                    <div class="text-end mb-5">
                        <strong>Date:</strong> <?= $letter_date ?>
                    </div>

                    <!-- Salutation -->
                    <div class="mb-4">
                        <p class="fw-bold fs-5">Dear <?= htmlspecialchars($application['full_name']) ?>,</p>
                    </div>

                    <!-- Main Content -->
                    <div class="mb-5">
                        <p>
                            We are delighted to inform you that your scholarship application for the 
                            <strong><?= $year ?> Academic Year</strong> has been 
                            <strong class="text-success">successfully approved</strong>.
                        </p>

                        <p>
                            After a thorough review of your academic records, personal statement, and supporting documents, 
                            the CSI EduAid Scholarship Committee is pleased to award you a 
                            <strong><?= ucfirst($type) ?> Scholarship</strong>.
                        </p>

                        <!-- Award Box -->
                        <div class="text-center my-5 p-4" style="background-color: #f8f9fa; border: 2px solid #41514a; border-radius: 12px;">
                            <h5 class="text-muted mb-2">You Have Been Awarded</h5>
                            <h2 class="award-amount mb-1">$<?= number_format($amount, 2) ?></h2>
                            <p class="fs-5 mb-0">
                                <strong><?= ucfirst($type) ?> Scholarship</strong> 
                                for the <?= $year ?> Academic Year
                            </p>
                        </div>

                        <p>
                            This scholarship is designed to support your higher education journey and help remove financial barriers 
                            so you can focus on achieving your academic and community goals.
                        </p>

                        <h6 class="fw-bold mt-4 mb-2">Next Steps:</h6>
                        <ol>
                            <li>Accept this award through your student dashboard</li>
                            <li>Submit your university enrollment verification (if applicable)</li>
                            <li>Attend the mandatory scholarship orientation session</li>
                            <li>Keep your contact details updated in your account</li>
                        </ol>

                        <p class="mt-4">
                            Should you have any questions regarding this award, please feel free to contact us at 
                            <strong>csieduaid@gmail.com</strong>.
                        </p>
                    </div>

                    <!-- Closing -->
                    <div class="text-center mt-5">
                        <p class="mb-4">With warm regards and best wishes for your bright future,</p>
                        
                        <div class="signature-line"></div>
                        <p class="mt-4">
                            <strong>Dr. Aung Myint</strong><br>
                            <em>Chair, Scholarship Committee</em><br>
                            Chin Student Initiative EduAid (CSI EduAid)
                        </p>
                    </div>

                    <!-- Official Footer Note -->
                    <div class="text-center mt-5 pt-4 border-top">
                        <small class="text-muted">
                            This is an official document from CSI EduAid. Please retain a copy for your records.
                        </small>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>