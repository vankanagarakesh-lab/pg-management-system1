@php
    if (!function_exists('getEmbedMapUrl')) {
        function getEmbedMapUrl($url) {
            if (empty($url)) {
                return '';
            }
            
            // If it looks like HTML iframe code, extract the src attribute
            if (strpos($url, '<iframe') !== false && preg_match('/src="([^"]+)"/', $url, $matches)) {
                $url = $matches[1];
            }
            
            // If it's already an embed URL, return it
            if (strpos($url, '/embed') !== false || strpos($url, 'output=embed') !== false) {
                return $url;
            }
            
            // Parse coordinates or place name from standard Google Maps URL
            if (preg_match('/\/maps\/place\/([^\/@]+)/', $url, $matches)) {
                $place = $matches[1];
                return "https://maps.google.com/maps?q=" . urlencode(urldecode($place)) . "&output=embed";
            }
            
            if (preg_match('/\/maps\/search\/([^\/@]+)/', $url, $matches)) {
                $searchTerm = $matches[1];
                return "https://maps.google.com/maps?q=" . urlencode(urldecode($searchTerm)) . "&output=embed";
            }
            
            if (preg_match('/\/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
                $lat = $matches[1];
                $lng = $matches[2];
                return "https://maps.google.com/maps?q={$lat},{$lng}&output=embed";
            }
            
            $parsed = parse_url($url);
            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $queryParams);
                if (isset($queryParams['q'])) {
                    return "https://maps.google.com/maps?q=" . urlencode($queryParams['q']) . "&output=embed";
                }
            }
            
            return $url;
        }
    }
@endphp

@extends('layouts.app')

@section('title', $content->pg_title ?? $content->seo_title ?? 'Premium PG Accommodations')
@section('meta_description', $content->seo_description ?? 'World class sharing and private occupancies.')
@section('meta_keywords', $content->seo_keywords ?? 'PG, rooms, hostel')

@section('content')
<!-- Public Landing Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light glass-nav fixed-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/">
            @if(!empty($content->logo_image))
                <img src="{{ $content->logo_image }}" alt="Logo" class="me-2" style="height: 36px; max-width: 120px; object-fit: contain;">
            @else
                <div class="avatar-box me-2" style="background: var(--primary-gradient);">
                    <i data-lucide="hotel" class="text-white" style="width: 20px; height: 20px;"></i>
                </div>
            @endif
            <span class="fw-bold text-dark fs-5 brand-text" style="letter-spacing: 0.05em; background: linear-gradient(to right, #0F172A, #2563EB); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $content->logo_text ?: 'PG MANAGEMENT SYSTEM' }}</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#landingNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="landingNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 gap-lg-3 fw-bold">
                <li class="nav-item"><a class="nav-link text-dark" href="#hero">Home</a></li>
                <li class="nav-item"><a class="nav-link text-dark" href="#facilities">Facilities</a></li>
                <li class="nav-item"><a class="nav-link text-dark" href="#rooms">Rooms</a></li>
                <li class="nav-item"><a class="nav-link text-dark" href="#locations">Locations</a></li>
                <li class="nav-item"><a class="nav-link text-dark" href="#pricing">Pricing</a></li>
                <li class="nav-item"><a class="nav-link text-dark" href="#contact">Contact</a></li>
            </ul>
            <div class="d-flex gap-2">
                <a href="/role-selection" class="btn btn-primary rounded-pill px-4">Login / Register</a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section Banner -->
