@extends('layouts.dashboard')

@section('title', 'Admin Dashboard | PG Management System')
@section('page_title')
    <span class="text-uppercase text-primary small fw-bold">Admin Panel</span> &mdash; 
    @if($tab === 'overview') Dashboard Overview
    @elseif($tab === 'pg-mgr') PG Buildings Directory
    @elseif($tab === 'room-mgr') Room Inventory
    @elseif($tab === 'student-mgr') Tenant Approvals & Registry
    @elseif($tab === 'rent-mgr') Rent Ledgers & Invoices
    @elseif($tab === 'complaints-mgr') Complaints Resolution Desk
    @elseif($tab === 'staff-mgr') Staff Members Roster
    @elseif($tab === 'notices-mgr') Announcements Board
    @elseif($tab === 'food-mgr') Food Preferences
    @elseif($tab === 'inventory-mgr') Logistics Inventory
    @elseif($tab === 'landing-mgr') Landing Page Editor
    @elseif($tab === 'inquiries') Visitor Inquiries
    @elseif($tab === 'reports-mgr') Reports & Analytics
    @endif
@endsection

@section('dashboard_content')
<!-- Active PG Building Scope selector -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-3 bg-white border rounded-4 shadow-sm">
    <div class="d-flex align-items-center gap-2">
        <i data-lucide="filter" class="text-primary" style="width:18px; height:18px;"></i>
        <span class="fw-bold text-muted small">Scope Filter:</span>
    </div>
    <div style="min-width: 250px;">
        <select class="form-select form-select-sm" onchange="location.href='/admin?tab={{ $tab }}&pg_id=' + this.value">
            <option value="all" {{ $activePgId === 'all' ? 'selected' : '' }}>-- All PG Buildings --</option>
            @foreach($pgs as $pg)
                <option value="{{ $pg->id }}" {{ $activePgId == $pg->id ? 'selected' : '' }}>{{ $pg->name }} ({{ ucfirst($pg->status) }})</option>
            @endforeach
        </select>
    </div>
</div>

<!-- ================= 1. TAB: OVERVIEW ================= -->
@if($tab === 'overview')
    <!-- Metrics Grid -->
    <div class="row g-3 mb-4">
        <!-- PGs count -->
        <div class="col-6 col-lg-3">
            <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                <div class="avatar-box bg-primary-soft text-primary"><i data-lucide="building-2"></i></div>
                <div>
                    <h4 class="text-dark mb-0 fw-bold">{{ $pgs->count() }}</h4>
                    <small class="text-muted text-xs">Total PGs ({{ $pgs->where('status', 'active')->count() }} Active)</small>
                </div>
            </div>
        </div>
        <!-- Rooms count -->
        <div class="col-6 col-lg-3">
            <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                <div class="avatar-box bg-success-soft text-success" style="background-color:rgba(22, 163, 74, 0.1);"><i data-lucide="door-open"></i></div>
                <div>
                    <h4 class="text-dark mb-0 fw-bold">{{ $totalRooms }}</h4>
                    <small class="text-muted text-xs">Rooms ({{ $occupiedSlots }} Occ | {{ $vacantSlots }} Vac)</small>
                </div>
            </div>
        </div>
        <!-- Students count -->
        <div class="col-6 col-lg-3">
            <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                <div class="avatar-box bg-info-soft text-info" style="background-color:rgba(14, 165, 233, 0.1);"><i data-lucide="user-check"></i></div>
                <div>
                    <h4 class="text-dark mb-0 fw-bold">{{ $students->count() }}</h4>
                    <small class="text-muted text-xs">Active Tenants ({{ $pendingApprovalsCount }} Pending)</small>
                </div>
            </div>
        </div>
        <!-- Due sum -->
        <div class="col-6 col-lg-3">
            <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                <div class="avatar-box bg-danger-soft text-danger" style="background-color:rgba(220, 38, 38, 0.1);"><i data-lucide="receipt"></i></div>
                <div>
                    <h4 class="text-dark mb-0 fw-bold">₹{{ number_format($dueSum) }}</h4>
                    <small class="text-muted text-xs">Due Outstanding</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Collection -->
        <div class="col-lg-8">
            <div class="premium-card h-100">
                <h5 class="text-dark fw-bold mb-4">Rent Collection Financials Overview</h5>
                <div style="height: 320px;">
                    <canvas id="rentBarChart"></canvas>
                </div>
            </div>
        </div>
        <!-- Occupancy -->
        <div class="col-lg-4">
            <div class="premium-card h-100">
                <h5 class="text-dark fw-bold mb-4">Room Occupancy Rate</h5>
                <div style="height: 320px; display:flex; align-items:center; justify-content:center;">
                    <canvas id="occupancyPieChart"></canvas>
                </div>
            </div>
    </div>

    <!-- System Diagnostics & Render Mail Test -->
    <div class="premium-card mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h6 class="text-dark fw-bold mb-1"><i data-lucide="mail-check" class="text-primary me-2"></i>Render SMTP Mail Notification Diagnostic</h6>
                <small class="text-muted">Test your deployed SMTP email notifications (Gmail / Custom SMTP) directly from your dashboard.</small>
            </div>
            <form action="/admin/test-email" method="POST" class="d-flex align-items-center gap-2 flex-wrap">
                @csrf
                <input type="email" name="email" class="form-control form-control-sm" placeholder="Recipient email address" value="{{ $loggedUser->email ?? '' }}" required style="max-width: 250px;">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill"><i data-lucide="send" style="width:14px; height:14px;"></i> Send Test Email</button>
            </form>
        </div>
    </div>

