@extends('layouts.dashboard')

@section('title', 'Tenant Dashboard | PG Management System')
@section('page_title')
    <span class="text-uppercase text-success small fw-bold">Student Portal</span> &mdash; 
    @if($tab === 'overview') My Accommodation
    @elseif($tab === 'rent') Rent Dues & Receipts
    @elseif($tab === 'food') Exclusions Food Preference
    @elseif($tab === 'complaints') Raise Maintenance Complaint
    @elseif($tab === 'notices') Notice Board
    @endif
@endsection

@section('dashboard_content')

<!-- ================= 1. TAB: ROOM OVERVIEW ================= -->
@if($tab === 'overview')
    <div class="row g-4 text-start">
        <!-- Room details -->
        <div class="col-lg-6">
            <div class="premium-card h-100 position-relative overflow-hidden" style="background-color: var(--card-bg);">
                <h5 class="text-dark fw-bold mb-4"><i data-lucide="door-closed" class="text-primary me-2"></i>My Room Details</h5>
                <div class="row g-3 mt-2">
                    <div class="col-6">
                        <small class="text-muted text-xs d-block">Room Number</small>
                        <span class="text-dark fw-bold fs-5">#{{ $student->room_number ?: 'Unassigned' }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-xs d-block">Sharing Configuration</small>
                        <span class="text-dark fw-bold fs-5">{{ $student->room_type ?: 'N/A' }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-xs d-block">Monthly Rent Amount</small>
                        <span class="text-primary fw-bold fs-5">₹{{ $room ? number_format($room->rent) : '0.00' }}/mo</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-xs d-block">PG Building Name</small>
                        <span class="text-dark fw-bold fs-5">{{ $pg->name ?? 'Unassigned Building' }}</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted text-xs d-block">PG Address</small>
                        <span class="text-muted small">{{ $pg->address ?? 'N/A' }}</span>
                    </div>
                    <div class="col-12 mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted text-xs d-block">Daily Cleaning Status</small>
                            <span class="badge {{ ($room->cleaning_status ?? 'Dirty') === 'Cleaned' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }} px-3 py-1 mt-1 rounded-pill fw-bold" style="font-size:11px;">
                                {{ strtoupper($room->cleaning_status ?? 'Dirty') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roommates list -->
        <div class="col-lg-6">
            <div class="premium-card h-100">
                <h5 class="text-dark fw-bold mb-4"><i data-lucide="users" class="text-success me-2"></i>My Roommates</h5>
                <div class="list-group list-group-flush">
                    @forelse($roommates as $m)
                        <div class="list-group-item bg-transparent px-0 py-3 d-flex align-items-center gap-3">
                            @if(!empty($m->profile_photo))
                                <img src="{{ $m->profile_photo }}" class="rounded-circle border" style="width: 44px; height: 44px; object-fit: cover;">
                            @else
                                <div class="avatar-box d-flex align-items-center justify-content-center text-secondary border bg-light text-xs font-bold text-center" style="width: 44px; height: 44px; border-radius: 50%; font-size: 9px; line-height: 1.1; padding: 2px;">
                                    No profile photo
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0 fw-bold small text-dark">{{ $m->name }}</h6>
                                <small class="text-muted text-xs">{{ $m->course }} | Year {{ $m->year }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted py-4 small">No other roommates are currently assigned to this room profile.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

<!-- ================= 2. TAB: RENT & ledgers ================= -->
@elseif($tab === 'rent')
    <!-- Invoice summaries cards -->
    <div class="row g-3 mb-4 text-start">
        <div class="col-md-4">
            <div class="premium-card p-4 h-100 d-flex align-items-center gap-3">
                <div class="avatar-box bg-success-soft text-success" style="background-color:rgba(22, 163, 74, 0.1);"><i data-lucide="check-square"></i></div>
                <div>
                    <h4 class="text-dark mb-0 fw-bold">₹{{ number_format($totalPaid) }}</h4>
                    <small class="text-muted text-xs">Total Rent Paid to Date</small>
                </div>
            </div>
        </div>
        
        @if($dues->count() === 0)
            <div class="col-md-8">
                <div class="premium-card p-4 h-100 bg-success-soft bg-opacity-5 border-success border-opacity-25 d-flex align-items-center gap-3">
                    <div class="avatar-box bg-success-soft text-success"><i data-lucide="thumbs-up"></i></div>
                    <div>
                        <h5 class="text-dark mb-0 fw-bold">No Due Outstanding!</h5>
                        <small class="text-muted text-xs">All bills reconciled. Great job!</small>
                    </div>
                </div>
            </div>
        @else
            @foreach($dues as $due)
                <div class="col-md-8">
                    <div class="premium-card p-4 h-100 bg-danger-soft bg-opacity-5 border-danger border-opacity-25 d-flex align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-box bg-danger-soft text-danger" style="background-color:rgba(220, 38, 38, 0.1);"><i data-lucide="alert-circle"></i></div>
                            <div>
                                <h5 class="text-dark mb-0 fw-bold">Payment Pending: {{ $due->month }}</h5>
                                <p class="text-muted text-xs mb-0">Due Amount: <b class="text-primary fs-6">₹{{ number_format($due->amount) }}</b></p>
                            </div>
                        </div>
                        <button class="btn btn-primary rounded-pill px-4" onclick="openPaymentModal('{{ $due->id }}', '{{ $due->amount }}', '{{ $due->month }}')">
                            <i data-lucide="wallet"></i> Pay Now
                        </button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Payments Ledger -->
    <div class="premium-table-container text-start">
        <div class="p-3 border-bottom"><h6 class="text-dark fw-bold mb-0">Transactions History Ledger</h6></div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Description / Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment Date</th>
                        <th>Transaction UTR ID</th>
                        <th>Method</th>
                        <th>Receipt Download</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr>
                            <td class="fw-bold text-dark">{{ $p->month }}</td>
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
                                @if($p->status === 'Paid')
                                    <button class="btn btn-sm btn-outline-primary py-1 px-3 rounded-pill" onclick="downloadReceiptPDF('{{ $p->id }}', '{{ $student->name }}', '{{ $p->room_number }}', '{{ $p->month }}', '{{ $p->amount }}', '{{ $p->tx_id }}', '{{ $p->method }}', '{{ $p->payment_date }}')">
                                        <i data-lucide="file-pdf"></i> Download
                                    </button>
                                @elseif($p->status === 'Pending Approval')
                                    <span class="text-xs text-warning fw-bold"><i data-lucide="clock" class="d-inline-block align-middle me-1" style="width:12px; height:12px;"></i> Awaiting Approval</span>
                                @else
                                    <button class="btn btn-primary btn-sm py-1 px-3 rounded-pill" onclick="openPaymentModal('{{ $p->id }}', '{{ $p->amount }}', '{{ $p->month }}')">
                                        Pay Invoice
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted p-4">No transactions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

<!-- ================= 3. TAB: FOOD preferences ================= -->
@elseif($tab === 'food')
    <div class="row g-4 text-start">
        <!-- Checkboxes -->
        <div class="col-md-5 d-flex flex-column gap-4">
            <div class="premium-card">
                <h5 class="text-dark fw-bold mb-3"><i data-lucide="ban" class="text-danger me-2"></i>No Food Preferences</h5>
                <p class="text-muted small mb-4">Select a target date and check the meals periods you wish to <b>opt out</b> of cooking plates quantities preparation.</p>
                
                <form action="/student/save-food" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Select Date</label>
                        <input type="date" name="date" class="form-control" value="{{ $foodDate }}" onchange="location.href='/student?tab=food&food_date=' + this.value" required>
                    </div>

                    <div class="p-3 bg-light rounded-3 border mb-4 d-flex flex-column gap-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="morning" id="morning" {{ $foodPref && $foodPref->morning ? 'checked' : '' }}>
                            <label class="form-check-label text-dark ms-2" for="morning"><b>Morning (Breakfast)</b> &mdash; Exclude</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="afternoon" id="afternoon" {{ $foodPref && $foodPref->afternoon ? 'checked' : '' }}>
                            <label class="form-check-label text-dark ms-2" for="afternoon"><b>After / Night (Lunch)</b> &mdash; Exclude</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="evening" id="evening" {{ $foodPref && $foodPref->evening ? 'checked' : '' }}>
                            <label class="form-check-label text-dark ms-2" for="evening"><b>Evening (Dinner)</b> &mdash; Exclude</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 rounded-pill py-2 fw-bold">Save Preferences</button>
                </form>
            </div>

            <!-- Submit Meal Feedback Form -->
            <div class="premium-card">
                <h5 class="text-dark fw-bold mb-3"><i data-lucide="message-square" class="text-primary me-2"></i>Submit Daily Meal Feedback</h5>
                <p class="text-muted small mb-3">Rate the quality and taste of the food served to you. Your feedback is sent directly to Food Management.</p>
                <form action="/student/submit-meal-feedback" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Meal Period</label>
                        <select name="meal_type" class="form-select" required>
                            <option value="Breakfast">Breakfast</option>
                            <option value="Lunch">Lunch</option>
                            <option value="Dinner">Dinner</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meal Quality Rating</label>
                        <select name="rating" class="form-select" required>
                            <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                            <option value="4">⭐⭐⭐⭐ Very Good</option>
                            <option value="3" selected>⭐⭐⭐ Average / Okay</option>
                            <option value="2">⭐⭐ Poor</option>
                            <option value="1">⭐ Terrible</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Feedback / Suggestions</label>
                        <textarea name="comment" class="form-control" rows="3" placeholder="Taste, portion size, comments..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">Submit Meal Feedback</button>
                </form>
            </div>
        </div>

        <!-- Weekly Menu Schedule -->
        <div class="col-md-7 d-flex flex-column gap-4">
            <div class="premium-card">
                <h5 class="text-dark fw-bold mb-4"><i data-lucide="calendar" class="text-success me-2"></i>Weekly Hostels Food Schedule</h5>
                <div class="accordion accordion-dark" id="menuAccordion">
                    @foreach($foodMenu as $menu)
                        <div class="accordion-item" style="border:1px solid var(--border-color); border-radius:12px; margin-bottom: 0.5rem; overflow:hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed text-capitalize fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $menu->day }}">
                                    {{ $menu->day }} meals
                                </button>
                            </h2>
                            <div id="collapse-{{ $menu->day }}" class="accordion-collapse collapse" data-bs-parent="#menuAccordion">
                                <div class="accordion-body bg-light">
                                    <div class="row g-2 text-xs text-muted">
                                        <div class="col-4"><b>Breakfast:</b><br>{{ $menu->breakfast }}</div>
                                        <div class="col-4"><b>Lunch:</b><br>{{ $menu->lunch }}</div>
                                        <div class="col-4"><b>Dinner:</b><br>{{ $menu->dinner }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Message to Food Management Form and Inbox -->
            <div class="premium-card text-start">
                <h5 class="text-dark fw-bold mb-3"><i data-lucide="message-square-plus" class="text-success me-2"></i>Message Food Management</h5>
                <p class="text-muted small mb-4">Send comment reviews, query questions, or diet requests directly to our kitchen staff. You will see replies here.</p>
                
                <form action="/student/raise-complaint" method="POST" class="mb-4">
                    @csrf
                    <input type="hidden" name="category" value="Food">
                    <input type="hidden" name="priority" value="Medium">
                    <div class="mb-3">
                        <label class="form-label">Subject / Title</label>
                        <input type="text" name="title" class="form-control" placeholder="E.g. Request extra milk, delayed arrival..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message Details</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Describe your request..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-bold text-white">Send Message</button>
                </form>

                <h6 class="text-dark fw-bold mb-3 border-top pt-3">Kitchen Inbox & Replies</h6>
                <div class="list-group list-group-flush" style="max-height: 350px; overflow-y: auto;">
                    @php
                        $foodMsgs = $complaints->where('category', 'Food');
                    @endphp
                    @forelse($foodMsgs as $msg)
                        <div class="list-group-item bg-transparent px-0 py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark text-sm">{{ $msg->title }}</span>
                                <span class="badge {{ $msg->status === 'Resolved' ? 'bg-success-soft text-success' : 'bg-warning-soft text-warning' }} text-xs">
                                    {{ $msg->status }}
                                </span>
                            </div>
                            <p class="text-muted small mb-2">{{ $msg->description }}</p>
                            @if($msg->reply)
                                <div class="p-3 bg-light rounded-3 border-start border-4 border-success mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="fw-bold text-success text-xs">Reply from Kitchen Staff:</small>
                                        <small class="text-muted text-xs">{{ $msg->resolved_date }}</small>
                                    </div>
                                    <p class="text-dark small mb-0">{{ $msg->reply }}</p>
                                </div>
                            @else
                                <small class="text-muted text-xs"><i class="text-warning">Awaiting reply...</i></small>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted text-center py-3 small">No messages sent to Kitchen yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

<!-- ================= 4. TAB: COMPLAINTS DESK ================= -->
@elseif($tab === 'complaints')
    <div class="row g-4 text-start">
        <div class="col-md-5">
            <div class="premium-card">
                <h5 class="text-dark fw-bold mb-4">Raise Room Maintenance Complaint</h5>
                <form action="/student/raise-complaint" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Subject / Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Leak flush, WiFi drops..." required>
                    </div>
                    <div class="row mb-3 g-2">
                        <div class="col-6">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="Electrical">Electrical</option>
                                <option value="Plumbing">Plumbing</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Wi-Fi">Wi-Fi</option>
                                <option value="Water">Water</option>
                                <option value="Food">Food / Meals</option>
                                <option value="Other">Other Issues</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select" required>
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                                <option value="Emergency">Emergency</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Elaborate issue</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Specify room area, description..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">Submit Complaint Ticket</button>
                </form>
            </div>
        </div>

        <div class="col-md-7">
            <div class="premium-card">
                <h5 class="text-dark fw-bold mb-4">Raised Tickets Log & Status</h5>
                <div class="timeline-notices">
                    @forelse($complaints as $c)
                        @php 
                            $resolved = $c->status === 'Resolved'; 
                            $pendingVerify = $c->verification_status === 'Pending Verification';
                            $verified = $c->verification_status === 'Verified';
                        @endphp
                        <div class="timeline-notice-item {{ $resolved ? '' : 'notice-danger' }}" style="padding-bottom:1.5rem;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="text-dark fw-bold mb-0 fs-6">{{ $c->title }}</h6>
                                <div class="d-flex gap-1">
                                    <span class="badge bg-light text-dark text-xs">{{ $c->category }}</span>
                                    <span class="badge {{ $resolved ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">{{ strtoupper($c->status) }}</span>
                                </div>
                            </div>
                            <p class="text-muted small mb-2">{{ $c->description }}</p>
                            <div class="d-flex justify-content-between text-xs text-muted mb-2">
                                <span>Priority: <b class="{{ $c->priority === 'Emergency' ? 'text-danger' : 'text-primary' }}">{{ $c->priority }}</b></span>
                                <span>Assigned: <b class="text-primary">{{ $c->assigned_to ?: 'Pending Roster' }}</b></span>
                            </div>
                            @if($resolved)
                                <div class="p-3 bg-light rounded text-success small text-xs mb-2">
                                    <b>Closing Reply:</b> "{{ $c->reply }}"<br>
                                    @if($c->materials_used)
                                        <b>Materials Used:</b> {{ $c->materials_used }}<br>
                                    @endif
                                    <b>Verification:</b> 
                                    @if($verified)
                                        <span class="badge bg-success-soft text-success">Verified</span>
                                    @elseif($c->verification_status === 'Unresolved')
                                        <span class="badge bg-danger-soft text-danger">Reported Unresolved</span>
                                    @else
                                        <span class="badge bg-warning-soft text-warning">Awaiting Your Verification</span>
                                    @endif
                                </div>

                                @if($pendingVerify)
                                    <div class="d-flex gap-2 mt-2">
                                        <form action="/student/verify-complaint/{{ $c->id }}/Verified" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-xs rounded-pill py-1 px-3 fs-xs text-xs">Verified</button>
                                        </form>
                                        <button class="btn btn-outline-danger btn-xs rounded-pill py-1 px-3 fs-xs text-xs" onclick="reopenComplaintPrompt('{{ $c->id }}')">Reopen Issue</button>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @empty
                        <p class="text-muted py-4 text-center">No complaints raised yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

<!-- ================= 5. TAB: NOTICES ================= -->
@elseif($tab === 'notices')
    <div class="premium-card text-start">
        <h5 class="text-dark fw-bold mb-4"><i data-lucide="megaphone" class="text-primary me-2"></i>Hostel Notices Board Updates</h5>
        <div class="timeline-notices">
            @forelse($notices as $n)
                @php
                    $isUrgent = Str::contains(strtolower($n->title), ['urgent', 'important', 'due']);
                @endphp
                <div class="timeline-notice-item {{ $isUrgent ? 'notice-danger' : '' }}">
                    <h6 class="text-dark fw-bold mb-1">{{ $n->title }}</h6>
                    <p class="text-muted small mb-2">{{ $n->content }}</p>
                    <span class="text-xs text-muted">Published: {{ $n->date }}</span>
                </div>
            @empty
                <p class="text-muted text-center py-4">No announcements posted.</p>
            @endforelse
        </div>
    </div>
@endif

<!-- ================= PAY RENT MODAL ================= -->
<div class="modal fade" id="payRentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:24px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark">Submit Rent Transaction UTR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <div class="p-3 bg-light rounded border text-center mb-4">
                    <small class="text-muted text-xs uppercase d-block">Outstanding Rent Amount</small>
                    <h2 class="text-primary fw-bold mb-0" id="modalRentAmount">₹0.00</h2>
                    <span class="badge bg-secondary-soft text-dark mt-2" id="modalRentMonth">Month -</span>
                </div>

                <form id="rentPaySubmitForm" method="POST">
                    @csrf
                    
                    <input type="hidden" name="month" id="modalRentMonthInput">

                    <div class="mb-3">
                        <label class="form-label">Payment Date (Month, Date, Year) *</label>
                        <input type="text" name="payment_date" id="modalPaymentDateInput" class="form-control" value="{{ date('Y-m-d') }}" placeholder="YYYY-MM-DD" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Payment Method</label>
                        <select name="method" id="payChannelSelect" class="form-select" onchange="renderChannelDetails(this.value)" required>
                            <option value="" disabled selected>-- Select Channel --</option>
                            <option value="UPI">Scan UPI QR Code</option>
                            <option value="PhonePe">PhonePe Transfer</option>
                            <option value="GPay">Google Pay (GPay)</option>
                            <option value="Paytm">Paytm Transfer</option>
                            <option value="Cash">Offline Cash Payment</option>
                        </select>
                    </div>

                    <!-- Instructions -->
                    <div id="channelInstructionsBox" class="p-3 bg-light border rounded mb-4 d-none text-xs text-muted">
                        <!-- Populated dynamically by JS -->
                    </div>

                    <div class="mb-4">
                        <label class="form-label" id="utrLabel">UTR / Transaction Reference Number *</label>
                        <input type="text" name="tx_id" id="utrInput" class="form-control" placeholder="12-digit transaction number" required>
                        <div class="form-text text-muted small" id="utrHelp">Required by administrator to reconcile and log transaction receipt.</div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold">Submit Transaction Reference</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Reopen form -->
<form id="jsReopenComplaintForm" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="reason" id="jsReopenReasonInput">
</form>

@endsection

@section('dashboard_scripts')
<script>
    const pgConfig = @json($paymentConfig);

    function openPaymentModal(invoiceId, amount, month) {
        document.getElementById('rentPaySubmitForm').action = `/student/pay-submit/${invoiceId}`;
        document.getElementById('modalRentAmount').innerText = `₹${parseInt(amount).toLocaleString()}.00`;
        document.getElementById('modalRentMonth').innerText = month;
        document.getElementById('modalRentMonthInput').value = month;

        // Reset
        document.getElementById('payChannelSelect').selectedIndex = 0;
        document.getElementById('channelInstructionsBox').classList.add('d-none');

        const modal = new bootstrap.Modal(document.getElementById('payRentModal'));
        modal.show();
    }

    function reopenComplaintPrompt(id) {
        Swal.fire({
            title: 'Reopen Complaint',
            input: 'text',
            inputPlaceholder: 'Reason for reopening (e.g. WiFi still lags)',
            confirmButtonText: 'Reopen Ticket',
            showCancelButton: true,
            confirmButtonColor: '#DC2626'
        }).then((res) => {
            if (res.isConfirmed && res.value) {
                const form = document.getElementById('jsReopenComplaintForm');
                form.action = `/student/verify-complaint/${id}/Unresolved`;
                document.getElementById('jsReopenReasonInput').value = res.value;
                form.submit();
            }
        });
    }

    function renderChannelDetails(val) {
        const box = document.getElementById('channelInstructionsBox');
        box.classList.remove('d-none');

        const utrLabel = document.getElementById('utrLabel');
        const utrInput = document.getElementById('utrInput');
        const utrHelp = document.getElementById('utrHelp');

        if(val === 'UPI') {
            box.innerHTML = `<b>UPI QR Code Scanner</b><br>Please scan this QR code to complete transfer:<br>
            ${pgConfig.qr_code ? `<div class="mt-2 text-center"><img src="/storage/${pgConfig.qr_code}" class="img-thumbnail" style="max-height:180px;"></div>` : '<div class="text-danger mt-2">QR Code not uploaded by Admin.</div>'}`;
            if (utrLabel) utrLabel.innerText = "UTR / Transaction Reference Number *";
            if (utrInput) utrInput.placeholder = "12-digit transaction number";
            if (utrHelp) utrHelp.innerText = "Required by administrator to reconcile and log transaction receipt.";
        } else if(val === 'PhonePe') {
            box.innerHTML = `<b>PhonePe App Mobile Transfer</b><br>Transfer to Number:<br><span class="text-primary fw-bold fs-6">${pgConfig.phonepe || 'N/A'}</span>`;
            if (utrLabel) utrLabel.innerText = "UTR / Transaction Reference Number *";
            if (utrInput) utrInput.placeholder = "12-digit transaction number";
            if (utrHelp) utrHelp.innerText = "Required by administrator to reconcile and log transaction receipt.";
        } else if(val === 'GPay') {
            box.innerHTML = `<b>Google Pay (GPay) Transfer</b><br>Transfer to Number:<br><span class="text-primary fw-bold fs-6">${pgConfig.gpay || 'N/A'}</span>`;
            if (utrLabel) utrLabel.innerText = "UTR / Transaction Reference Number *";
            if (utrInput) utrInput.placeholder = "12-digit transaction number";
            if (utrHelp) utrHelp.innerText = "Required by administrator to reconcile and log transaction receipt.";
        } else if(val === 'Paytm') {
            box.innerHTML = `<b>Paytm Wallet Transfer</b><br>Transfer to Number:<br><span class="text-primary fw-bold fs-6">${pgConfig.paytm || 'N/A'}</span>`;
            if (utrLabel) utrLabel.innerText = "UTR / Transaction Reference Number *";
            if (utrInput) utrInput.placeholder = "12-digit transaction number";
            if (utrHelp) utrHelp.innerText = "Required by administrator to reconcile and log transaction receipt.";
        } else if(val === 'Cash') {
            box.innerHTML = `<b>Offline Cash Payment</b><br>Please handover the cash amount directly to the building warden or manager.`;
            if (utrLabel) utrLabel.innerText = "Payment Description / Handover Details *";
            if (utrInput) utrInput.placeholder = "e.g., Handed cash to Warden Suresh at 6 PM";
            if (utrHelp) utrHelp.innerText = "Provide details about who received the cash and when, to help the administrator approve it.";
        }
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
        doc.text('Verified Transaction logged digitally by XAMPP MySQL Student panel.', 30, y + 27);
        doc.save(`Receipt_${month.replace(' ', '_')}_${id}.pdf`);
    }
</script>
@endsection
