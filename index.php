<?php include 'C:\xampp\htdocs\CSI_EduAid\includes\header.php'; ?>

<!-- Advanced Animation Styles -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    /* Subtle hero background zoom animation */
    .hero-bg {
        animation: subtleZoom 25s infinite alternate ease-in-out;
        transition: transform 0.8s ease;
    }
    @keyframes subtleZoom {
        from { transform: scale(1.0); }
        to   { transform: scale(1.08); }
    }

    /* Enhanced button hover with lift + glow */
    .hero-btn {
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .hero-btn:hover {
        transform: translateY(-6px) scale(1.05);
        box-shadow: 0 20px 40px rgba(255, 152, 0, 0.35) !important;
    }

    /* Counter number styling */
    .counter {
        font-variant-numeric: tabular-nums;
    }

    /* ============= MOBILE RESPONSIVE FIXES ============= */
    @media (max-width: 768px) {
        .hero-bg {
            height: 75vh !important;
        }
        
        .display-3 {
            font-size: 2.2rem !important;
            line-height: 1.1 !important;
        }
        
        .lead.fs-4 {
            font-size: 1.1rem !important;
        }
        
        .hero-btn {
            font-size: 1.1rem !important;
            padding: 12px 28px !important;
            width: 100%;
        }
        
        .stat-item .p-4 {
            padding: 1.5rem !important;
        }
        
        .display-5 {
            font-size: 2rem !important;
        }
        
        .accordion-button {
            font-size: 1rem !important;
            padding: 1rem 1.25rem !important;
        }
    }

    @media (max-width: 576px) {
        .hero-bg {
            height: 70vh !important;
        }
        
        .display-3 {
            font-size: 1.9rem !important;
        }
    }
</style>

<!-- Hero Section -->
<section class="position-relative text-center text-white overflow-hidden">
    <div class="position-absolute top-0 start-0 w-100 h-100" 
         style="background: linear-gradient(to bottom, rgba(0,0,0,0.32) 0%, rgba(0,0,0,0.65) 100%); z-index: 1;"></div>
    
    <img src="img/gogo.jpg" alt="Chin students studying and smiling together in a classroom setting" 
         class="img-fluid w-100 object-fit-cover hero-bg" 
         style="height: 88vh; filter: brightness(0.88) contrast(1.08);">

    <div class="position-absolute top-50 start-50 translate-middle w-100 px-4 px-md-5" 
         style="z-index: 2;" data-aos="fade-up" data-aos-duration="1200">
        <div class="container">
            <h1 class="display-3 display-md-1 fw-bold mb-3 mb-md-4" 
                style="text-shadow: 3px 3px 18px rgba(0,0,0,0.9); line-height: 1.05; letter-spacing: -1px;"
                data-aos="fade-up" data-aos-delay="200">
                Education for Chin Students<br>
                <span style="color: #ff9800;">Chase Your Dream.</span>
            </h1>
            
            <p class="lead fs-4 fs-md-3 mb-4 mb-md-5 fw-light" 
               style="text-shadow: 1px 1px 10px rgba(0,0,0,0.85); max-width: 820px; margin-left: auto; margin-right: auto;"
               data-aos="fade-up" data-aos-delay="400">
                Scholarships • Mentorship • Real Support<br>
                Helping talented Chin youth from Myanmar build a brighter future — one degree at a time.
            </p>

            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center" data-aos="fade-up" data-aos-delay="600">
                <a href="application.php" class="btn btn-lg px-5 py-3 fw-bold shadow-lg hero-btn"
                   style="background-color: #ff9800; color: white; border: none; border-radius: 50rem; font-size: 1.3rem;">
                    Apply for Scholarship Now
                </a>
                <a href="#about" class="btn btn-lg px-5 py-3 fw-bold border border-2 border-white text-white bg-transparent hero-btn"
                   style="border-radius: 50rem; font-size: 1.3rem;">
                    Learn More
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Animated Impact Stats -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row text-center g-4" id="stats-row">
            <div class="col-md-4 stat-item" data-aos="fade-up" data-aos-delay="100">
                <div class="p-4">
                    <h3 class="display-5 fw-bold text-primary mb-1" style="color: #ff9800 !important;">
                        <span class="counter" data-target="140">0</span><span style="font-size:0.7em;">+</span>
                    </h3>
                    <p class="fs-5 text-muted">Students supported since 2018</p>
                </div>
            </div>
            <div class="col-md-4 stat-item" data-aos="fade-up" data-aos-delay="200">
                <div class="p-4">
                    <h3 class="display-5 fw-bold text-primary mb-1" style="color: #ff9800 !important;">
                        <span class="counter" data-target="3">0</span>
                    </h3>
                    <p class="fs-5 text-muted">Partner universities & colleges</p>
                </div>
            </div>
            <div class="col-md-4 stat-item" data-aos="fade-up" data-aos-delay="300">
                <div class="p-4">
                    <h3 class="display-5 fw-bold text-primary mb-1" style="color: #ff9800 !important;">
                        <span class="counter" data-target="95">0</span><span style="font-size:0.7em;">%</span>
                    </h3>
                    <p class="fs-5 text-muted">Completion rate of sponsored students</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About / Mission Section -->
<section id="about" class="py-5 py-md-6 bg-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                <h2 class="display-5 fw-bold mb-4" style="color: #2c4a2c;">
                    Empowering Chin Youth Through Education
                </h2>
                <p class="lead text-muted mb-4">
                    Chin students face unique challenges — remote geography, limited infrastructure, regional instability, and financial hardship.
                </p>
                <p class="mb-4">
                    Founded in 2018, CSI EduAid is a registered non-profit dedicated to removing these barriers. We offer full or partial scholarships, academic mentoring, English support, and career guidance to promising Chin students.
                </p>
                <ul class="list-unstyled fs-5 mb-4">
                    <li class="mb-3" data-aos="fade-up" data-aos-delay="100"><i class="bi bi-check-circle-fill text-success me-3"></i>Full or partial tuition coverage</li>
                    <li class="mb-3" data-aos="fade-up" data-aos-delay="200"><i class="bi bi-check-circle-fill text-success me-3"></i>Monthly living allowance</li>
                    <li class="mb-3" data-aos="fade-up" data-aos-delay="300"><i class="bi bi-check-circle-fill text-success me-3"></i>One-on-one mentorship & academic coaching</li>
                    <li data-aos="fade-up" data-aos-delay="400"><i class="bi bi-check-circle-fill text-success me-3"></i>Pre-departure orientation & university transition support</li>
                </ul>
                <a href="about.php" class="btn btn-outline-primary btn-lg px-4" data-aos="fade-up" data-aos-delay="500">Read Our Full Story & Impact →</a>
            </div>
            <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000">
                <img src="img/student002.jpg" alt="Group of Chin university students in a classroom" 
                     class="img-fluid rounded-4 shadow-lg">
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="container my-5 py-5">
    <h2 class="text-center mb-5 fw-bold" style="color: #2c4a2c;" data-aos="fade-up">
        Voices from Our Scholars
    </h2>
    <div class="row g-4">
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card shadow border-0 h-100 p-4 hover-lift">
                <p class="fst-italic mb-4">"Thanks to CSI EduAid, I became the first in my village to attend university. I’m now in my third year of Civil Engineering and hope to return home to build better roads and schools."</p>
                <strong style="color: #2c4a2c;">— Sui Tha Par, 3rd Year Civil Engineering</strong>
            </div>
        </div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card shadow border-0 h-100 p-4 hover-lift">
                <p class="fst-italic mb-4">"The mentorship program transformed my English and helped me write a winning university application. Today I’m studying Nursing in Yangon and plan to serve rural Chin clinics."</p>
                <strong style="color: #2c4a2c;">— Hnin Si, 2nd Year Nursing</strong>
            </div>
        </div>
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card shadow border-0 h-100 p-4 hover-lift">
                <p class="fst-italic mb-4">"I never believed higher education was possible for someone from my background. CSI EduAid gave me more than money — they gave me hope, guidance, and a real future."</p>
                <strong style="color: #2c4a2c;">— Van Bawi, 1st Year Computer Science</strong>
            </div>
        </div>
    </div>
</section>

<!-- Professional FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <h2 class="text-center mb-5 fw-bold" style="color: #2c4a2c;" data-aos="fade-up">
                    Frequently Asked Questions
                </h2>
                
                <div class="accordion" id="faqAccordion">

                    <!-- Original Questions (Unchanged) -->
                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="100">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Who is eligible to apply for the CSI EduAid Scholarship?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                            <div class="accordion-body">
                                Open to Chin students from Myanmar (or the diaspora) who have completed high school, show strong academic potential, genuine financial need, and a clear commitment to serve their community. Priority is given to applicants from Chin State.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="200">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                What exactly does the scholarship cover?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                            <div class="accordion-body">
                                Full or partial tuition, monthly living allowance, textbooks, one-on-one mentorship, English support, and pre-university orientation — tailored to each scholar’s needs.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="300">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Is the scholarship renewable?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree">
                            <div class="accordion-body">
                                Yes — renewable for up to 4 years (or the full program length) as long as the student maintains good academic standing and submits progress reports.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="400">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                How competitive is the selection process?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour">
                            <div class="accordion-body">
                                Highly competitive. Hundreds of applications are received each year. Selection is based on academic merit, financial need, personal statement, and community impact. Shortlisted candidates attend an interview.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="500">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                What is the application deadline?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive">
                            <div class="accordion-body">
                                Applications for the 2026–2027 academic year close on <strong>April 30, 2026</strong>. Only complete applications are considered.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="600">
                        <h2 class="accordion-header" id="headingSix">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                How do I apply and what documents are required?
                            </button>
                        </h2>
                        <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix">
                            <div class="accordion-body">
                                Submit online at <a href="application.php" class="text-decoration-underline">application.php</a>. You will need: academic transcripts, personal statement, two recommendation letters, proof of financial need, and a passport photo. Full checklist is on the application page.
                            </div>
                        </div>
                    </div>

                    <!-- Newly Added Questions -->
                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="700">
                        <h2 class="accordion-header" id="headingSeven">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                Can I apply if I am already studying at a university?
                            </button>
                        </h2>
                        <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven">
                            <div class="accordion-body">
                                Yes, current university students in their 1st or 2nd year may apply for continuing support, provided they maintain good academic performance and demonstrate continued financial need.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="800">
                        <h2 class="accordion-header" id="headingEight">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                                Do you support students studying abroad?
                            </button>
                        </h2>
                        <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight">
                            <div class="accordion-body">
                                Currently, our scholarships primarily support students studying within Myanmar or in neighboring countries (India, Thailand, Malaysia). Support for studying in Western countries is considered case-by-case for exceptional students.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="900">
                        <h2 class="accordion-header" id="headingNine">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                                Is there an age limit for applicants?
                            </button>
                        </h2>
                        <div id="collapseNine" class="accordion-collapse collapse" aria-labelledby="headingNine">
                            <div class="accordion-body">
                                Generally, applicants should be between 17 and 25 years old at the time of application. Exceptional cases beyond this range may be considered.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="1000">
                        <h2 class="accordion-header" id="headingTen">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                                When will I know if my application is successful?
                            </button>
                        </h2>
                        <div id="collapseTen" class="accordion-collapse collapse" aria-labelledby="headingTen">
                            <div class="accordion-body">
                                Shortlisted candidates are usually contacted within 6–8 weeks after the deadline for an interview. Final decisions are typically announced within 3 months of the application closing date.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA -->
<section class="text-dark py-5 text-center" style="background-color: #ffffff;">
    <div class="container py-4" data-aos="fade-up">
        <h2 class="display-5 fw-bold mb-4">Ready to Change Your Future?</h2>
        <p class="lead mb-4">Applications for the 2026–2027 academic year are open now.<br>Don’t miss your chance to study and lead.</p>
        <a href="application.php" class="btn btn-light btn-lg px-5 py-3 fw-bold shadow hero-btn"
           style="background-color: #ff9800; color: white; border: none; font-size: 1.3rem;">
            Start Your Application Today
        </a>
        <p class="mt-4 small text-muted">Deadline: April 30, 2026 • Only complete applications reviewed</p>
    </div>
</section>

<!-- AOS Library + Counter Animation Script -->
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        AOS.init({
            duration: 1000,
            once: true,
            offset: 80
        });

        function animateCounter(el) {
            const target = parseFloat(el.getAttribute('data-target'));
            const suffix = el.innerHTML.includes('%') ? '%' : '+';
            let current = 0;
            const increment = target / 45;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    el.textContent = Math.floor(target) + suffix;
                    clearInterval(timer);
                } else {
                    el.textContent = Math.floor(current) + (suffix === '%' ? '%' : '');
                }
            }, 35);
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.querySelectorAll('.counter').forEach(counter => {
                        if (!counter.dataset.animated) {
                            animateCounter(counter);
                            counter.dataset.animated = 'true';
                        }
                    });
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.6 });

        const statsSection = document.getElementById('stats-row');
        if (statsSection) observer.observe(statsSection);
    });
</script>

<?php include 'includes/footer.php'; ?>