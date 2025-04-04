<?php

use App\Http\Controllers\Admin\AuthenticateController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CalendarViewController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceController;
// use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

// Route::middleware('guest')->group(function () {
    
    //------------------- AdminRoute -------------------//app/Http/Controllers/Admin/AdminController.php
// Admin Authentication Routes
Route::middleware('guest:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AuthenticateController::class, 'login'])->name('login');
    Route::post('/login', [AuthenticateController::class, 'handleLogin'])->name('handleLogin');
});

// Protected Admin Routes
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/bookings', [BookingController::class, 'indexAll'])->name('bookings');
    Route::post('/bookings/update_pay_status', [BookingController::class, 'updatePaymentStatus'])->name('bookings.update_pay_status');
    Route::get('/viewBooking/{id}', [BookingController::class, 'viewApplication'])->name('viewBooking');  // View a registration using id 
    Route::get('/updateBooking/{id}', [BookingController::class, 'getUpdateBooking'])->name('bookings.get_update'); // Get Update booking
    Route::patch('/updateBooking/{id}', [BookingController::class, 'updateBooking'])->name('update_postBooking');

    // Correct route definition for POST request
    Route::post('/viewBooking/admin.bookings.update_payment_status', [BookingController::class, 'updatePaymentStatusBookingView'])
     ->name('bookings.update_payment_status');
    Route::post('/delete_application', [BookingController::class, 'deleteApplication'])->name('delete_application');

    Route::get('/scheduled_bookings', [CalendarViewController::class, 'indexAll'])->name('scheduled_bookings');
    Route::post('/all_date_model', [CalendarViewController::class, 'viewDatesBooking'])->name('viewdate_booking');
    Route::post('/calendar/update_cal_payment_status', [CalendarViewController::class, 'updatePaymentStatusC'])->name('calendar.update_cal_payment_status');

    //------------------- AdminRoute Inspectors-------------------//
    Route::get('/inspectors', [AdminController::class, 'indexInspectors'])->name('inspectors');


    //------------------- AdminRoute REPORTS,ACTIVITIES & CERTIFICATES-------------------//
    Route::get('/inspection/completed', [ReportController::class, 'inspectionCompleted'])->name('inspection.completed'); // Completed inspection- Doc. Pending
    Route::get('/inspection/review_needed', [ReportController::class, 'reportReview'])->name('inspection.review_needed'); // Admin Review Needed
    Route::get('/inspection/report_sent', [ReportController::class, 'reportSent'])->name('inspection.report_sent'); // Report Sent
    Route::get('/inspection/expire90', [ReportController::class, 'certificatesExpiring'])->name('inspection.expire90'); // Expiring in 90 Days

    Route::get('/report/send_report_status', [ReportController::class, 'sendReport'])->name('admin.send_report_status'); // Re-Send Report Status
    Route::get('/report/viewRecord/{id}', [ReportController::class, 'viewResources'])->name('viewRecord'); // View Record
    Route::get('/signature/{filename}', [ReportController::class, 'showSignature'])->name('signature.show'); // take the filename passed from the frontend, and the showSignature method will serve the image to the user.
    Route::post('/report/submit', [ReportController::class, 'approveReport'])->name('report.submit'); // Reject or Approve the report


    Route::get('/report/reset_application/{id}', [ReportController::class, 'applicationReset'])->name('reset_application'); // Cancel Reset

    Route::get('/discount/code', [AdminController::class, 'viewCode'])->name('discount.code');
    Route::get('/code/reset/{id}', [AdminController::class, 'resetCode'])->name('code.reset');
    Route::get('/discount/deactivate/{id}', [AdminController::class, 'killCode'])->name('code.deactivate');
    Route::get('/code/create', [AdminController::class, 'createCode'])->name('code.create');
    Route::post('/code/create', [AdminController::class, 'storeCode'])->name('newcode.create');

    Route::get('/inspectors/index', [AdminController::class, 'inspectorsIndex'])->name('inspectors.index');
    Route::get('/inspectors/view/{id}', [AdminController::class, 'inspectorsView'])->name('inspector.view');
    Route::get('/inspectors/status/{id}', [AdminController::class, 'inspectorsStatus'])->name('inspector.deactivate');
    // Route::get('/inspectors/index', [AdminController::class, 'inspectorsIndex'])->name('inspectors.index');

    //------------------- AdminRoute SERVICE && PRICE-------------------//
    Route::get('/service/index', [ServiceController::class, 'serviceIndex'])->name('service.index');
    Route::get('/service/create', [ServiceController::class, 'createServiceGet'])->name('service.create');
    Route::post('/service/create', [ServiceController::class, 'storeService'])->name('service.store');
    Route::get('/service/view/{id}', [ServiceController::class, 'getService'])->name('service.view');
    Route::get('/service/status/{id}', [ServiceController::class, 'getUpdateService'])->name('service.update');
    Route::patch('/service/update/{id}', [ServiceController::class, 'updateService'])->name('post.update');
    Route::post('/service/delete', [ServiceController::class, 'Destroy'])->name('service.delete');

    // Sign Up code
    Route::get('/service/code_inspector', [ServiceController::class, 'codeIndex'])->name('service.sign_code');
    Route::get('/service/red_code_create', [ServiceController::class, 'codeCreate'])->name('regcode.create');
    Route::post('/service/red_code_create', [ServiceController::class, 'storeCode'])->name('regcode.store');
    Route::post('/service/kill_code/{id}', [ServiceController::class, 'DestroyCode'])->name('service.kill_code');



    // Resource Routes
    // Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    // Route::get('/bookings', [BookingController::class, 'index'])->name('admin.bookings');
    // Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');

    // Booking Request Routes
    // Route::get('/bookings', [BookingController::class, 'index'])->name('admin.bookings');
    
    // Logout
    Route::post('/logout', [AuthenticateController::class, 'destroy'])->name('logout');

});