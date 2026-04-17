<?php
/**
 * CSI EduAid - Student Login Page
 * Updated for new realistic workflow (Register first, then Login)
 */

session_start();
require_once 'config/db.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Security validation failed.";
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $error = "Email and password are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email address.";
        } else {
            try {
                // Updated to use 'students' table
                $stmt = $pdo->prepare("SELECT id, full_name, password_hash FROM students WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $student = $stmt->fetch();

                if ($student && password_verify($password, $student['password_hash'])) {
                    session_regenerate_id(true);

                    $_SESSION['student_id']   = $student['id'];
                    $_SESSION['student_name'] = $student['full_name'];

                    // Update last login
                    $pdo->prepare("UPDATE students SET last_login = NOW() WHERE id = ?")
                        ->execute([$student['id']]);

                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                    header("Location: student_dashboard.php");
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            } catch (PDOException $e) {
                error_log("Student Login Error: " . $e->getMessage());
                $error = "System error. Please try again.";
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Animate.css for subtle animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-4 my-4">

            <div class="card shadow border-0 login-card">
                <div class="card-header text-white text-center py-3" 
                     style="background: linear-gradient(135deg, #41514a, #2c3a32);">
                    <h5 class="mb-0 fw-bold">Student Login</h5>
                </div>
                
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 small animate__animated animate__shakeX">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" id="loginForm">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($email) ?>" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="passwordField" class="form-control" required>
                                <button class="btn btn-outline-secondary px-3" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn w-100 py-2 fw-semibold" id="submitBtn"
                                style="background-color: #198754; color: white; font-size: 1rem;">
                            <span class="spinner-border spinner-border-sm d-none me-2" id="spinner"></span>
                            <span id="btnText">Login</span>
                        </button>
                    </form>

                    <!-- Admin Login Section (kept unchanged) -->
                    <div class="text-center mt-4">
                        <p class="text-muted small mb-2">Are you an administrator?</p>
                        <a href="login.php" class="btn w-100 py-2" 
                           style="background-color: #41514a; color: white; font-size: 0.95rem;">
                            <i class="bi bi-shield-lock-fill me-2"></i> Admin Login
                        </a>
                    </div>

                    <!-- Updated: Changed from "Apply here" to "Create Account" -->
                    <div class="text-center mt-3">
                        <small class="text-muted">No account yet? 
                            <a href="student_register.php" class="text-success fw-semibold">Create Account</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('togglePassword');
    const passInput = document.getElementById('passwordField');
    const form = document.getElementById('loginForm');
    const btn = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');
    const btnText = document.getElementById('btnText');

    // Password toggle
    toggle.addEventListener('click', () => {
        if (passInput.type === 'password') {
            passInput.type = 'text';
            toggle.querySelector('i').classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passInput.type = 'password';
            toggle.querySelector('i').classList.replace('bi-eye-slash', 'bi-eye');
        }
    });

    // Submit animation
    form.addEventListener('submit', () => {
        btn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Logging in...';
        btn.style.transform = 'scale(0.98)';
    });

    // Card subtle entrance animation
    setTimeout(() => {
        document.querySelector('.login-card').classList.add('animate__animated', 'animate__fadeInUp');
    }, 100);
});
</script>

<style>
.login-card {
    transition: all 0.3s ease;
    border-radius: 12px;
}

.login-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
}

.card-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(to right, #0d6efd, #41514a);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>