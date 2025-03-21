<?php

use App\Http\Controllers\Payment\StripePaymentController;
use App\Http\Controllers\Payment\StripeWebhookController;

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    
    //------------------- Payment -------------------//
    Route::get('/service/booking', [StripePaymentController::class, 'getCheckout']);
    Route::post('/service/booking', [StripePaymentController::class, 'handleSingleCheckout'])->name('service.booking');
    Route::get('/payment/success', [StripePaymentController::class, 'success'])->name('payment.success');

    //------------------- Webhook -------------------//
    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);



});

