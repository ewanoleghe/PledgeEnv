<?php

namespace App\Http\Controllers\Inspector;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;


class EmailVerificationController extends Controller
{
    public function notice()
    {
        return Inertia::render('Auth/VerifyEmail', [
            'status' => session('status')
        ]);
    }

    public function handler(EmailVerificationRequest $request)
    {
        // This will automatically find the user by ID and verify the email
        $request->fulfill();
        
        return redirect()->route('dashboard');

    }


    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent!');
    }
}
