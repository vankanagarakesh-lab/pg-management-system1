@extends('layouts.dashboard')

@section('title', $staff->staff_role . ' Dashboard | PG Management System')
@section('page_title')
    <span class="text-uppercase text-warning small fw-bold">{{ $staff->staff_role }} Portal</span> &mdash;
    @if($tab === 'tasks') Room Housekeeping
    @elseif($tab === 'common-areas') Common Areas
    @elseif($tab === 'food') Food Prep Roster
    @elseif($tab === 'maintenance') Maintenance Board
    @elseif($tab === 'complaints') Student Tickets Feed
    @elseif($tab === 'notices') Notices timelines
    @elseif($tab === 'reports') Daily Work Reports
    @elseif($tab === 'profile') My Profile Desk
    @endif
@endsection

@section('dashboard_content')

@php
    $assignedPgName = $assignedPg->name ?? 'All PG Buildings';
@endphp
<!-- Staff building header scope -->
<div class="p-3 bg-white border rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
        <i data-lucide="map-pin" class="text-warning"></i>
        <span class="fw-bold text-dark">Scope Location:</span>
        <span class="badge bg-primary-soft text-primary px-3 py-1 rounded-pill">{{ $assignedPgName }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="/staff?tab=reports" class="btn btn-xs btn-outline-primary py-1 px-3 rounded-pill text-xs">Work Report log</a>
        <a href="/staff?tab=profile" class="btn btn-xs btn-secondary py-1 px-3 rounded-pill text-xs"><i data-lucide="user" style="width:12px; height:12px;"></i> Profile</a>
    </div>
</div>

<!-- ========================================================================= -->
<!-- ======================== 1. HOUSEKEEPING LAYOUT ======================== -->
<!-- ========================================================================= -->
@if($staff->staff_role === 'Housekeeping')

    <!-- HOUSEKEEPING: ROOM CLEANING TASKS -->
    @if($tab === 'tasks')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="text-dark fw-bold mb-0">Rooms Daily Cleaning Checklist</h5>
            <form action="/staff/reset-cleaning" method="POST">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm rounded-pill"><i data-lucide="refresh-cw" style="width:14px; height:14px;"></i> Reset Checklist</button>
            </form>
        </div>

        <!-- Stats row -->
        <div class="row g-3 mb-4 text-start">
            <div class="col-md-4">
                <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="avatar-box bg-primary-soft text-primary"><i data-lucide="door-closed"></i></div>
                    <div>
                        <h4 class="text-dark mb-0 fw-bold">{{ $rooms->count() }}</h4>
                        <small class="text-muted text-xs">Total Assigned Rooms</small>
                    </div>
                </div>
            </div>
            @php
                $cleanedCount = $rooms->where('cleaning_status', 'Cleaned')->count();
                $dirtyCount = $rooms->count() - $cleanedCount;
            @endphp
            <div class="col-md-4">
                <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="avatar-box bg-success-soft text-success"><i data-lucide="check-circle-2"></i></div>
                    <div>
                        <h4 class="text-dark mb-0 fw-bold">{{ $cleanedCount }}</h4>
                        <small class="text-muted text-xs">Cleaned Rooms</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="avatar-box bg-danger-soft text-danger"><i data-lucide="alert-octagon"></i></div>
                    <div>
                        <h4 class="text-dark mb-0 fw-bold">{{ $dirtyCount }}</h4>
                        <small class="text-muted text-xs">Dirty / Pending Cleaning</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="premium-table-container text-start">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Room Number</th>
                            <th>Sharing Configuration</th>
                            <th>Occupants</th>
                            <th>Status</th>
                            <th>Action Check</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                            @php
                                $status = $room->cleaning_status ?: 'Dirty';
                                $isCleaned = $status === 'Cleaned';
                            @endphp
                            <tr>
                                <td class="fw-bold text-dark">#{{ $room->number }}</td>
                                <td>{{ $room->type }}</td>
                                <td>{{ $room->occupied }} / {{ $room->capacity }} beds</td>
                                <td>
                                    <span class="badge {{ $isCleaned ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                        {{ strtoupper($status) }}
                                    </span>
                                </td>
                                <td>
                                    <form action="/staff/toggle-cleaning/{{ $room->id }}/{{ $isCleaned ? 'Dirty' : 'Cleaned' }}" method="POST">
                                        @csrf
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" onchange="this.form.submit()" {{ $isCleaned ? 'checked' : '' }}>
                                            <label class="form-check-label text-muted small ms-2">{{ $isCleaned ? 'Mark Dirty' : 'Mark Cleaned' }}</label>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted p-4">No rooms inside your assigned PG scope.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Housekeeping: Report Maintenance issue -->
        <div class="premium-card text-start mt-5">
            <h5 class="text-dark fw-bold mb-3"><i data-lucide="alert-triangle" class="text-danger me-2"></i>Report Facility Maintenance Issue</h5>
            <p class="text-muted small">Notify the administrator regarding plumbing, electrical, or structural issues observed in rooms or common halls.</p>
            <form action="/staff/report-maintenance" method="POST" class="row g-3 mt-1">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Issue Area / Subject</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Ground floor geyser broken" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        <option value="Electrical">Electrical</option>
                        <option value="Plumbing">Plumbing</option>
                        <option value="Furniture">Furniture</option>
                        <option value="Wi-Fi">Wi-Fi</option>
                        <option value="Water">Water</option>
                        <option value="Other">Other Issues</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select" required>
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                        <option value="Emergency">Emergency</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Detailed Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Specify details, location, scope..." required></textarea>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Submit Alert Ticket</button>
                </div>
            </form>
        </div>
    @endif

    <!-- HOUSEKEEPING: COMMON AREAS TIMELINE -->
    @if($tab === 'common-areas')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="text-dark fw-bold mb-0">Common Area Cleaning Roster</h5>
        </div>
        <div class="premium-table-container text-start">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Common Area Hall</th>
                            <th>Status</th>
                            <th>Last Cleaned Date/Time</th>
                            <th>Action Check</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commonAreaTasks as $task)
                            @php $isCleaned = $task->status === 'Cleaned'; @endphp
                            <tr>
                                <td class="fw-bold text-dark"><i data-lucide="map" class="text-primary me-2 d-inline" style="width:16px; height:16px;"></i>{{ $task->area_name }}</td>
                                <td>
                                    <span class="badge {{ $isCleaned ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                        {{ strtoupper($task->status) }}
                                    </span>
                                </td>
                                <td class="text-xs text-muted">{{ $task->last_cleaned_at ?: 'Pending check today' }}</td>
                                <td>
                                    <form action="/staff/toggle-common-area/{{ $task->id }}/{{ $isCleaned ? 'Pending' : 'Cleaned' }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm py-1 px-3 rounded-pill {{ $isCleaned ? 'btn-outline-danger' : 'btn-success' }}">
                                            {{ $isCleaned ? 'Mark Pending' : 'Mark Cleaned' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted p-4">No common areas assigned. Please ask Admin to seed.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endif

<!-- ========================================================================= -->
<!-- ======================== 2. FOOD MANAGEMENT LAYOUT ======================= -->
<!-- ========================================================================= -->
@if($staff->staff_role === 'Food Management')

    <!-- FOOD: MEAL STATISTICS COUNTERS -->
    @if($tab === 'food')
        <!-- PG building selection selector -->
        <div class="p-3 bg-white border rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between text-start">
            <div class="d-flex align-items-center gap-2 w-100">
                <i data-lucide="building" class="text-success" style="width:20px; height:20px;"></i>
                <label class="fw-bold text-dark me-2 mb-0" style="font-size:14px;">Select Hostel PG Building:</label>
                <select id="foodPgSelect" class="form-select w-auto py-1 px-3" style="max-width: 250px; font-size: 13px; border-radius:12px;" onchange="filterFoodByPg(this.value)">
                    @foreach($pgs as $p)
                        <option value="{{ $p->id }}" {{ $activePgId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                    <option value="all" {{ $activePgId == 'all' ? 'selected' : '' }}>All PG Buildings</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-4 text-start">
            <!-- Total students in PG -->
            <div class="col-md-3">
                <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="avatar-box bg-primary-soft text-primary"><i data-lucide="users"></i></div>
                    <div>
                        <h4 class="text-dark mb-0 fw-bold">{{ $totalStudents }}</h4>
                        <small class="text-muted text-xs">Total PG Students</small>
                    </div>
                </div>
            </div>
            <!-- Breakfast meals needed -->
            @php
                $breakfastNeed = $totalStudents - $morningExclusionsCount;
                $lunchNeed = $totalStudents - $afternoonExclusionsCount;
                $dinnerNeed = $totalStudents - $eveningExclusionsCount;
            @endphp
            <div class="col-md-3">
                <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="avatar-box bg-warning-soft text-warning" style="background-color:rgba(245, 158, 11, 0.1);"><i data-lucide="coffee"></i></div>
                    <div>
                        <h4 class="text-dark mb-0 fw-bold">{{ $breakfastNeed }} Eating</h4>
                        <small class="text-muted text-xs d-block">{{ $morningExclusionsCount }} Not Eating (Breakfast)</small>
                    </div>
                </div>
            </div>
            <!-- Lunch meals needed -->
            <div class="col-md-3">
                <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="avatar-box bg-success-soft text-success" style="background-color:rgba(22, 163, 74, 0.1);"><i data-lucide="soup"></i></div>
                    <div>
                        <h4 class="text-dark mb-0 fw-bold">{{ $lunchNeed }} Eating</h4>
                        <small class="text-muted text-xs d-block">{{ $afternoonExclusionsCount }} Not Eating (Lunch)</small>
                    </div>
                </div>
            </div>
            <!-- Dinner meals needed -->
            <div class="col-md-3">
                <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="avatar-box bg-info-soft text-info" style="background-color:rgba(14, 165, 233, 0.1);"><i data-lucide="utensils"></i></div>
                    <div>
                        <h4 class="text-dark mb-0 fw-bold">{{ $dinnerNeed }} Eating</h4>
                        <small class="text-muted text-xs d-block">{{ $eveningExclusionsCount }} Not Eating (Dinner)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly menu panels -->
        <div class="premium-card text-start mb-4">
            <h5 class="text-dark fw-bold mb-4"><i data-lucide="calendar-range" class="text-success me-2"></i>Weekly Hostels Food Schedule</h5>
            <div class="row g-3">
                @foreach($foodMenu as $menu)
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3 border rounded-4 bg-light h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="text-primary fw-bold text-capitalize mb-0">{{ $menu->day }}</h6>
                                    <button class="btn btn-xs btn-outline-primary py-1 px-3 rounded-pill text-xs" onclick="editFoodMenuPrompt('{{ $menu->id }}', '{{ $menu->day }}', '{{ $menu->breakfast }}', '{{ $menu->lunch }}', '{{ $menu->dinner }}')">Modify</button>
                                </div>
                                <div class="small text-muted text-xs">
                                    <div class="mb-1"><b>Breakfast:</b> {{ $menu->breakfast ?: 'Not set' }}</div>
                                    <div class="mb-1"><b>Lunch:</b> {{ $menu->lunch ?: 'Not set' }}</div>
                                    <div><b>Dinner:</b> {{ $menu->dinner ?: 'Not set' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- No Food Exclusions list -->
        <div class="premium-table-container text-start">
            <div class="p-3 border-bottom"><h6 class="text-dark fw-bold mb-0">Daily Food Exclusions Logs ({{ date('d M Y') }})</h6></div>
            <div class="table-responsive">
                <table class="table text-center align-middle">
                    <thead>
                        <tr>
                            <th class="text-start">Tenant</th>
                            <th>Room No</th>
                            <th>Breakfast Excluded?</th>
                            <th>Lunch Excluded?</th>
                            <th>Dinner Excluded?</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($foodExclusions as $f)
                            <tr>
                                <td class="fw-bold text-dark text-start">{{ $f->name }}</td>
                                <td>#{{ $f->room }}</td>
                                <td>
                                    @if($f->morning) <span class="badge bg-danger-soft text-danger">No Food</span> @else <span class="text-muted text-xs">-</span> @endif
                                </td>
                                <td>
                                    @if($f->afternoon) <span class="badge bg-danger-soft text-danger">No Food</span> @else <span class="text-muted text-xs">-</span> @endif
                                </td>
                                <td>
                                    @if($f->evening) <span class="badge bg-danger-soft text-danger">No Food</span> @else <span class="text-muted text-xs">-</span> @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted p-4">All tenants checked in for standard meals today.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif



    <!-- FOOD: COMPLAINTS FEED -->
    @if($tab === 'complaints')
        <div class="premium-table-container text-start">
            <div class="p-3 border-bottom"><h6 class="text-dark fw-bold mb-0">Student Food Complaints & Messages Feed</h6></div>
            <div class="table-responsive">
                <table class="table align-middle text-center">
                    <thead>
                        <tr>
                            <th>Tenant Name</th>
                            <th>Room</th>
                            <th>Subject</th>
                            <th>Description</th>
                            <th>Date Raised</th>
                            <th>Status</th>
                            <th>Kitchen Reply</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($foodComplaints as $c)
                            <tr>
                                <td class="fw-bold text-dark">{{ $c->student_name }}</td>
                                <td>#{{ $c->room_number }}</td>
                                <td class="fw-bold">{{ $c->title }}</td>
                                <td class="small text-muted">{{ $c->description }}</td>
                                <td class="text-xs text-muted">{{ $c->created_date }}</td>
                                <td>
                                    <span class="badge {{ $c->status === 'Resolved' ? 'bg-success-soft text-success' : 'bg-warning-soft text-warning' }}">
                                        {{ $c->status }}
                                    </span>
                                </td>
                                <td class="small text-muted" style="max-width: 200px; white-space: normal;">{{ $c->reply ?: '-' }}</td>
                                <td>
                                    <button class="btn btn-outline-success btn-xs rounded-pill py-1 px-3 fs-xs text-xs" onclick="openFoodReplyModal('{{ $c->id }}', '{{ addslashes($c->reply) }}', '{{ $c->status }}')">
                                        Reply / Resolve
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted p-4">No kitchen messages or meal complaints logged.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FOOD REPLY MODAL -->
        <div class="modal fade" id="foodReplyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:24px;">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-dark">Reply to Student Message</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        <form id="foodReplyForm" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Message Status</label>
                                <select name="status" id="foodReplyStatus" class="form-select">
                                    <option value="Pending">Pending</option>
                                    <option value="Resolved">Resolved</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Reply Content</label>
                                <textarea name="reply" id="foodReplyText" class="form-control" rows="4" placeholder="Write your response..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100 py-2 rounded-pill text-white fw-bold">Submit Kitchen Reply</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endif

<!-- ========================================================================= -->
<!-- ======================== 3. MAINTENANCE LAYOUT ========================= -->
<!-- ========================================================================= -->
@if($staff->staff_role === 'Maintenance')

    <!-- MAINTENANCE: BOARD STATS -->
    @if($tab === 'maintenance')
        <!-- Statistics row -->
        <div class="row g-3 mb-4 text-start">
            <div class="col-md-3">
                <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="avatar-box bg-primary-soft text-primary"><i data-lucide="list-todo"></i></div>
                    <div>
                        <h4 class="text-dark mb-0 fw-bold">{{ $complaints->count() }}</h4>
                        <small class="text-muted text-xs">Total Assigned Tickets</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="avatar-box bg-danger-soft text-danger"><i data-lucide="alert-circle"></i></div>
                    <div>
                        <h4 class="text-dark mb-0 fw-bold">{{ $complaints->where('status', 'Pending')->count() }}</h4>
                        <small class="text-muted text-xs">Pending Repair</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="avatar-box bg-info-soft text-info" style="background-color:rgba(14,165,233,0.1);"><i data-lucide="loader"></i></div>
                    <div>
                        <h4 class="text-dark mb-0 fw-bold">{{ $complaints->where('status', 'In Progress')->count() }}</h4>
                        <small class="text-muted text-xs">In Progress Tasks</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                    <div class="avatar-box bg-success-soft text-success"><i data-lucide="check-square"></i></div>
                    <div>
                        <h4 class="text-dark mb-0 fw-bold">{{ $complaints->where('status', 'Resolved')->count() }}</h4>
                        <small class="text-muted text-xs">Completed & Closed</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency alerts warning if any -->
        @php $emergencyTickets = $complaints->where('status', 'Pending')->where('priority', 'Emergency'); @endphp
        @if($emergencyTickets->count() > 0)
            <div class="p-3 bg-danger-soft text-danger border border-danger border-opacity-25 rounded-4 mb-4 text-start d-flex align-items-center gap-3">
                <div class="avatar-box bg-danger text-white"><i data-lucide="shield-alert"></i></div>
                <div>
                    <h6 class="fw-bold mb-0">Emergency Tasks Action Needed</h6>
                    <small class="text-muted">You have <b>{{ $emergencyTickets->count() }} emergency ticket(s)</b> pending urgent resolution.</small>
                </div>
            </div>
        @endif

        <!-- Assigned Maintenance Tickets -->
        <div class="premium-table-container text-start">
            <div class="p-3 border-bottom"><h6 class="text-dark fw-bold mb-0">Assigned Student Repair Tickets</h6></div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Tenant / Room</th>
                            <th>Ticket Details</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Verification Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $c)
                            @php 
                                $isPending = $c->status === 'Pending';
                                $isProgress = $c->status === 'In Progress';
                                $isResolved = $c->status === 'Resolved';
                            @endphp
                            <tr>
                                <td class="fw-bold text-dark">{{ $c->student_name }}<br><small class="text-muted text-xs">Room #{{ $c->room_number }}</small></td>
                                <td>
                                    <span class="badge bg-light text-dark text-xs mb-1">{{ $c->category }}</span><br>
                                    <b>{{ $c->title }}</b><br>
                                    <small class="text-muted text-xs">{{ $c->description }}</small>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($c->priority === 'Emergency') bg-danger-soft text-danger
                                        @elseif($c->priority === 'High') bg-warning-soft text-warning
                                        @else bg-primary-soft text-primary @endif">
                                        {{ $c->priority }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($isResolved) bg-success-soft text-success
                                        @elseif($isProgress) bg-info-soft text-info
                                        @else bg-danger-soft text-danger @endif">
                                        {{ strtoupper($c->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($c->verification_status === 'Verified')
                                        <span class="badge bg-success-soft text-success">Verified</span>
                                    @elseif($c->verification_status === 'Unresolved')
                                        <span class="badge bg-danger-soft text-danger">Reopened / Unresolved</span>
                                    @else
                                        <span class="badge bg-warning-soft text-warning">Awaiting Verification</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-outline-primary btn-sm rounded-pill" onclick="openUpdateMaintenanceModal('{{ $c->id }}', '{{ $c->status }}', '{{ $c->priority }}', '{{ $c->materials_used }}', '{{ $c->repair_expense }}')">
                                        Update Ticket
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted p-4">No repair tickets assigned to your roster.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- MAINTENANCE COMPLAINT DETAIL MODAL -->
    <div class="modal fade" id="updateMaintenanceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:24px;">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark">Update Ticket Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <form id="updateMaintenanceForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Ticket Status</label>
                            <select name="status" id="modalTicketStatus" class="form-select">
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Resolved">Resolved</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Task Priority</label>
                            <select name="priority" id="modalTicketPriority" class="form-select">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Emergency">Emergency</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Materials Used</label>
                            <input type="text" name="materials_used" id="modalTicketMaterials" class="form-control" placeholder="e.g. 15W LED Bulb, Teflon tape">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Repair Expense (INR)</label>
                            <input type="number" name="repair_expense" id="modalTicketExpense" class="form-control" placeholder="e.g. 250">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill">Update Ticket Log</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endif

<!-- ========================================================================= -->
<!-- ===================== 4. ANNOUNCEMENTS TAB (COMMON) ===================== -->
<!-- ========================================================================= -->
@if($tab === 'notices')
    <div class="premium-card text-start">
        <h5 class="text-dark fw-bold mb-4">Notices Board Alerts</h5>
        <div class="timeline-notices">
            @forelse($notices as $n)
                @php $isUrgent = Str::contains(strtolower($n->title), ['urgent', 'important', 'due']); @endphp
                <div class="timeline-notice-item {{ $isUrgent ? 'notice-danger' : '' }}">
                    <h6 class="text-dark fw-bold mb-1">{{ $n->title }}</h6>
                    <p class="text-muted small mb-2">{{ $n->content }}</p>
                    <span class="text-xs text-muted">Published: {{ $n->date }}</span>
                </div>
            @empty
                <p class="text-muted text-center py-4">No active announcements.</p>
            @endforelse
        </div>
    </div>
@endif

<!-- ========================================================================= -->
<!-- ===================== 5. DAILY REPORTS TAB (COMMON) ===================== -->
<!-- ========================================================================= -->
@if($tab === 'reports')
    <div class="row g-4 text-start">
        <div class="col-md-5">
            <div class="premium-card">
                <h5 class="text-dark fw-bold mb-4">Submit Daily Work Log Report</h5>
                <form action="/staff/submit-report" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label">Describe Work Performed today</label>
                        <textarea name="report_text" class="form-control" rows="6" placeholder="Provide details (e.g. Rooms 101 to 105 deep cleaned. Plumber called for room 201 flush.)" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">Submit Daily Log</button>
                </form>
            </div>
        </div>
        <div class="col-md-7">
            <div class="premium-card">
                <h5 class="text-dark fw-bold mb-4">My Submitted Logs</h5>
                <div class="list-group list-group-flush">
                    @forelse($workReports as $report)
                        <div class="list-group-item bg-transparent px-0 py-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="badge bg-secondary-soft text-dark">Date: {{ $report->date }}</span>
                            </div>
                            <p class="text-muted small mb-0">{{ $report->report_text }}</p>
                        </div>
                    @empty
                        <p class="text-muted py-4 text-center">No reports logged.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endif

<!-- ========================================================================= -->
<!-- ====================== 6. PROFILE TAB (COMMON) ======================= -->
<!-- ========================================================================= -->
@if($tab === 'profile')
    <div class="premium-card text-start max-w-xl mx-auto">
        <h5 class="text-dark fw-bold mb-4">My Profile Settings</h5>
        <form action="/staff/update-profile" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ $staff->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address (Registered)</label>
                <input type="email" class="form-control bg-light text-muted" value="{{ $staff->email }}" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-control" value="{{ $staff->phone }}" required>
            </div>
            <div class="mb-4">
                <label class="form-label">New Password (Leave blank to keep same)</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">Save Profile Updates</button>
        </form>
    </div>
@endif

<!-- Hidden POST Actions Forms used by JavaScript prompts -->
<form id="jsStaffPromptActionForm" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="breakfast" id="jsStaffBreakfastInput">
    <input type="hidden" name="lunch" id="jsStaffLunchInput">
    <input type="hidden" name="dinner" id="jsStaffDinnerInput">
    <input type="hidden" name="count" id="jsStaffCountInput">
</form>

@endsection

@section('dashboard_scripts')
<script>
    // Food Menu prompt editor
    function editFoodMenuPrompt(id, day, b, l, d) {
        Swal.fire({
            title: `Modify ${day.toUpperCase()} Meals`,
            html: `
                <div class="mb-3 text-start">
                    <label class="form-label text-muted small">Morning Breakfast</label>
                    <input type="text" id="swal-f-b" class="form-control" value="${b}">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label text-muted small">Afternoon Lunch</label>
                    <input type="text" id="swal-f-l" class="form-control" value="${l}">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label text-muted small">Evening Dinner</label>
                    <input type="text" id="swal-f-d" class="form-control" value="${d}">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Update Menu',
            confirmButtonColor: '#2563EB',
            preConfirm: () => {
                return {
                    b: document.getElementById('swal-f-b').value,
                    l: document.getElementById('swal-f-l').value,
                    d: document.getElementById('swal-f-d').value
                }
            }
        }).then((res) => {
            if (res.isConfirmed) {
                const form = document.getElementById('jsStaffPromptActionForm');
                form.action = `/staff/update-menu/${id}`;
                document.getElementById('jsStaffBreakfastInput').value = res.value.b;
                document.getElementById('jsStaffLunchInput').value = res.value.l;
                document.getElementById('jsStaffDinnerInput').value = res.value.d;
                form.submit();
            }
        });
    }

    // Food stock editor
    function adjustFoodStockPrompt(id, currentCount) {
        Swal.fire({
            title: 'Adjust Stock Count',
            input: 'number',
            inputValue: currentCount,
            confirmButtonText: 'Update Stock',
            showCancelButton: true,
            confirmButtonColor: '#2563EB'
        }).then((res) => {
            if (res.isConfirmed && res.value) {
                const form = document.getElementById('jsStaffPromptActionForm');
                form.action = `/staff/adjust-inventory/${id}`;
                document.getElementById('jsStaffCountInput').value = res.value;
                form.submit();
            }
        });
    }

    // Maintenance ticket modal
    function openUpdateMaintenanceModal(id, status, priority, materials, expense) {
        document.getElementById('updateMaintenanceForm').action = `/staff/update-complaint/${id}`;
        document.getElementById('modalTicketStatus').value = status;
        document.getElementById('modalTicketPriority').value = priority;
        document.getElementById('modalTicketMaterials').value = materials || '';
        document.getElementById('modalTicketExpense').value = expense || 0;

        const modal = new bootstrap.Modal(document.getElementById('updateMaintenanceModal'));
        modal.show();
    }

    function filterFoodByPg(pgId) {
        window.location.href = `/staff?tab=food&pg_id=${pgId}`;
    }

    function openFoodReplyModal(id, reply, status) {
        document.getElementById('foodReplyForm').action = `/staff/reply-food-message/${id}`;
        document.getElementById('foodReplyStatus').value = status;
        document.getElementById('foodReplyText').value = reply || '';

        const modal = new bootstrap.Modal(document.getElementById('foodReplyModal'));
        modal.show();
    }
</script>
@endsection
