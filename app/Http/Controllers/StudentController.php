<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Room;
use App\Models\PgBuilding;
use App\Models\Payment;
use App\Models\PaymentConfig;
use App\Models\Complaint;
use App\Models\FoodPreference;
use App\Models\FoodMenu;
use App\Models\Notice;
use App\Models\SystemNotification;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    private function getActiveUser()
    {
        $userId = session('pg_user_id');
        if (!$userId) return null;
        return User::find($userId);
    }

    public function index(Request $request)
    {
        $student = $this->getActiveUser();
        if (!$student || $student->role !== 'student') {
            return redirect('/role-selection')->withErrors(['auth' => 'Please sign in as a student to access this dashboard.']);
        }

        $tab = $request->query('tab', 'overview');

        // 1. My Room Details
        $pg = PgBuilding::find($student->pg_building_id);
        $room = Room::where('pg_building_id', $student->pg_building_id)->where('number', $student->room_number)->first();
        
        // Find Roommates
        $roommates = User::where('role', 'student')
                         ->where('approval_status', 'approved')
                         ->where('pg_building_id', $student->pg_building_id)
                         ->where('room_number', $student->room_number)
                         ->where('id', '!=', $student->id)
                         ->get();

        // 2. Payments & Rent Invoice
        $payments = Payment::where('student_email', $student->email)->latest()->get();
        $dues = $payments->where('status', 'Due');
        $totalPaid = $payments->where('status', 'Paid')->sum('amount');
        $paymentConfig = PaymentConfig::first();

        // 3. Food Preferences & menu
        $foodDate = $request->query('food_date', date('Y-m-d', strtotime('+1 day')));
        $foodPref = FoodPreference::where('email', $student->email)->where('date', $foodDate)->first();
        $foodMenu = FoodMenu::all();

        // 4. Complaints
        $complaints = Complaint::where('student_email', $student->email)->latest()->get();

        // 5. Notices
        $notices = Notice::whereIn('target', ['all', 'student'])->latest()->get();

        return view('student.dashboard', compact(
            'student', 'tab', 'pg', 'room', 'roommates', 'payments', 'dues', 'totalPaid',
            'paymentConfig', 'foodDate', 'foodPref', 'foodMenu', 'complaints', 'notices'
        ));
    }

    public function submitPayment(Request $request, $id)
    {
        $request->validate([
            'tx_id' => 'required',
            'method' => 'required',
            'month' => 'required',
            'payment_date' => 'required'
        ]);
        
        $student = $this->getActiveUser();
        if (!$student) return redirect('/login/student');

        $payment = Payment::findOrFail($id);
        $payment->status = 'Pending Approval';
        $payment->month = $request->month;
        $payment->payment_date = $request->payment_date;
        $payment->tx_id = $request->tx_id;
        $payment->method = $request->method;
        $payment->save();

        // Notify Admin
        SystemNotification::create([
            'date' => date('Y-m-d'),
            'text' => "Payment Approval Required: Tenant \"{$student->name}\" submitted UTR/Ref: {$request->tx_id} for {$payment->month} via {$request->method}.",
            'type' => 'admin',
            'read' => false
        ]);

        return back()->with('success', 'Payment reference submitted! Awaiting administrator approval.');
    }

    public function raiseComplaint(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
            'priority' => 'required'
        ]);
        
        $student = $this->getActiveUser();
        if (!$student) return redirect('/login/student');

        $c = Complaint::create([
            'student_email' => $student->email,
            'student_name' => $student->name,
            'room_number' => $student->room_number ?? 'Unassigned',
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority,
            'status' => 'Pending',
            'verification_status' => 'Pending',
            'created_date' => date('Y-m-d')
        ]);

        // Notify Admin
        SystemNotification::create([
            'date' => date('Y-m-d'),
            'text' => "New Complaint: \"{$request->title}\" registered by Room #{$student->room_number} occupant.",
            'type' => 'admin',
            'read' => false
        ]);

        // Notify Staff Roles matching category in the student's PG building
        if ($student->pg_building_id) {
            $staffMembers = \App\Models\User::where('role', 'staff')
                ->where('pg_building_id', $student->pg_building_id)
                ->get();

            if ($request->category === 'Food') {
                $targetStaff = $staffMembers->where('staff_role', 'Food Management');
            } elseif (in_array($request->category, ['Electrical', 'Plumbing', 'Furniture', 'Wi-Fi', 'Water'])) {
                $targetStaff = $staffMembers->where('staff_role', 'Maintenance');
            } else {
                $targetStaff = $staffMembers->where('staff_role', 'Housekeeping');
            }

            foreach ($targetStaff as $staffObj) {
                SystemNotification::create([
                    'date' => date('Y-m-d'),
                    'text' => "New Message/Complaint: \"{$request->title}\" from {$student->name} (Room #{$student->room_number}).",
                    'type' => 'staff',
                    'user_id' => $staffObj->id,
                    'read' => false
                ]);
            }
        }

        return back()->with('success', 'Complaint ticket submitted successfully.');
    }

    public function verifyComplaint(Request $request, $id, $state)
    {
        $c = Complaint::findOrFail($id);
        
        if ($state === 'Verified') {
            $c->verification_status = 'Verified';
        } elseif ($state === 'Unresolved') {
            $c->verification_status = 'Unresolved';
            $c->status = 'Pending'; // Reopened!
            $c->reply = "Reopened by tenant: " . ($request->reason ?: 'Issue still persists.');
            
            // Notify Admin
            SystemNotification::create([
                'date' => date('Y-m-d'),
                'text' => "Ticket Reopened: Complaint \"{$c->title}\" marked as unresolved by tenant.",
                'type' => 'admin',
                'read' => false
            ]);
        }
        $c->save();

        return back()->with('success', "Ticket marked as {$state} successfully.");
    }

    public function saveFoodPreference(Request $request)
    {
        $request->validate(['date' => 'required']);
        $student = $this->getActiveUser();
        if (!$student) return redirect('/login/student');

        // Checkbox values (true means OPT OUT / NO FOOD)
        $morning = $request->has('morning') ? 1 : 0;
        $afternoon = $request->has('afternoon') ? 1 : 0;
        $evening = $request->has('evening') ? 1 : 0;

        $pref = FoodPreference::where('email', $student->email)->where('date', $request->date)->first();

        if ($pref) {
            $pref->update([
                'morning' => $morning,
                'afternoon' => $afternoon,
                'evening' => $evening
            ]);
        } else {
            FoodPreference::create([
                'email' => $student->email,
                'name' => $student->name,
                'room' => $student->room_number ?? 'Unassigned',
                'date' => $request->date,
                'morning' => $morning,
                'afternoon' => $afternoon,
                'evening' => $evening
            ]);
        }

        return back()->with('success', 'Exclusion food preferences logged successfully!');
    }

    public function submitMealFeedback(Request $request)
    {
        $request->validate([
            'meal_type' => 'required',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'required'
        ]);

        $student = $this->getActiveUser();
        if (!$student) return redirect('/login/student');

        Complaint::create([
            'student_email' => $student->email,
            'student_name' => $student->name,
            'room_number' => $student->room_number ?? 'Unassigned',
            'title' => "Meal Feedback: {$request->meal_type}",
            'description' => "Rating: {$request->rating}/5 stars. Comment: {$request->comment}",
            'category' => 'Food',
            'priority' => 'Medium',
            'status' => 'Resolved',
            'verification_status' => 'Verified',
            'created_date' => date('Y-m-d')
        ]);

        return back()->with('success', 'Thank you! Your meal feedback has been logged for Food Management.');
    }

    public function markNotifRead($id)
    {
        $n = SystemNotification::findOrFail($id);
        $n->read = true;
        $n->save();
        return back()->with('success', 'Notification marked read.');
    }
}
