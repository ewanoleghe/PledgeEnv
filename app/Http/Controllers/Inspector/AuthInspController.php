<?php

namespace App\Http\Controllers\Inspector;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;


use App\Models\Inspector;
use App\Models\Record;
use App\Models\DiscountCode;
use App\Models\BookingRequest;

class AuthInspController extends Controller
{
    /**
     * Display a Agent Login Page.
     */
    public function index()
    {
        return Inertia::render('Inspector/Login');    
    }

    
    // Inspectors Register & Login Controller
    /**
     * Handle inspector login.
     */
    public function handler(Request $request)
    {
        // Validate login credentials
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Attempt login
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            // Get the logged-in inspector
            $inspector = Auth::user();

            $discountCodes = DiscountCode::where('inspector_name', $inspector->name)->get();

            // Get the current week's Sunday to Saturday range
            $startOfWeek = now()->startOfWeek(); // Sunday
            $endOfWeek = $startOfWeek->copy()->addDays(6); // Saturday

            // Fetch counts of bookings
            $countInsBookings = BookingRequest::where('assignTo', $inspector->name)->count();
            $countIncRec = Record::where('inspector_name', $inspector->name)
            ->groupBy('order_id')
            ->count();

            $countIncCompleted = BookingRequest::where('assignTo', $inspector->name)
                ->where('jobStatus', 'completed')
                ->count();
            $countIncPending = BookingRequest::where('assignTo', $inspector->name)
                ->where('jobStatus', 'pending')
                ->count();
            $overDue = BookingRequest::where('assignTo', $inspector->name)  // Include the inspector filter
                ->whereIn('jobStatus', ['pending', 'completed'])  // Pending or completed but date is past 3 days
                ->where('selected_date', '<', Carbon::now()->subDays(3))
                ->count();

            $upComing = BookingRequest::where('assignTo', $inspector->name)  // Include the inspector filter
                ->whereIn('jobStatus', ['pending', 'completed'])  // Pending or completed but date is past 3 days
                ->where('selected_date', '>', Carbon::now())
                ->count();
        
            $reInspectionBook = BookingRequest::where('assignTo', $inspector->name)
                ->where('jobStatus', '!=', 'completed')  // Pending or completed but date is past 3 days
                ->whereNotNull('order_id')
                ->whereNotNull('old_order_id')
                ->count();

            // Get the bookings assigned to the inspector
            $bookingRequests = BookingRequest::where('assignTo', $inspector->name)
                ->orderBy('selected_date')
                ->get()
                ->map(function ($item) {
                    $item->selected_date = \Carbon\Carbon::parse($item->selected_date)->format('Y-m-d');
                    return $item;
                })
                ->groupBy('selected_date');

            // Extract order_ids from booking requests
            $orderIds = $bookingRequests->flatten()->pluck('order_id')->unique();

            // Retrieve records grouped by order_id
            $records = Record::whereIn('order_id', $orderIds)
                ->get()
                ->groupBy('order_id'); // Group by order_id

                // Extract order_ids from booking requests
            $orderIds = $bookingRequests->flatten()->pluck('order_id')->unique();

            // Retrieve records grouped by order_id
            $records = Record::whereIn('order_id', $orderIds)
                ->get()
                ->groupBy('order_id'); // Group by order_id

            // Pass the counts and data to the view
            return Inertia::render('Inspector/Dashboard', [
                'eUser' => $inspector,
                'discountCodes' => $discountCodes,
                'bookingRequests' => $bookingRequests,
                'records' => $records,
                'countInsBookings' => $countInsBookings,
                'countIncRec' => $countIncRec,  // Pass the correct variable
                'countIncCompleted' => $countIncCompleted,
                'countIncPending' => $countIncPending,  // Pass the correct variable
                'overDue' => $overDue,  // Already passed correctly
                'upComing' => $upComing,  // Already passed correctly
                'reInspectionBook' => $reInspectionBook  // Already passed correctly
            ]);
        }
    
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inspector.login');
    }

    public function requestPass()
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status')
        ]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPass(Request $request)
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token')  // grab the token from the route
        ]);
    }

    public function updatePass(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:3|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Inspector $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('inspector.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

   // Method to display the user's settings page
    public function userSettings()
    {
        $inspector = Auth::user();  // Get the currently authenticated user

        return Inertia::render('Inspector/Profile/UserSettings', [
            'eUser' => $inspector, // Pass the user data to the front-end
        ]);
    }

    // Method to display the inspector's edit page
    public function editInspector($id)
    {
        // Find the inspector by ID (or any other logic you need)
        $inspector = Inspector::find($id);  // Assuming 'User' is the model for your inspector

        return Inertia::render('Inspector/Profile/Edit', [
            'eUser' => $inspector, // Pass the inspector's data to the edit page
        ]);
    }

    public function updateInfo(Request $request)
    {
        $inspector = Auth::user();
        
        // Validate the incoming data
        $fields = $request->validate([
            'phone_number' => ['required', 'max:15'],
            'email' => ['required', 'lowercase', 'email', 'max:255', Rule::unique(Inspector::class)->ignore($request->user()->id)]
        ]);

        // Update the user's Phone and email
        $request->user()->fill($fields);

        // If email is updated, mark as unverified and Request a verification notification
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Save the updated user
        $inspector->save();

        // Get the logged-in user's ID
        $userId = $request->user()->id;
        
        // Redirect back to profile edit page with a success message
        return redirect()->route('edit-inspector', ['id' => $userId]);
    }

    public function updatePassword(Request $request)
    {
        // Validate the incoming request data
        $fields = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:3']
            // 'password' => ['required', 'confirmed', 'min:8']  // Enforce stronger password
        ]);

        // Update the user's password
        $request->user()->update([
            'password' => Hash::make($fields['password'])
        ]);

        // Get the logged-in user's ID
        $userId = $request->user()->id;

        // Redirect back to profile edit page, passing the user's ID
        return redirect()->route('edit-inspector', ['id' => $userId]);
    }



}

