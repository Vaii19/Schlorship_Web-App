<?php
/**
 * CSI EduAid - Public Header (Premium Edition)
 * Final Year Bachelor's Project - 2025 Modern Design
 */

require_once __DIR__ . '/../config/db.php';

// Current page for active link
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CSI EduAid empowers talented Chin students from Myanmar through scholarships, mentorship, and educational advancement opportunities.">
    <meta name="keywords" content="CSI EduAid, Chin scholarship, Myanmar education, student support, higher education aid">

    <title>CSI EduAid – Empowering Chin Futures Through Education</title>

    <!-- Favicon set -->
    <link rel="icon"          type="image/png" href="img/logoo.jpg" sizes="32x32">
    <link rel="icon"          type="image/png" href="img/favicon-192.png" sizes="192x192">
    <link rel="apple-touch-icon" href="img/apple-touch-icon.png">

    <!-- Bootstrap 5.3 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary:    #2f3e35;
            --primary-dark: #1e2a23;
            --success:    #4a6c58;
            --success-dark:#3a5545;
            --light:      #f9fafb;
            --dark:       #111827;
            --gray:       #6b7280;
            --glass:      rgba(255, 255, 255, 0.07);
        }

        [data-bs-theme="dark"] {
            --light:      #111827;
            --dark:       #f3f4f6;
            --gray:       #9ca3af;
            --glass:      rgba(31, 41, 55, 0.5);
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            color: var(--dark);
            background: var(--light);
            transition: background 0.4s, color 0.4s;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            transition: all 0.4s ease;
            padding: 1.1rem 0;
        }

        .navbar.scrolled {
            padding: 0.8rem 0;
            box-shadow: 0 6px 25px rgba(0,0,0,0.25);
        }

        .navbar-brand {
            color: white !important;
            font-weight: 800;
            font-size: 1.75rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.22);
            object-fit: cover;
            margin-right: 14px;
            transition: all 0.4s ease;
        }

        .navbar-brand:hover img {
            transform: rotate(10deg) scale(1.12);
            border-color: rgba(255,255,255,0.45);
        }

        .nav-link {
            color: rgba(255,255,255,0.90) !important;
            font-weight: 500;
            position: relative;
            padding: 0.6rem 1.25rem !important;
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2.5px;
            bottom: 0;
            left: 50%;
            background: var(--success);
            transition: all 0.35s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 60%;
        }

        .nav-link:hover, .nav-link.active {
            color: white !important;
        }

        .btn-apply {
            background: linear-gradient(90deg, var(--success) 0%, #5a8a6e 100%);
            border: none;
            color: white;
            font-weight: 700;
            padding: 0.75rem 1.9rem;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(90,125,104,0.3);
            transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .btn-apply:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 12px 30px rgba(90,125,104,0.45);
        }

        .btn-apply:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-login {
            color: rgba(255,255,255,0.90) !important;
            font-weight: 500;
            padding: 0.6rem 1.5rem !important;
            border-radius: 50px;
            transition: all 0.3s;
        }

        .btn-login:hover {
            color: white !important;
            background: rgba(255,255,255,0.13);
            box-shadow: 0 0 15px rgba(255,255,255,0.15);
        }

        @media (max-width: 991px) {
            .navbar-collapse {
                background: var(--primary);
                margin: 1rem 0;
                padding: 1.5rem;
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            }
            .nav-link, .btn-apply, .btn-login {
                text-align: center;
                margin: 0.6rem 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="img/logoo.jpg" alt="CSI EduAid Logo">
            CSI EduAid
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">

                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'about.php' ? 'active' : '' ?>" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'program.php' ? 'active' : '' ?>" href="program.php">Programs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'explore.php' ? 'active' : '' ?>" href="explore.php">Scholarships</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'requirements.php' ? 'active' : '' ?>" href="requirements.php">How to Apply</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'contact.php' ? 'active' : '' ?>" href="contact.php">Contact</a>
                </li>

                <li class="nav-item ms-lg-4 mt-3 mt-lg-0">
                    <a class="btn btn-apply px-4 py-2" href="application.php">
                        <i class="bi bi-rocket-takeoff-fill me-1"></i> Apply Now
                    </a>
                </li>

                <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                    <a class="btn btn-login px-4 py-2" href="student_login.php">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<!-- Spacer for fixed navbar -->
<div style="height: 80px;"></div>

<script>
// Navbar scroll effect
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 80) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});
</script>

<main class="container my-5 pt-3" role="main">