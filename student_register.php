<?php
/**
 * CSI EduAid - Student Registration
 * Clean & Professional Version - Matching Login Design & Animation
 */

session_start();
require_once 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm'] ?? '';

    if (empty($full_name) || empty($email) || empty($password)) {
        $error = "Full name, email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = "An account with this email already exists.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                
                $pdo->prepare("INSERT INTO students (full_name, email, phone, password_hash) 
                              VALUES (?, ?, ?, ?)")
                    ->execute([$full_name, $email, $phone, $hash]);

                $student_id = $pdo->lastInsertId();

                // Auto login after registration
                $_SESSION['student_id']   = $student_id;
                $_SESSION['student_name'] = $full_name;

                header("Location: student_dashboard.php");
                exit();
            }
        } catch (PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
            $error = "Registration failed. Please try again.";
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
                    <h5 class="mb-0 fw-bold">Create Your Account</h5>
                </div>
                
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 small animate__animated animate__shakeX">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" id="registerForm">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Phone Number (Optional)</label>
                            <input type="tel" name="phone" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="passwordField" 
                                       class="form-control" required minlength="8">
                                <button class="btn btn-outline-secondary px-3" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Confirm Password</label>
                            <input type="password" name="confirm" id="confirmField" 
                                   class="form-control" required>
                        </div>

                        <button type="submit" class="btn w-100 py-2 fw-semibold" id="submitBtn"
                                style="background-color: #198754; color: white; font-size: 1rem;">
                            <span class="spinner-border spinner-border-sm d-none me-2" id="spinner"></span>
                            <span id="btnText">Create Account</span>
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        Already have an account? 
                        <a href="student_login.php" class="text-success fw-semibold">Login here</a>
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
    const form = document.getElementById('registerForm');
    const btn = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');
    const btnText = document.getElementById('btnText');

    // Password visibility toggle
    toggle.addEventListener('click', () => {
        if (passInput.type === 'password') {
            passInput.type = 'text';
            toggle.querySelector('i').classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passInput.type = 'password';
            toggle.querySelector('i').classList.replace('bi-eye-slash', 'bi-eye');
        }
    });

    // Submit animation (same as login page)
    form.addEventListener('submit', () => {
        btn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Creating Account...';
        btn.style.transform = 'scale(0.98)';
    });

    // Card subtle entrance animation (same as login)
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
    background: linear-gradient(to right, #198754, #41514a);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>