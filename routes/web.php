<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StaffController;

// 1. Landing Routes
Route::get('/', [LandingController::class, 'index']);
Route::post('/inquiry', [LandingController::class, 'inquiry']);

// 2. Authentication Routes
Route::get('/role-selection', [AuthController::class, 'showRoleSelection']);
Route::get('/login/{role}', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'handleLogin']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'handleRegister']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::get('/forgot-password/{role}', [AuthController::class, 'showForgotPassword']);
Route::post('/forgot-password', [AuthController::class, 'handleForgotPassword']);
Route::get('/forgot-password-verify', [AuthController::class, 'showForgotPasswordVerify']);
Route::post('/forgot-password-verify', [AuthController::class, 'handleForgotPasswordVerify']);

// 3. Admin Dashboard Routes
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    
    // PG Buildings actions
    Route::post('/add-pg', [AdminController::class, 'addPg']);
    Route::post('/toggle-pg/{id}', [AdminController::class, 'togglePgStatus']);
    Route::post('/delete-pg/{id}', [AdminController::class, 'deletePg']);
    
    // Rooms actions
    Route::post('/add-room', [AdminController::class, 'addRoom']);
    Route::post('/edit-room-rent/{id}', [AdminController::class, 'editRoomRent']);
    Route::post('/delete-room/{id}', [AdminController::class, 'deleteRoom']);
    
    // Student Approvals actions
    Route::post('/approve-student/{id}', [AdminController::class, 'approveStudent']);
    Route::post('/reject-student/{id}', [AdminController::class, 'rejectStudent']);
    Route::post('/revoke-student/{id}', [AdminController::class, 'revokeStudent']);
    Route::post('/delete-student/{id}', [AdminController::class, 'deleteStudent']);
    
    // Payments actions
    Route::post('/update-payment-config', [AdminController::class, 'updatePaymentConfig']);
    Route::post('/generate-due', [AdminController::class, 'generateDue']);
    Route::post('/reconcile-payment/{id}', [AdminController::class, 'reconcilePaymentManual']);
    Route::post('/approve-payment/{id}', [AdminController::class, 'approvePayment']);
    Route::post('/update-invoice/{id}', [AdminController::class, 'updateInvoice']);
    Route::post('/delete-invoice/{id}', [AdminController::class, 'deleteInvoice']);
    
    // Complaints actions
    Route::post('/assign-complaint/{id}', [AdminController::class, 'assignComplaint']);
    Route::post('/resolve-complaint/{id}', [AdminController::class, 'resolveComplaint']);
    
    // Staff actions
    Route::post('/add-staff', [AdminController::class, 'addStaff']);
    Route::post('/delete-staff/{id}', [AdminController::class, 'deleteStaff']);
    Route::post('/assign-room-cleaning/{id}', [AdminController::class, 'assignRoomCleaning']);
    Route::post('/assign-common-cleaning/{id}', [AdminController::class, 'assignCommonAreaCleaning']);
    Route::post('/assign-room-maintenance', [AdminController::class, 'assignRoomMaintenance']);
    
    // Notices actions
    Route::post('/publish-notice', [AdminController::class, 'publishNotice']);
    Route::post('/delete-notice/{id}', [AdminController::class, 'deleteNotice']);
    
    // Food Menus actions
    Route::post('/update-menu/{id}', [AdminController::class, 'updateFoodMenu']);
    
    // Inventory actions
    Route::post('/add-inventory', [AdminController::class, 'addInventory']);
    Route::post('/adjust-inventory/{id}', [AdminController::class, 'adjustInventory']);
    Route::post('/delete-inventory/{id}', [AdminController::class, 'deleteInventory']);
    
    // CMS Settings actions
    Route::post('/update-landing', [AdminController::class, 'updateLanding']);
    
    // Visitor Inquiries
    Route::post('/resolve-inquiry/{id}', [AdminController::class, 'resolveInquiry']);
    Route::post('/delete-inquiry/{id}', [AdminController::class, 'deleteInquiry']);
    
    // Notifications & Diagnostics
    Route::post('/mark-read/{id}', [AdminController::class, 'markNotifRead']);
    Route::post('/test-email', [AdminController::class, 'testEmail']);
});

// 4. Student Dashboard Routes
Route::prefix('student')->group(function () {
    Route::get('/', [StudentController::class, 'index']);
    Route::post('/pay-submit/{id}', [StudentController::class, 'submitPayment']);
    Route::post('/raise-complaint', [StudentController::class, 'raiseComplaint']);
    Route::post('/save-food', [StudentController::class, 'saveFoodPreference']);
    Route::post('/verify-complaint/{id}/{state}', [StudentController::class, 'verifyComplaint']);
    Route::post('/submit-meal-feedback', [StudentController::class, 'submitMealFeedback']);
    Route::post('/mark-read/{id}', [StudentController::class, 'markNotifRead']);
});

// 5. Staff Dashboard Routes
Route::prefix('staff')->group(function () {
    Route::get('/', [StaffController::class, 'index']);
    Route::post('/toggle-cleaning/{id}/{state}', [StaffController::class, 'toggleCleaningState']);
    Route::post('/toggle-common-area/{id}/{state}', [StaffController::class, 'toggleCommonAreaCleaningState']);
    Route::post('/reset-cleaning', [StaffController::class, 'resetCleaningChecklist']);
    Route::post('/resolve-complaint/{id}', [StaffController::class, 'resolveComplaintStaff']);
    Route::post('/report-maintenance', [StaffController::class, 'reportMaintenanceIssue']);
    Route::post('/submit-report', [StaffController::class, 'submitDailyReport']);
    Route::post('/update-profile', [StaffController::class, 'updateProfile']);
    Route::post('/mark-read/{id}', [StaffController::class, 'markNotifRead']);
    
    // Food specific
    Route::post('/update-menu/{id}', [StaffController::class, 'updateFoodMenu']);
    Route::post('/adjust-inventory/{id}', [StaffController::class, 'adjustFoodInventory']);
    
    // Maintenance specific
    Route::post('/update-complaint/{id}', [StaffController::class, 'updateMaintenanceComplaint']);
    Route::post('/reply-food-message/{id}', [StaffController::class, 'replyFoodMessage']);
});

Route::post('/profile/update', [AuthController::class, 'updateProfile']);
Route::get('/resume', function () { return view('resume'); });
