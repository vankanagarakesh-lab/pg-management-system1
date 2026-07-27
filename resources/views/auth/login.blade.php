@extends('layouts.app')

@section('title', 'Sign In | ' . ($landingContent?->pg_title ?? 'PG Management System'))

@section('content')
<div class="container-fluid px-0">
    <div class="row g-0 min-vh-100 split-layout">
        
        <!-- Left half: Premium interior space overlay image -->
        <div class="col-lg-6 d-none d-lg-block split-left-bg">
            <div class="split-left-overlay">
                <div class="max-w-2xl text-start">
                    <span class="badge bg-primary-soft text-white px-3 py-2 rounded-pill mb-3" style="background-color: rgba(255,255,255,0.15);">SaaS Accommodation Portal</span>
                    <h1 class="display-4 fw-bold text-white mb-4">A Cozy, Premium Safe Space For You</h1>
                    <p class="lead text-light-muted">Find luxury sharing and single occupancies equipped with high speed fiber internet, professional housekeeping, and healthy home-like organic meals.</p>
                </div>
            </div>
        </div>

        <!-- Right half: Clean Notion/Airbnb style card -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 split-right-bg">
            <div class="w-100" style="max-width: 440px;">
                <!-- Brand logo -->
                <div class="text-center text-lg-start mb-5">
                    <a href="/role-selection" class="d-inline-flex align-items-center text-decoration-none mb-4">
                        @if(!empty($landingContent?->logo_image))
                            <img src="{{ $landingContent->logo_image }}" alt="Logo" class="me-2" style="height: 36px; max-width: 120px; object-fit: contain;">
                        @else
                            <div class="avatar-box me-2" style="background: var(--primary-gradient);">
                                <i data-lucide="hotel" class="text-white" style="width: 18px; height: 18px;"></i>
                            </div>
                        @endif
                        <span class="fw-bold text-dark fs-5" style="letter-spacing: 0.05em;">{{ $landingContent?->logo_text ?: 'PG SYSTEM' }}</span>
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
                    
                    <h2 class="text-dark fw-bold display-7">Welcome Back</h2>
                    <p class="text-muted small">Please sign in to your dashboard panel</p>
                </div>

                <!-- Form Login -->
                <form action="/login" method="POST">
                    @csrf
                    <input type="hidden" name="role" value="{{ $role }}">

                    <!-- Email input -->
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="name@domain.com" required autofocus>
                        @if($role === 'admin')
                            <div class="form-text text-muted small"><i data-lucide="help-circle" class="d-inline me-1" style="width:12px; height:12px;"></i> Sign in with your registered admin email.</div>
                        @elseif($role === 'staff')
                            <div class="form-text text-muted small"><i data-lucide="help-circle" class="d-inline me-1" style="width:12px; height:12px;"></i> Sign in with your registered staff email.</div>
                        @endif
                    </div>

                    <!-- Password input -->
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <!-- Options -->
                    <div class="d-flex align-items-center justify-content-between mb-4 small">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label text-muted" for="rememberMe">Remember me</label>
                        </div>
                        <a href="/forgot-password/{{ $role }}" class="text-primary text-decoration-none fw-bold">Forgot password?</a>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill">Sign In <i data-lucide="log-in" style="width: 18px; height: 18px;"></i></button>
                </form>

                <!-- Redirect options for student -->
                @if($role === 'student')
                    <div class="text-center mt-4">
                        <p class="text-muted small mb-0">Don't have an approved account? <a href="/register" class="text-primary text-decoration-none fw-bold">Create Account</a></p>
                    </div>
                @endif

                <div class="text-center mt-5">
                    <a href="/role-selection" class="text-muted text-decoration-none small d-inline-flex align-items-center gap-1">
                        <i data-lucide="chevron-left" style="width:16px; height:16px;"></i> Switch Role Selection
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
