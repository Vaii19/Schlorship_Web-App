<?php
/**
 * Set Password Page - After Application Submission
 * Final Year Bachelor's Project
 */

session_start();
require_once 'config/db.php';

$email = $_GET['email'] ?? '';

// Security: If no email is passed, go back to home
if (empty($email)) {
    header("Location: index.php");
    exit();
}

// Check if this email has an application and password is NOT set yet
$stmt = $pdo->prepare("
    SELECT id, full_name 
    FROM applications 
    WHERE email = ? AND password_hash IS NULL 
    LIMIT 1
");
$stmt->execute([$email]);
$app = $stmt->fetch();

if (!$app) {
    // If password already set or email not found
    header("Location: student_login.php?error=already_set");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $pdo->prepare("UPDATE applications SET password_hash = ? WHERE email = ?")
            ->execute([$hash, $email]);

        // Auto-login the student
        $_SESSION['student_id']   = $app['id'];
        $_SESSION['student_name'] = $app['full_name'];

        header("Location: student_dashboard.php");
        exit();
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white text-center py-4">
                    <h4 class="mb-0 fw-bold">Set Your Password</h4>
                </div>
                <div class="card-body p-5">
                    <p class="text-center mb-4">
                        Creating account for: <strong><?= htmlspecialchars($email) ?></strong>
                    </p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-4">
                            <label class="form-label fw-bold">New Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" 
                                   required minlength="8" placeholder="Minimum 8 characters">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Confirm Password</label>
                            <input type="password" name="confirm" class="form-control form-control-lg" required>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">
                            Set Password & Go to My Dashboard
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <a href="index.php" class="text-muted small">← Back to Home Page</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>