<header id="hero" class="min-vh-100 d-flex align-items-center" style="background-image: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.45)), url('{{ $content->banner_image }}'); background-size: cover; background-position: center; margin-top:0px;">
    <div class="container pt-5">
        <div class="row align-items-center min-vh-100 pt-5">
            <div class="col-lg-6 text-center text-lg-start text-white">
                <span class="badge bg-primary-soft text-white px-3 py-2 rounded-pill mb-3" style="background-color: rgba(255, 255, 255, 0.15);">{{ $content->banner_tag ?: 'Premium Living Redefined' }}</span>
                <h1 class="display-4 fw-bold text-white mb-4 leading-tight">{{ $content->banner_title }}</h1>
                <p class="lead text-light-muted mb-5">{{ $content->banner_subtitle }}</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a href="#rooms" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg">Explore Rooms <i data-lucide="arrow-right"></i></a>
                    <a href="#about" class="btn btn-outline-light btn-lg rounded-pill px-5">Learn More</a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-center position-relative" style="min-height: 400px;">
                <!-- Rating Card -->
                <div class="premium-card p-3 rounded-4 shadow border border-light border-opacity-10 backdrop-blur" style="background: rgba(255, 255, 255, 0.95); max-width: 300px; position: absolute; top: 40px; right: 20px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-box bg-warning-soft" style="width:40px; height:40px; background-color: rgba(245, 158, 11, 0.1);"><i data-lucide="star" class="text-warning"></i></div>
                        <div class="text-start">
                            <h6 class="text-dark mb-0 fw-bold small">4.9 / 5.0 Rating</h6>
                            <small class="text-muted">By 500+ happy tenants</small>
                        </div>
                    </div>
                </div>

                <!-- Creator Preview Card -->
                <div class="premium-card p-3 rounded-4 shadow border border-light border-opacity-10 backdrop-blur creator-preview-card" style="background: rgba(15, 23, 42, 0.75); border: 1px solid rgba(255,255,255,0.1) !important; max-width: 320px; position: absolute; bottom: 40px; right: 20px; cursor: pointer; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);" data-bs-toggle="modal" data-bs-target="#creatorProfileModal">
                    <div class="d-flex align-items-center gap-3">
                        <div class="position-relative">
                            <img src="/creator.jpg" class="rounded-circle border border-2 border-primary" style="width:55px; height:55px; object-fit:cover; box-shadow: 0 0 15px rgba(37, 99, 235, 0.5);">
                            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" style="width: 12px; height: 12px;"></span>
                        </div>
                        <div class="text-start text-white">
                            <h6 class="text-white mb-0 fw-bold small">VANKA NAGA RAKESH</h6>
                            <small class="text-primary fw-bold" style="font-size: 11px;">Founder & Developer</small>
                            <span class="d-block text-muted text-xs mt-1"><i data-lucide="sparkles" class="d-inline-block align-middle me-1" style="width:12px; height:12px;"></i> View Creator Profile</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- About Section -->
<section id="about" class="section-padding bg-white-dynamic">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="row g-3">
                    <div class="col-6"><img src="https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=400&q=80" class="img-fluid rounded-4 shadow" alt="Room View" style="border-radius: 20px;"></div>
                    <div class="col-6"><img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=400&q=80" class="img-fluid rounded-4 shadow mt-4" alt="Hall View" style="border-radius: 20px;"></div>
                </div>
            </div>
            <div class="col-lg-6 ps-lg-5 text-start">
                <span class="text-primary fw-bold text-uppercase tracking-wider">{{ $content->about_badge ?: 'About Us' }}</span>
                <h2 class="display-6 text-dark fw-bold mt-2 mb-4">{{ $content->about_title ?: 'A Cozy, Premium Safe Space For You' }}</h2>
                <p class="text-muted mb-4 lead" style="font-size: 1.05rem;">{{ $content->about_text }}</p>
                <div class="row g-4 mt-2">
                    <div class="col-md-6 d-flex gap-3">
                        <div class="avatar-box bg-primary-soft text-primary flex-shrink-0" style="width: 50px; height: 50px;"><i data-lucide="shield-check"></i></div>
                        <div>
                            <h5 class="text-dark mb-1 fw-bold fs-6">Secure Environment</h5>
                            <p class="text-muted small">Biometric gates and round-the-clock camera monitoring.</p>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex gap-3">
                        <div class="avatar-box bg-success-soft text-success flex-shrink-0" style="width: 50px; height: 50px;"><i data-lucide="utensils"></i></div>
                        <div>
                            <h5 class="text-dark mb-1 fw-bold fs-6">Hygienic Kitchen</h5>
                            <p class="text-muted small">Healthy and delicious food cooked under expert supervision.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Facilities Section -->
