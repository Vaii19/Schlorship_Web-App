<?php
/**
 * CSI EduAid - Admin Login Page
 * Final Year Bachelor's Project - Secure & Professional Authentication
 */

session_start();
require_once 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, password_hash, full_name FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                session_regenerate_id(true);

                $_SESSION['admin_id']   = $admin['id'];
                $_SESSION['admin_name'] = $admin['full_name'] ?? $username;

                header("Location: admin/dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            error_log("Admin Login Error: " . $e->getMessage());
            $error = "An unexpected error occurred. Please try again later.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Animate.css for subtle animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-4 my-4">   <!-- Smaller width -->

            <div class="card shadow border-0 login-card">
                <div class="card-header text-white text-center py-3" 
                     style="background: linear-gradient(135deg, #41514a, #2c3a32);">
                    <h5 class="mb-0 fw-bold">Admin Login</h5>
                </div>
                
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 small animate__animated animate__shakeX">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" id="adminLoginForm">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control" required autofocus>
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
                            <span id="btnText">Admin Login</span>
                        </button>
                    </form>

                    <!-- Student Login Link -->
                    <div class="text-center mt-4">
                        <p class="text-muted small mb-2">Are you a student?</p>
                        <a href="student_login.php" class="btn w-100 py-2" 
                           style="background-color: #41514a; color: white; font-size: 0.95rem;">
                            <i class="bi bi-person-circle me-2"></i> Student Login
                        </a>
                    </div>

                    <div class="text-center mt-3">
                        <a href="index.php" class="text-muted small">← Back to Home</a>
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
    const form = document.getElementById('adminLoginForm');
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

.card-header {
    position: relative;
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