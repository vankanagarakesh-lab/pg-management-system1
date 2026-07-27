@extends('layouts.app')

@section('title', 'Register Tenant | ' . ($landingContent->pg_title ?? 'PG Management System'))

@section('content')
<div class="bg-auth-gradient min-vh-100 d-flex align-items-center py-5">
    <div class="container">
        
        <div class="max-w-2xl mx-auto text-center mb-5">
            <a href="/" class="d-inline-flex align-items-center text-decoration-none mb-4">
                @if(!empty($landingContent->logo_image))
                    <img src="{{ $landingContent->logo_image }}" alt="Logo" class="me-2" style="height: 36px; max-width: 120px; object-fit: contain;">
                @else
                    <div class="avatar-box me-2" style="background: var(--primary-gradient);">
                        <i data-lucide="hotel" class="text-white" style="width: 18px; height: 18px;"></i>
                    </div>
                @endif
                <span class="fw-bold text-white fs-5 brand-text" style="letter-spacing: 0.05em;">{{ $landingContent->logo_text ?: 'PG SYSTEM' }}</span>
            </a>
            <h2 class="text-white fw-bold display-7">Register Tenant Account</h2>
            <p class="text-muted small">Submit your details to apply for PG room assignment approval</p>
        </div>

        <div class="premium-card max-w-2xl mx-auto p-4 p-md-5">
            <!-- Progress Steps Indicator -->
            <div class="progress-dots" id="progressIndicator">
                <div class="dot active" id="dot-1"></div>
                <div class="dot" id="dot-2"></div>
                <div class="dot" id="dot-3"></div>
                <div class="dot" id="dot-4"></div>
            </div>

            <!-- Form -->
            <form action="/register" method="POST" enctype="multipart/form-data" id="multiStepForm">
                @csrf
                
                <!-- STEP 1: Personal info -->
                <div class="step-section" id="step-1">
                    <h5 class="text-dark fw-bold mb-4 border-bottom pb-2">1. Personal Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="john@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" name="phone" id="phone" class="form-control" placeholder="10-digit number" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Min 6 characters" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Home Address *</label>
                            <textarea name="address" id="address" class="form-control" rows="2" placeholder="Full home residential address" required></textarea>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-primary px-5 rounded-pill" onclick="nextStep(2)">Continue <i data-lucide="arrow-right"></i></button>
                    </div>
                </div>

                <!-- STEP 2: College info -->
                <div class="step-section d-none" id="step-2">
                    <h5 class="text-dark fw-bold mb-4 border-bottom pb-2">2. College & Course Details</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">College / University Name *</label>
                            <input type="text" name="college" id="college" class="form-control" placeholder="University of Technology" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Course / Stream *</label>
                            <input type="text" name="course" id="course" class="form-control" placeholder="B.Tech Computer Science" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year of Study *</label>
                            <select name="year" id="year" class="form-select" required>
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary px-4 rounded-pill" onclick="prevStep(1)"><i data-lucide="arrow-left"></i> Back</button>
                        <button type="button" class="btn btn-primary px-5 rounded-pill" onclick="nextStep(3)">Continue <i data-lucide="arrow-right"></i></button>
                    </div>
                </div>

                <!-- STEP 3: Accommodation details -->
                <div class="step-section d-none" id="step-3">
                    <h5 class="text-dark fw-bold mb-4 border-bottom pb-2">3. Select PG Accommodation</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Select PG Building *</label>
                            <select name="pg_building_id" id="pg_building_id" class="form-select" onchange="filterRooms()" required>
                                <option value="" disabled selected>-- Select PG --</option>
                                @foreach($pgs as $pg)
                                    <option value="{{ $pg->id }}">{{ $pg->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Select Room Number *</label>
                            <select name="room_number" id="room_number" class="form-select" onchange="updateRoomType()" required>
                                <option value="" disabled selected>-- Select Room --</option>
                                <!-- Loaded dynamically via JS -->
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Room Specifications</label>
                            <input type="text" id="room_type_display" class="form-control bg-light text-muted" readonly placeholder="No room selected">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary px-4 rounded-pill" onclick="prevStep(2)"><i data-lucide="arrow-left"></i> Back</button>
                        <button type="button" class="btn btn-primary px-5 rounded-pill" onclick="nextStep(4)">Continue <i data-lucide="arrow-right"></i></button>
                    </div>
                </div>

                <!-- STEP 4: Uploads & Guardian details -->
                <div class="step-section d-none" id="step-4">
                    <h5 class="text-dark fw-bold mb-4 border-bottom pb-2">4. Guardian & Document Uploads</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Guardian Full Name *</label>
                            <input type="text" name="guardian_name" id="guardian_name" class="form-control" placeholder="Father or Mother name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Guardian Phone Number *</label>
                            <input type="tel" name="guardian_phone" id="guardian_phone" class="form-control" placeholder="10-digit number" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Profile Photo (Optional)</label>
                            <input type="file" name="profile_photo" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ID Proof (Aadhar/PAN - Optional)</label>
                            <input type="file" name="id_proof" class="form-control" accept="image/*,application/pdf">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary px-4 rounded-pill" onclick="prevStep(3)"><i data-lucide="arrow-left"></i> Back</button>
                        <button type="submit" class="btn btn-success px-5 rounded-pill">Submit Application <i data-lucide="check-circle"></i></button>
                    </div>
                </div>

            </form>
        </div>

        <div class="text-center mt-4">
            <a href="/login/student" class="text-muted text-decoration-none small">
                Already have an account? <b class="text-white fw-bold">Sign In here</b>
            </a>
        </div>
    </div>
</div>

<style>
    .bg-auth-gradient {
        background: radial-gradient(circle at 10% 20%, #0F172A 0%, #020617 90%);
    }
</style>
@endsection

@section('scripts')
<script>
    // JSON arrays of rooms passed from controller
    const allRooms = @json($rooms);

    function filterRooms() {
        const pgId = document.getElementById('pg_building_id').value;
        const select = document.getElementById('room_number');
        
        // Clear previous options
        select.innerHTML = '<option value="" disabled selected>-- Select Room --</option>';
        document.getElementById('room_type_display').value = '';

        // Filter and populate
        const filtered = allRooms.filter(r => r.pg_building_id == pgId);
        filtered.forEach(room => {
            const spaces = room.capacity - room.occupied;
            select.innerHTML += `<option value="${room.number}" data-type="${room.type}" data-rent="${room.rent}">${room.number} (${room.type} - ${spaces} left)</option>`;
        });
    }

    function updateRoomType() {
        const select = document.getElementById('room_number');
        const opt = select.options[select.selectedIndex];
        const type = opt.getAttribute('data-type');
        const rent = opt.getAttribute('data-rent');

        if(type && rent) {
            document.getElementById('room_type_display').value = `${type} - Rent: ₹${parseInt(rent).toLocaleString()}/month`;
        }
    }

    // Wizard Nav controls
    function nextStep(step) {
        // Simple client-side validate before moving next
        if (step === 2) {
            if (!document.getElementById('name').value || !document.getElementById('email').value || !document.getElementById('phone').value || !document.getElementById('password').value || !document.getElementById('address').value) {
                Swal.fire('Input Required', 'Please fill out all mandatory fields in this section.', 'warning');
                return;
            }
        }
        if (step === 3) {
            if (!document.getElementById('college').value || !document.getElementById('course').value) {
                Swal.fire('Input Required', 'Please fill out all college details.', 'warning');
                return;
            }
        }
        if (step === 4) {
            if (!document.getElementById('pg_building_id').value || !document.getElementById('room_number').value) {
                Swal.fire('Input Required', 'Please select a building and room number.', 'warning');
                return;
            }
        }

        // Hide all steps
        document.querySelectorAll('.step-section').forEach(sec => sec.classList.add('d-none'));
        // Show target step
        document.getElementById(`step-${step}`).classList.remove('d-none');
        
        // Update dots indicator
        document.querySelectorAll('.progress-dots .dot').forEach(d => d.classList.remove('active'));
        document.getElementById(`dot-${step}`).classList.add('active');
    }

    function prevStep(step) {
        document.querySelectorAll('.step-section').forEach(sec => sec.classList.add('d-none'));
        document.getElementById(`step-${step}`).classList.remove('d-none');

        document.querySelectorAll('.progress-dots .dot').forEach(d => d.classList.remove('active'));
        document.getElementById(`dot-${step}`).classList.add('active');
    }
</script>
@endsection
