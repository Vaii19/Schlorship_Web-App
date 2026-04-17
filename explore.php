<?php include 'includes/header.php'; ?>

<!-- AOS CSS -->
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />

<div class="container py-5 my-5">
    
    <!-- Page Header -->
    <div class="text-center mb-5 pb-4" data-aos="fade-up">
        <h1 class="display-4 fw-bold mb-3" style="color: #000000;">
            International Scholarship Opportunities
        </h1>
        <div class="mx-auto" style="max-width: 820px;">
            <p class="lead text-muted">
                Fully funded and partially funded scholarships for 2026–2027<br>
                carefully selected for talented Chin and Myanmar students — all degree levels
            </p>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-5" data-aos="fade-up">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm p-4">
                <div class="row g-3 align-items-center">
                    <!-- Search Bar -->
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="searchInput" 
                                   class="form-control border-start-0 ps-0" 
                                   placeholder="Search scholarships... (e.g. Chevening, Australia, Korea)">
                        </div>
                    </div>

                    <!-- Filter by Major -->
                    <div class="col-md-3">
                        <select id="majorFilter" class="form-select">
                            <option value="">All Majors / Fields</option>
                            <option value="business">Business, Management & Economics</option>
                            <option value="it">IT, Computer Science & Technology</option>
                            <option value="english">English Language & Education</option>
                            <option value="web">Web Development & Digital Media</option>
                            <option value="worldwide">Worldwide / General</option>
                        </select>
                    </div>

                    <!-- Filter by Country -->
                    <div class="col-md-3">
                        <select id="countryFilter" class="form-select">
                            <option value="">All Countries</option>
                            <option value="uk">United Kingdom</option>
                            <option value="usa">United States</option>
                            <option value="germany">Germany</option>
                            <option value="japan">Japan</option>
                            <option value="hungary">Hungary</option>
                            <option value="turkey">Türkiye</option>
                            <option value="australia">Australia</option>
                            <option value="korea">South Korea</option>
                            <option value="china">China</option>
                            <option value="sweden">Sweden</option>
                            <option value="newzealand">New Zealand</option>
                            <option value="global">Global / Multiple</option>
                        </select>
                    </div>

                    <!-- Clear Filters -->
                    <div class="col-md-1">
                        <button id="clearFilters" class="btn btn-outline-secondary w-100">
                            Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scholarship Sections -->
    <?php 
    $sections = [
        [
            "id" => "business",
            "title" => "Business, Management &amp; Economics",
            "country" => "global",
            "items" => [
                ["title" => "Chevening Scholarships (UK)", "desc" => "1-year fully funded degree in any field. Covers tuition, living expenses, travel, and leadership development.", "link" => "https://www.chevening.org/", "link_text" => "Official Website →", "country" => "uk", "deadline" => "October 7, 2025"],
                ["title" => "Mastercard Foundation Scholars Program", "desc" => "Bachelor’s & Master’s degrees at leading universities with strong leadership focus.", "link" => "https://mastercardfdn.org/", "link_text" => "Learn More →", "country" => "global", "deadline" => "Varies by university (check partner sites)"],
                ["title" => "Joint Japan/World Bank Graduate Scholarship", "desc" => "Graduate programs in development-related fields worldwide.", "link" => "https://www.worldbank.org/en/programs/scholarships", "link_text" => "Apply Here →", "country" => "global", "deadline" => "Window 1: Feb 27, 2026 | Window 2: May 29, 2026"]
            ]
        ],
        [
            "id" => "it",
            "title" => "IT, Computer Science &amp; Technology",
            "country" => "global",
            "items" => [
                ["title" => "DAAD Scholarships – ICT &amp; Engineering (Germany)", "desc" => "Programs in AI, Data Science, Cybersecurity, and Software Engineering.", "link" => "https://www.daad.de/en/", "link_text" => "DAAD Portal →", "country" => "germany", "deadline" => "Varies by program (Aug–Oct 2025 for 2026 intake)"],
                ["title" => "Google Lime Scholarship", "desc" => "Support for studies in Computer Science and related technology fields.", "link" => "https://buildyourfuture.withgoogle.com/scholarships", "link_text" => "Official Site →", "country" => "global", "deadline" => "Typically March–April (check official site)"],
                ["title" => "Meta Emerging Tech Scholarship", "desc" => "Opportunities in AI, Machine Learning, and Digital Innovation.", "link" => "https://research.fb.com/fellowships/", "link_text" => "Check Programs →", "country" => "global", "deadline" => "Varies annually (check Meta Research site)"]
            ]
        ],
        [
            "id" => "english",
            "title" => "English Language &amp; Education",
            "country" => "global",
            "items" => [
                ["title" => "English Language Teaching Scholarships (UK)", "desc" => "Programs in TESOL, Applied Linguistics, and English Education.", "link" => "https://www.britishcouncil.org/", "link_text" => "British Council →", "country" => "uk", "deadline" => "Varies (check British Council)"],
                ["title" => "Fulbright English Teaching Assistant (ETA) Program", "desc" => "Teach English while pursuing studies in the United States.", "link" => "https://foreign.fulbrightonline.org/", "link_text" => "Fulbright ETA →", "country" => "usa", "deadline" => "Varies by country (usually mid-2026)"]
            ]
        ],
        [
            "id" => "web",
            "title" => "Web Development &amp; Digital Media",
            "country" => "global",
            "items" => [
                ["title" => "Google Digital Skills for Africa Scholarship", "desc" => "Training and funding in Web Development, UX/UI Design, and Digital Marketing.", "link" => "https://buildyourfuture.withgoogle.com/", "link_text" => "Google Digital →", "country" => "global", "deadline" => "Rolling / Varies by cohort"],
                ["title" => "Adobe Creative Cloud Scholarship", "desc" => "Opportunities in Web Design, UI/UX, and Digital Media.", "link" => "https://www.adobe.com/education.html", "link_text" => "Adobe Education →", "country" => "global", "deadline" => "Check Adobe Education portal"]
            ]
        ],
        [
            "id" => "worldwide",
            "title" => "Worldwide Fully Funded Opportunities",
            "country" => "global",
            "items" => [
                ["title" => "Fulbright Foreign Student Program (USA)", "desc" => "Fully funded programs in any field (Bachelor’s, Master’s & PhD).", "link" => "https://foreign.fulbrightonline.org/", "link_text" => "Fulbright →", "country" => "usa", "deadline" => "Varies by country (often May–Oct 2026)"],
                ["title" => "MEXT Scholarship (Japan)", "desc" => "All degree levels with Japanese language training.", "link" => "https://www.studyinjapan.go.jp/", "link_text" => "MEXT →", "country" => "japan", "deadline" => "Varies by embassy (usually April–June)"],
                ["title" => "Stipendium Hungaricum (Hungary)", "desc" => "Bachelor’s to PhD at 28 Hungarian universities.", "link" => "https://stipendiumhungaricum.hu/", "link_text" => "Hungary →", "country" => "hungary", "deadline" => "Usually January–March 2026"],
                ["title" => "Türkiye Scholarships", "desc" => "All levels with Turkish language course.", "link" => "https://www.turkiyeburslari.gov.tr/", "link_text" => "Türkiye →", "country" => "turkey", "deadline" => "Typically February–March"],
                
                // Newly Added Scholarships
                ["title" => "Australia Awards Scholarships", "desc" => "Fully funded Bachelor’s, Master’s & PhD for development-focused studies in Australia.", "link" => "https://www.dfat.gov.au/people-to-people/australia-awards", "link_text" => "Australia Awards →", "country" => "australia", "deadline" => "Usually April–May 2026"],
                ["title" => "Erasmus Mundus Joint Masters (Europe)", "desc" => "Fully funded Master’s programs studied across multiple European universities.", "link" => "https://www.eacea.ec.europa.eu/scholarships/erasmus-mundus-catalogue_en", "link_text" => "Erasmus Mundus →", "country" => "global", "deadline" => "Typically October–January 2026"],
                ["title" => "Global Korea Scholarship (GKS)", "desc" => "Undergraduate & Graduate programs in South Korea with full support.", "link" => "https://www.studyinkorea.go.kr/", "link_text" => "GKS →", "country" => "korea", "deadline" => "Usually February–March 2026"],
                ["title" => "China Scholarship Council (CSC)", "desc" => "Bachelor’s, Master’s & PhD at Chinese universities (many English-taught).", "link" => "https://www.campuschina.org/", "link_text" => "CSC →", "country" => "china", "deadline" => "Usually March–April 2026"],
                ["title" => "Swedish Institute Scholarships", "desc" => "Master’s programs in Sweden with full tuition, living expenses & travel grant.", "link" => "https://si.se/en/", "link_text" => "Swedish Institute →", "country" => "sweden", "deadline" => "Usually January–February 2026"],
                ["title" => "Manaaki New Zealand Scholarships", "desc" => "Fully funded undergraduate, Master’s & PhD in New Zealand.", "link" => "https://www.mfat.govt.nz/en/aid-and-development/scholarships/", "link_text" => "Manaaki NZ →", "country" => "newzealand", "deadline" => "Varies – check official site"],
                ["title" => "Commonwealth Scholarships", "desc" => "Master’s & PhD scholarships for students from Commonwealth developing countries.", "link" => "https://cscuk.fcdo.gov.uk/", "link_text" => "Commonwealth →", "country" => "global", "deadline" => "Varies by country (often December–March)"]
            ]
        ]
    ];

    foreach ($sections as $index => $section): 
    ?>
    <div class="scholar-section mb-8" id="section-<?= $section['id'] ?>" data-aos="fade-up" data-aos-delay="<?= $index * 80 ?>">
        <h3 class="text-center mb-4 fw-bold" style="color: #41514a;"><?= $section['title'] ?></h3>
        <div class="border-bottom mb-5 mx-auto" style="max-width: 90px; border-color: #ff9800; border-width: 3px;"></div>
        
        <div class="row g-4 g-lg-5">
            <?php foreach ($section['items'] as $item): ?>
            <div class="col-md-6 col-lg-4 scholarship-card" 
                 data-major="<?= $section['id'] ?>" 
                 data-country="<?= $item['country'] ?>">
                <div class="card scholar-card shadow-lg border-0 rounded-4 h-100 overflow-hidden">
                    <div class="bg-warning text-white text-center py-3 fw-bold" style="font-size: 0.95rem;">
                        Fully Funded
                    </div>
                    <div class="card-body p-5 d-flex flex-column">
                        <h5 class="fw-bold mb-3" style="color: #41514a; line-height: 1.35;">
                            <?= $item['title'] ?>
                        </h5>
                        <p class="text-muted flex-grow-1 mb-3">
                            <?= $item['desc'] ?>
                        </p>
                        <p class="text-success fw-semibold small mb-4">
                            <i class="bi bi-calendar-check"></i> Deadline: <?= $item['deadline'] ?>
                        </p>
                        <a href="<?= $item['link'] ?>" target="_blank" class="btn btn-outline-success mt-auto">
                            <?= $item['link_text'] ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Final CTA -->
    <div class="text-center mt-6 pt-5 pb-4 bg-light rounded-4 shadow-sm p-5" data-aos="fade-up">
        <h3 class="fw-bold mb-3" style="color: #41514a;">Need help choosing the right scholarship?</h3>
        <p class="lead text-muted mb-4">
            Our CSI EduAid mentors will guide you through the application process for any degree level.
        </p>
        <a href="requirements.php" class="btn btn-success btn-lg px-5 py-3 fw-bold shadow-lg">
            <i class="bi bi-rocket-takeoff-fill me-2"></i> Start Your Application Journey
        </a>
        <p class="text-muted mt-4 small">
            <strong>Important:</strong> Deadlines are approximate and subject to change. Always verify the exact dates and requirements on the official websites (updated April 2026).
        </p>
    </div>