<!-- ================= 2. TAB: PG MANAGEMENT ================= -->
@elseif($tab === 'pg-mgr')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="text-dark fw-bold mb-0">PG Buildings Directory</h5>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addPgModal">
            <i data-lucide="plus"></i> Add New PG
        </button>
    </div>
    
    <div class="premium-table-container">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Building Name</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeTabPgBuildings as $pg)
                        <tr>
                            <td class="fw-bold text-dark">{{ $pg->name }}</td>
                            <td>{{ $pg->address }}</td>
                            <td>
                                <span class="badge {{ $pg->status === 'active' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                    {{ strtoupper($pg->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form action="/admin/toggle-pg/{{ $pg->id }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill">Toggle Status</button>
                                    </form>
                                    <form action="/admin/delete-pg/{{ $pg->id }}" method="POST" onsubmit="return confirm('Deleting this PG building deletes all associated rooms!')">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm rounded-pill"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Common Area cleaning assignments roster -->
    <div class="premium-card text-start mt-5">
        <h5 class="text-dark fw-bold mb-4"><i data-lucide="brush" class="text-primary me-2"></i>Common Area Cleaning Roster Assignments</h5>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Area / Room</th>
                        <th>PG Building</th>
                        <th>Status</th>
                        <th>Last Cleaned</th>
                        <th>Assigned Staff member</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commonAreaTasks as $task)
                        @php
                            $bName = $pgs->find($task->pg_building_id)->name ?? 'All Buildings';
                        @endphp
                        <tr>
                            <td class="fw-bold text-dark">{{ $task->area_name }}</td>
                            <td class="text-primary fw-bold">{{ $bName }}</td>
                            <td>
                                <span class="badge {{ $task->status === 'Cleaned' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                    {{ strtoupper($task->status) }}
                                </span>
                            </td>
                            <td class="text-xs text-muted">{{ $task->last_cleaned_at ?: 'Never' }}</td>
                            <td>
                                <form action="/admin/assign-common-cleaning/{{ $task->id }}" method="POST">
                                    @csrf
                                    <select name="assigned_to" class="form-select form-select-xs rounded-pill" onchange="this.form.submit()" style="font-size:11px; max-width: 150px;">
                                        <option value="">Unassigned</option>
                                        @foreach($staffMembers->where('staff_role', 'Housekeeping') as $hStaff)
                                            <option value="{{ $hStaff->name }}" {{ $task->assigned_to === $hStaff->name ? 'selected' : '' }}>{{ $hStaff->name }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted p-4">No common area cleaning tasks found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

<!-- ================= 3. TAB: ROOM MANAGEMENT ================= -->
@elseif($tab === 'room-mgr')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="text-dark fw-bold mb-0">Room Inventory Profiles</h5>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addRoomModal">
            <i data-lucide="plus"></i> Add Room
        </button>
    </div>
    
    <div class="premium-table-container">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>PG Building</th>
                        <th>Sharing Type</th>
                        <th>Monthly Rent</th>
                        <th>Capacity Limit</th>
                        <th>Occupied Beds</th>
                        <th>Housekeeping</th>
                        <th>Cleaning Status</th>
                        <th>Room Maintenance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeTabRooms as $room)
                        @php
                            $pgName = $pgs->find($room->pg_building_id)->name ?? 'Unknown';
                        @endphp
                        <tr>
                            <td class="fw-bold text-dark">#{{ $room->number }}</td>
                            <td class="text-primary fw-bold">{{ $pgName }}</td>
                            <td>{{ $room->type }}</td>
                            <td class="text-success fw-bold">₹{{ number_format($room->rent) }}</td>
                            <td>{{ $room->capacity }} Beds</td>
                            <td>
                                <span class="badge {{ $room->occupied >= $room->capacity ? 'bg-danger-soft text-danger' : 'bg-success-soft text-success' }}">
                                    {{ $room->occupied }} Occupied
                                </span>
                            </td>
                            <td>
                                <form action="/admin/assign-room-cleaning/{{ $room->id }}" method="POST">
                                    @csrf
                                    <select name="assigned_to" class="form-select form-select-xs rounded-pill" onchange="this.form.submit()" style="font-size:11px; max-width: 140px;">
                                        <option value="">Unassigned</option>
                                        @foreach($staffMembers->where('staff_role', 'Housekeeping') as $hStaff)
                                            <option value="{{ $hStaff->name }}" {{ $room->assigned_to === $hStaff->name ? 'selected' : '' }}>{{ $hStaff->name }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td>
                                <span class="badge {{ ($room->cleaning_status ?? 'Dirty') === 'Cleaned' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                    {{ strtoupper($room->cleaning_status ?? 'Dirty') }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-xs rounded-pill px-3 py-1 fs-xs" onclick="openRoomMaintenanceModal('{{ $room->number }}')" style="font-size:11px;">
                                    <i data-lucide="wrench" style="width:12px; height:12px;" class="me-1"></i> Log Task
                                </button>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm rounded-pill" onclick="editRentPrompt('{{ $room->id }}', '{{ $room->rent }}')"><i data-lucide="edit-3" style="width:14px; height:14px;"></i> Edit Rent</button>
                                    <form action="/admin/delete-room/{{ $room->id }}" method="POST" onsubmit="return confirm('Delete this room?')">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm rounded-pill"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

<!-- ================= 4. TAB: STUDENT APPROVALS ================= -->
@elseif($tab === 'student-mgr')
    <!-- Filter UI -->
    <div class="premium-card p-4 mb-4">
        <h6 class="text-dark fw-bold mb-3">Search & Filter Tenant Database</h6>
        <form method="GET" action="/admin" class="row g-2">
            <input type="hidden" name="tab" value="student-mgr">
            <input type="hidden" name="pg_id" value="{{ $activePgId }}">
            <input type="hidden" name="subtab" value="{{ $studentSubTab }}">
            
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search Name/Email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="college" class="form-select">
                    <option value="">All Colleges</option>
                    @foreach($filterColleges as $c)
                        <option value="{{ $c }}" {{ request('college') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="course" class="form-select">
                    <option value="">All Courses</option>
                    @foreach($filterCourses as $c)
                        <option value="{{ $c }}" {{ request('course') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="year" class="form-select">
                    <option value="">All Years</option>
                    <option value="1st Year" {{ request('year') === '1st Year' ? 'selected' : '' }}>1st Year</option>
                    <option value="2nd Year" {{ request('year') === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                    <option value="3rd Year" {{ request('year') === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                    <option value="4th Year" {{ request('year') === '4th Year' ? 'selected' : '' }}>4th Year</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment" class="form-select">
                    <option value="">All Payments</option>
                    <option value="Paid" {{ request('payment') === 'Paid' ? 'selected' : '' }}>Fully Paid</option>
                    <option value="Due" {{ request('payment') === 'Due' ? 'selected' : '' }}>Due Outstanding</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100 rounded-pill"><i data-lucide="search"></i></button>
            </div>
        </form>
    </div>

    <!-- Sub-tab switchers -->
    <div class="d-flex gap-2 mb-3">
        <a href="/admin?tab=student-mgr&subtab=pending" class="btn btn-sm rounded-pill {{ $studentSubTab === 'pending' ? 'btn-primary' : 'btn-secondary' }}">
            Pending Approvals <span class="badge bg-danger ms-1">{{ $pendingApprovalsCount }}</span>
        </a>
        <a href="/admin?tab=student-mgr&subtab=approved" class="btn btn-sm rounded-pill {{ $studentSubTab === 'approved' ? 'btn-primary' : 'btn-secondary' }}">
            Approved Tenants
        </a>
        <a href="/admin?tab=student-mgr&subtab=rejected" class="btn btn-sm rounded-pill {{ $studentSubTab === 'rejected' ? 'btn-primary' : 'btn-secondary' }}">
            Rejected Accounts
        </a>
    </div>

    <div class="premium-table-container">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    @if($studentSubTab === 'pending')
                        <tr>
                            <th>S.No</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>College & Course</th>
                            <th>PG Choice</th>
                            <th>Room Pref</th>
                            <th>Guardian Contacts</th>
                            <th>Uploads</th>
                            <th>Actions</th>
                        </tr>
                    @else
                        <tr>
                            <th>S.No</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Email Address</th>
                            <th>Assigned Room</th>
                            <th>College & Course</th>
                            <th>Year of study</th>
                            <th>Action</th>
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @forelse($activeTabStudents as $student)
                        @php
                            $studentPg = $pgs->find($student->pg_building_id)->name ?? 'Unknown';
                        @endphp
                        @if($studentSubTab === 'pending')
                            <tr>
                                <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    @if(!empty($student->profile_photo))
                                        <img src="{{ $student->profile_photo }}" class="rounded-circle border" style="width: 44px; height: 44px; object-fit: cover;">
                                    @else
                                        <span class="text-muted text-xs">No profile photo</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-dark">{{ $student->name }}<br><small class="text-muted text-xs">{{ $student->phone }}</small></td>
                                <td>{{ $student->college }}<br><small class="text-muted text-xs">{{ $student->course }}</small></td>
                                <td>{{ $studentPg }}</td>
                                <td>Room #{{ $student->room_number }} ({{ $student->room_type }})</td>
                                <td>Name: {{ $student->guardian_name }}<br><small class="text-muted text-xs">Ph: {{ $student->guardian_phone }}</small></td>
                                <td>
                                    @if($student->id_proof)
                                        <a href="{{ $student->id_proof }}" download="id_{{ $student->name }}" class="btn btn-sm btn-secondary rounded-pill py-1"><i data-lucide="download" style="width:12px; height:12px;"></i> ID</a>
                                    @else
                                        <span class="text-muted text-xs">No ID</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <form action="/admin/approve-student/{{ $student->id }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm rounded-pill"><i data-lucide="check"></i> Approve</button>
                                        </form>
                                        <form action="/admin/reject-student/{{ $student->id }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm rounded-pill"><i data-lucide="x"></i> Reject</button>
                                        </form>
                                        <form action="/admin/delete-student/{{ $student->id }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this student record from the database?')">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm rounded-pill"><i data-lucide="trash-2"></i> Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    @if(!empty($student->profile_photo))
                                        <img src="{{ $student->profile_photo }}" class="rounded-circle border" style="width: 44px; height: 44px; object-fit: cover;">
                                    @else
                                        <span class="text-muted text-xs">No profile photo</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-dark">{{ $student->name }}<br><small class="text-muted text-xs">{{ $student->phone }}</small></td>
                                <td>{{ $student->email }}</td>
                                <td>Room #{{ $student->room_number }} <br><small class="text-primary fw-bold">{{ $studentPg }}</small></td>
                                <td>{{ $student->college }}<br><small class="text-muted text-xs">{{ $student->course }}</small></td>
                                <td>{{ $student->year }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if($studentSubTab === 'approved')
                                            <form action="/admin/revoke-student/{{ $student->id }}" method="POST" onsubmit="return confirm('Discharge this tenant?')">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning btn-sm rounded-pill">Discharge</button>
                                            </form>
                                        @else
                                            <form action="/admin/approve-student/{{ $student->id }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill">Re-Approve</button>
                                            </form>
                                        @endif
                                        <form action="/admin/delete-student/{{ $student->id }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this student record from the database?')">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm rounded-pill"><i data-lucide="trash-2"></i> Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="10" class="text-center text-muted p-4">No student records found matching filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

<!-- ================= 5. TAB: RENT & PAYMENTS ================= -->
@elseif($tab === 'rent-mgr')
    <div class="row g-4 mb-4 text-start">
        <!-- Gateway config -->
        <div class="col-md-5">
            <div class="premium-card h-100">
                <h6 class="text-dark fw-bold mb-4"><i data-lucide="settings" class="text-primary me-2"></i>Configure Channels Info</h6>
                <form action="/admin/update-payment-config" method="POST" enctype="multipart/form-data" class="row g-2">
                    @csrf
                    <div class="col-12">
                        <label class="form-label">Account Holder Name</label>
                        <input type="text" name="account_holder" class="form-control form-control-sm" value="{{ $paymentConfig->account_holder ?? '' }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Upload QR Code (UPI Payment QR)</label>
                        <input type="file" name="qr_code" class="form-control form-control-sm" accept="image/*">
                        @if(!empty($paymentConfig->qr_code))
                            <div class="mt-2 text-center p-2 bg-light border rounded">
                                <small class="text-muted d-block mb-1">Active QR Code Preview:</small>
                                <img src="{{ asset('storage/' . $paymentConfig->qr_code) }}" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                        @endif
                    </div>
                    <div class="col-6">
                        <label class="form-label">PhonePe Number</label>
                        <input type="text" name="phonepe" class="form-control form-control-sm" value="{{ $paymentConfig->phonepe ?? '' }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Google Pay (GPay)</label>
                        <input type="text" name="gpay" class="form-control form-control-sm" value="{{ $paymentConfig->gpay ?? '' }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Paytm Number</label>
                        <input type="text" name="paytm" class="form-control form-control-sm" value="{{ $paymentConfig->paytm ?? '' }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Other payment details</label>
                        <input type="text" name="other" class="form-control form-control-sm" value="{{ $paymentConfig->other ?? '' }}">
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill">Update Configurations</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Dues generation -->
        <div class="col-md-7">
            <div class="premium-card h-100">
                <h6 class="text-dark fw-bold mb-4"><i data-lucide="plus-circle" class="text-success me-2"></i>Generate Payment Due Invoice</h6>
                <form action="/admin/generate-due" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label">Payment Type</label>
                        <select name="month" class="form-select" required>
                            <option value="" disabled selected>-- Select Type --</option>
                            <option value="Academic Payment">Academic Payment</option>
                            <option value="Semester Payment">Semester Payment</option>
                            <option value="Part Payment">Part Payment</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Select Target Tenant</label>
                        <select name="student_email" class="form-select" required>
                            <option value="" disabled selected>-- Select Tenant --</option>
                            @foreach($students as $t)
                                <option value="{{ $t->email }}">{{ $t->name }} (Room #{{ $t->room_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Amount (INR)</label>
                        <input type="number" name="amount" class="form-control" placeholder="e.g. 15000" min="1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Billing Date</label>
                        <input type="date" name="created_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-success rounded-pill px-4">Generate Due Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payments Ledger -->
    <div class="premium-table-container">
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="text-dark fw-bold mb-0">Payments Ledger Registry</h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Room</th>
                        <th>Description / Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment Date</th>
                        <th>UTR / Transaction ID</th>
                        <th>Method</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeTabPayments as $p)
                        @php
                            $student = $students->where('email', $p->student_email)->first();
                        @endphp
                        <tr>
                            <td class="fw-bold text-dark">{{ $student ? $student->name : 'Unknown' }}<br><small class="text-muted text-xs">{{ $p->student_email }}</small></td>
                            <td>#{{ $p->room_number }}</td>
                            <td>{{ $p->month }}</td>
                            <td class="text-primary fw-bold">₹{{ number_format($p->amount) }}</td>
                            <td>
                                <span class="badge 
                                    @if($p->status === 'Paid') bg-success-soft text-success
                                    @elseif($p->status === 'Pending Approval') bg-warning-soft text-warning
                                    @else bg-danger-soft text-danger
                                    @endif">
                                    {{ strtoupper($p->status) }}
                                </span>
                            </td>
                            <td>{{ $p->payment_date ?: '-' }}</td>
                            <td><code class="text-xs text-muted">{{ $p->tx_id ?: '-' }}</code></td>
                            <td>{{ $p->method ?: '-' }}</td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @if($p->status === 'Paid')
                                        <button class="btn btn-sm btn-outline-primary py-1 px-2 rounded-pill" onclick="downloadReceiptPDF('{{ $p->id }}', '{{ $student ? $student->name : 'Tenant' }}', '{{ $p->room_number }}', '{{ $p->month }}', '{{ $p->amount }}', '{{ $p->tx_id }}', '{{ $p->method }}', '{{ $p->payment_date }}')" style="font-size:11px;">
                                            <i data-lucide="file-text" style="width:12px; height:12px;"></i> Receipt
                                        </button>
                                    @elseif($p->status === 'Pending Approval')
                                        <form action="/admin/approve-payment/{{ $p->id }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm py-1 px-2 rounded-pill" style="font-size:11px;">
                                                <i data-lucide="check-circle" style="width:12px; height:12px;"></i> Approve
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-success btn-sm py-1 px-2 rounded-pill" onclick="collectManualPrompt('{{ $p->id }}')" style="font-size:11px;">
                                            <i data-lucide="banknote" style="width:12px; height:12px;"></i> Collect
                                        </button>
                                    @endif
                                    <button class="btn btn-outline-secondary btn-sm py-1 px-2 rounded-pill" onclick="openEditInvoiceModal('{{ $p->id }}', '{{ $p->month }}', '{{ $p->amount }}', '{{ $p->status }}', '{{ $p->payment_date }}', '{{ $p->tx_id }}', '{{ $p->method }}')" style="font-size:11px;">
                                        <i data-lucide="edit" style="width:12px; height:12px;"></i> Edit
                                    </button>
                                    <form action="/admin/delete-invoice/{{ $p->id }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this payment invoice from the registry?');" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2 rounded-pill" style="font-size:11px;">
                                            <i data-lucide="trash-2" style="width:12px; height:12px;"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted p-4">No payments recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

<!-- ================= 6. TAB: COMPLAINTS DESK ================= -->
@elseif($tab === 'complaints-mgr')
    <div class="premium-table-container">
        <div class="p-3 border-bottom"><h6 class="text-dark fw-bold mb-0">Complaints Tickets Log</h6></div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Room</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Assigned Staff</th>
                        <th>Raised Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeTabComplaints as $c)
                        <tr>
                            <td class="fw-bold text-dark">{{ $c->student_name }}<br><small class="text-muted text-xs">{{ $c->student_email }}</small></td>
                            <td>#{{ $c->room_number }}</td>
                            <td>{{ $c->title }}</td>
                            <td>{{ $c->description }}</td>
                            <td>
                                <span class="badge {{ $c->status === 'Resolved' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                    {{ strtoupper($c->status) }}
                                </span>
                            </td>
                            <td>{{ $c->assigned_to ?: 'Unassigned' }}</td>
                            <td>{{ $c->created_date }}</td>
                            <td>
                                @if($c->status === 'Pending')
                                    <div class="d-flex gap-1">
                                        <form action="/admin/assign-complaint/{{ $c->id }}" method="POST" class="d-flex align-items-center gap-1">
                                            @csrf
                                            <select name="assigned_to" class="form-select form-select-sm" style="width:130px;" onchange="this.form.submit()">
                                                <option value="" disabled selected>-- Assign --</option>
                                                @foreach($staffMembers as $s)
                                                    <option value="{{ $s->name }}">{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                        <button class="btn btn-success btn-sm rounded-pill" onclick="resolveComplaintPrompt('{{ $c->id }}')">Resolve</button>
                                    </div>
                                @else
                                    <span class="text-muted small text-xs">Closed: {{ $c->resolved_date }}<br><i class="text-muted">Reply: "{{ $c->reply }}"</i></span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted p-4">No complaints raised.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($tab === 'staff-mgr')
    <!-- Sub-tab switchers -->
    @php
        $staffSubTab = request()->query('subtab', 'roster');
    @endphp
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex gap-2">
            <a href="/admin?tab=staff-mgr&subtab=roster" class="btn btn-sm rounded-pill {{ $staffSubTab === 'roster' ? 'btn-primary' : 'btn-secondary' }}">
                Staff Roster
            </a>
            <a href="/admin?tab=staff-mgr&subtab=reports" class="btn btn-sm rounded-pill {{ $staffSubTab === 'reports' ? 'btn-primary' : 'btn-secondary' }}">
                Daily Work Reports
            </a>
        </div>
        @if($staffSubTab === 'roster')
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i data-lucide="plus"></i> Add Staff Member
            </button>
        @endif
    </div>
    
    @if($staffSubTab === 'roster')
        <div class="premium-table-container">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Staff Operations Role</th>
                            <th>Assigned PG Scope</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffMembers as $s)
                            @php
                                $assignedBuilding = $pgs->find($s->pg_building_id)->name ?? 'All Buildings';
                            @endphp
                            <tr>
                                <td class="fw-bold text-dark">{{ $s->name }}</td>
                                <td>{{ $s->email }}</td>
                                <td>{{ $s->phone }}</td>
                                <td>
                                    <span class="badge 
                                        @if($s->staff_role === 'Housekeeping') bg-primary-soft text-primary
                                        @elseif($s->staff_role === 'Food Management') bg-success-soft text-success
                                        @else bg-warning-soft text-warning @endif">
                                        {{ $s->staff_role ?: 'Housekeeping' }}
                                    </span>
                                </td>
                                <td class="text-primary fw-bold">{{ $assignedBuilding }}</td>
                                <td>
                                    @if($s->email !== 'staff@gmail.com')
                                        <form action="/admin/delete-staff/{{ $s->id }}" method="POST" onsubmit="return confirm('Fire staff member?')">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm rounded-pill"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
                                        </form>
                                    @else
                                        <span class="text-muted text-xs">Baseline Account</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="premium-table-container">
            <div class="table-responsive">
                <table class="table align-middle text-start">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Staff Member</th>
                            <th>Staff Operations Role</th>
                            <th>Report logs content</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workReports as $report)
                            <tr>
                                <td class="text-xs fw-bold">{{ $report->date }}</td>
                                <td class="fw-bold text-dark">{{ $report->name }}</td>
                                <td>
                                    <span class="badge 
                                        @if($report->staff_role === 'Housekeeping') bg-primary-soft text-primary
                                        @elseif($report->staff_role === 'Food Management') bg-success-soft text-success
                                        @else bg-warning-soft text-warning @endif">
                                        {{ $report->staff_role }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $report->report_text }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted p-4">No daily work reports submitted yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

<!-- ================= 8. TAB: ANNOUNCEMENTS ================= -->
@elseif($tab === 'notices-mgr')
    <div class="row g-4 text-start">
        <div class="col-md-5">
            <div class="premium-card">
                <h6 class="text-dark fw-bold mb-4">Publish Announcement</h6>
                <form action="/admin/publish-notice" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Notice Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Maintenance update" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notice Content</label>
                        <textarea name="content" class="form-control" rows="4" placeholder="Brief description..." required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Target Audience</label>
                        <select name="target" class="form-select">
                            <option value="all">Everyone (Students & Staff)</option>
                            <option value="student">Students / Tenants Only</option>
                            <option value="staff">Staff / Housekeeping Only</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Publish Notice</button>
                </form>
            </div>
        </div>

        <div class="col-md-7">
            <div class="premium-card">
                <h6 class="text-dark fw-bold mb-4">Active Notices & Announcements Board</h6>
                <div class="timeline-notices">
                    @forelse($activeTabNotices as $n)
                        <div class="timeline-notice-item">
                            <div class="d-flex justify-content-between">
                                <h6 class="text-dark fw-bold mb-1">{{ $n->title }}</h6>
                                <form action="/admin/delete-notice/{{ $n->id }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 text-danger" style="border:none; background:none;"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
                                </form>
                            </div>
                            <p class="text-muted small mb-2">{{ $n->content }}</p>
                            <div class="d-flex justify-content-between text-xs text-muted">
                                <span>Date: {{ $n->date }}</span>
                                <span>Target: <b class="text-uppercase text-primary">{{ $n->target }}</b></span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No active notices.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

<!-- ================= 11. TAB: LANDING CMS EDITOR ================= -->
@elseif($tab === 'landing-mgr')
    @php
        $cmsFacilities = json_decode($landingContent->facilities_json ?? '[]');
        $cmsPricingPlans = json_decode($landingContent->pricing_plans_json ?? '[]');
        $cmsLocations = json_decode($landingContent->locations_json ?? '[]');
        $cmsTestimonials = json_decode($landingContent->testimonials_json ?? '[]');
    @endphp
    
    <div class="premium-card text-start">
        <h5 class="text-dark fw-bold mb-4"><i data-lucide="layout-template" class="text-primary me-2"></i>Landing Page Editor & Content CMS</h5>
        <form action="/admin/update-landing" method="POST" enctype="multipart/form-data" class="row g-4">
            @csrf
            
            <!-- SECTION 1: HEADER & BRANDING -->
            <div class="col-12"><h6 class="text-primary border-bottom pb-2 fw-bold">Header & Branding</h6></div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">PG Brand Title (Browser tab)</label>
                <input type="text" name="pg_title" class="form-control" value="{{ $landingContent->pg_title ?? '' }}" placeholder="e.g. PG Management System">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">Navbar Logo Text</label>
                <input type="text" name="logo_text" class="form-control" value="{{ $landingContent->logo_text ?? '' }}" placeholder="e.g. PG MANAGEMENT SYSTEM">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">Upload Logo Image (Overrides Logo Text)</label>
                <input type="file" name="logo_image_file" class="form-control" accept="image/*">
                @if(!empty($landingContent->logo_image))
                    <div class="mt-2 text-xs text-muted">Current: <a href="{{ $landingContent->logo_image }}" target="_blank">View Logo</a></div>
                @endif
            </div>

            <!-- SECTION 2: HOME & HERO -->
            <div class="col-12 mt-4"><h6 class="text-primary border-bottom pb-2 fw-bold">Home & Hero Section</h6></div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">Hero Badge Tagline</label>
                <input type="text" name="banner_tag" class="form-control" value="{{ $landingContent->banner_tag ?? '' }}" placeholder="e.g. Premium Living Redefined">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">Hero Headline Title</label>
                <input type="text" name="banner_title" class="form-control" value="{{ $landingContent->banner_title ?? '' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">Hero Subtitle</label>
                <input type="text" name="banner_subtitle" class="form-control" value="{{ $landingContent->banner_subtitle ?? '' }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-bold small">Banner Background Image (Upload)</label>
                <input type="file" name="banner_image_file" class="form-control mb-2" accept="image/*">
                @if(!empty($landingContent->banner_image))
                    <div class="mt-2 d-flex align-items-center gap-3">
                        <div>
                            <small class="text-muted d-block mb-1">Current Banner Image:</small>
                            <img src="{{ $landingContent->banner_image }}" class="rounded shadow-sm" style="max-height: 80px; max-width: 160px; object-fit: cover;">
                        </div>
                    </div>
                @endif
            </div>

            <!-- SECTION 3: ABOUT US -->
            <div class="col-12 mt-4"><h6 class="text-primary border-bottom pb-2 fw-bold">About Us Section</h6></div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">About Us Badge Tag</label>
                <input type="text" name="about_badge" class="form-control" value="{{ $landingContent->about_badge ?? '' }}" placeholder="e.g. About Us">
            </div>
            <div class="col-md-8">
                <label class="form-label fw-bold small">About Us Section Title</label>
                <input type="text" name="about_title" class="form-control" value="{{ $landingContent->about_title ?? '' }}" placeholder="e.g. A Cozy, Premium Safe Space For You">
            </div>
            <div class="col-12">
                <label class="form-label fw-bold small">About Us Paragraph Description</label>
                <textarea name="about_text" class="form-control" rows="3">{{ $landingContent->about_text ?? '' }}</textarea>
            </div>

            <!-- SECTION 4: FACILITIES -->
            <div class="col-12 mt-4"><h6 class="text-primary border-bottom pb-2 fw-bold">Facilities (Amenities) Section</h6></div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Facilities Section Title</label>
                <input type="text" name="facilities_title" class="form-control" value="{{ $landingContent->facilities_title ?? '' }}" placeholder="e.g. Top Class Facilities">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Facilities Section Subtitle</label>
                <input type="text" name="facilities_subtitle" class="form-control" value="{{ $landingContent->facilities_subtitle ?? '' }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-bold small">List of Amenities (Dynamic)</label>
                <div id="facilitiesContainer" class="d-flex flex-column gap-3 mb-2">
                    @foreach($cmsFacilities as $index => $fac)
                        <div class="row g-2 align-items-end facility-row border p-2 rounded">
                            <div class="col-md-3">
                                <label class="form-label text-xs">Icon Name (Lucide)</label>
                                <input type="text" name="facilities[{{ $index }}][icon]" class="form-control form-control-sm" value="{{ $fac->icon ?? '' }}" placeholder="e.g. wifi, shield, utensils">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-xs">Amenity Name</label>
                                <input type="text" name="facilities[{{ $index }}][name]" class="form-control form-control-sm" value="{{ $fac->name ?? '' }}" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label text-xs">Brief Description</label>
                                <input type="text" name="facilities[{{ $index }}][desc]" class="form-control form-control-sm" value="{{ $fac->desc ?? '' }}">
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="this.closest('.facility-row').remove()"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill mt-2" onclick="addFacilityRow()"><i data-lucide="plus" style="width: 14px; height: 14px;"></i> Add Amenity Row</button>
            </div>

            <!-- SECTION 5: ROOMS & PRICING -->
            <div class="col-12 mt-4"><h6 class="text-primary border-bottom pb-2 fw-bold">Rooms & Pricing Plans</h6></div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Rooms Heading Title</label>
                <input type="text" name="rooms_title" class="form-control" value="{{ $landingContent->rooms_title ?? '' }}" placeholder="Explore Our Rooms">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Rooms Heading Subtitle</label>
                <input type="text" name="rooms_subtitle" class="form-control" value="{{ $landingContent->rooms_subtitle ?? '' }}">
            </div>
            <div class="col-md-6 mt-3">
                <label class="form-label fw-bold small">Pricing Section Title</label>
                <input type="text" name="pricing_title" class="form-control" value="{{ $landingContent->pricing_title ?? '' }}" placeholder="Simple, Transparent Pricing">
            </div>
            <div class="col-md-6 mt-3">
                <label class="form-label fw-bold small">Pricing Section Subtitle</label>
                <input type="text" name="pricing_subtitle" class="form-control" value="{{ $landingContent->pricing_subtitle ?? '' }}">
            </div>
            <div class="col-12 mt-3">
                <label class="form-label fw-bold small">Pricing Packages / Rooms List (Dynamic)</label>
                <div id="pricingContainer" class="d-flex flex-column gap-3 mb-2">
                    @foreach($cmsPricingPlans as $index => $plan)
                        <div class="row g-2 align-items-end pricing-row border p-2 rounded">
                            <div class="col-md-2">
                                <label class="form-label text-xs">Package Name</label>
                                <input type="text" name="pricing_plans[{{ $index }}][name]" class="form-control form-control-sm" value="{{ $plan->name ?? '' }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xs">Price Label</label>
                                <input type="text" name="pricing_plans[{{ $index }}][price]" class="form-control form-control-sm" value="{{ $plan->price ?? '' }}" placeholder="e.g. ₹8,000 / month" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xs">Brief Description</label>
                                <input type="text" name="pricing_plans[{{ $index }}][desc]" class="form-control form-control-sm" value="{{ $plan->desc ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-xs">Features (comma-separated)</label>
                                <input type="text" name="pricing_plans[{{ $index }}][features]" class="form-control form-control-sm" value="{{ is_array($plan->features ?? '') ? implode(', ', $plan->features) : ($plan->features ?? '') }}" placeholder="e.g. AC, Wi-Fi, Food">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xs">Room Image (Upload)</label>
                                <div class="d-flex align-items-center gap-1">
                                    <input type="file" name="pricing_plans_files[{{ $index }}]" class="form-control form-control-sm" accept="image/*">
                                    <input type="hidden" name="pricing_plans[{{ $index }}][image_url]" value="{{ $plan->image_url ?? '' }}">
                                    @if(!empty($plan->image_url))
                                        <img src="{{ $plan->image_url }}" class="rounded border" style="width: 32px; height: 32px; object-fit: cover;" title="Current Image">
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="this.closest('.pricing-row').remove()"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill mt-2" onclick="addPricingRow()"><i data-lucide="plus" style="width: 14px; height: 14px;"></i> Add Pricing Package Row</button>
            </div>

            <!-- SECTION 6: LOCATIONS -->
            <div class="col-12 mt-4"><h6 class="text-primary border-bottom pb-2 fw-bold">Locations Section</h6></div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Locations Title</label>
                <input type="text" name="locations_title" class="form-control" value="{{ $landingContent->locations_title ?? '' }}" placeholder="Prime Locations">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Locations Subtitle</label>
                <input type="text" name="locations_subtitle" class="form-control" value="{{ $landingContent->locations_subtitle ?? '' }}">
            </div>
            <div class="col-12 mt-3">
                <label class="form-label fw-bold small">Locations List (Dynamic)</label>
                <div id="locationsContainer" class="d-flex flex-column gap-3 mb-2">
                    @foreach($cmsLocations as $index => $loc)
                        <div class="row g-2 align-items-end location-row border p-2 rounded">
                            <div class="col-md-3">
                                <label class="form-label text-xs">City & Area Name</label>
                                <input type="text" name="locations[{{ $index }}][city]" class="form-control form-control-sm" value="{{ $loc->city ?? '' }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-xs">Description / Proximity Details</label>
                                <input type="text" name="locations[{{ $index }}][area]" class="form-control form-control-sm" value="{{ $loc->area ?? '' }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-xs">Google Map Embed URL (Optional)</label>
                                <input type="text" name="locations[{{ $index }}][map_url]" class="form-control form-control-sm" value="{{ $loc->map_url ?? '' }}" placeholder="https://www.google.com/maps/embed?pb=...">
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="this.closest('.location-row').remove()"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill mt-2" onclick="addLocationRow()"><i data-lucide="plus" style="width: 14px; height: 14px;"></i> Add Location Row</button>
            </div>

            <!-- SECTION 7: TESTIMONIALS -->
            <div class="col-12 mt-4"><h6 class="text-primary border-bottom pb-2 fw-bold">Testimonials Section</h6></div>
            <div class="col-12">
                <label class="form-label fw-bold small">Testimonials List (Dynamic)</label>
                <div id="testimonialsContainer" class="d-flex flex-column gap-3 mb-2">
                    @foreach($cmsTestimonials as $index => $test)
                        <div class="row g-2 align-items-end testimonial-row border p-2 rounded">
                            <div class="col-md-3">
                                <label class="form-label text-xs">Reviewer Name</label>
                                <input type="text" name="testimonials[{{ $index }}][name]" class="form-control form-control-sm" value="{{ $test->name ?? '' }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-xs">Role / Designation</label>
                                <input type="text" name="testimonials[{{ $index }}][role]" class="form-control form-control-sm" value="{{ $test->role ?? '' }}" placeholder="e.g. Student, IT Professional" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-xs">Feedback Review Text</label>
                                <input type="text" name="testimonials[{{ $index }}][review]" class="form-control form-control-sm" value="{{ $test->review ?? '' }}" required>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="this.closest('.testimonial-row').remove()"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill mt-2" onclick="addTestimonialRow()"><i data-lucide="plus" style="width: 14px; height: 14px;"></i> Add Testimonial Row</button>
            </div>

            <!-- SECTION 8: CONTACT DETAILS -->
            <div class="col-12 mt-4"><h6 class="text-primary border-bottom pb-2 fw-bold">Contact Details</h6></div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Contact Title</label>
                <input type="text" name="contact_title" class="form-control" value="{{ $landingContent->contact_title ?? '' }}" placeholder="Contact Information">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Contact Description Subtitle</label>
                <input type="text" name="contact_subtitle" class="form-control" value="{{ $landingContent->contact_subtitle ?? '' }}">
            </div>
            <div class="col-md-4 mt-3">
                <label class="form-label fw-bold small">Contact Phone</label>
                <input type="text" name="contact_phone" class="form-control" value="{{ $landingContent->contact_phone ?? '' }}">
            </div>
            <div class="col-md-4 mt-3">
                <label class="form-label fw-bold small">Contact Email</label>
                <input type="email" name="contact_email" class="form-control" value="{{ $landingContent->contact_email ?? '' }}">
            </div>
            <div class="col-md-4 mt-3">
                <label class="form-label fw-bold small">Contact Office/PG Address</label>
                <input type="text" name="contact_address" class="form-control" value="{{ $landingContent->contact_address ?? '' }}">
            </div>

            <!-- SEO METADATA -->
            <div class="col-12 mt-4"><h6 class="text-primary border-bottom pb-2 fw-bold">SEO & Metadata Configurations</h6></div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">SEO Title Tag</label>
                <input type="text" name="seo_title" class="form-control" value="{{ $landingContent->seo_title ?? '' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">SEO Description</label>
                <input type="text" name="seo_description" class="form-control" value="{{ $landingContent->seo_description ?? '' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">SEO Keywords</label>
                <input type="text" name="seo_keywords" class="form-control" value="{{ $landingContent->seo_keywords ?? '' }}">
            </div>

            <div class="col-12 text-end mt-5">
                <button type="submit" class="btn btn-primary px-5 rounded-pill py-3 fw-bold shadow-sm">Save All Changes</button>
            </div>
        </form>
    </div>
@elseif($tab === 'inquiries')
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="premium-card p-4 d-flex align-items-center gap-3">
                <div class="avatar-box bg-primary-soft text-primary"><i data-lucide="mail"></i></div>
                <div>
                    <h4 class="text-dark mb-0 fw-bold">{{ $activeTabInquiries->count() }}</h4>
                    <small class="text-muted text-xs">Total Inquiries Received</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="premium-card p-4 d-flex align-items-center gap-3">
                <div class="avatar-box bg-danger-soft text-danger"><i data-lucide="clock"></i></div>
                <div>
                    <h4 class="text-dark mb-0 fw-bold">{{ $activeTabInquiries->where('status', 'pending')->count() }}</h4>
                    <small class="text-muted text-xs">Pending Review</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="premium-card p-4 d-flex align-items-center gap-3">
                <div class="avatar-box bg-success-soft text-success"><i data-lucide="check-circle-2"></i></div>
                <div>
                    <h4 class="text-dark mb-0 fw-bold">{{ $activeTabInquiries->where('status', 'resolved')->count() }}</h4>
                    <small class="text-muted text-xs">Resolved / Read</small>
                </div>
            </div>
        </div>
    </div>

    <div class="premium-table-container">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Sender Details</th>
                        <th>Subject</th>
                        <th>Message Content</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeTabInquiries as $inq)
                        <tr>
                            <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $inq->name }}</span><br>
                                <a href="mailto:{{ $inq->email }}" class="text-primary text-xs">{{ $inq->email }}</a>
                            </td>
                            <td class="fw-bold text-dark">{{ $inq->subject }}</td>
                            <td>
                                <div class="text-muted small max-w-xs text-truncate" title="{{ $inq->message }}">
                                    {{ $inq->message }}
                                </div>
                            </td>
                            <td class="text-muted small">{{ $inq->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <span class="badge {{ $inq->status === 'resolved' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                                    {{ strtoupper($inq->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <form action="/admin/resolve-inquiry/{{ $inq->id }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $inq->status === 'resolved' ? 'btn-outline-secondary' : 'btn-success' }} rounded-pill">
                                            {{ $inq->status === 'resolved' ? 'Mark Pending' : 'Mark Read' }}
                                        </button>
                                    </form>
                                    <form action="/admin/delete-inquiry/{{ $inq->id }}" method="POST" onsubmit="return confirm('Permanently delete this inquiry from database?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i data-lucide="trash-2"></i> Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted p-5">
                                <i data-lucide="mail-open" class="mb-2" style="width: 48px; height: 48px;"></i>
                                <p class="mb-0">No visitor messages or inquiries received yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@elseif($tab === 'reports-mgr')
    <!-- Filter Panel -->
    <div class="premium-card p-4 mb-4 text-start">
        <h5 class="text-dark fw-bold mb-3"><i data-lucide="bar-chart-3" class="text-primary me-2"></i>Reports & Analytics Dashboard</h5>
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-xs fw-bold">Select PG Building</label>
                <select id="reportPgSelect" class="form-select form-select-sm rounded-pill" onchange="filterReportsData()">
                    <option value="all">All PGs (Aggregate)</option>
                    @foreach($pgs as $pg)
                        <option value="{{ $pg->id }}">{{ $pg->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-xs fw-bold">Filter Date</label>
                <input type="date" id="reportDateInput" class="form-control form-control-sm rounded-pill" onchange="filterReportsData()">
            </div>
            <div class="col-md-2">
                <label class="form-label text-xs fw-bold">Filter Month</label>
                <select id="reportMonthSelect" class="form-select form-select-sm rounded-pill" onchange="filterReportsData()">
                    <option value="all">All Months</option>
                    <option value="January">January</option>
                    <option value="February">February</option>
                    <option value="March">March</option>
                    <option value="April">April</option>
                    <option value="May">May</option>
                    <option value="June">June</option>
                    <option value="July">July</option>
                    <option value="August">August</option>
                    <option value="September">September</option>
                    <option value="October">October</option>
                    <option value="November">November</option>
                    <option value="December">December</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-xs fw-bold">Filter Year</label>
                <select id="reportYearSelect" class="form-select form-select-sm rounded-pill" onchange="filterReportsData()">
                    <option value="all">All Years</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026" selected>2026</option>
                    <option value="2027">2027</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs fw-bold">Search Table Records</label>
                <input type="text" id="reportSearchInput" class="form-control form-control-sm rounded-pill" placeholder="Type to search..." onkeyup="filterReportsData()">
            </div>
        </div>
    </div>

    <!-- Tab Selector -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <button class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-bold active-report-btn" id="btnReportStudents" onclick="switchReportTab('students')"><i data-lucide="users" class="d-inline me-1" style="width:14px; height:14px;"></i> Student Reports</button>
        <button class="btn btn-sm btn-secondary rounded-pill px-3 py-2 fw-bold" id="btnReportOccupancy" onclick="switchReportTab('occupancy')"><i data-lucide="door-closed" class="d-inline me-1" style="width:14px; height:14px;"></i> Occupancy & Vacancy</button>
        <button class="btn btn-sm btn-secondary rounded-pill px-3 py-2 fw-bold" id="btnReportRent" onclick="switchReportTab('rent')"><i data-lucide="credit-card" class="d-inline me-1" style="width:14px; height:14px;"></i> Rent & Payments</button>
        <button class="btn btn-sm btn-secondary rounded-pill px-3 py-2 fw-bold" id="btnReportIncome" onclick="switchReportTab('income')"><i data-lucide="wallet" class="d-inline me-1" style="width:14px; height:14px;"></i> Income & Expenses</button>
        <button class="btn btn-sm btn-secondary rounded-pill px-3 py-2 fw-bold" id="btnReportPending" onclick="switchReportTab('pending')"><i data-lucide="alert-circle" class="d-inline me-1" style="width:14px; height:14px;"></i> Pending Payments</button>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4 text-start">
        <div class="col-md-4 col-xl-2">
            <div class="premium-card p-3 h-100 border-start border-primary border-4">
                <small class="text-muted text-xs d-block">ACTIVE TENANTS</small>
                <h3 class="fw-bold text-dark mt-1 mb-0" id="kpiActiveTenants">0</h3>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="premium-card p-3 h-100 border-start border-success border-4">
                <small class="text-muted text-xs d-block">OCCUPANCY RATE</small>
                <h3 class="fw-bold text-dark mt-1 mb-0" id="kpiOccupancyRate">0%</h3>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="premium-card p-3 h-100 border-start border-info border-4">
                <small class="text-muted text-xs d-block">TOTAL INCOME (INR)</small>
                <h3 class="fw-bold text-dark mt-1 mb-0" id="kpiTotalIncome">₹0</h3>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="premium-card p-3 h-100 border-start border-warning border-4">
                <small class="text-muted text-xs d-block">TOTAL EXPENSES</small>
                <h3 class="fw-bold text-dark mt-1 mb-0" id="kpiTotalExpenses">₹0</h3>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="premium-card p-3 h-100 border-start border-danger border-4">
                <small class="text-muted text-xs d-block">PENDING OUTSTANDING</small>
                <h3 class="fw-bold text-dark mt-1 mb-0" id="kpiPendingDues">₹0</h3>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="premium-card p-3 h-100 border-start border-secondary border-4">
                <small class="text-muted text-xs d-block">NET NET MARGIN</small>
                <h3 class="fw-bold text-dark mt-1 mb-0" id="kpiNetMargin">₹0</h3>
            </div>
        </div>
    </div>

    <!-- Visual Charts Grid -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 text-start">
            <div class="premium-card p-4 h-100">
                <h6 class="text-dark fw-bold mb-3" id="chart1Title">Chart Title 1</h6>
                <div style="position: relative; height:240px;">
                    <canvas id="reportsChart1"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-start">
            <div class="premium-card p-4 h-100">
                <h6 class="text-dark fw-bold mb-3" id="chart2Title">Chart Title 2</h6>
                <div style="position: relative; height:240px;">
                    <canvas id="reportsChart2"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Registry & Download options -->
    <div class="premium-table-container text-start">
        <div class="p-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h6 class="text-dark fw-bold mb-0" id="reportTableTitle">Data Registry Table</h6>
            <!-- Download Button Dropdowns -->
            <div class="d-flex gap-2">
                <button class="btn btn-xs btn-outline-danger rounded-pill px-3 py-1" onclick="exportReport('pdf')"><i data-lucide="file-text" style="width:12px; height:12px;" class="me-1"></i> PDF</button>
                <button class="btn btn-xs btn-outline-success rounded-pill px-3 py-1" onclick="exportReport('excel')"><i data-lucide="file-spreadsheet" style="width:12px; height:12px;" class="me-1"></i> Excel</button>
                <button class="btn btn-xs btn-outline-primary rounded-pill px-3 py-1" onclick="exportReport('word')"><i data-lucide="file-box" style="width:12px; height:12px;" class="me-1"></i> Word</button>
                <button class="btn btn-xs btn-outline-secondary rounded-pill px-3 py-1" onclick="exportReport('csv')"><i data-lucide="file-code" style="width:12px; height:12px;" class="me-1"></i> CSV</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle text-start" id="reportsMainTable">
                <thead id="reportsTableHeader">
                    <!-- Loaded dynamically -->
                </thead>
                <tbody id="reportsTableBody">
                    <!-- Loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- ================= MODALS & POPUPS ================= -->

<!-- Add PG Building Modal -->
<div class="modal fade" id="addPgModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:24px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark">Register PG Building</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form action="/admin/add-pg" method="POST" id="addPgForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Building Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Grand Palace Residency" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" placeholder="Sector 4, Bangalore" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-2 py-2">Add PG Building</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:24px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark">Add New Room Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form action="/admin/add-room" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Select PG Building</label>
                        <select name="pg_building_id" class="form-select" required>
                            @foreach($pgs as $pg)
                                <option value="{{ $pg->id }}">{{ $pg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Room Number</label>
                        <input type="text" name="number" class="form-control" placeholder="104" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Room Type</label>
                        <select name="type" class="form-select">
                            <option value="Single Sharing">Single Sharing</option>
                            <option value="Double Sharing">Double Sharing</option>
                            <option value="Triple Sharing">Triple Sharing</option>
                            <option value="4 Sharing">4 Sharing</option>
                            <option value="5 Sharing">5 Sharing</option>
                            <option value="6 Sharing">6 Sharing</option>
                            <option value="7 Sharing">7 Sharing</option>
                            <option value="8 Sharing">8 Sharing</option>
                            <option value="9 Sharing">9 Sharing</option>
                            <option value="10 Sharing">10 Sharing</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monthly Rent Amount (INR)</label>
                        <input type="number" name="rent" class="form-control" placeholder="9000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bed Slots Capacity</label>
                        <input type="number" name="capacity" class="form-control" placeholder="2" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-2 py-2">Add Room Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:24px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark">Add Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form action="/admin/add-staff" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Ramesh Kumar" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="staff2@gmail.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="9876543210" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Staff Role</label>
                        <select name="staff_role" class="form-select" required>
                            <option value="Housekeeping">Housekeeping</option>
                            <option value="Food Management">Food Management</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign PG Building</label>
                        <select name="pg_building_id" class="form-select" required>
                            @foreach($pgs as $pg)
                                <option value="{{ $pg->id }}">{{ $pg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="staff123" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-2 py-2">Add Staff Member</button>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Log Room Maintenance Modal -->
<div class="modal fade" id="logRoomMaintenanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:24px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark">Log Room Maintenance Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form action="/admin/assign-room-maintenance" method="POST">
                    @csrf
                    <input type="hidden" name="room_number" id="modalMaintRoomNumber">
                    
                    <div class="mb-3">
                        <label class="form-label">Room Scope</label>
                        <input type="text" id="modalMaintRoomDisplay" class="form-control bg-light" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign Maintenance Engineer</label>
                        <select name="assigned_to" class="form-select" required>
                            @foreach($staffMembers->where('staff_role', 'Maintenance') as $mStaff)
                                <option value="{{ $mStaff->name }}">{{ $mStaff->name }} ({{ $pgs->find($mStaff->pg_building_id)->name ?? 'All PGs' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Maintenance Issue Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Specify what needs repair (e.g. Toilet flush leaking, WiFi router broken...)" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 py-2 rounded-pill fw-bold text-dark">Assign Maintenance Task</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hidden POST Actions forms used by JavaScript alerts prompts -->
<form id="jsPromptActionForm" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="rent" id="jsPromptRentInput">
    <input type="hidden" name="tx_id" id="jsPromptTxInput">
    <input type="hidden" name="method" id="jsPromptMethodInput">
    <input type="hidden" name="reply" id="jsPromptReplyInput">
    <input type="hidden" name="count" id="jsPromptCountInput">
    <input type="hidden" name="breakfast" id="jsPromptBreakfastInput">
    <input type="hidden" name="lunch" id="jsPromptLunchInput">
    <input type="hidden" name="dinner" id="jsPromptDinnerInput">
</form>
@endsection

@section('dashboard_scripts')
<script>
    // 1. Chart.js initializations for tab=overview
    @if($tab === 'overview')
        const months = @json($months);
        const paidData = @json($chartPaidData);
        const dueData = @json($chartDueData);
        const occupied = {{ $occupiedSlots }};
        const vacant = {{ $vacantSlots }};

        // Bar Chart Collection
        const ctxBar = document.getElementById('rentBarChart');
        if (ctxBar) {
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: months.length ? months : ['No Data'],
                    datasets: [
                        { label: 'Collection Paid', data: paidData.length ? paidData : [0], backgroundColor: '#16A34A', borderRadius: 8 },
                        { label: 'Outstandings Due', data: dueData.length ? dueData : [0], backgroundColor: '#DC2626', borderRadius: 8 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { labels: { font: { family: 'Poppins' } } } },
                    scales: {
                        x: { grid: { display: false } },
                        y: { grid: { color: '#F1F5F9' } }
                    }
                }
            });
        }

        // Pie Chart Occupancy
        const ctxPie = document.getElementById('occupancyPieChart');
        if (ctxPie) {
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: ['Occupied Slots', 'Vacant Slots'],
                    datasets: [{
                        data: (occupied + vacant) === 0 ? [0, 1] : [occupied, vacant],
                        backgroundColor: ['#2563EB', '#E2E8F0'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { font: { family: 'Poppins' } } } },
                    cutout: '70%'
                }
            });
        }
    @endif

    // 2. JavaScript Alerts Form Submitter helper prompts
    function editRentPrompt(id, currentRent) {
        Swal.fire({
            title: 'Edit Room Rent',
            input: 'number',
            inputValue: currentRent,
            inputLabel: 'Modify Monthly Rent Amount (INR)',
            confirmButtonText: 'Update Rent',
            showCancelButton: true,
            confirmButtonColor: '#2563EB'
        }).then((res) => {
            if (res.isConfirmed && res.value) {
                const form = document.getElementById('jsPromptActionForm');
                form.action = `/admin/edit-room-rent/${id}`;
                document.getElementById('jsPromptRentInput').value = res.value;
                form.submit();
            }
        });
    }

    function collectManualPrompt(id) {
        Swal.fire({
            title: 'Collect Rent Manually',
            html: `
                <input type="text" id="swal-utr" class="form-control mb-3" placeholder="Transaction ID (optional)">
                <select id="swal-method" class="form-select">
                    <option value="Cash at Reception">Cash at Reception</option>
                    <option value="Direct Bank Transfer">Direct Bank Transfer</option>
                    <option value="UPI Reconciled">UPI Reconciled</option>
                </select>
            `,
            showCancelButton: true,
            confirmButtonText: 'Collect Rent',
            confirmButtonColor: '#16A34A',
            preConfirm: () => {
                return {
                    tx: document.getElementById('swal-utr').value,
                    method: document.getElementById('swal-method').value
                }
            }
        }).then((res) => {
            if (res.isConfirmed) {
                const form = document.getElementById('jsPromptActionForm');
                form.action = `/admin/reconcile-payment/${id}`;
                document.getElementById('jsPromptTxInput').value = res.value.tx;
                document.getElementById('jsPromptMethodInput').value = res.value.method;
                form.submit();
            }
        });
    }

    function resolveComplaintPrompt(id) {
        Swal.fire({
            title: 'Resolve Ticket',
            input: 'text',
            inputPlaceholder: 'Add closing comment for tenant (e.g. WiFi fixed)',
            confirmButtonText: 'Close Ticket',
            showCancelButton: true,
            confirmButtonColor: '#16A34A'
        }).then((res) => {
            if (res.isConfirmed && res.value) {
                const form = document.getElementById('jsPromptActionForm');
                form.action = `/admin/resolve-complaint/${id}`;
                document.getElementById('jsPromptReplyInput').value = res.value;
                form.submit();
            }
        });
    }

    function adjustInventoryPrompt(id, currentCount) {
        Swal.fire({
            title: 'Adjust Stock Quantity',
            input: 'number',
            inputValue: currentCount,
            confirmButtonText: 'Apply Quantity',
            showCancelButton: true,
            confirmButtonColor: '#2563EB'
        }).then((res) => {
            if (res.isConfirmed && res.value) {
                const form = document.getElementById('jsPromptActionForm');
                form.action = `/admin/adjust-inventory/${id}`;
                document.getElementById('jsPromptCountInput').value = res.value;
                form.submit();
            }
        });
    }

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
                const form = document.getElementById('jsPromptActionForm');
                form.action = `/admin/update-menu/${id}`;
                document.getElementById('jsPromptBreakfastInput').value = res.value.b;
                document.getElementById('jsPromptLunchInput').value = res.value.l;
                document.getElementById('jsPromptDinnerInput').value = res.value.d;
                form.submit();
            }
        });
    }

    // PDF receipt downloader function using jsPDF
    function downloadReceiptPDF(id, name, room, month, amount, tx, method, date) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.setFillColor(15, 23, 42);
        doc.rect(0, 0, 210, 50, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(22);
        doc.text('PG MANAGEMENT SYSTEM', 20, 25);
        doc.setFontSize(10);
        doc.setFont('helvetica', 'normal');
        doc.text('OFFICIAL RENT RECEIPT', 20, 35);
        doc.setTextColor(50, 50, 50);
        doc.setFontSize(11);
        doc.setFont('helvetica', 'bold');
        doc.text('Transaction Details:', 20, 65);
        doc.setFont('helvetica', 'normal');
        const rows = [
            ['Receipt Ref Number:', `REC-${id}`],
            ['Tenant Full Name:', name],
            ['Room Number:', `#${room}`],
            ['Billed Month:', month],
            ['Rent Amount Reconciled:', `INR ${parseInt(amount).toLocaleString()}.00`],
            ['UTR Reference Code:', tx],
            ['Payment Method Channel:', method],
            ['Payment Reconciled Date:', date]
        ];
        let y = 75;
        rows.forEach(r => {
            doc.setFont('helvetica', 'bold');
            doc.text(r[0], 20, y);
            doc.setFont('helvetica', 'normal');
            doc.text(r[1], 75, y);
            y += 10;
        });
        doc.rect(20, y + 10, 170, 30, 'S');
        doc.setFont('helvetica', 'bold');
        doc.text('Reconciliation Stamp', 30, y + 20);
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(9);
        doc.text('Verified Transaction logged digitally by XAMPP MySQL Admin panel.', 30, y + 27);
        doc.save(`Receipt_${month.replace(' ', '_')}_${id}.pdf`);
    }

    function openRoomMaintenanceModal(roomNumber) {
        document.getElementById('modalMaintRoomNumber').value = roomNumber;
        document.getElementById('modalMaintRoomDisplay').value = "Room #" + roomNumber;
        const modal = new bootstrap.Modal(document.getElementById('logRoomMaintenanceModal'));
        modal.show();
    }

    function openEditInvoiceModal(id, month, amount, status, paymentDate, txId, method) {
        document.getElementById('editInvoiceForm').action = `/admin/update-invoice/${id}`;
        document.getElementById('editInvoiceMonth').value = month;
        document.getElementById('editInvoiceAmount').value = amount;
        document.getElementById('editInvoiceStatus').value = status;
        document.getElementById('editInvoicePaymentDate').value = paymentDate || '';
        document.getElementById('editInvoiceTxId').value = txId || '';
        document.getElementById('editInvoiceMethod').value = method || '';
        
        const modal = new bootstrap.Modal(document.getElementById('editInvoiceModal'));
        modal.show();
    }

    // ==========================================
    // REPORTS & ANALYTICS CLIENT-SIDE JS ENGINE
    // ==========================================
    let activeReportTab = 'students';
    let chart1 = null;
    let chart2 = null;

    // Load Database Records
    const rRooms = @json($allRooms ?? []);
    const rPayments = @json($allPayments ?? []);
    const rStudents = @json($allStudents ?? []);
    const rComplaints = @json($allComplaints ?? []);
    const rPgs = @json($pgs ?? []);

    function switchReportTab(tabName) {
        activeReportTab = tabName;
        const btns = ['btnReportStudents', 'btnReportOccupancy', 'btnReportRent', 'btnReportIncome', 'btnReportPending'];
        btns.forEach(id => {
            const btn = document.getElementById(id);
            if (btn) {
                btn.className = 'btn btn-sm btn-secondary rounded-pill px-3 py-2 fw-bold';
            }
        });
        const activeId = 'btnReport' + tabName.charAt(0).toUpperCase() + tabName.slice(1);
        const activeBtn = document.getElementById(activeId);
        if (activeBtn) {
            activeBtn.className = 'btn btn-sm btn-primary rounded-pill px-3 py-2 fw-bold active-report-btn';
        }
        document.getElementById('reportSearchInput').value = '';
        filterReportsData();
    }

    function getFilteredData() {
        const pg = document.getElementById('reportPgSelect').value;
        const fDate = document.getElementById('reportDateInput').value;
        const fMonth = document.getElementById('reportMonthSelect').value;
        const fYear = document.getElementById('reportYearSelect').value;
        const search = document.getElementById('reportSearchInput').value.toLowerCase();

        // 1. Filter Students
        let filteredStudents = rStudents.filter(s => {
            if (pg !== 'all' && s.pg_building_id != pg) return false;
            if (fDate && s.created_at && !s.created_at.startsWith(fDate)) return false;
            if (fMonth !== 'all' && s.created_at) {
                const dateObj = new Date(s.created_at);
                if (dateObj.toLocaleString('default', { month: 'long' }) !== fMonth) return false;
            }
            if (fYear !== 'all' && s.created_at) {
                const dateObj = new Date(s.created_at);
                if (dateObj.getFullYear() != fYear) return false;
            }
            if (search && !(s.name.toLowerCase().includes(search) || s.email.toLowerCase().includes(search))) return false;
            return true;
        });

        // 2. Filter Rooms
        let filteredRooms = rRooms.filter(r => {
            if (pg !== 'all' && r.pg_building_id != pg) return false;
            const pgObj = rPgs.find(p => p.id == r.pg_building_id);
            const pgName = pgObj ? pgObj.name.toLowerCase() : '';
            if (search && !(r.number.toLowerCase().includes(search) || r.type.toLowerCase().includes(search) || pgName.includes(search))) return false;
            return true;
        });

        // 3. Filter Payments
        let filteredPayments = rPayments.filter(p => {
            if (pg !== 'all' && p.pg_building_id != pg) return false;
            if (fDate && p.payment_date && !p.payment_date.startsWith(fDate)) return false;
            if (fMonth !== 'all' && !p.month.includes(fMonth)) return false;
            if (fYear !== 'all' && !p.month.includes(fYear)) return false;
            if (search && !(p.student_email.toLowerCase().includes(search) || p.room_number.toLowerCase().includes(search) || (p.method && p.method.toLowerCase().includes(search)))) return false;
            return true;
        });

        // 4. Filter Complaints (Expenses repair items)
        let filteredComplaints = rComplaints.filter(c => {
            const studentObj = rStudents.find(s => s.email === c.student_email);
            if (pg !== 'all' && (!studentObj || studentObj.pg_building_id != pg)) return false;
            if (fDate && c.created_date && !c.created_date.startsWith(fDate)) return false;
            if (fMonth !== 'all' && c.created_date) {
                const dateObj = new Date(c.created_date);
                if (dateObj.toLocaleString('default', { month: 'long' }) !== fMonth) return false;
            }
            if (fYear !== 'all' && c.created_date) {
                const dateObj = new Date(c.created_date);
                if (dateObj.getFullYear() != fYear) return false;
            }
            if (search && !(c.student_name.toLowerCase().includes(search) || c.title.toLowerCase().includes(search))) return false;
            return true;
        });

        return { filteredStudents, filteredRooms, filteredPayments, filteredComplaints };
    }

    function renderReportTable(data) {
        const thead = document.getElementById('reportsTableHeader');
        const tbody = document.getElementById('reportsTableBody');
        const title = document.getElementById('reportTableTitle');

        if (!thead || !tbody) return;

        thead.innerHTML = '';
        tbody.innerHTML = '';

        if (activeReportTab === 'students') {
            title.innerText = 'Tenant Database Records Roster';
            thead.innerHTML = `
                <tr>
                    <th style="width: 60px;">S.No.</th>
                    <th>Tenant Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Room Assigned</th>
                    <th>College / Course</th>
                    <th>Status</th>
                </tr>
            `;
            data.filteredStudents.forEach((s, idx) => {
                tbody.innerHTML += `
                    <tr>
                        <td class="fw-bold">${idx + 1}</td>
                        <td class="fw-bold text-dark">${s.name}</td>
                        <td>${s.email}</td>
                        <td>${s.phone || '-'}</td>
                        <td>Room #${s.room_number || 'Unassigned'}</td>
                        <td>${s.college || '-'} (${s.course || '-'})</td>
                        <td>
                            <span class="badge ${s.approval_status === 'approved' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger'}">
                                ${s.approval_status.toUpperCase()}
                            </span>
                        </td>
                    </tr>
                `;
            });
        } else if (activeReportTab === 'occupancy') {
            title.innerText = 'Room Occupancy and Vacancy Registry';
            thead.innerHTML = `
                <tr>
                    <th style="width: 60px;">S.No.</th>
                    <th>Room Number</th>
                    <th>PG Building</th>
                    <th>Sharing Type</th>
                    <th>Bed Slots Capacity</th>
                    <th>Occupied Beds</th>
                    <th>Vacant Slots</th>
                    <th>Cleaning Status</th>
                </tr>
            `;
            data.filteredRooms.forEach((r, idx) => {
                const pgObj = rPgs.find(p => p.id == r.pg_building_id);
                const pgName = pgObj ? pgObj.name : 'Unknown';
                const vacant = r.capacity - r.occupied;
                tbody.innerHTML += `
                    <tr>
                        <td class="fw-bold">${idx + 1}</td>
                        <td class="fw-bold text-dark">#${r.number}</td>
                        <td class="text-primary fw-bold">${pgName}</td>
                        <td>${r.type}</td>
                        <td>${r.capacity} Beds</td>
                        <td><span class="badge bg-primary-soft text-primary">${r.occupied} Occupied</span></td>
                        <td><span class="badge ${vacant > 0 ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger'}">${vacant} Vacant</span></td>
                        <td>
                            <span class="badge ${r.cleaning_status === 'Cleaned' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger'}">
                                ${r.cleaning_status || 'Dirty'}
                            </span>
                        </td>
                    </tr>
                `;
            });
        } else if (activeReportTab === 'rent') {
            title.innerText = 'Rent and Payment Transaction Registry';
            thead.innerHTML = `
                <tr>
                    <th style="width: 60px;">S.No.</th>
                    <th>Tenant Name</th>
                    <th>Tenant Email</th>
                    <th>Room</th>
                    <th>Billing Month</th>
                    <th>Rent Amount</th>
                    <th>Payment Status</th>
                    <th>Payment Date</th>
                    <th>Transaction UTR</th>
                    <th>Channel Method</th>
                </tr>
            `;
            data.filteredPayments.forEach((p, idx) => {
                const studentObj = rStudents.find(s => s.email === p.student_email);
                const tenantName = studentObj ? studentObj.name : 'Unknown';
                tbody.innerHTML += `
                    <tr>
                        <td class="fw-bold">${idx + 1}</td>
                        <td class="fw-bold text-dark">${tenantName}</td>
                        <td>${p.student_email}</td>
                        <td>#${p.room_number}</td>
                        <td>${p.month}</td>
                        <td class="text-success fw-bold">₹${parseInt(p.amount).toLocaleString()}</td>
                        <td>
                            <span class="badge ${p.status === 'Paid' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger'}">
                                ${p.status.toUpperCase()}
                            </span>
                        </td>
                        <td>${p.payment_date || '-'}</td>
                        <td><code class="text-xs text-muted">${p.tx_id || '-'}</code></td>
                        <td>${p.method || '-'}</td>
                    </tr>
                `;
            });
        } else if (activeReportTab === 'income') {
            title.innerText = 'Income and Maintenance Expense Registry';
            thead.innerHTML = `
                <tr>
                    <th style="width: 60px;">S.No.</th>
                    <th>Ref Item / Category</th>
                    <th>Title / Description</th>
                    <th>Type</th>
                    <th>Date Logged</th>
                    <th>Total Cash Flow (INR)</th>
                </tr>
            `;
            let sNo = 1;
            data.filteredPayments.filter(p => p.status === 'Paid').forEach(p => {
                tbody.innerHTML += `
                    <tr>
                        <td class="fw-bold">${sNo++}</td>
                        <td class="fw-bold text-dark">Rent Payment (ID: #${p.id})</td>
                        <td>Collected rent for month: ${p.month} via ${p.method || 'Offline'}</td>
                        <td><span class="badge bg-success-soft text-success">INCOME</span></td>
                        <td>${p.payment_date || p.created_at.split('T')[0]}</td>
                        <td class="text-success fw-bold">+ ₹${parseInt(p.amount).toLocaleString()}</td>
                    </tr>
                `;
            });
            data.filteredComplaints.filter(c => c.status === 'Resolved' && c.repair_expense > 0).forEach(c => {
                tbody.innerHTML += `
                    <tr>
                        <td class="fw-bold">${sNo++}</td>
                        <td class="fw-bold text-dark">Maintenance (Ticket: #${c.id})</td>
                        <td>${c.title} &mdash; ${c.description}</td>
                        <td><span class="badge bg-danger-soft text-danger">EXPENSE</span></td>
                        <td>${c.created_date}</td>
                        <td class="text-danger fw-bold">- ₹${parseInt(c.repair_expense).toLocaleString()}</td>
                    </tr>
                `;
            });
        } else if (activeReportTab === 'pending') {
            title.innerText = 'Pending Outstanding Payment Dues Roster';
            thead.innerHTML = `
                <tr>
                    <th style="width: 60px;">S.No.</th>
                    <th>Tenant Name</th>
                    <th>Tenant Email</th>
                    <th>Room</th>
                    <th>Billing Month</th>
                    <th>Dues Amount</th>
                    <th>Payment Status</th>
                </tr>
            `;
            data.filteredPayments.filter(p => p.status === 'Due').forEach((p, idx) => {
                const studentObj = rStudents.find(s => s.email === p.student_email);
                const tenantName = studentObj ? studentObj.name : 'Unknown';
                tbody.innerHTML += `
                    <tr>
                        <td class="fw-bold">${idx + 1}</td>
                        <td class="fw-bold text-dark">${tenantName}</td>
                        <td>${p.student_email}</td>
                        <td>#${p.room_number}</td>
                        <td>${p.month}</td>
                        <td class="text-danger fw-bold">₹${parseInt(p.amount).toLocaleString()}</td>
                        <td><span class="badge bg-danger-soft text-danger">DUE / OUTSTANDING</span></td>
                    </tr>
                `;
            });
        }
        lucide.createIcons();
    }

    function updateReportsCharts(data) {
        const canvas1 = document.getElementById('reportsChart1');
        const canvas2 = document.getElementById('reportsChart2');
        if (!canvas1 || !canvas2) return;

        if (chart1) chart1.destroy();
        if (chart2) chart2.destroy();

        const ctx1 = canvas1.getContext('2d');
        const ctx2 = canvas2.getContext('2d');

        if (activeReportTab === 'students') {
            document.getElementById('chart1Title').innerText = 'Students Distribution by Course';
            document.getElementById('chart2Title').innerText = 'Students Distribution by College';

            const courseCounts = {};
            data.filteredStudents.forEach(s => {
                const c = s.course || 'Unknown';
                courseCounts[c] = (courseCounts[c] || 0) + 1;
            });

            const collegeCounts = {};
            data.filteredStudents.forEach(s => {
                const col = s.college || 'Unknown';
                collegeCounts[col] = (collegeCounts[col] || 0) + 1;
            });

            chart1 = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: Object.keys(courseCounts),
                    datasets: [{
                        label: 'Tenants',
                        data: Object.values(courseCounts),
                        backgroundColor: 'rgba(37, 99, 235, 0.65)',
                        borderColor: 'rgb(37, 99, 235)',
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            chart2 = new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: Object.keys(collegeCounts),
                    datasets: [{
                        data: Object.values(collegeCounts),
                        backgroundColor: ['#2563EB', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        } else if (activeReportTab === 'occupancy') {
            document.getElementById('chart1Title').innerText = 'Room Occupancy Status (Beds)';
            document.getElementById('chart2Title').innerText = 'Room Cleaning Status';

            const totalBeds = data.filteredRooms.reduce((acc, r) => acc + r.capacity, 0);
            const occupiedBeds = data.filteredRooms.reduce((acc, r) => acc + r.occupied, 0);
            const vacantBeds = totalBeds - occupiedBeds;

            const cleanedRooms = data.filteredRooms.filter(r => r.cleaning_status === 'Cleaned').length;
            const dirtyRooms = data.filteredRooms.length - cleanedRooms;

            chart1 = new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: ['Occupied Beds', 'Vacant Bed Slots'],
                    datasets: [{
                        data: [occupiedBeds, vacantBeds],
                        backgroundColor: ['#2563EB', '#10B981']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            chart2 = new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: ['Cleaned Rooms', 'Dirty / Pending Rooms'],
                    datasets: [{
                        data: [cleanedRooms, dirtyRooms],
                        backgroundColor: ['#10B981', '#EF4444']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        } else if (activeReportTab === 'rent' || activeReportTab === 'pending') {
            document.getElementById('chart1Title').innerText = 'Collected vs Due Invoices (Amount)';
            document.getElementById('chart2Title').innerText = 'Payment Methods Distribution';

            const paidSum = data.filteredPayments.filter(p => p.status === 'Paid').reduce((acc, p) => acc + p.amount, 0);
            const dueSum = data.filteredPayments.filter(p => p.status === 'Due').reduce((acc, p) => acc + p.amount, 0);

            const methods = {};
            data.filteredPayments.filter(p => p.status === 'Paid').forEach(p => {
                const m = p.method || 'Cash at Reception';
                methods[m] = (methods[m] || 0) + p.amount;
            });

            chart1 = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: ['Paid collected', 'Outstanding Dues'],
                    datasets: [{
                        label: 'Amount (INR)',
                        data: [paidSum, dueSum],
                        backgroundColor: ['rgba(16, 185, 129, 0.65)', 'rgba(239, 68, 68, 0.65)'],
                        borderColor: ['#10B981', '#EF4444'],
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            chart2 = new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: Object.keys(methods),
                    datasets: [{
                        data: Object.values(methods),
                        backgroundColor: ['#2563EB', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        } else if (activeReportTab === 'income') {
            document.getElementById('chart1Title').innerText = 'Cash Flow Statement: Income vs Expense';
            document.getElementById('chart2Title').innerText = 'Income & Expense Distribution Pie';

            const totalInc = data.filteredPayments.filter(p => p.status === 'Paid').reduce((acc, p) => acc + p.amount, 0);
            const totalExp = data.filteredComplaints.filter(c => c.status === 'Resolved' && c.repair_expense > 0).reduce((acc, c) => acc + c.repair_expense, 0);

            chart1 = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: ['Total Income', 'Total Expenses'],
                    datasets: [{
                        label: 'Amount (INR)',
                        data: [totalInc, totalExp],
                        backgroundColor: ['rgba(16, 185, 129, 0.65)', 'rgba(239, 68, 68, 0.65)'],
                        borderColor: ['#10B981', '#EF4444'],
                        borderWidth: 1
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            chart2 = new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: ['Income (Rents)', 'Expenses (Repairs)'],
                    datasets: [{
                        data: [totalInc, totalExp],
                        backgroundColor: ['#10B981', '#EF4444']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    }

    function calculateKPIs(data) {
        // Active Tenants
        const activeTenants = data.filteredStudents.length;
        document.getElementById('kpiActiveTenants').innerText = activeTenants;

        // Occupancy Rate
        const totalBeds = data.filteredRooms.reduce((acc, r) => acc + r.capacity, 0);
        const occupiedBeds = data.filteredRooms.reduce((acc, r) => acc + r.occupied, 0);
        const occupancyRate = totalBeds > 0 ? Math.round((occupiedBeds / totalBeds) * 100) : 0;
        document.getElementById('kpiOccupancyRate').innerText = `${occupancyRate}%`;

        // Total Income
        const totalIncome = data.filteredPayments.filter(p => p.status === 'Paid').reduce((acc, p) => acc + p.amount, 0);
        document.getElementById('kpiTotalIncome').innerText = `₹${totalIncome.toLocaleString()}`;

        // Total Expenses (Resolved Complaints with repairs cost)
        const totalExpenses = data.filteredComplaints.filter(c => c.status === 'Resolved' && c.repair_expense > 0).reduce((acc, c) => acc + c.repair_expense, 0);
        document.getElementById('kpiTotalExpenses').innerText = `₹${totalExpenses.toLocaleString()}`;

        // Outstanding Dues
        const pendingDues = data.filteredPayments.filter(p => p.status === 'Due').reduce((acc, p) => acc + p.amount, 0);
        document.getElementById('kpiPendingDues').innerText = `₹${pendingDues.toLocaleString()}`;

        // Net Margin
        const netMargin = totalIncome - totalExpenses;
        document.getElementById('kpiNetMargin').innerText = `${netMargin >= 0 ? '+' : ''}₹${netMargin.toLocaleString()}`;
    }

    function filterReportsData() {
        const data = getFilteredData();
        calculateKPIs(data);
        renderReportTable(data);
        updateReportsCharts(data);
    }

    function exportReport(format) {
        const table = document.getElementById('reportsMainTable');
        if (!table) return;

        const titleText = document.getElementById('reportTableTitle').innerText;
        let rows = Array.from(table.rows);
        let csvContent = "";

        rows.forEach(row => {
            let cols = Array.from(row.cells).map(c => '"' + c.innerText.trim().replace(/\r?\n|\r/g, ' ').replace(/"/g, '""') + '"');
            csvContent += cols.join(",") + "\r\n";
        });

        if (format === 'csv') {
            const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = `${titleText.toLowerCase().replace(/ /g, '_')}.csv`;
            link.click();
        } else if (format === 'excel') {
            let htmlContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>${titleText}</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
                    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; font-size: 11pt; }
                    th { background-color: #f2f2f2; font-weight: bold; }
                </style>
            </head>
            <body>
                <h2>${titleText}</h2>
                <p style="font-size: 10pt; color: #555;">Generated on: ${new Date().toLocaleString()}</p>
                <table border="1">${table.innerHTML}</table>
            </body>
            </html>`;
            const blob = new Blob(["\uFEFF" + htmlContent], { type: 'application/vnd.ms-excel;charset=utf-8;' });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = `${titleText.toLowerCase().replace(/ /g, '_')}.xls`;
            link.click();
        } else if (format === 'word') {
            let htmlContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>${titleText}</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
                    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; font-size: 11pt; }
                    th { background-color: #f2f2f2; font-weight: bold; }
                </style>
            </head>
            <body>
                <h2>${titleText}</h2>
                <p style="font-size: 10pt; color: #555;">Generated on: ${new Date().toLocaleString()}</p>
                <table border="1">${table.innerHTML}</table>
            </body>
            </html>`;
            const blob = new Blob([htmlContent], { type: 'application/msword;charset=utf-8;' });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = `${titleText.toLowerCase().replace(/ /g, '_')}.doc`;
            link.click();
        } else if (format === 'pdf') {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4'); // Landscape is much better for wide tables
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(16);
            doc.text(titleText, 14, 15);
            doc.setFontSize(9);
            doc.setFont('helvetica', 'normal');
            doc.text(`Generated on: ${new Date().toLocaleString()}`, 14, 22);

            let y = 32;
            const pageWidth = doc.internal.pageSize.getWidth();
            const margin = 14;
            const usableWidth = pageWidth - (margin * 2);
            
            rows.forEach((row, rowIdx) => {
                const cells = Array.from(row.cells);
                const numCols = cells.length;
                const colWidth = usableWidth / numCols;

                if (y > 185) { // A4 landscape height is 210mm
                    doc.addPage();
                    y = 20;
                }

                cells.forEach((cell, colIdx) => {
                    const x = margin + (colIdx * colWidth);
                    let cellText = cell.innerText.trim().replace(/\r?\n|\r/g, ' ');
                    
                    if (rowIdx === 0) {
                        doc.setFont('helvetica', 'bold');
                        doc.setFontSize(9);
                        doc.setFillColor(240, 240, 240);
                        doc.rect(x, y - 5, colWidth, 7, 'F');
                        doc.setTextColor(50, 50, 50);
                    } else {
                        doc.setFont('helvetica', 'normal');
                        doc.setFontSize(8);
                        doc.setTextColor(80, 80, 80);
                    }
                    
                    doc.text(cellText, x + 2, y, { maxWidth: colWidth - 4 });
                });

                // Draw thin divider line under the row
                doc.setDrawColor(220, 220, 220);
                doc.line(margin, y + 2, margin + usableWidth, y + 2);

                y += 8;
            });
            doc.save(`${titleText.toLowerCase().replace(/ /g, '_')}.pdf`);
        }
    }

    // Dynamic Row Helpers for Landing Page Editor
    function addFacilityRow() {
        const container = document.getElementById('facilitiesContainer');
        const index = container.querySelectorAll('.facility-row').length;
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-end facility-row border p-2 rounded';
        row.innerHTML = `
            <div class="col-md-3">
                <label class="form-label text-xs">Icon Name (Lucide)</label>
                <input type="text" name="facilities[${index}][icon]" class="form-control form-control-sm" placeholder="e.g. wifi, shield, utensils" required>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs">Amenity Name</label>
                <input type="text" name="facilities[${index}][name]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-5">
                <label class="form-label text-xs">Brief Description</label>
                <input type="text" name="facilities[${index}][desc]" class="form-control form-control-sm">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="this.closest('.facility-row').remove()"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
            </div>
        `;
        container.appendChild(row);
        lucide.createIcons();
    }

    function addPricingRow() {
        const container = document.getElementById('pricingContainer');
        const index = container.querySelectorAll('.pricing-row').length;
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-end pricing-row border p-2 rounded';
        row.innerHTML = `
            <div class="col-md-2">
                <label class="form-label text-xs">Package Name</label>
                <input type="text" name="pricing_plans[${index}][name]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-2">
                <label class="form-label text-xs">Price Label</label>
                <input type="text" name="pricing_plans[${index}][price]" class="form-control form-control-sm" placeholder="e.g. ₹8,000 / month" required>
            </div>
            <div class="col-md-2">
                <label class="form-label text-xs">Brief Description</label>
                <input type="text" name="pricing_plans[${index}][desc]" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs">Features (comma-separated)</label>
                <input type="text" name="pricing_plans[${index}][features]" class="form-control form-control-sm" placeholder="e.g. AC, Wi-Fi, Food">
            </div>
            <div class="col-md-2">
                <label class="form-label text-xs">Room Image (Upload)</label>
                <input type="file" name="pricing_plans_files[${index}]" class="form-control form-control-sm" accept="image/*">
                <input type="hidden" name="pricing_plans[${index}][image_url]" value="">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="this.closest('.pricing-row').remove()"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
            </div>
        `;
        container.appendChild(row);
        lucide.createIcons();
    }

    function addLocationRow() {
        const container = document.getElementById('locationsContainer');
        const index = container.querySelectorAll('.location-row').length;
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-end location-row border p-2 rounded';
        row.innerHTML = `
            <div class="col-md-3">
                <label class="form-label text-xs">City & Area Name</label>
                <input type="text" name="locations[${index}][city]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-4">
                <label class="form-label text-xs">Description / Proximity Details</label>
                <input type="text" name="locations[${index}][area]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-4">
                <label class="form-label text-xs">Google Map Embed URL (Optional)</label>
                <input type="text" name="locations[${index}][map_url]" class="form-control form-control-sm" placeholder="https://www.google.com/maps/embed?pb=...">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="this.closest('.location-row').remove()"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
            </div>
        `;
        container.appendChild(row);
        lucide.createIcons();
    }

    function addTestimonialRow() {
        const container = document.getElementById('testimonialsContainer');
        const index = container.querySelectorAll('.testimonial-row').length;
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-end testimonial-row border p-2 rounded';
        row.innerHTML = `
            <div class="col-md-3">
                <label class="form-label text-xs">Reviewer Name</label>
                <input type="text" name="testimonials[${index}][name]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-2">
                <label class="form-label text-xs">Role / Designation</label>
                <input type="text" name="testimonials[${index}][role]" class="form-control form-control-sm" placeholder="e.g. Student, VIT" required>
            </div>
            <div class="col-md-6">
                <label class="form-label text-xs">Feedback Review Text</label>
                <input type="text" name="testimonials[${index}][review]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="this.closest('.testimonial-row').remove()"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
            </div>
        `;
        container.appendChild(row);
        lucide.createIcons();
    }

    document.addEventListener("DOMContentLoaded", () => {
        if (window.location.search.includes("tab=reports-mgr")) {
            filterReportsData();
        }
    });
</script>

<!-- EDIT INVOICE MODAL -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:24px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i data-lucide="edit-3" class="text-primary me-2"></i>Edit Payment Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form id="editInvoiceForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Payment Type / Description</label>
                        <input type="text" name="month" id="editInvoiceMonth" class="form-control" list="modalPaymentTypesList" placeholder="e.g. Academic Payment" required>
                        <datalist id="modalPaymentTypesList">
                            <option value="Academic Payment"></option>
                            <option value="Semester Payment"></option>
                            <option value="Part Payment"></option>
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Amount (INR)</label>
                        <input type="number" name="amount" id="editInvoiceAmount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Status</label>
                        <select name="status" id="editInvoiceStatus" class="form-select" required>
                            <option value="Due">Due Outstanding</option>
                            <option value="Paid">Paid</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Date (if Paid)</label>
                        <input type="text" name="payment_date" id="editInvoicePaymentDate" class="form-control" placeholder="YYYY-MM-DD or custom text">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">UTR / Transaction Reference ID</label>
                        <input type="text" name="tx_id" id="editInvoiceTxId" class="form-control" placeholder="12-digit reference number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="method" id="editInvoiceMethod" class="form-select">
                            <option value="">-- None --</option>
                            <option value="UPI">Scan QR Code</option>
                            <option value="PhonePe">PhonePe Transfer</option>
                            <option value="GPay">Google Pay (GPay)</option>
                            <option value="Paytm">Paytm Transfer</option>
                            <option value="Cash at Reception">Cash at Reception</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
