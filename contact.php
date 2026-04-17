<?php include 'includes/header.php'; ?>

<?php
// Handle form submission
$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['full_name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $errors[] = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } elseif (strlen($message) < 10) {
        $errors[] = "Your message is too short. Please provide more details.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO contact_messages (full_name, email, phone, message, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $email, $phone, $message]);
            $success = true;

            // Clear form after success
            $_POST = [];
        } catch (PDOException $e) {
            $errors[] = "Sorry, there was an error sending your message. Please try again later.";
        }
    }
}
?>

<!-- AOS CSS -->
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />

<div class="container py-5 my-5">
    
    <!-- Page Header -->
    <div class="text-center mb-5 pb-4" data-aos="fade-up">
        <h1 class="display-4 fw-bold mb-3" style="color: #000000;">
            Get in Touch
        </h1>
        <div class="mx-auto" style="max-width: 820px;">
            <p class="lead text-muted">
                We'd love to hear from you. Whether you have questions about scholarships, 
                want to volunteer, or need more information — drop us a message.
            </p>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show text-center shadow-sm" role="alert" data-aos="fade-up">
            <strong>Thank you!</strong> Your message has been sent successfully. We'll get back to you within 24–48 hours.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" data-aos="fade-up">
            <strong>Oops!</strong> Please correct the following:
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-lg overflow-hidden rounded-4" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-0">

            <!-- Left: Contact Info -->
            <div class="col-lg-5 p-5 text-white d-flex flex-column justify-content-between"
                 style="background: linear-gradient(135deg, #41514a 0%, #2e3b34 100%);">
                <div>
                    <h3 class="fw-bold mb-4">Contact Information</h3>
                    <p class="opacity-75 mb-5">
                        We're here to help. Reach out anytime — we usually reply within 24–48 hours.
                    </p>

                    <ul class="list-unstyled mb-5">
                        <li class="mb-4">
                            <i class="bi bi-envelope-fill fs-4 me-3 text-warning"></i>
                            <a href="mailto:csieduaid@gmail.com" class="text-white text-decoration-none">
                                csieduaid@gmail.com
                            </a>
                        </li>
                        <li class="mb-4">
                            <i class="bi bi-telephone-fill fs-4 me-3 text-warning"></i>
                            <span>+95 9 XXX XXXX (Myanmar)</span>
                        </li>
                        <li class="mb-4">
                            <i class="bi bi-geo-alt-fill fs-4 me-3 text-warning"></i>
                            <span>Supporting Chin Communities – Myanmar &amp; Diaspora</span>
                        </li>
                    </ul>
                </div>

                <!-- Social -->
                <div>
                    <h6 class="mb-3 opacity-75">Follow Us</h6>
                    <div class="d-flex gap-4">
                        <a href="https://facebook.com/CSIEduAid" target="_blank" class="text-white fs-3 hover-opacity">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" target="_blank" class="text-white fs-3 hover-opacity">
                            <i class="bi bi-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right: Form -->
            <div class="col-lg-7 p-5 bg-white">
                <h3 class="fw-bold mb-4" style="color: #41514a;">Send Us a Message</h3>

                <form method="post" class="needs-validation" novalidate>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="full_name" class="form-control" id="fullName" 
                                       placeholder="Full Name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                                <label for="fullName">Full Name *</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" name="email" class="form-control" id="email" 
                                       placeholder="name@example.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                <label for="email">Email Address *</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <input type="tel" name="phone" class="form-control" id="phone" 
                                       placeholder="Phone Number" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                                <label for="phone">Phone Number</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <textarea name="message" class="form-control" id="message" 
                                          style="height: 160px;" placeholder="Your message..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                                <label for="message">Your Message *</label>
                            </div>
                        </div>

                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success btn-lg px-5 py-3 fw-bold">
                                <i class="bi bi-send-fill me-2"></i> Send Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="text-center mt-5 pt-4" data-aos="fade-up" data-aos-delay="200">
        <p class="lead fw-bold mb-3" style="color: #41514a;">
            Prefer another way to reach us?
        </p>
        <div class="d-flex justify-content-center gap-4 flex-wrap">
            <a href="mailto:csieduaid@gmail.com" class="btn btn-outline-dark btn-lg px-5">
                <i class="bi bi-envelope me-2"></i> Email Us
            </a>
            <a href="https://facebook.com/CSIEduAid" target="_blank" class="btn btn-outline-primary btn-lg px-5">
                <i class="bi bi-facebook me-2"></i> Message on Facebook
            </a>
        </div>
    </div>
</div>

<style>
    .scholar-card, .card {
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px -12px rgb(0 0 0 / 15%) !important;
    }
</style>

<!-- AOS Script -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });
</script>

<!-- Bootstrap form validation -->
<script>
    (function () {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

<?php include 'includes/footer.php'; ?>