<section id="facilities" class="section-padding bg-premium border-top border-bottom">
    <div class="container text-center">
        <div class="mb-5 max-w-2xl mx-auto">
            <span class="text-primary fw-bold text-uppercase tracking-wider">Amenities</span>
            <h2 class="display-6 text-dark fw-bold mt-2">{{ $content->facilities_title ?: 'Top Class Facilities' }}</h2>
            <p class="text-muted">{{ $content->facilities_subtitle ?: 'We provide premium amenities to make your stay hassle-free and comfortable.' }}</p>
        </div>
        <div class="row g-4 text-start">
            @foreach($facilities as $fac)
                <div class="col-md-4 col-sm-6">
                    <div class="premium-card h-100 p-4">
                        <div class="avatar-box mb-4" style="width:50px; height:50px; background-color: rgba(37,99,235,0.1);">
                            <i data-lucide="{{ str_replace('fa-', '', $fac->icon === 'fa-shield-halved' ? 'shield' : (str_replace('fa-utensils', 'cooking-pot', str_replace('fa-broom', 'cleaning-bucket', str_replace('fa-shirt', 'shirt', str_replace('fa-bolt', 'bolt', str_replace('fa-wifi', 'wifi', $fac->icon))))))) }}" class="text-primary"></i>
                        </div>
                        <h5 class="text-dark fw-bold mb-2 fs-6">{{ $fac->name }}</h5>
                        <p class="text-muted small mb-0">{{ $fac->desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Rooms Section -->
<section id="rooms" class="section-padding bg-white-dynamic">
    <div class="container text-center">
        <div class="mb-5 max-w-2xl mx-auto">
            <span class="text-primary fw-bold text-uppercase tracking-wider">Available Packages</span>
            <h2 class="display-6 text-dark fw-bold mt-2">{{ $content->rooms_title ?: 'Explore Our Rooms' }}</h2>
            <p class="text-muted">{{ $content->rooms_subtitle ?: 'Choose a room option that fits your budget and lifestyle preferences.' }}</p>
        </div>
        <div class="row g-4 justify-content-center text-start">
            @php
                $mockImages = [
                  'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=600&q=80',
                  'https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=600&q=80',
                  'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=600&q=80'
                ];
            @endphp
            @foreach($pricingPlans as $index => $plan)
                @php
                    $planImage = !empty($plan->image_url) ? $plan->image_url : $mockImages[$index % count($mockImages)];
                @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="premium-card rounded-4 overflow-hidden h-100 d-flex flex-column p-0 border border-light">
                        <img src="{{ $planImage }}" class="img-fluid" alt="{{ $plan->name }}" style="height: 220px; object-fit: cover; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                        <div class="p-4 flex-grow-1 d-flex flex-column">
                            <h4 class="text-dark fw-bold mb-2 fs-5">{{ $plan->name }}</h4>
                            <p class="text-muted small flex-grow-1">{{ $plan->desc }}</p>
                            <ul class="list-unstyled my-4">
                                @foreach($plan->features as $f)
                                    <li class="small text-muted mb-2 d-flex align-items-center gap-2">
                                        <i data-lucide="check-circle" class="text-primary" style="width: 16px; height: 16px;"></i> {{ $f }}
                                    </li>
                                @endforeach
                            </ul>
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light">
                                <span class="text-primary fw-bold fs-5">{{ $plan->price }}</span>
                                <a href="/role-selection" class="btn btn-primary btn-sm rounded-pill px-4">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Locations Section -->
<section id="locations" class="section-padding bg-premium border-top border-bottom">
    <div class="container text-center">
        <div class="mb-5 max-w-2xl mx-auto">
            <span class="text-primary fw-bold text-uppercase tracking-wider">Our Presence</span>
            <h2 class="display-6 text-dark fw-bold mt-2">{{ $content->locations_title ?: 'Prime Locations' }}</h2>
            <p class="text-muted">{{ $content->locations_subtitle ?: 'Our properties are strategically placed in prime residential and tech hubs.' }}</p>
        </div>
        <div class="row g-4 text-start">
            @foreach($locations as $loc)
                <div class="col-md-6">
                    <div class="premium-card p-4 d-flex flex-column gap-3 h-100">
                        <div class="d-flex align-items-start gap-3">
                            <div class="avatar-box bg-primary-soft text-primary flex-shrink-0" style="width: 50px; height: 50px;"><i data-lucide="map-pin"></i></div>
                            <div>
                                <h5 class="text-dark fw-bold mb-2 fs-6">{{ $loc->city }}</h5>
                                <p class="text-muted small mb-0">{{ $loc->area }}</p>
                            </div>
                        </div>
                        @if(!empty($loc->map_url))
                            <div class="mt-2 overflow-hidden rounded shadow-sm border w-100" style="height: 220px;">
                                <iframe 
                                    src="{{ getEmbedMapUrl($loc->map_url) }}" 
                                    width="100%" 
                                    height="100%" 
                                    style="border:0;" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section id="testimonials" class="section-padding bg-white-dynamic">
    <div class="container text-center">
        <div class="mb-5 max-w-2xl mx-auto">
            <span class="text-primary fw-bold text-uppercase tracking-wider">Testimonials</span>
            <h2 class="display-6 text-dark fw-bold mt-2">What Our Tenants Say</h2>
            <p class="text-muted">Read honest feedback from students and professionals residing in our PGs.</p>
        </div>
        <div class="row g-4 text-start">
            @foreach($testimonials as $test)
                <div class="col-md-6">
                    <div class="premium-card p-4 h-100 d-flex flex-column justify-content-between">
                        <p class="text-muted italic small mb-4">"{{ $test->review }}"</p>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-box" style="width: 45px; height: 45px; background-color: rgba(37,99,235,0.1);">
                                <i data-lucide="user" class="text-primary"></i>
                            </div>
                            <div>
                                <h6 class="text-dark fw-bold mb-0 small">{{ $test->name }}</h6>
                                <small class="text-muted text-xs">{{ $test->role }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Pricing Plans Section -->
<section id="pricing" class="section-padding bg-premium border-top border-bottom">
    <div class="container text-center">
        <div class="mb-5 max-w-2xl mx-auto">
            <span class="text-primary fw-bold text-uppercase tracking-wider">Pricing</span>
            <h2 class="display-6 text-dark fw-bold mt-2">{{ $content->pricing_title ?: 'Simple, Transparent Pricing' }}</h2>
            <p class="text-muted">{{ $content->pricing_subtitle ?: 'No hidden charges. Security deposit refundable as per terms.' }}</p>
        </div>
        <div class="row g-4 justify-content-center text-start">
            @foreach($pricingPlans as $plan)
                <div class="col-lg-4 col-md-6">
                    <div class="premium-card text-center p-4 d-flex flex-column h-100">
                        <h5 class="text-muted fw-bold mb-3 fs-6">{{ $plan->name }}</h5>
                        <h2 class="text-dark fw-bold mb-1 display-7">{{ $plan->price }}</h2>
                        <small class="text-muted d-block mb-4">All inclusive monthly rent</small>
                        <hr class="my-3">
                        <ul class="list-unstyled text-start flex-grow-1 my-3">
                            @foreach($plan->features as $f)
                                <li class="small text-muted mb-2 d-flex align-items-center gap-2">
                                    <i data-lucide="check" class="text-success" style="width: 18px; height: 18px;"></i> {{ $f }}
                                </li>
                            @endforeach
                        </ul>
                        <a href="/role-selection" class="btn btn-outline-primary rounded-pill px-4 mt-4 w-100">Select Package</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="section-padding bg-white-dynamic">
    <div class="container text-start">
        <div class="row g-5">
            <div class="col-lg-5">
                <span class="text-primary fw-bold text-uppercase tracking-wider">Get In Touch</span>
                <h2 class="display-6 text-dark fw-bold mt-2 mb-4">{{ $content->contact_title ?: 'Contact Information' }}</h2>
                <p class="text-muted mb-5">{{ $content->contact_subtitle ?: 'Have queries regarding room availability, booking policies, or special discounts? Shoot us a message or call directly.' }}</p>
                
                <div class="d-flex align-items-center gap-4 mb-4">
                    <div class="avatar-box bg-primary-soft text-primary" style="width: 50px; height: 50px; border: 1px solid var(--border-color);"><i data-lucide="phone"></i></div>
                    <div>
                        <h6 class="text-dark mb-1 fw-bold">Call Us</h6>
                        <span class="text-muted">{{ $content->contact_phone }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-4 mb-4">
                    <div class="avatar-box bg-primary-soft text-primary" style="width: 50px; height: 50px; border: 1px solid var(--border-color);"><i data-lucide="mail"></i></div>
                    <div>
                        <h6 class="text-dark mb-1 fw-bold">Email Address</h6>
                        <span class="text-muted">{{ $content->contact_email }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <div class="avatar-box bg-primary-soft text-primary" style="width: 50px; height: 50px; border: 1px solid var(--border-color);"><i data-lucide="map-pin"></i></div>
                    <div>
                        <h6 class="text-dark mb-1 fw-bold">Visit Us</h6>
                        <span class="text-muted">{{ $content->contact_address }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="premium-card p-4 p-md-5 h-100">
                    <h4 class="text-dark mb-4 fw-bold">Send a Message</h4>
                    <form action="/inquiry" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Your Name</label>
                                <input type="text" name="visitor_name" class="form-control" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="visitor_email" class="form-control" placeholder="john@example.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Subject</label>
                                <input type="text" name="visitor_subject" class="form-control" placeholder="Inquiry about Single Room" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message</label>
                                <textarea name="visitor_message" class="form-control" rows="4" placeholder="Hi, I wanted to ask..." required></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 rounded-pill py-3">Submit Inquiry</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="py-5 bg-dark text-center text-white" style="background-color: var(--sidebar-bg) !important;">
    <div class="container">
        <p class="mb-0 text-muted">© 2026 PG Management System. All rights reserved.</p>
        <small class="text-muted opacity-50">Designed with a luxury Airbnb & Notion inspired layout.</small>
    </div>
</footer>

<!-- Floating Creator Badge Trigger -->
<div class="creator-fab-trigger" data-bs-toggle="modal" data-bs-target="#creatorProfileModal" style="position: fixed; bottom: 30px; right: 30px; z-index: 1040; cursor: pointer; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);">
    <div class="d-flex align-items-center gap-2 p-2 rounded-pill bg-dark bg-opacity-75 border border-light border-opacity-20 backdrop-blur shadow-lg creator-glow-ring">
        <img src="/creator.jpg" class="rounded-circle border border-2 border-primary" style="width: 40px; height: 40px; object-fit: cover;">
        <div class="pe-3 d-none d-md-block text-start">
            <small class="text-xs text-muted d-block" style="font-size: 9px; letter-spacing: 0.1em; text-transform: uppercase;">Creator</small>
            <span class="text-white fw-bold" style="font-size: 11px; white-space: nowrap;">VANKA NAGA RAKESH</span>
        </div>
    </div>
</div>

<!-- CREATOR PROFILE MODAL -->
<div class="modal fade" id="creatorProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content creator-modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
            <div class="modal-body text-center p-5 position-relative text-white">
                <div class="creator-bg-glow-1"></div>
                <div class="creator-bg-glow-2"></div>
                
                <button type="button" class="btn-close btn-close-white position-absolute" data-bs-dismiss="modal" aria-label="Close" style="top: 20px; right: 20px; z-index: 10;"></button>
                
                <div class="position-relative z-index-2">
                    <!-- Profile Picture -->
                    <div class="mb-4">
                        <div class="position-relative d-inline-block">
                            <div class="creator-avatar-pulse" style="width: 145px; height: 145px;"></div>
                            <img src="/creator.jpg" class="rounded-circle border border-3 border-primary shadow-lg position-relative z-index-2" style="width:120px; height:120px; object-fit:cover;">
                        </div>
                    </div>
                    
                    <!-- Name & Role -->
                    <h4 class="fw-bold mb-1 text-white">VANKA NAGA RAKESH</h4>
                    <span class="badge bg-primary px-3 py-1 rounded-pill mb-3" style="font-size: 11px;">Founder & Developer</span>
                    
                    <p class="text-muted text-xs leading-relaxed mb-4 px-2">
                        A passionate CSE student from Madanapalle Institute of Technology & Science (MITS). Specialized in creating premium, modern web applications, scalable backend APIs, and intuitive user experiences.
                    </p>
                    
                    <!-- Contact Details -->
                    <div class="d-flex flex-column align-items-center gap-2 mb-4 text-xs text-muted">
                        <div class="d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary" style="width:14px; height:14px;">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <span>rakesh28200511@gmail.com</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary" style="width:14px; height:14px;">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"></path>
                            </svg>
                            <span>B.Tech in CSE (MITS)</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary" style="width:14px; height:14px;">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span>Madanapalle, India</span>
                        </div>
                    </div>
                    
                    <!-- Social links -->
                    <div class="pt-4 border-top border-secondary border-opacity-25 w-100">
                        <div class="d-flex gap-3 justify-content-center">
                            <a href="https://www.linkedin.com/in/vanka-naga-rakesh-5b2894323" target="_blank" class="social-icon-btn linkedin-brand" title="LinkedIn Profile">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                            <a href="https://www.instagram.com/its_rocky_rakesh_/" target="_blank" class="social-icon-btn instagram-brand" title="Instagram Profile">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;">
                                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                                </svg>
                            </a>
                            <a href="mailto:rakesh28200511@gmail.com" class="social-icon-btn email-brand" title="Send Email">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
