<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Room;
use App\Models\PgBuilding;
use App\Models\Complaint;
use App\Models\FoodPreference;
use App\Models\Notice;
use App\Models\SystemNotification;
use App\Models\CommonAreaTask;
use App\Models\WorkReport;
use App\Models\Inventory;
use App\Models\FoodMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    private function getActiveUser()
    {
        $userId = session('pg_user_id');
        if (!$userId) return null;
        return User::find($userId);
    }

    public function index(Request $request)
    {
        $staff = $this->getActiveUser();
        if (!$staff || $staff->role !== 'staff') {
            return redirect('/role-selection')->withErrors(['auth' => 'Please sign in as staff to access this dashboard.']);
        }

        $pgs = PgBuilding::where('status', 'active')->get();
        $activePgId = $request->query('pg_id', $staff->pg_building_id);
        if (!$activePgId && count($pgs) > 0) {
            $activePgId = $pgs[0]->id;
        }

        $assignedPg = PgBuilding::find($activePgId);
        $tab = $request->query('tab');
        
        // Dynamic tab routing defaults
        if (!$tab) {
            if ($staff->staff_role === 'Housekeeping') $tab = 'tasks';
            elseif ($staff->staff_role === 'Food Management') $tab = 'food';
            elseif ($staff->staff_role === 'Maintenance') $tab = 'maintenance';
        }

        // 1. Housekeeping Datasets (Only show rooms/common tasks explicitly assigned to this staff member)
        $rooms = Room::where('pg_building_id', $staff->pg_building_id)
            ->where('assigned_to', $staff->name)
            ->get();
        $commonAreaTasks = CommonAreaTask::where('pg_building_id', $staff->pg_building_id)
            ->where('assigned_to', $staff->name)
            ->get();

        // 2. Food Management Datasets
        $studentsQuery = User::where('role', 'student')->where('approval_status', 'approved');
        if ($activePgId && $activePgId !== 'all') {
            $studentsQuery->where('pg_building_id', $activePgId);
        }
        $totalStudents = $studentsQuery->count();

        $todayStr = date('Y-m-d');
        // Pluck emails of students registered in the selected PG
        $studentEmailsQuery = User::where('role', 'student');
        if ($activePgId && $activePgId !== 'all') {
            $studentEmailsQuery->where('pg_building_id', $activePgId);
        }
        $studentEmailsInPg = $studentEmailsQuery->pluck('email')->toArray();

        $foodExclusions = FoodPreference::where('date', $todayStr)
            ->whereIn('email', $studentEmailsInPg)
            ->get();

        $morningExclusionsCount = $foodExclusions->where('morning', 1)->count();
        $afternoonExclusionsCount = $foodExclusions->where('afternoon', 1)->count();
        $eveningExclusionsCount = $foodExclusions->where('evening', 1)->count();

        $foodMenu = FoodMenu::all();
        $foodInventory = Inventory::all();
        // Load food complaints or general complaints in the assigned PG
        $foodComplaints = Complaint::where('category', 'Food')
            ->whereIn('student_email', $studentEmailsInPg)
            ->latest()
            ->get();

        // 3. Maintenance Datasets
        $complaints = Complaint::where('assigned_to', $staff->name)
            ->latest()
            ->get();

        // 4. Common logs
        $notices = Notice::whereIn('target', ['all', 'staff'])->latest()->get();
        $workReports = WorkReport::where('user_id', $staff->id)->latest()->get();

        return view('staff.dashboard', compact(
            'staff', 'tab', 'assignedPg', 'rooms', 'commonAreaTasks', 'pgs', 'activePgId',
            'totalStudents', 'foodExclusions', 'morningExclusionsCount', 'afternoonExclusionsCount', 'eveningExclusionsCount',
            'foodMenu', 'foodInventory', 'foodComplaints', 'complaints', 'notices', 'workReports'
        ));
    }

    // ------------------- HOUSEKEEPING ACTIONS -------------------
    public function toggleCleaningState($id, $state)
    {
        $room = Room::findOrFail($id);
        $room->cleaning_status = $state;
        $room->save();

        return back()->with('success', "Room #{$room->number} cleaning marked as {$state}.");
    }

    public function toggleCommonAreaCleaningState($id, $state)
    {
        $task = CommonAreaTask::findOrFail($id);
        $task->status = $state;
        $task->last_cleaned_at = ($state === 'Cleaned') ? date('Y-m-d H:i') : null;
        $task->save();

        return back()->with('success', "Common area {$task->area_name} marked as {$state}.");
    }

    public function resetCleaningChecklist()
    {
        $staff = $this->getActiveUser();
        if ($staff) {
            Room::where('pg_building_id', $staff->pg_building_id)
                ->where('assigned_to', $staff->name)
                ->update(['cleaning_status' => 'Dirty']);
        }
        return back()->with('success', 'Housekeeping checklist reset to Dirty.');
    }

    public function reportMaintenanceIssue(Request $request)
    {
        $request->validate(['title' => 'required', 'description' => 'required', 'category' => 'required']);
        $staff = $this->getActiveUser();
        if (!$staff) return redirect('/role-selection');

        Complaint::create([
            'student_email' => $staff->email,
            'student_name' => $staff->name . ' (Staff Log)',
            'room_number' => 'Common Area / Facility',
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority ?? 'Medium',
            'status' => 'Pending',
            'verification_status' => 'Pending',
            'created_date' => date('Y-m-d')
        ]);

        return back()->with('success', 'Maintenance issue reported to administrator.');
    }

    // ------------------- FOOD MANAGEMENT ACTIONS -------------------
    public function updateFoodMenu(Request $request, $id)
    {
        $request->validate(['breakfast' => 'required', 'lunch' => 'required', 'dinner' => 'required']);
        $menu = FoodMenu::findOrFail($id);
        $menu->update($request->all());

        return back()->with('success', 'Weekly menu updated successfully.');
    }

    public function adjustFoodInventory(Request $request, $id)
    {
        $request->validate(['count' => 'required|numeric']);
        $inv = Inventory::findOrFail($id);
        $inv->count = $request->count;
        $inv->status = ($request->count <= 15) ? 'Low Stock' : 'In Stock';
        $inv->save();

        return back()->with('success', "Stock count for {$inv->item} updated.");
    }

    // ------------------- MAINTENANCE ACTIONS -------------------
    public function updateMaintenanceComplaint(Request $request, $id)
    {
        $request->validate(['status' => 'required', 'priority' => 'required']);
        $c = Complaint::findOrFail($id);
        $c->status = $request->status;
        $c->priority = $request->priority;
        $c->materials_used = $request->materials_used;
        $c->repair_expense = $request->repair_expense ?: 0;

        if ($request->status === 'Resolved') {
            $c->resolved_date = date('Y-m-d');
            $c->verification_status = 'Pending Verification'; // Student verification required
        }
        $c->save();

        // Notify Student
        $studentObj = User::where('email', $c->student_email)->first();
        if ($studentObj) {
            SystemNotification::create([
                'date' => date('Y-m-d'),
                'text' => "Your room complaint \"{$c->title}\" status updated to: {$request->status}.",
                'type' => 'student',
                'user_id' => $studentObj->id,
                'read' => false
            ]);
        }

        return back()->with('success', 'Ticket log status updated.');
    }

    public function replyFoodMessage(Request $request, $id)
    {
        $request->validate(['status' => 'required', 'reply' => 'required']);
        $c = Complaint::findOrFail($id);
        $c->status = $request->status;
        $c->reply = $request->reply;
        if ($request->status === 'Resolved') {
            $c->resolved_date = date('Y-m-d');
            $c->verification_status = 'Pending Verification';
        }
        $c->save();

        // Notify Student
        $studentObj = User::where('email', $c->student_email)->first();
        if ($studentObj) {
            SystemNotification::create([
                'date' => date('Y-m-d'),
                'text' => "Your food message \"{$c->title}\" has a new reply from Food Management.",
                'type' => 'student',
                'user_id' => $studentObj->id,
                'read' => false
            ]);
        }

        return back()->with('success', 'Reply submitted successfully.');
    }

    // ------------------- COMMON ACTIONS -------------------
    public function submitDailyReport(Request $request)
    {
        $request->validate(['report_text' => 'required']);
        $staff = $this->getActiveUser();
        if (!$staff) return redirect('/role-selection');

        WorkReport::create([
            'user_id' => $staff->id,
            'name' => $staff->name,
            'staff_role' => $staff->staff_role,
            'report_text' => $request->report_text,
            'date' => date('Y-m-d')
        ]);

        return back()->with('success', 'Daily work report submitted to administrator.');
    }

    public function updateProfile(Request $request)
    {
        $request->validate(['name' => 'required', 'phone' => 'required']);
        $staff = $this->getActiveUser();
        if (!$staff) return redirect('/role-selection');

        $staff->name = $request->name;
        $staff->phone = $request->phone;
        if ($request->filled('password')) {
            $request->validate([
                'password' => [
                    'min:8',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*#?&~_]/',
                ]
            ], [
                'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character/symbol.',
                'password.min' => 'The password must be at least 8 characters long.',
            ]);
            $staff->password = Hash::make($request->password);
        }
        $staff->save();

        return back()->with('success', 'Profile and login credentials updated successfully.');
    }

    // Retained for compatibility with general routing parameters
    public function resolveComplaintStaff(Request $request, $id)
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

        return back()->with('success', 'Complaint resolved successfully.');
    }

    public function markNotifRead($id)
    {
        $n = SystemNotification::findOrFail($id);
        $n->read = true;
        $n->save();
        return back()->with('success', 'Notification marked read.');
    }
}
