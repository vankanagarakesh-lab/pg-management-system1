@extends('layouts.app')

@section('content')
<div class="dashboard-wrapper">
    <!-- Backdrop overlay for mobile drawer -->
    <div class="sidebar-backdrop d-lg-none" id="sidebarBackdrop" onclick="toggleSidebarMenu()"></div>

    <!-- Collapsible Sidebar -->
    <aside class="dashboard-sidebar p-4" id="sidebarMenu">
        <!-- Sidebar Header -->
        <div class="d-flex align-items-center justify-content-between mb-5">
            @php
                $landingContent = $landingContent ?? \App\Models\LandingContent::first();
            @endphp
            <a href="/" class="d-flex align-items-center text-decoration-none">
                @if(!empty($landingContent->logo_image))
                    <img src="{{ $landingContent->logo_image }}" alt="Logo" class="me-2" style="height: 36px; max-width: 120px; object-fit: contain;">
                @else
                    <div class="avatar-box me-2" style="background: var(--primary-gradient);">
                        <i data-lucide="hotel" class="text-white" style="width: 20px; height: 20px;"></i>
                    </div>
                @endif
                <span class="fw-bold text-white fs-5 brand-text" style="letter-spacing: 0.05em;">{{ $landingContent->logo_text ?: 'PG SYSTEM' }}</span>
            </a>
            <button class="btn btn-link text-muted p-0 d-lg-none" onclick="toggleSidebarMenu()">
                <i data-lucide="x" style="width: 24px; height: 24px;"></i>
            </button>
        </div>

        <!-- Sidebar Navigation List -->
        <nav class="nav flex-column gap-2 flex-grow-1">
            @if($userRole === 'admin')
                <!-- ADMIN LINKS -->
                <a class="sidebar-link {{ $activeTab === 'overview' ? 'active' : '' }}" href="/admin?tab=overview">
                    <i data-lucide="layout-dashboard"></i> Overview
                </a>
                <a class="sidebar-link {{ $activeTab === 'pg-mgr' ? 'active' : '' }}" href="/admin?tab=pg-mgr">
                    <i data-lucide="building-2"></i> PG Buildings
                </a>
                <a class="sidebar-link {{ $activeTab === 'room-mgr' ? 'active' : '' }}" href="/admin?tab=room-mgr">
                    <i data-lucide="door-open"></i> Room Inventory
                </a>
                <a class="sidebar-link {{ $activeTab === 'student-mgr' ? 'active' : '' }}" href="/admin?tab=student-mgr">
                    <i data-lucide="users"></i> Student Approvals
                </a>
                <a class="sidebar-link {{ $activeTab === 'rent-mgr' ? 'active' : '' }}" href="/admin?tab=rent-mgr">
                    <i data-lucide="indian-rupee"></i> Rent & Payments
                </a>
                <a class="sidebar-link {{ $activeTab === 'complaints-mgr' ? 'active' : '' }}" href="/admin?tab=complaints-mgr">
                    <i data-lucide="alert-triangle"></i> Complaints Desk
                </a>
                <a class="sidebar-link {{ $activeTab === 'staff-mgr' ? 'active' : '' }}" href="/admin?tab=staff-mgr">
                    <i data-lucide="contact-2"></i> Staff Directory
                </a>
                <a class="sidebar-link {{ $activeTab === 'notices-mgr' ? 'active' : '' }}" href="/admin?tab=notices-mgr">
                    <i data-lucide="megaphone"></i> Announcements
                </a>
                <a class="sidebar-link {{ $activeTab === 'landing-mgr' ? 'active' : '' }}" href="/admin?tab=landing-mgr">
                    <i data-lucide="layout-template"></i> Landing Page Editor
                </a>
                @php
                    $pendingInquiriesCount = \App\Models\Inquiry::where('status', 'pending')->count();
                @endphp
                <a class="sidebar-link {{ $activeTab === 'inquiries' ? 'active' : '' }}" href="/admin?tab=inquiries">
                    <i data-lucide="message-square"></i> Visitor Inquiries
                    @if($pendingInquiriesCount > 0)
                        <span class="badge bg-danger ms-auto rounded-pill" style="font-size: 0.75rem;">{{ $pendingInquiriesCount }}</span>
                    @endif
                </a>
                <a class="sidebar-link {{ $activeTab === 'reports-mgr' ? 'active' : '' }}" href="/admin?tab=reports-mgr">
                    <i data-lucide="bar-chart-3"></i> Reports & Analytics
                </a>
            @elseif($userRole === 'student')
                <!-- STUDENT LINKS -->
                <a class="sidebar-link {{ $activeTab === 'overview' ? 'active' : '' }}" href="/student?tab=overview">
                    <i data-lucide="home"></i> My Room
                </a>
                <a class="sidebar-link {{ $activeTab === 'rent' ? 'active' : '' }}" href="/student?tab=rent">
                    <i data-lucide="credit-card"></i> Pay Rent & Receipts
                </a>
                <a class="sidebar-link {{ $activeTab === 'complaints' ? 'active' : '' }}" href="/student?tab=complaints">
                    <i data-lucide="message-square-dashed"></i> Raise Complaint
                </a>
                <a class="sidebar-link {{ $activeTab === 'notices' ? 'active' : '' }}" href="/student?tab=notices">
                    <i data-lucide="bell-ring"></i> Notice Board
                </a>
            @elseif($userRole === 'staff')
                <!-- STAFF LINKS -->
                @if($staffRole === 'Housekeeping')
                    <a class="sidebar-link {{ $activeTab === 'tasks' ? 'active' : '' }}" href="/staff?tab=tasks">
                        <i data-lucide="list-todo"></i> Room Tasks
                    </a>
                    <a class="sidebar-link {{ $activeTab === 'notices' ? 'active' : '' }}" href="/staff?tab=notices">
                        <i data-lucide="bell"></i> Announcements
                    </a>
                @elseif($staffRole === 'Food Management')
                    <a class="sidebar-link {{ $activeTab === 'food' ? 'active' : '' }}" href="/staff?tab=food">
                        <i data-lucide="utensils"></i> Menu & Meals
                    </a>
                    <a class="sidebar-link {{ $activeTab === 'complaints' ? 'active' : '' }}" href="/staff?tab=complaints">
                        <i data-lucide="message-square-warning"></i> Meal Feedback
                    </a>
                    <a class="sidebar-link {{ $activeTab === 'notices' ? 'active' : '' }}" href="/staff?tab=notices">
                        <i data-lucide="bell"></i> Announcements
                    </a>
                @elseif($staffRole === 'Maintenance')
                    <a class="sidebar-link {{ $activeTab === 'maintenance' ? 'active' : '' }}" href="/staff?tab=maintenance">
                        <i data-lucide="alert-triangle"></i> Maintenance Board
                    </a>
                    <a class="sidebar-link {{ $activeTab === 'complaints' ? 'active' : '' }}" href="/staff?tab=complaints">
                        <i data-lucide="helping-hand"></i> Assigned Tickets
                    </a>
                    <a class="sidebar-link {{ $activeTab === 'notices' ? 'active' : '' }}" href="/staff?tab=notices">
                        <i data-lucide="bell"></i> Announcements
                    </a>
                @endif
            @endif
        </nav>

        <!-- Sidebar Footer -->
        <div class="border-top border-secondary border-opacity-25 pt-4 mt-auto">
            <a href="/logout" class="btn btn-danger w-100 rounded-pill"><i data-lucide="log-out"></i> Log Out</a>
        </div>
    </aside>

    <!-- Main Content Panel -->
    <div class="dashboard-content">
        <!-- Topbar -->
        <header class="dashboard-topbar px-4 py-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-secondary p-2" onclick="toggleSidebarMenu()">
                    <i data-lucide="menu" style="width: 20px; height: 20px;"></i>
                </button>
                <h5 class="fw-bold mb-0 text-dark d-none d-sm-block">@yield('page_title', 'Dashboard')</h5>
            </div>

            <!-- Profile context -->
            <div class="d-flex align-items-center gap-3">
                <!-- Theme Toggle Button -->
                <button class="btn btn-secondary p-2 rounded-circle" type="button" id="themeToggleBtn" onclick="toggleTheme()" style="width: 40px; height: 40px;">
                    <i data-lucide="moon" id="themeToggleIcon" style="width: 20px; height: 20px;"></i>
                </button>

                <!-- Notifications Bell -->
                @if(isset($notifications))
                <div class="dropdown">
                    <button class="btn btn-secondary p-2 position-relative rounded-circle" type="button" data-bs-toggle="dropdown" style="width: 40px; height: 40px;">
                        <i data-lucide="bell" style="width: 20px; height: 20px;"></i>
                        @php
                            $unreadNotifs = $notifications->where('read', 0)->count();
                        @endphp
                        @if($unreadNotifs > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-2 border-white">
                                {{ $unreadNotifs }}
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2 border shadow-lg" style="width: 320px; max-height: 400px; overflow-y: auto; border-radius: 16px;">
                        <li><h6 class="dropdown-header text-dark fw-bold mb-2">System Notifications</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        @forelse($notifications as $n)
                            <li class="p-2 border-bottom {{ $n->read ? 'opacity-50' : '' }} small">
                                <span class="d-block text-dark fw-bold">{{ $n->text }}</span>
                                <div class="d-flex justify-content-between text-xs text-muted mt-1">
                                    <span>{{ $n->date }}</span>
                                    @if(!$n->read)
                                        <form action="/{{ $userRole }}/mark-read/{{ $n->id }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-link p-0 text-primary text-decoration-none fw-bold text-xs" style="border:none; background:none;">Mark Read</button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="p-3 text-center text-muted small">No notifications logged.</li>
                        @endforelse
                    </ul>
                </div>
                @endif

                <!-- User profile Details -->
                <button type="button" class="btn p-0 border-0 bg-transparent text-start d-flex align-items-center gap-2 border-start border-light ps-3" data-bs-toggle="modal" data-bs-target="#globalProfileModal" style="box-shadow: none;">
                    <div class="avatar-box">
                        @if(!empty($loggedUser->profile_photo))
                            <img src="{{ $loggedUser->profile_photo }}" alt="Avatar">
                        @else
                            <i data-lucide="user" style="width: 18px; height: 18px;"></i>
                        @endif
                    </div>
                    <div class="text-start d-none d-md-block">
                        <h6 class="text-dark mb-0 fw-bold small">{{ $loggedUser->name }}</h6>
                        <span class="text-muted small text-xs text-uppercase">{{ $userRole }} Panel</span>
                    </div>
                </button>
            </div>
        </header>

        <!-- Panel Body -->
        <div class="p-4">
            @yield('dashboard_content')
        </div>
    </div>
</div>

<!-- GLOBAL PROFILE EDIT MODAL -->
<div class="modal fade" id="globalProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:24px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i data-lucide="user" class="text-primary me-2"></i>My Profile Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form action="/profile/update" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $loggedUser->name ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        @if($userRole === 'admin')
                            <input type="email" name="email" class="form-control" value="{{ $loggedUser->email ?? '' }}" required>
                            <small class="text-muted text-xs">Admin email address can be updated.</small>
                        @else
                            <input type="email" class="form-control" value="{{ $loggedUser->email ?? '' }}" readonly disabled>
                            <small class="text-muted text-xs">Email address cannot be changed.</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ $loggedUser->phone ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Update Profile Photo</label>
                        <input type="file" name="profile_photo" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password (optional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">Apply Profile Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleSidebarMenu() {
        const sidebar = document.getElementById('sidebarMenu');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (window.innerWidth < 992) {
            sidebar.classList.toggle('show');
            if (backdrop) {
                backdrop.classList.toggle('show', sidebar.classList.contains('show'));
            }
        } else {
            sidebar.classList.toggle('collapsed');
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar_collapsed', isCollapsed ? 'true' : 'false');
        }
    }

    function toggleTheme() {
        const html = document.documentElement;
        const btnIcon = document.getElementById('themeToggleIcon');
        if (html.classList.contains('dark-mode')) {
            html.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
            if (btnIcon) btnIcon.setAttribute('data-lucide', 'moon');
        } else {
            html.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
            if (btnIcon) btnIcon.setAttribute('data-lucide', 'sun');
        }
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        const sidebar = document.getElementById('sidebarMenu');
        if (window.innerWidth >= 992) {
            const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
            }
        }

        const theme = localStorage.getItem('theme') || 'light';
        const btnIcon = document.getElementById('themeToggleIcon');
        if (btnIcon) {
            btnIcon.setAttribute('data-lucide', theme === 'dark' ? 'sun' : 'moon');
        }
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>
@yield('dashboard_scripts')
@endsection
