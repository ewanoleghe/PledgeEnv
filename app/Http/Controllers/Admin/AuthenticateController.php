<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthenticateController extends Controller
{
    public function login()
    {
        // Return the 'Admin/Login' Inertia page to render the login form
        return Inertia::render('Admin/Login');
    }

    public function handleLogin(Request $request)
    {
        // Validate the incoming request to ensure email and password are provided
        $credentials = $request->validate([
            'email' => 'required|email',  // Email is required and must be a valid email format
            'password' => 'required'      // Password is required
        ]);

        // Attempt to authenticate the admin using the provided credentials
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            // If authentication is successful, regenerate the session to prevent session fixation attacks
            $request->session()->regenerate();
            // Redirect the authenticated admin to the dashboard
            return redirect()->route('admin.dashboard');
        }

        // If authentication fails, redirect back with an error message for the email field
        return back()->withErrors([
            'email' => 'Invalid credentials',  // Custom error message for invalid login
        ])->onlyInput('email');  // Retain the email input value so the user can correct it
    }

    public function destroy(Request $request)
    {
        // Log the admin out of the application
        Auth::guard('admin')->logout();
        // Invalidate the session to prevent unauthorized access
        $request->session()->invalidate();
        // Regenerate the CSRF token to prevent session fixation or hijacking
        $request->session()->regenerateToken();
        // Redirect the user back to the login page
        return redirect()->route('admin.login');
    }


}
