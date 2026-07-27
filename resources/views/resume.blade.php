<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume - Vanka Naga Rakesh</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #2563eb;
            --dark: #0f172a;
            --light-bg: #f8fafc;
            --border: #e2e8f0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-bg);
            color: #334155;
            line-height: 1.6;
        }
        .resume-container {
            max-width: 900px;
            background: #ffffff;
            margin: 40px auto;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            border: 1px solid var(--border);
            padding: 50px;
            position: relative;
        }
        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #ffffff;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
        }
        h1, h2, h3, h4, h5, h6 {
            color: var(--dark);
            font-weight: 700;
        }
        .section-title {
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--primary);
            border-bottom: 2px solid var(--border);
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .timeline-item {
            position: relative;
            padding-left: 24px;
            border-left: 2px solid var(--border);
            padding-bottom: 20px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 6px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--primary);
            border: 2px solid #ffffff;
        }
        .badge-custom {
            background-color: #eff6ff;
            color: var(--primary);
            font-weight: 600;
            border-radius: 30px;
            padding: 5px 15px;
            font-size: 0.75rem;
            border: 1px solid #dbeafe;
        }
        .social-link {
            color: #475569;
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s ease;
        }
        .social-link:hover {
            color: var(--primary);
        }
        @media print {
            body {
                background-color: #ffffff;
            }
            .resume-container {
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="container pb-5">
        <!-- Floating Actions -->
        <div class="d-flex justify-content-end gap-2 mt-4 max-w-4xl mx-auto no-print" style="max-width:900px;">
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4"><i data-lucide="printer" class="d-inline-block align-middle me-1"></i> Print / Save as PDF</button>
            <a href="/" class="btn btn-outline-secondary rounded-pill px-4">Back to Website</a>
        </div>

        <div class="resume-container">
            <!-- Header -->
            <div class="row align-items-center mb-5 pb-4 border-bottom">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <img src="/creator.jpg" alt="Vanka Naga Rakesh" class="profile-img">
                </div>
                <div class="col-md-9 text-center text-md-start">
                    <h1 class="display-5 mb-1">Vanka Naga Rakesh</h1>
                    <p class="lead text-primary fw-bold mb-3">Founder & Full Stack Developer</p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-md-start">
                        <a href="mailto:rakesh28200511@gmail.com" class="social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px; height:14px; display:inline-block; vertical-align:middle; margin-right:4px;">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            rakesh28200511@gmail.com
                        </a>
                        <a href="https://www.linkedin.com/in/vanka-naga-rakesh-5b2894323" target="_blank" class="social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:14px; height:14px; display:inline-block; vertical-align:middle; margin-right:4px;">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                            LinkedIn
                        </a>
                        <a href="https://www.instagram.com/its_rocky_rakesh_/" target="_blank" class="social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px; height:14px; display:inline-block; vertical-align:middle; margin-right:4px;">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                            Instagram
                        </a>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-md-8">
                    <!-- About Me -->
                    <div class="mb-5">
                        <h3 class="section-title">Summary</h3>
                        <p>An innovative CSE Undergraduate and Full-Stack Developer specializing in crafting premium, high-performance web systems and database architectures. Highly experienced in PHP, Laravel framework, Node.js development, and responsive UI design workflows inspired by Apple and Stripe design aesthetics.</p>
                    </div>

                    <!-- Journey / Education -->
                    <div class="mb-5">
                        <h3 class="section-title">Education</h3>
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="mb-0 fw-bold">B.Tech in Computer Science & Engineering</h6>
                                <small class="text-primary fw-bold">2023 - Present</small>
                            </div>
                            <p class="mb-1 text-muted text-xs">Madanapalle Institute of Technology & Science (MITS)</p>
                            <span class="text-xs text-primary bg-primary-soft">CGPA: 8.5+</span>
                        </div>
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="mb-0 fw-bold">Full-Stack Development Architect</h6>
                                <small class="text-primary fw-bold">2024 - Present</small>
                            </div>
                            <p class="mb-0 text-muted text-xs">Built complex PG/Hostel management ecosystems and open-source packages.</p>
                        </div>
                    </div>

                    <!-- Projects -->
                    <div>
                        <h3 class="section-title">Key Projects</h3>
                        <div class="mb-4">
                            <h6 class="fw-bold mb-1">PG Management System</h6>
                            <p class="text-muted text-sm mb-1">A luxury housing accommodations dashboard featuring automated payment invoicing, dynamic room roster mapping, visitor inquiry desk, and plate food management modules.</p>
                            <small class="text-muted">Technologies: Laravel, PHP, Bootstrap, MySQL, jsPDF</small>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Stripe Webhook Composer Package</h6>
                            <p class="text-muted text-sm mb-1">An open-source integration utility designed to map and process webhook notifications securely for SaaS projects.</p>
                            <small class="text-muted">Technologies: PHP, Composer Engine</small>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-4">
                    <!-- Core Skills -->
                    <div class="mb-5">
                        <h3 class="section-title">Skills</h3>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge-custom">Laravel</span>
                            <span class="badge-custom">PHP</span>
                            <span class="badge-custom">React.js</span>
                            <span class="badge-custom">Node.js</span>
                            <span class="badge-custom">MySQL</span>
                            <span class="badge-custom">MongoDB</span>
                            <span class="badge-custom">Tailwind CSS</span>
                            <span class="badge-custom">Docker</span>
                            <span class="badge-custom">AWS</span>
                            <span class="badge-custom">Git & GitHub</span>
                        </div>
                    </div>

                    <!-- Achievements -->
                    <div class="mb-5">
                        <h3 class="section-title">Achievements</h3>
                        <ul class="ps-3 mb-0 text-sm">
                            <li class="mb-2"><b>MITS Hackathon Winner</b>: Secured 1st place in the campus-wide smart logistics application design competition.</li>
                            <li><b>Oracle Certified Associate</b>: Fundamental accreditation validating object-oriented paradigms.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
