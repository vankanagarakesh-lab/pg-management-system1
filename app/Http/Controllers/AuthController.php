<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PgBuilding;
use App\Models\Room;
use App\Models\SystemNotification;
use App\Models\LandingContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRoleSelection()
    {
        $landingContent = LandingContent::first();
        return view('auth.role_selection', compact('landingContent'));
    }

    public function showLogin($role)
    {
        if (!in_array($role, ['admin', 'student', 'staff'])) {
            abort(404);
        }
        $landingContent = LandingContent::first();
        return view('auth.login', compact('role', 'landingContent'));
    }

    public function handleLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:admin,student,staff'
        ]);

        $email = strtolower($request->email);
        $role = $request->role;

        // Admin Email Guard
        if ($role === 'admin') {
            $adminUser = User::where('role', 'admin')->first();
            $adminEmail = $adminUser ? $adminUser->email : 'admin@gmail.com';
            if ($email !== $adminEmail) {
                return back()->withErrors(['email' => 'Only the registered admin account can log in as Admin.']);
            }
        }

        $user = User::where('email', $email)->where('role', $role)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid email address or password. Please try again.']);
        }

        // Student Approval Guard
        if ($role === 'student') {
            if ($user->approval_status === 'pending') {
                return back()->withErrors(['email' => 'Your tenant account registration is pending review by the administrator. Please await confirmation.']);
            } elseif ($user->approval_status === 'rejected') {
                return back()->withErrors(['email' => 'Your tenant account registration has been rejected. Contact administration for details.']);
            }
        }

        // Set custom session
        session(['pg_user_id' => $user->id, 'pg_user_role' => $user->role]);

        return redirect($role === 'admin' ? '/admin' : ($role === 'student' ? '/student' : '/staff'));
    }

    public function showRegister()
    {
        $pgs = PgBuilding::where('status', 'active')->get();
        // Load rooms with space left
        $rooms = Room::all()->filter(function($r) {
            return ($r->capacity - $r->occupied) > 0;
        })->values();
        $landingContent = LandingContent::first();
        return view('auth.register', compact('pgs', 'rooms', 'landingContent'));
    }

    public function handleRegister(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|digits:10',
            'password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&~_]/',
            ],
            'college' => 'required',
            'course' => 'required',
            'year' => 'required',
            'pg_building_id' => 'required',
            'room_number' => 'required',
            'guardian_name' => 'required',
            'guardian_phone' => 'required|digits:10',
            'address' => 'required'
        ], [
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character/symbol.',
            'password.min' => 'The password must be at least 8 characters long.',
        ]);

        // Calculate room type
        $room = Room::where('pg_building_id', $request->pg_building_id)
                    ->where('number', $request->room_number)
                    ->first();

        if ($room && $room->occupied >= $room->capacity) {
            return back()->withErrors(['room_number' => 'Selected room is already fully occupied.']);
        }

        // Handle profile photo (convert to Base64)
        $profilePhoto = '';
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $profilePhoto = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file));
        }

        // Handle ID proof (convert to Base64)
        $idProof = '';
        if ($request->hasFile('id_proof')) {
            $file = $request->file('id_proof');
            $idProof = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file));
        }

        $student = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'room_number' => $request->room_number,
            'room_type' => $room ? $room->type : 'Single Sharing',
            'year' => $request->year,
            'course' => $request->course,
            'college' => $request->college,
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
            'address' => $request->address,
            'profile_photo' => $profilePhoto,
            'id_proof' => $idProof,
            'role' => 'student',
            'approval_status' => 'pending',
            'pg_building_id' => $request->pg_building_id,
            'room_id' => $room ? $room->id : null
        ]);

        // Notify Admin (Insert into database notifications)
        SystemNotification::create([
            'date' => date('Y-m-d'),
            'text' => "New registration: {$request->name} (Room #{$request->room_number}) is pending approval.",
            'type' => 'admin',
            'read' => false
        ]);

        return redirect('/login/student')->with('success', 'Your registration request is submitted! Please wait for admin approval before logging in.');
    }

    public function logout()
    {
        session()->forget(['pg_user_id', 'pg_user_role']);
        return redirect('/');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required'
        ]);

        $userId = session('pg_user_id');
        if (!$userId) {
            return redirect('/role-selection')->withErrors(['auth' => 'Please sign in first.']);
        }

        $user = User::findOrFail($userId);
        $user->name = $request->name;
        $user->phone = $request->phone;

        if ($user->role === 'admin') {
            $request->validate([
                'email' => 'required|email|unique:users,email,' . $user->id
            ]);
            $user->email = strtolower($request->email);
        }

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
            $user->password = Hash::make($request->password);
        }

        // Handle Profile Photo Upload
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/profiles'), $filename);
            $user->profile_photo = '/storage/profiles/' . $filename;
        }

        $user->save();

        return back()->with('success', 'Your profile details updated successfully.');
    }

    public function showForgotPassword($role)
    {
        if (!in_array($role, ['admin', 'student', 'staff'])) {
            abort(404);
        }
        $landingContent = LandingContent::first();
        return view('auth.forgot_password', compact('role', 'landingContent'));
    }

    public function handleForgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:admin,student,staff'
        ]);

        $email = strtolower(trim($request->email));
        $role = trim($request->role);

        $user = User::whereRaw('LOWER(TRIM(email)) = ?', [$email])
                    ->where(function($q) use ($role) {
                        $q->whereRaw('LOWER(TRIM(role)) = ?', [strtolower($role)])
                          ->orWhereRaw('LOWER(TRIM(staff_role)) = ?', [strtolower($role)]);
                    })
                    ->first();

        if (!$user) {
            return back()->withInput()->withErrors(['email' => 'No account found with email "' . $email . '" for role "' . ucfirst($role) . '".']);
        }

        // Generate 6 digit OTP
        $code = rand(100000, 999999);

        // Store details in session
        session([
            'reset_email' => $user->email,
            'reset_role' => $role,
            'reset_code' => $code
        ]);

        // Send OTP mail
        try {
            $fromAddress = config('mail.from.address') ?: (env('MAIL_USERNAME') ?: 'hello@example.com');
            $fromName = config('mail.from.name') ?: (env('APP_NAME') ?: 'Thulasi PG');
            
            \Illuminate\Support\Facades\Mail::raw("Hello {$user->name},\n\nYour password reset verification code for Thulasi PG is: {$code}\n\nIf you did not request this code, please ignore this email.\n\nBest regards,\nThulasi PG Team", function ($message) use ($user, $fromAddress, $fromName) {
                $message->from($fromAddress, $fromName)
                        ->to($user->email)
                        ->subject("Password Reset OTP - Thulasi PG");
            });

            \Illuminate\Support\Facades\Log::info("[Password Reset OTP Sent] To: {$user->email}, Code: {$code}");

            return redirect('/forgot-password-verify')->with('info', "Verification code has been sent to {$user->email}.");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Mail delivery failed during password reset for {$user->email}: " . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            \Illuminate\Support\Facades\Log::info("RESET OTP CODE FOR {$user->email} ($role): $code");

            return back()->withInput()->withErrors(['email' => 'Failed to deliver OTP email to ' . $user->email . ': ' . $e->getMessage() . '. Please verify your Render SMTP environment settings.']);
        }
    }

    public function showForgotPasswordVerify()
    {
        $email = session('reset_email');
        $role = session('reset_role');

        if (!$email || !$role) {
            return redirect('/role-selection');
        }

        $landingContent = LandingContent::first();
        return view('auth.forgot_password_verify', compact('email', 'role', 'landingContent'));
    }

    public function handleForgotPasswordVerify(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'password' => [
                'required',
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

        $sessionCode = session('reset_code');
        $email = session('reset_email');
        $role = session('reset_role');

        if (!$email || !$role || !$sessionCode) {
            return redirect('/role-selection')->withErrors(['error' => 'Reset session expired. Please try again.']);
        }

        if (trim($request->code) !== (string)$sessionCode) {
            return back()->withErrors(['code' => 'Invalid or expired verification code.']);
        }

        // Update password
        $user = User::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($email))])->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            // Clear session keys
            session()->forget(['reset_email', 'reset_role', 'reset_code']);

            return redirect('/login/' . $role)->with('success', 'Password reset successfully. Please log in with your new password.');
        }

        return redirect('/role-selection')->withErrors(['error' => 'User not found.']);
    }
}
