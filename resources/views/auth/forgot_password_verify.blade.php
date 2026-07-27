@extends('layouts.app')

@section('title', 'Verify OTP | ' . ($landingContent->pg_title ?? 'PG Management System'))

@section('content')
<div class="container-fluid px-0">
    <div class="row g-0 min-vh-100 split-layout">
        
        <!-- Left half: Premium interior space overlay image -->
        <div class="col-lg-6 d-none d-lg-block split-left-bg">
            <div class="split-left-overlay">
                <div class="max-w-2xl text-start">
                    <span class="badge bg-primary-soft text-white px-3 py-2 rounded-pill mb-3" style="background-color: rgba(255,255,255,0.15);">SaaS Account Security</span>
                    <h1 class="display-4 fw-bold text-white mb-4">Reset Your Secure Password</h1>
                    <p class="lead text-light-muted">Recover your account settings, check assigned accommodations, payment invoice histories, and notices boards.</p>
                </div>
            </div>
        </div>

        <!-- Right half: Clean Notion/Airbnb style card -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 split-right-bg">
            <div class="w-100" style="max-width: 440px;">
                <!-- Brand logo -->
                <div class="text-center text-lg-start mb-5">
                    <a href="/role-selection" class="d-inline-flex align-items-center text-decoration-none mb-4">
                        @if(!empty($landingContent->logo_image))
                            <img src="{{ $landingContent->logo_image }}" alt="Logo" class="me-2" style="height: 36px; max-width: 120px; object-fit: contain;">
                        @else
                            <div class="avatar-box me-2" style="background: var(--primary-gradient);">
                                <i data-lucide="hotel" class="text-white" style="width: 18px; height: 18px;"></i>
                            </div>
                        @endif
                        <span class="fw-bold text-dark fs-5" style="letter-spacing: 0.05em;">{{ $landingContent->logo_text ?: 'PG SYSTEM' }}</span>
                    </a>
                    
                    <span class="badge px-3 py-2 rounded-pill mb-2 d-inline-flex align-items-center gap-2 
                        @if($role === 'admin') bg-primary-soft text-primary 
                        @elseif($role === 'student') bg-success-soft text-success 
                        @else bg-warning-soft text-warning @endif">
                        <i data-lucide="
                            @if($role === 'admin') user-cog 
                            @elseif($role === 'student') graduation-cap 
                            @else broom @endif" style="width:14px; height:14px;"></i> 
                        <span class="text-capitalize">{{ $role }} Portal</span>
                    </span>
                    
                    <h2 class="text-dark fw-bold display-7">Verify Verification Code</h2>
                    <p class="text-muted small">We sent a verification code to <b>{{ $email }}</b>. Enter the code and set your new password below.</p>
                </div>

                <!-- Form Forgot Password Verify -->
                <form action="/forgot-password-verify" method="POST">
                    @csrf

                    <!-- OTP Code input -->
                    <div class="mb-3">
                        <label class="form-label">6-Digit Verification Code</label>
                        <input type="text" name="code" class="form-control text-center fs-4 fw-bold letter-spacing-md" placeholder="123456" maxlength="6" required autofocus>
                    </div>

                    <!-- New Password input -->
                    <div class="mb-4">
                        <label class="form-label">Choose New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" minlength="6" required>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill">Reset Password & Sign In <i data-lucide="key-round" style="width: 18px; height: 18px;"></i></button>
                </form>

                <div class="text-center mt-5">
                    <a href="/login/{{ $role }}" class="text-muted text-decoration-none small d-inline-flex align-items-center gap-1">
                        <i data-lucide="chevron-left" style="width:16px; height:16px;"></i> Return to Sign In
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
