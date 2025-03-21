<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\PageController;

Route::inertia('/', 'Home')->name('home');
Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
Route::get('/schedule/many', [ScheduleController::class, 'MultipleSchedule'])->name('schedule.many');
// Route::get('/service/request', [ScheduleController::class, 'RequestService'])->name('request.service');

// Display form (GET request)
Route::get('service/request', [ScheduleController::class, 'RequestService'])->name('request.service');
// Handle form submission (POST request)
Route::post('service/request', [ScheduleController::class, 'handleSingleRequestDate']);

Route::get('/service/time', [ScheduleController::class, 'ScheduleDate'])->name('request.time');
Route::post('/service/time', [ScheduleController::class, 'handleSingleRequestTime'])->name('request.time');

Route::get('/service/requested', [ScheduleController::class, 'getSingleRequestData'])->name('service.requested');
Route::post('/service/requested', [ScheduleController::class, 'handleSingleRequestData'])->name('service.requested');

// Terms
Route::get('/service/terms', [ScheduleController::class, 'getTerms'])->name('service.terms');
Route::post('/service/terms', [ScheduleController::class, 'handleSingleTOS'])->name('service.terms');

// Handle Discount Code submission (POST request)
Route::get('service/applyDiscount', [ScheduleController::class, 'getDiscountCode'])->name('service.applyDiscount');
Route::post('service/applyDiscount', [ScheduleController::class, 'DiscountCode'])->name('service.applyDiscount');

// SELECT PAYMENT TYPE (POST request)
Route::get('service/paymentOptions', [ScheduleController::class, 'payOptions'])->name('service.paymentOptions');
Route::post('service/paymentOptions', [ScheduleController::class, 'selectPayment'])->name('service.paymentOptions');

// About Us
Route::get('/about_us', [PageController::class, 'about'])->name('about');
Route::get('/contact_us', [PageController::class, 'contact'])->name('contact');
// Contact form submission route
Route::post('/contact_us', [PageController::class, 'submitContact'])->name('contact.submit');

// Route::get('/create-payment-intent', [ScheduleController::class, 'createPaymentIntent']);

// Download Route
Route::get('docs/{filename}', function ($filename) {
    // Ensure the file exists in the storage
    $filePath = storage_path('app/public/images/Docs/' . $filename);
    if (file_exists($filePath)) {
        return response()->download($filePath);
    }

    return abort(404, 'File not found');
});

require __DIR__ . '/paySchedule.php';
require __DIR__ . '/AdminRoute.php';
require __DIR__ . '/InspectorRoute.php';

// 404 Catch-all Route - MUST BE LAST
// Add at the bottom of your routes file
Route::fallback([ErrorController::class, 'notFound']);
