<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PgBuilding;
use App\Models\Room;
use App\Models\Payment;
use App\Models\PaymentConfig;
use App\Models\Complaint;
use App\Models\FoodPreference;
use App\Models\FoodMenu;
use App\Models\Notice;
use App\Models\LandingContent;
use App\Models\Inventory;
use App\Models\SystemNotification;
use App\Models\WorkReport;
use App\Models\CommonAreaTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    private function getActiveUser()
    {
        $userId = session('pg_user_id');
        if (!$userId) return null;
        return User::find($userId);
    }

    public function index(Request $request)
    {
        $admin = $this->getActiveUser();
        if (!$admin || $admin->role !== 'admin') {
            return redirect('/role-selection')->withErrors(['auth' => 'Please sign in as Admin to access this dashboard.']);
        }

        $tab = $request->query('tab', 'overview');
        $activePgId = $request->query('pg_id', 'all');

        $pgs = PgBuilding::all();
        $paymentConfig = PaymentConfig::first();

        // 1. Calculations for overview stats
        $roomsQuery = Room::query();
        if ($activePgId !== 'all') $roomsQuery->where('pg_building_id', $activePgId);
        $rooms = $roomsQuery->get();

        $studentsQuery = User::where('role', 'student')->where('approval_status', 'approved');
        if ($activePgId !== 'all') $studentsQuery->where('pg_building_id', $activePgId);
        $students = $studentsQuery->get();

        $pendingApprovalsCount = User::where('role', 'student')->where('approval_status', 'pending')->count();

        $paymentsQuery = Payment::query();
        if ($activePgId !== 'all') $paymentsQuery->where('pg_building_id', $activePgId);
        $payments = $paymentsQuery->get();

        $totalRooms = $rooms->count();
        $occupiedSlots = $rooms->sum('occupied');
        $vacantSlots = $rooms->sum(function($r) { return $r->capacity - $r->occupied; });
        $dueSum = $payments->where('status', 'Due')->sum('amount');
        $paidSum = $payments->where('status', 'Paid')->sum('amount');

        $complaintsQuery = Complaint::where('status', 'Pending');
        if ($activePgId !== 'all') {
            $complaintsQuery->whereIn('student_email', $students->pluck('email'));
        }
        $pendingComplaintsCount = $complaintsQuery->count();

        // Exclusions food count today
        $todayStr = date('Y-m-d');
        $foodPrefsQuery = FoodPreference::where('date', $todayStr);
        if ($activePgId !== 'all') {
            $foodPrefsQuery->whereIn('email', $students->pluck('email'));
        }
        $foodExclusions = $foodPrefsQuery->get();
        $optedOutFoodCount = $foodExclusions->filter(function($f) {
            return $f->morning || $f->afternoon || $f->evening;
        })->count();

        // Notifications
        $notifications = SystemNotification::where('type', 'admin')->latest()->get();

        // Sub-tabs specific data loading
        $activeTabRooms = $rooms;
        $activeTabPgBuildings = $pgs;
        
        // Students approvals tabs query
        $studentSubTab = $request->query('subtab', 'approved');
        $studentSearch = $request->query('search', '');
        $studentCollege = $request->query('college', '');
        $studentCourse = $request->query('course', '');
        $studentYear = $request->query('year', '');
        $studentPayment = $request->query('payment', '');

        $studentsListQuery = User::where('role', 'student')->where('approval_status', $studentSubTab);
        if ($activePgId !== 'all') $studentsListQuery->where('pg_building_id', $activePgId);

        if ($studentSearch) {
            $studentsListQuery->where(function($q) use ($studentSearch) {
                $q->where('name', 'like', "%{$studentSearch}%")
                  ->orWhere('email', 'like', "%{$studentSearch}%");
            });
        }
        if ($studentCollege) $studentsListQuery->where('college', $studentCollege);
        if ($studentCourse) $studentsListQuery->where('course', $studentCourse);
        if ($studentYear) $studentsListQuery->where('year', $studentYear);
        if ($studentPayment) {
            if ($studentPayment === 'Paid') {
                $studentsListQuery->whereDoesntHave('payments', function($q) {
                    $q->where('status', 'Due');
                });
            } elseif ($studentPayment === 'Due') {
                $studentsListQuery->whereHas('payments', function($q) {
                    $q->where('status', 'Due');
                });
            }
        }

        $activeTabStudents = $studentsListQuery->get();
        $filterColleges = User::where('role', 'student')->pluck('college')->unique()->filter();
        $filterCourses = User::where('role', 'student')->pluck('course')->unique()->filter();

        // Payments logs
        $activeTabPayments = Payment::latest()->get();

        // Complaints desk
        $activeTabComplaints = Complaint::latest()->get();
        $staffMembers = User::where('role', 'staff')->get();

        // Notices
        $activeTabNotices = Notice::latest()->get();

        // Food menus & inventory
        $activeTabFoodMenu = FoodMenu::all();
        $activeTabInventory = Inventory::all();

        // Landing Content CMS Form
        $landingContent = LandingContent::first();

        // Visitor Inquiries
        $activeTabInquiries = \App\Models\Inquiry::latest()->get();

        // Work reports logs for Admin
        $workReports = WorkReport::latest()->get();
        $commonAreaTasks = CommonAreaTask::all();

        // Load collections for Reports Tab
        $allRooms = Room::all();
        $allPayments = Payment::all();
        $allStudents = User::where('role', 'student')->get();
        $allComplaints = Complaint::all();

        // Render dynamic Chart.js datasets parameters
        $months = Payment::select('month')->distinct()->pluck('month')->toArray();
        $chartPaidData = [];
        $chartDueData = [];
        foreach ($months as $m) {
            $chartPaidData[] = Payment::where('month', $m)->where('status', 'Paid')->sum('amount');
            $chartDueData[] = Payment::where('month', $m)->where('status', 'Due')->sum('amount');
        }

        return view('admin.dashboard', compact(
            'admin', 'tab', 'activePgId', 'pgs', 'students', 'paymentConfig', 'totalRooms', 'occupiedSlots', 'vacantSlots',
            'dueSum', 'paidSum', 'pendingComplaintsCount', 'optedOutFoodCount', 'notifications',
            'activeTabRooms', 'activeTabPgBuildings', 'studentSubTab', 'activeTabStudents',
            'filterColleges', 'filterCourses', 'activeTabPayments', 'activeTabComplaints',
            'staffMembers', 'activeTabNotices', 'activeTabFoodMenu', 'foodExclusions',
            'activeTabInventory', 'landingContent', 'months', 'chartPaidData', 'chartDueData', 'pendingApprovalsCount', 'workReports', 'commonAreaTasks',
            'allRooms', 'allPayments', 'allStudents', 'allComplaints', 'activeTabInquiries'
        ));
    }

    // ------------------- ACTIONS FOR PG BUILDINGS -------------------
    public function addPg(Request $request)
    {
        $request->validate(['name' => 'required', 'address' => 'required', 'status' => 'required']);
        PgBuilding::create($request->all());
        return back()->with('success', 'New PG Building registered!');
    }

    public function togglePgStatus($id)
    {
        $pg = PgBuilding::findOrFail($id);
        $pg->status = $pg->status === 'active' ? 'inactive' : 'active';
        $pg->save();
        return back()->with('success', "PG status toggled successfully!");
    }

    public function deletePg($id)
    {
        PgBuilding::findOrFail($id)->delete();
        Room::where('pg_building_id', $id)->delete();
        return back()->with('success', 'PG Building and associated rooms deleted.');
    }

    // ------------------- ACTIONS FOR ROOMS -------------------
    public function addRoom(Request $request)
    {
        $request->validate(['pg_building_id' => 'required', 'number' => 'required', 'type' => 'required', 'rent' => 'required|integer', 'capacity' => 'required|integer']);
        Room::create($request->all());
        return back()->with('success', 'Room profile added.');
    }

    public function editRoomRent(Request $request, $id)
    {
        $request->validate(['rent' => 'required|integer']);
        $room = Room::findOrFail($id);
        $room->rent = $request->rent;
        $room->save();
        return back()->with('success', 'Room rent updated!');
    }

    public function deleteRoom($id)
    {
        Room::findOrFail($id)->delete();
        return back()->with('success', 'Room profile removed.');
    }

    // ------------------- ACTIONS FOR STUDENT APPROVALS -------------------
    public function approveStudent($id)
    {
        $student = User::findOrFail($id);
        
        $room = Room::where('pg_building_id', $student->pg_building_id)
                    ->where('number', $student->room_number)
                    ->first();

        if ($room && $room->occupied >= $room->capacity) {
            return back()->withErrors(['error' => 'Room is already fully occupied! Please reassign room first.']);
        }

        $student->approval_status = 'approved';
        $student->save();

        if ($room) {
            $room->occupied++;
            $room->save();
        }

        // Auto generate first invoice
        Payment::create([
            'student_email' => $student->email,
            'pg_building_id' => $student->pg_building_id,
            'room_number' => $student->room_number,
            'month' => 'Academic Payment',
            'amount' => $room ? $room->rent : 8000,
            'status' => 'Due'
        ]);

        // Publish generic congratulation notice
        Notice::create([
            'date' => date('Y-m-d'),
            'title' => 'Welcome Tenant!',
            'content' => "Welcome {$student->name}, your registration request for room #{$student->room_number} is approved by administration.",
            'target' => 'student'
        ]);

        SystemNotification::create([
            'date' => date('Y-m-d'),
            'text' => "Your registration request has been approved! Welcome to room #{$student->room_number}.",
            'type' => 'student',
            'user_id' => $student->id,
            'read' => false
        ]);

        return back()->with('success', "Student registration approved successfully!");
    }

    public function rejectStudent($id)
    {
        $student = User::findOrFail($id);
        $student->approval_status = 'rejected';
        $student->save();
        return back()->with('success', "Student registration has been rejected.");
    }

    public function revokeStudent($id)
    {
        $student = User::findOrFail($id);
        $student->approval_status = 'rejected';
        $student->save();

        $room = Room::where('pg_building_id', $student->pg_building_id)
                    ->where('number', $student->room_number)
                    ->first();

        if ($room && $room->occupied > 0) {
            $room->occupied--;
            $room->save();
        }

        return back()->with('success', "Tenant account has been discharged and bed slot released.");
    }

    public function deleteStudent($id)
    {
        $student = User::findOrFail($id);

        $room = Room::where('pg_building_id', $student->pg_building_id)
                    ->where('number', $student->room_number)
                    ->first();

        // If the user was approved, release their room slot
        if ($room && $student->approval_status === 'approved' && $room->occupied > 0) {
            $room->occupied--;
            $room->save();
        }

        $student->delete();

        return back()->with('success', "Student record permanently deleted from database.");
    }

    // ------------------- ACTIONS FOR RENTS & PAYMENTS -------------------
    public function updatePaymentConfig(Request $request)
    {
        $config = PaymentConfig::first();
        if (!$config) {
            $config = new PaymentConfig();
        }
        $config->fill($request->except('qr_code'));
        
        if ($request->hasFile('qr_code')) {
            $path = $request->file('qr_code')->store('payments', 'public');
            $config->qr_code = $path;
        }
        
        $config->save();
        return back()->with('success', 'Payment configuration details updated.');
    }

    public function generateDue(Request $request)
    {
        $request->validate([
            'month' => 'required',
            'student_email' => 'required',
            'amount' => 'required|numeric',
            'created_date' => 'nullable|date'
        ]);

        $student = User::where('email', $request->student_email)->first();
        if (!$student) return back()->withErrors(['error' => 'Tenant email not found.']);

        $payment = Payment::create([
            'student_email' => $student->email,
            'pg_building_id' => $student->pg_building_id,
            'room_number' => $student->room_number,
            'month' => $request->month,
            'amount' => $request->amount,
            'status' => 'Due'
        ]);

        if ($request->filled('created_date')) {
            $payment->created_at = $request->created_date . ' 12:00:00';
            $payment->save();
        }

        SystemNotification::create([
            'date' => date('Y-m-d'),
            'text' => "New payment invoice of ₹" . number_format($request->amount) . " generated for \"{$request->month}\". Please pay your dues.",
            'type' => 'student',
            'user_id' => $student->id,
            'read' => false
        ]);

        return back()->with('success', 'Outstanding payment due invoice generated successfully.');
    }

    public function reconcilePaymentManual(Request $request, $id)
    {
        $invoice = Payment::findOrFail($id);
        $invoice->status = 'Paid';
        $invoice->payment_date = date('Y-m-d');
        $invoice->tx_id = $request->tx_id ?: 'CASH-' . rand(100000, 999999);
        $invoice->method = $request->method ?: 'Cash at Reception';
        $invoice->save();

        return back()->with('success', 'Invoice payment reconciled manually.');
    }

    public function approvePayment(Request $request, $id)
    {
        $invoice = Payment::findOrFail($id);
        $invoice->status = 'Paid';
        if (empty($invoice->payment_date)) {
            $invoice->payment_date = date('Y-m-d');
        }
        $invoice->save();

        // Notify student
        $student = User::where('email', $invoice->student_email)->first();
        if ($student) {
            SystemNotification::create([
                'date' => date('Y-m-d'),
                'text' => "Your payment of ₹" . number_format($invoice->amount) . " for {$invoice->month} has been approved by the Administrator.",
                'type' => 'student',
                'user_id' => $student->id,
                'read' => false
            ]);
        }

        return back()->with('success', 'Rent payment approved and receipt activated.');
    }

    public function updateInvoice(Request $request, $id)
    {
        $request->validate([
            'month' => 'required',
            'amount' => 'required|numeric',
            'status' => 'required',
        ]);

        $invoice = Payment::findOrFail($id);
        $invoice->month = $request->month;
        $invoice->amount = $request->amount;
        $invoice->status = $request->status;
        $invoice->payment_date = $request->payment_date;
        $invoice->tx_id = $request->tx_id;
        $invoice->method = $request->method;
        $invoice->save();

        return back()->with('success', 'Rent due invoice details updated successfully.');
    }

    // ------------------- ACTIONS FOR COMPLAINTS -------------------
    public function assignComplaint(Request $request, $id)
    {
        $request->validate(['assigned_to' => 'required']);
        $c = Complaint::findOrFail($id);
        $c->assigned_to = $request->assigned_to;
        $c->save();

        $staffUser = User::where('name', $request->assigned_to)->where('role', 'staff')->first();
        SystemNotification::create([
            'date' => date('Y-m-d'),
            'text' => "New Task Assigned: Complaint \"{$c->title}\" assigned to you.",
            'type' => 'staff',
            'user_id' => $staffUser ? $staffUser->id : null,
            'read' => false
        ]);

        return back()->with('success', 'Complaint assigned to staff member.');
    }

    public function resolveComplaint(Request $request, $id)
    {
        $request->validate(['reply' => 'required']);
        $c = Complaint::findOrFail($id);
        $c->status = 'Resolved';
        $c->resolved_date = date('Y-m-d');
        $c->reply = $request->reply;
        $c->save();

        $student = User::where('email', $c->student_email)->where('role', 'student')->first();
        SystemNotification::create([
            'date' => date('Y-m-d'),
            'text' => "Your complaint ticket \"{$c->title}\" has been resolved: {$request->reply}.",
            'type' => 'student',
            'user_id' => $student ? $student->id : null,
            'read' => false
        ]);

        return back()->with('success', 'Ticket closed successfully.');
    }

    // ------------------- ACTIONS FOR STAFF ROSTER -------------------
    public function addStaff(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&~_]/',
            ],
            'staff_role' => 'required|in:Housekeeping,Food Management,Maintenance',
            'pg_building_id' => 'required|exists:pg_buildings,id'
        ], [
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character/symbol.',
            'password.min' => 'The password must be at least 8 characters long.',
        ]);
        
        User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'staff',
            'staff_role' => $request->staff_role,
            'pg_building_id' => $request->pg_building_id,
            'approval_status' => 'approved'
        ]);

        return back()->with('success', 'Staff member hired.');
    }

    public function deleteStaff($id)
    {
        $user = User::findOrFail($id);
        if ($user->email === 'staff@gmail.com') {
            return back()->withErrors(['error' => 'Baseline staff account cannot be removed.']);
        }
        $user->delete();
        return back()->with('success', 'Staff member deleted.');
    }

    public function assignRoomCleaning(Request $request, $id)
    {
        $request->validate(['assigned_to' => 'required']);
        $room = Room::findOrFail($id);
        $room->assigned_to = $request->assigned_to;
        $room->save();

        return back()->with('success', "Room #{$room->number} cleaning assigned to {$request->assigned_to}.");
    }

    public function assignCommonAreaCleaning(Request $request, $id)
    {
        $request->validate(['assigned_to' => 'required']);
        $task = CommonAreaTask::findOrFail($id);
        $task->assigned_to = $request->assigned_to;
        $task->save();

        return back()->with('success', "Common area {$task->area_name} cleaning assigned to {$request->assigned_to}.");
    }

    public function assignRoomMaintenance(Request $request)
    {
        $request->validate([
            'room_number' => 'required',
            'assigned_to' => 'required',
            'description' => 'required'
        ]);

        $adminUser = User::where('role', 'admin')->first();
        $adminEmail = $adminUser ? $adminUser->email : 'admin@gmail.com';

        Complaint::create([
            'student_name' => 'Admin (Roster)',
            'student_email' => $adminEmail,
            'room_number' => $request->room_number,
            'title' => 'Room #' . $request->room_number . ' Maintenance Task',
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'category' => 'Other',
            'priority' => 'Medium',
            'status' => 'Pending',
            'verification_status' => 'Pending',
            'created_date' => date('Y-m-d')
        ]);

        return back()->with('success', "Maintenance task logged and assigned to {$request->assigned_to} for Room #{$request->room_number}.");
    }

    // ------------------- ACTIONS FOR NOTICES -------------------
    public function publishNotice(Request $request)
    {
        $request->validate(['title' => 'required', 'content' => 'required', 'target' => 'required']);
        Notice::create([
            'date' => date('Y-m-d'),
            'title' => $request->title,
            'content' => $request->content,
            'target' => $request->target
        ]);

        if (in_array($request->target, ['student', 'all'])) {
            SystemNotification::create([
                'date' => date('Y-m-d'),
                'text' => "New Notice: \"{$request->title}\" - {$request->content}",
                'type' => 'student',
                'read' => false
            ]);
        }
        if (in_array($request->target, ['staff', 'all'])) {
            SystemNotification::create([
                'date' => date('Y-m-d'),
                'text' => "New Notice: \"{$request->title}\" - {$request->content}",
                'type' => 'staff',
                'read' => false
            ]);
        }

        return back()->with('success', 'Notice board updated.');
    }

    public function deleteNotice($id)
    {
        Notice::findOrFail($id)->delete();
        return back()->with('success', 'Notice deleted.');
    }

    // ------------------- FOOD MENU MANAGEMENT -------------------
    public function updateFoodMenu(Request $request, $id)
    {
        $menu = FoodMenu::findOrFail($id);
        $menu->update($request->all());
        return back()->with('success', 'Meal menu updated.');
    }

    // ------------------- LOGISTICS INVENTORY -------------------
    public function addInventory(Request $request)
    {
        $request->validate(['item' => 'required', 'count' => 'required|integer']);
        Inventory::create([
            'item' => $request->item,
            'count' => $request->count,
            'status' => $request->count <= 15 ? 'Low Stock' : 'In Stock'
        ]);
        return back()->with('success', 'Logistics item logged.');
    }

    public function adjustInventory(Request $request, $id)
    {
        $request->validate(['count' => 'required|integer']);
        $i = Inventory::findOrFail($id);
        $i->count = $request->count;
        $i->status = $request->count <= 15 ? 'Low Stock' : 'In Stock';
        $i->save();
        return back()->with('success', 'Stock balance updated.');
    }

    public function deleteInventory($id)
    {
        Inventory::findOrFail($id)->delete();
        return back()->with('success', 'Logistics item removed.');
    }

    // ------------------- LANDING PAGE CMS updates -------------------
    public function updateLanding(Request $request)
    {
        $content = LandingContent::first();
        if (!$content) {
            $content = new LandingContent();
        }
        
        $data = $request->except(['_token', 'facilities', 'pricing_plans', 'locations', 'testimonials', 'logo_image_file', 'banner_image_file', 'pricing_plans_files']);
        
        // Handle logo file upload
        if ($request->hasFile('logo_image_file')) {
            $file = $request->file('logo_image_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/brand'), $filename);
            $data['logo_image'] = '/storage/brand/' . $filename;
        }

        // Handle banner file upload
        if ($request->hasFile('banner_image_file')) {
            $file = $request->file('banner_image_file');
            $filename = time() . '_banner_' . $file->getClientOriginalName();
            $file->move(public_path('storage/brand'), $filename);
            $data['banner_image'] = '/storage/brand/' . $filename;
        }

        // Convert structured arrays to JSON
        if ($request->has('facilities')) {
            $data['facilities_json'] = json_encode(array_values($request->facilities));
        }
        if ($request->has('pricing_plans')) {
            $plans = $request->pricing_plans;
            
            // Check for uploaded files for pricing plans
            if ($request->hasFile('pricing_plans_files')) {
                $uploadedFiles = $request->file('pricing_plans_files');
                foreach ($uploadedFiles as $index => $file) {
                    if (isset($plans[$index])) {
                        $filename = time() . '_plan_' . $index . '_' . $file->getClientOriginalName();
                        $file->move(public_path('storage/plans'), $filename);
                        $plans[$index]['image_url'] = '/storage/plans/' . $filename;
                    }
                }
            }
            
            foreach ($plans as $k => $plan) {
                if (isset($plan['features']) && !is_array($plan['features'])) {
                    $plans[$k]['features'] = array_filter(array_map('trim', explode(',', $plan['features'])));
                }
            }
            $data['pricing_plans_json'] = json_encode(array_values($plans));
        }
        if ($request->has('locations')) {
            $data['locations_json'] = json_encode(array_values($request->locations));
        }
        if ($request->has('testimonials')) {
            $data['testimonials_json'] = json_encode(array_values($request->testimonials));
        }

        $content->fill($data);
        $content->save();
        return back()->with('success', 'Landing page modifications applied successfully!');
    }

    // Mark Notif Read
    public function markNotifRead($id)
    {
        $n = SystemNotification::findOrFail($id);
        $n->read = true;
        $n->save();
        return back()->with('success', 'Notification marked read.');
    }

    public function resolveInquiry($id)
    {
        $inquiry = \App\Models\Inquiry::findOrFail($id);
        $inquiry->status = $inquiry->status === 'resolved' ? 'pending' : 'resolved';
        $inquiry->save();
        return back()->with('success', 'Inquiry status updated successfully.');
    }

    public function deleteInquiry($id)
    {
        $inquiry = \App\Models\Inquiry::findOrFail($id);
        $inquiry->delete();
        return back()->with('success', 'Inquiry permanently deleted from database.');
    }

    public function deleteInvoice($id)
    {
        $invoice = Payment::findOrFail($id);
        $invoice->delete();
        return back()->with('success', 'Payment invoice record deleted successfully.');
    }
}
