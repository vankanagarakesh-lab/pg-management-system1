@extends('layouts.app')

@section('title', ($landingContent->pg_title ?? 'PG Management System') . ' | Select Role')

@section('content')
<div class="bg-auth-gradient min-vh-100 d-flex align-items-center py-5">
    <div class="container">
        <!-- Brand Title Header -->
        <div class="text-center mb-5">
            <a href="/" class="d-inline-flex align-items-center text-decoration-none mb-4">
                @if(!empty($landingContent->logo_image))
                    <img src="{{ $landingContent->logo_image }}" alt="Logo" class="me-2" style="height: 36px; max-width: 120px; object-fit: contain;">
                @else
                    <div class="avatar-box me-2" style="background: var(--primary-gradient);">
                        <i data-lucide="hotel" class="text-white" style="width: 20px; height: 20px;"></i>
                    </div>
                @endif
                <h3 class="brand-text text-white mb-0" style="letter-spacing: 0.05em;">{{ $landingContent->logo_text ?: 'PG MANAGEMENT SYSTEM' }}</h3>
            </a>
            <h2 class="text-white fw-bold display-6">Select Your Portal</h2>
            <p class="text-muted">Choose your user role to access the corresponding panel dashboard</p>
        </div>

        <!-- 3 Cards -->
        <div class="row g-4 justify-content-center max-w-4xl mx-auto">
            <!-- Admin -->
            <div class="col-md-4">
                <div class="premium-card text-center h-100 d-flex flex-column justify-content-between p-4 pointer" onclick="location.href='/login/admin'">
                    <div>
                        <div class="avatar-box mx-auto mb-4" style="width: 60px; height: 60px; background-color: rgba(37, 99, 235, 0.1);">
                            <i data-lucide="user-cog" class="text-primary" style="width: 28px; height: 28px;"></i>
                        </div>
                        <h4 class="text-dark mb-2 fw-bold fs-5">Admin / PG Owner</h4>
                        <p class="text-muted small">Manage rooms, student approval queues, rent configurations, and cms settings.</p>
                    </div>
                    <button class="btn btn-outline-primary rounded-pill w-100 mt-4">Access Admin Portal</button>
                </div>
            </div>
            
            <!-- Student -->
            <div class="col-md-4">
                <div class="premium-card text-center h-100 d-flex flex-column justify-content-between p-4 pointer" onclick="location.href='/login/student'">
                    <div>
                        <div class="avatar-box mx-auto mb-4" style="width: 60px; height: 60px; background-color: rgba(22, 163, 74, 0.1);">
                            <i data-lucide="graduation-cap" class="text-success" style="width: 28px; height: 28px;"></i>
                        </div>
                        <h4 class="text-dark mb-2 fw-bold fs-5">Student / Tenant</h4>
                        <p class="text-muted small">Reconcile outstanding monthly rents, mark meals exclusions, and register complaints.</p>
                    </div>
                    <button class="btn btn-outline-primary rounded-pill w-100 mt-4" style="border-color:#16A34A; color:#16A34A; --hover-glow: rgba(22, 163, 74, 0.05);">Access Tenant Portal</button>
                </div>
            </div>

            <!-- Staff -->
            <div class="col-md-4">
                <div class="premium-card text-center h-100 d-flex flex-column justify-content-between p-4 pointer" onclick="location.href='/login/staff'">
                    <div>
                        <div class="avatar-box mx-auto mb-4" style="width: 60px; height: 60px; background-color: rgba(245, 158, 11, 0.1);">
                            <i data-lucide="broom" class="text-warning" style="width: 28px; height: 28px;"></i>
                        </div>
                        <h4 class="text-dark mb-2 fw-bold fs-5">Staff / Housekeeping</h4>
                        <p class="text-muted small">Update rooms cleaning logs, resolve assigned tickets, and review cooking counts.</p>
                    </div>
                    <button class="btn btn-outline-primary rounded-pill w-100 mt-4" style="border-color:#F59E0B; color:#F59E0B;">Access Staff Portal</button>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="/" class="text-muted text-decoration-none d-inline-flex align-items-center gap-2">
                <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Back to Landing Page
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
