<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class PageController extends Controller
{
    //About Us
    public function about()
    {
        return Inertia::render('AboutUs');
    }

    // Contact us
    public function contact()
    {
        return Inertia::render('ContactUs');
    }

    // Handle Contact Form Submission
    public function submitContact(Request $request)
    {
        // Validate form input
        $validated = $request->validate([
            'name'    => 'required|string|max:255|regex:/^(?!.*https?:\/\/).*$/',  // Disallow URLs
            'email'   => 'required|email|max:255|regex:/^(?!.*https?:\/\/).*$/', // Disallow URLs
            'phone'   => 'required|string|max:20|regex:/^(?!.*https?:\/\/).*$/', // Disallow URLs
            'subject' => 'required|string|max:255|regex:/^(?!.*https?:\/\/).*$/', // Disallow URLs
            'message' => 'required|string|min:10|regex:/^(?!.*https?:\/\/).*$/', // Disallow URLs
        ]);

        // Send email
        Mail::to(env('COMPANY_EMAIL'))->send(new ContactFormMail($validated));

        return redirect()->route('contact')
            ->with('message', 'Thank You! Your message has been successfully sent. We appreciate you reaching out and will get back to you as soon as possible.');
    }

}