</div>

<style>
    .scholar-card {
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .scholar-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 50px -12px rgb(0 0 0 / 15%) !important;
    }

    .scholar-card .btn {
        width: 100% !important;
        min-height: 54px !important;
        padding: 12px 24px !important;
        font-size: 1.05rem !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .mb-8 {
        margin-bottom: 5.5rem !important;
    }

    .scholar-section.hidden {
        display: none;
    }
</style>

<!-- JavaScript for Search & Filters -->
<script>
    const searchInput = document.getElementById('searchInput');
    const majorFilter = document.getElementById('majorFilter');
    const countryFilter = document.getElementById('countryFilter');
    const clearBtn = document.getElementById('clearFilters');
    const cards = document.querySelectorAll('.scholarship-card');
    const sections = document.querySelectorAll('.scholar-section');

    function filterScholarships() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedMajor = majorFilter.value;
        const selectedCountry = countryFilter.value;

        cards.forEach(card => {
            const title = card.querySelector('h5').textContent.toLowerCase();
            const desc = card.querySelector('p').textContent.toLowerCase();
            const major = card.getAttribute('data-major');
            const country = card.getAttribute('data-country');

            const matchesSearch = !searchTerm || title.includes(searchTerm) || desc.includes(searchTerm);
            const matchesMajor = !selectedMajor || major === selectedMajor;
            const matchesCountry = !selectedCountry || country === selectedCountry;

            card.style.display = (matchesSearch && matchesMajor && matchesCountry) ? 'block' : 'none';
        });

        sections.forEach(section => {
            const visibleCards = Array.from(section.querySelectorAll('.scholarship-card'))
                .some(card => card.style.display !== 'none');
            section.classList.toggle('hidden', !visibleCards);
        });
    }

    searchInput.addEventListener('input', filterScholarships);
    majorFilter.addEventListener('change', filterScholarships);
    countryFilter.addEventListener('change', filterScholarships);

    clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        majorFilter.value = '';
        countryFilter.value = '';
        filterScholarships();
    });

    filterScholarships();
</script>

<!-- AOS Script -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });
</script>

<?php include 'includes/footer.php'; ?>