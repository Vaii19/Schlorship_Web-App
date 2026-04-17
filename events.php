<?php include 'includes/header.php'; ?>

<!-- AOS CSS -->
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />

<div class="container my-5 py-4">

    <!-- Page Header -->
    <div class="text-center mb-5 pb-4" data-aos="fade-up">
        <h1 class="display-5 fw-bold mb-3" style="color: #000;">
            Workshops & Events
        </h1>
        <p class="lead text-muted mx-auto" style="max-width: 820px;">
            Practical workshops, leadership programs, and community events designed to build skills, 
            confidence, and connections for Chin students in Myanmar and the diaspora.
        </p>
    </div>

    <!-- Upcoming Events -->
    <h2 class="h3 fw-bold mb-5 text-center" style="color: #2c4a2c;" data-aos="fade-up">
        Upcoming Events
    </h2>

    <div class="row g-4 g-lg-5 justify-content-center">

        <!-- Event 1 -->
        <div class="col-lg-6 col-xl-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card h-100 border-0 shadow-lg overflow-hidden hover-lift rounded-4">
                <div class="card-header bg-primary text-white text-center py-4">
                    <i class="bi bi-mic-fill fs-1"></i>
                </div>
                <div class="card-body p-4 p-lg-5 d-flex flex-column text-center">
                    <span class="badge bg-success mb-3 align-self-center">May 15 – June 5, 2026</span>
                    <h3 class="h4 fw-bold mb-4" style="color: #000;">English for Academic Success</h3>
                    <p class="text-muted mb-4 flex-grow-1">
                        4-week online workshop focused on academic writing, presentation skills, 
                        and university-level English preparation.
                    </p>
                    <div class="mt-auto">
                        <a href="#" class="btn btn-outline-primary btn-lg px-5 fw-bold w-100">
                            Register Now
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event 2 -->
        <div class="col-lg-6 col-xl-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card h-100 border-0 shadow-lg overflow-hidden hover-lift rounded-4">
                <div class="card-header bg-success text-white text-center py-4">
                    <i class="bi bi-laptop fs-1"></i>
                </div>
                <div class="card-body p-4 p-lg-5 d-flex flex-column text-center">
                    <span class="badge bg-success mb-3 align-self-center">June 10 – 12, 2026</span>
                    <h3 class="h4 fw-bold mb-4" style="color: #000;">Digital Skills Bootcamp</h3>
                    <p class="text-muted mb-4 flex-grow-1">
                        Hands-on training in Microsoft Office, Google Workspace, basic coding, 
                        and digital research skills. Hybrid format available.
                    </p>
                    <div class="mt-auto">
                        <a href="#" class="btn btn-outline-success btn-lg px-5 fw-bold w-100">
                            Register Now
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event 3 -->
        <div class="col-lg-6 col-xl-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card h-100 border-0 shadow-lg overflow-hidden hover-lift rounded-4">
                <div class="card-header bg-warning text-dark text-center py-4">
                    <i class="bi bi-people-fill fs-1"></i>
                </div>
                <div class="card-body p-4 p-lg-5 d-flex flex-column text-center">
                    <span class="badge bg-success mb-3 align-self-center">June 28, 2026</span>
                    <h3 class="h4 fw-bold mb-4" style="color: #000;">Chin Youth Leadership Forum</h3>
                    <p class="text-muted mb-4 flex-grow-1">
                        Full-day networking and leadership event with panel discussions, 
                        workshops, and mentorship from Chin professionals and alumni.
                    </p>
                    <div class="mt-auto">
                        <a href="#" class="btn btn-outline-warning btn-lg px-5 fw-bold text-dark w-100">
                            Register Now
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Past Events -->
    <div class="mt-6 pt-5">
        <h2 class="h3 fw-bold mb-5 text-center" style="color: #2c4a2c;" data-aos="fade-up">
            Past Events
        </h2>

        <div class="accordion" id="pastEventsAccordion">

            <div class="accordion-item" data-aos="fade-up" data-aos-delay="100">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        <strong>March 2026:</strong> University Application Workshop Series
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#pastEventsAccordion">
                    <div class="accordion-body">
                        Three-part online series that helped over 70 Chin students improve their personal statements, 
                        secure recommendation letters, and prepare strong scholarship applications.
                    </div>
                </div>
            </div>

            <div class="accordion-item" data-aos="fade-up" data-aos-delay="150">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <strong>January 2026:</strong> Chin New Year Celebration & Networking Night
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#pastEventsAccordion">
                    <div class="accordion-body">
                        Hybrid event featuring traditional cultural performances, food, and career networking. 
                        More than 120 students and alumni joined from across Myanmar and overseas.
                    </div>
                </div>
            </div>

            <div class="accordion-item" data-aos="fade-up" data-aos-delay="200">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        <strong>November 2025:</strong> Mental Health & Resilience for Students
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#pastEventsAccordion">
                    <div class="accordion-body">
                        Interactive webinar with psychologists and peer counselors focused on coping with stress, 
                        anxiety, and building resilience — specially designed for students from conflict-affected areas.
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Call to Action -->
    <div class="text-center mt-6 pt-5 pb-4 bg-light rounded-4 shadow-sm p-5" data-aos="fade-up" data-aos-delay="300">
        <h3 class="fw-bold mb-4" style="color: #000;">
            Have an Idea for a Workshop or Event?
        </h3>
        <p class="lead text-muted mb-4" style="color: #000;">
            We welcome suggestions from the Chin community. Share your ideas or volunteer to facilitate a session.
        </p>
        <a href="contact.php" class="btn btn-danger btn-lg px-5 py-3 fw-bold shadow-lg">
            <i class="bi bi-chat-dots-fill me-2"></i> Suggest an Event
        </a>
    </div>

</div>

<style>
    .hover-lift {
        transition: all 0.35s ease;
    }
    .hover-lift:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important;
    }
    .card-header {
        font-size: 2.5rem;
    }
    /* Make buttons consistent */
    .card-body .btn {
        width: 100% !important;
        min-height: 58px !important;
        padding: 12px 20px !important;
        font-size: 1.1rem !important;
    }
</style>

<!-- AOS Script -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 900,
        once: true,
        offset: 80
    });
</script>

<?php include 'includes/footer.php'; ?>