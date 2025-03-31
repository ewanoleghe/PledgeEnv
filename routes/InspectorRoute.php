<?php

use App\Http\Controllers\Inspector\InspectorController;
use App\Http\Controllers\Inspector\DashboardController;
use App\Http\Controllers\Inspector\AuthInspController;
use App\Http\Controllers\Inspector\FileController;
use App\Http\Controllers\Inspector\EmailVerificationController; 

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    
    //------------------- InspectorRoute -------------------//
    Route::get('/register', [InspectorController::class, 'create'])->name('register');
    Route::post('/register', [InspectorController::class, 'store']);
    Route::get('/inspector/verify/{token}', [InspectorController::class,'verifyEmail'])->name('inspector.verify');
    //------------------- InspectorRoute -------------------//
    // Route::post('/inspector', [AuthInspController::class, 'authenticate']);
    Route::get('login', [AuthInspController::class, 'index'])->name('login');
    Route::get('/inspector', [AuthInspController::class, 'index'])->name('inspector.login');
    Route::post('/login', [AuthInspController::class, 'handler'])->name('login.handler');

    Route::get('/forgot-password', [AuthInspController::class, 'requestPass'])->name('password.request');
    Route::post('/forgot-password', [AuthInspController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthInspController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [AuthInspController::class, 'updatePass'])->name('password.update');
    //------------------- InspectorRoute -------------------//
    
    Route::apiResource('inspectors', InspectorController::class);

    //------------------- InspectorRoute -------------------//
    
});

Route::middleware('auth')->group(function () {
    //------------------- Email Verification -------------------//
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'handler'])->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');
    //------------------- Email Verification -------------------//
    // InspectorRoute AUTH
    Route::get('/dashboard', [InspectorController::class, 'dashboard'])->middleware('verified')->name('dashboard');
    Route::get('/view_inspection', [InspectorController::class, 'show'])->name('view_inspection'); // Show the inspection details
    Route::post('/update/booking', [InspectorController::class, 'updateJob'])->name('update_jobstatus'); // jobStatus == completed
    Route::post('/records/store', [InspectorController::class, 'storeRecords'])->name('records.store'); // Submit the inspection details
    // Route::get('/view_inspection/{id}', [InspectorController::class, 'showInspection'])->name('view_inspection'); // Get the page and allow refreshing the page
    Route::get('/booking/list', [InspectorController::class, 'bookingList'])->name('booking.list');
    Route::post('/update/status', [InspectorController::class, 'updateJobStatus'])->name('update_jobstatus_book'); // jobStatus == completed
    Route::get('/inspector/records/{id}', [InspectorController::class, 'documentUpload'])->name('records.show');
    // Other inspector routes can be added here booking.list
    Route::post('/logout', [AuthInspController::class, 'destroy'])->name('logout');
    // File routes
    Route::get('/file/license/{filename}', [FileController::class, 'showLicense']);
    Route::get('/file/signature/{filename}', [FileController::class, 'showSignature']);
    //------------------- InspectorRoute -------------------// 
    //------------------- InspectorRoute - AuthInspController -------------------// 
    Route::get('/dashboard/settings', [AuthInspController::class, 'userSettings'])->name('dashboard.settings'); // Auth user settings
    // This is the route that will be hit when the user clicks the Edit button.
    Route::get('/inspector/edit/{id}', [AuthInspController::class, 'editInspector'])->name('edit-inspector');

    // POST route for saving the updated inspector information.
    Route::post('/inspector/edit', [AuthInspController::class, 'storeInspector'])->middleware('throttle:6,1');
    Route::patch('/profile', [AuthInspController::class, 'updateInfo'])->name('profile.info');
    Route::put('/inspector', [AuthInspController::class, 'updatePassword'])->name('profile.password');
    
    //------------------- Inspector Dashboard - DashboardController -------------------// 

    Route::get('/dashboard/inspected', [DashboardController::class, 'bookingInspected'])->name('dashboard.inspected'); // Inspected
    Route::get('/dashboard/pending', [DashboardController::class, 'bookingPending'])->name('dashboard.pending'); // Pending
    Route::get('/dashboard/reinspection', [DashboardController::class, 'bookingReInspect'])->name('dashboard.reinspection'); // Re_nspection
    Route::get('/dashboard/queued', [DashboardController::class, 'bookingQueued'])->name('dashboard.queued'); // Queued
    Route::get('/dashboard/uploaded', [DashboardController::class, 'bookingUploaded'])->name('dashboard.uploaded'); // Uploaded
    Route::get('/dashboard/overdue', [DashboardController::class, 'bookingOverdue'])->name('dashboard.overdue'); // Overdue

    // Download Route
    Route::get('docs/{filename}', function ($filename) {
        // Ensure the file exists in the storage
        $filePath = storage_path('app/public/images/Docs/' . $filename);
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return abort(404, 'File not found');
    });

});


// Route::get('/secure-file/{filename}', [YourController::class, 'getFile'])
//     ->middleware('auth'); // Ensures only logged-in users can access the files
