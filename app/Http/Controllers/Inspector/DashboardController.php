<?php

namespace App\Http\Controllers\Inspector;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Auth\Events\Registered;


use App\Models\Inspector;
use App\Models\TeamCode;
use App\Models\DiscountCode;
use App\Models\BookingRequest;
use App\Models\Record;
use App\Models\RecordPhoto;
use Carbon\Carbon;

use Inertia\Inertia;


class DashboardController extends Controller
{
    //Inspected
    public function bookingInspected(Request $request){

        // Ensure the user is authenticated before serving the file
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $inspector = Auth::user();

        // Retrieve the latest 10 inspected bookings
        $bookings = BookingRequest::where('assignTo', $inspector->name)
            ->where('jobStatus', 'completed') // filter for assigned bookings only
            ->when($request->search, function($query) use ($request) {
                $query->where(function ($query) use ($request) { // group the search conditions
                    $query->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('email', 'like', '%' . $request->search . '%')
                          ->orWhere('phone', 'like', '%' . $request->search . '%')
                          ->orWhere('city', 'like', '%' . $request->search . '%')
                          ->orWhere('county', 'like', '%' . $request->search . '%')
                          ->orWhere('municipality', 'like', '%' . $request->search . '%')
                          ->orWhere('InspectionType', 'like', '%' . $request->search . '%')
                          ->orWhere('methodology', 'like', '%' . $request->search . '%')
                          ->orWhere('payment_status', 'like', '%' . $request->search . '%')
                          ->orWhere('jobStatus', 'like', '%' . $request->search . '%')
                          ->orWhere('selected_date', 'like', '%' . $request->search . '%')
                          ->orWhere('order_id', 'like', '%' . $request->search . '%')
                          ->orWhere('old_order_id', 'like', '%' . $request->search . '%');
                });
    
            })
            ->orderBy('created_at', 'desc') // Change 'created_at' to your desired column
            ->paginate(10)
            ->withQueryString();
    
            // Extract order_ids from booking requests
            $orderIds = $bookings->pluck('order_id')->unique();
    
            // Retrieve records grouped by order_id
            $records = Record::whereIn('order_id', $orderIds)
                ->get()
                ->groupBy('order_id'); // Group by order_id

            $pageName = 'Site Visited, Documents Collected - Completed Inspection';
        
            return Inertia::render('Inspector/BookingList', [
                'bookings' => $bookings,
                'searchTerm' => $request->search,
                'records' => $records, // Now records are structured as { order_id: [records] }
                'pageName' => $pageName,
    
            ]);
    }

    // Pending Inspection
    public function bookingPending(Request $request){

        // Ensure the user is authenticated before serving the file
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $inspector = Auth::user();

        // Retrieve the latest 10 inspected bookings
        $bookings = BookingRequest::where('assignTo', $inspector->name)
            ->where('jobStatus', 'pending') // filter for assigned bookings only
            ->when($request->search, function($query) use ($request) {
                $query->where(function ($query) use ($request) { // group the search conditions
                    $query->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('email', 'like', '%' . $request->search . '%')
                          ->orWhere('phone', 'like', '%' . $request->search . '%')
                          ->orWhere('city', 'like', '%' . $request->search . '%')
                          ->orWhere('county', 'like', '%' . $request->search . '%')
                          ->orWhere('municipality', 'like', '%' . $request->search . '%')
                          ->orWhere('InspectionType', 'like', '%' . $request->search . '%')
                          ->orWhere('methodology', 'like', '%' . $request->search . '%')
                          ->orWhere('payment_status', 'like', '%' . $request->search . '%')
                          ->orWhere('jobStatus', 'like', '%' . $request->search . '%')
                          ->orWhere('selected_date', 'like', '%' . $request->search . '%')
                          ->orWhere('order_id', 'like', '%' . $request->search . '%')
                          ->orWhere('old_order_id', 'like', '%' . $request->search . '%');
                });
    
            })
            ->orderBy('created_at', 'desc') // Change 'created_at' to your desired column
            ->paginate(10)
            ->withQueryString();
    
            // Extract order_ids from booking requests
            $orderIds = $bookings->pluck('order_id')->unique();
    
            // Retrieve records grouped by order_id
            $records = Record::whereIn('order_id', $orderIds)
                ->get()
                ->groupBy('order_id'); // Group by order_id
        
                $pageName = 'All Pending Inspection';

            return Inertia::render('Inspector/BookingList', [
                'bookings' => $bookings,
                'searchTerm' => $request->search,
                'records' => $records, // Now records are structured as { order_id: [records] }
                'pageName' => $pageName,
    
            ]);
    }

    // Re-Inspection
    public function bookingReInspect(Request $request){

        // Ensure the user is authenticated before serving the file
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $inspector = Auth::user();

        // Retrieve the latest 10 inspected bookings
        $bookings = BookingRequest::where('assignTo', $inspector->name)
            ->where('jobStatus', '!=', 'completed')  // Pending or completed but date is past 3 days
            ->whereNotNull('order_id')
            ->whereNotNull('old_order_id') 
            ->when($request->search, function($query) use ($request) {
                $query->where(function ($query) use ($request) { // group the search conditions
                    $query->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('email', 'like', '%' . $request->search . '%')
                          ->orWhere('phone', 'like', '%' . $request->search . '%')
                          ->orWhere('city', 'like', '%' . $request->search . '%')
                          ->orWhere('county', 'like', '%' . $request->search . '%')
                          ->orWhere('municipality', 'like', '%' . $request->search . '%')
                          ->orWhere('InspectionType', 'like', '%' . $request->search . '%')
                          ->orWhere('methodology', 'like', '%' . $request->search . '%')
                          ->orWhere('payment_status', 'like', '%' . $request->search . '%')
                          ->orWhere('jobStatus', 'like', '%' . $request->search . '%')
                          ->orWhere('selected_date', 'like', '%' . $request->search . '%')
                          ->orWhere('order_id', 'like', '%' . $request->search . '%')
                          ->orWhere('old_order_id', 'like', '%' . $request->search . '%');
                });
    
            })
            ->orderBy('created_at', 'desc') // Change 'created_at' to your desired column
            ->paginate(10)
            ->withQueryString();
    
            // Extract order_ids from booking requests
            $orderIds = $bookings->pluck('order_id')->unique();
    
            // Retrieve records grouped by order_id
            $records = Record::whereIn('order_id', $orderIds)
                ->get()
                ->groupBy('order_id'); // Group by order_id
        
                $pageName = 'All Re-Inspection';

            return Inertia::render('Inspector/BookingList', [
                'bookings' => $bookings,
                'searchTerm' => $request->search,
                'records' => $records, // Now records are structured as { order_id: [records] }
                'pageName' => $pageName,
    
            ]);
    }

    // All In Queued
    public function bookingQueued(Request $request){

        // Ensure the user is authenticated before serving the file
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $inspector = Auth::user();

        // Retrieve the latest 10 inspected bookings
        $bookings = BookingRequest::where('assignTo', $inspector->name)
            ->whereIn('jobStatus', ['pending', 'completed'])  // Pending or completed but date is past 3 days
            ->where('selected_date', '>', Carbon::now()) 
            ->when($request->search, function($query) use ($request) {
                $query->where(function ($query) use ($request) { // group the search conditions
                    $query->where('name', 'like', '%' . $request->search . '%')
                          ->orWhere('email', 'like', '%' . $request->search . '%')
                          ->orWhere('phone', 'like', '%' . $request->search . '%')
                          ->orWhere('city', 'like', '%' . $request->search . '%')
                          ->orWhere('county', 'like', '%' . $request->search . '%')
                          ->orWhere('municipality', 'like', '%' . $request->search . '%')
                          ->orWhere('InspectionType', 'like', '%' . $request->search . '%')
                          ->orWhere('methodology', 'like', '%' . $request->search . '%')
                          ->orWhere('payment_status', 'like', '%' . $request->search . '%')
                          ->orWhere('jobStatus', 'like', '%' . $request->search . '%')
                          ->orWhere('selected_date', 'like', '%' . $request->search . '%')
                          ->orWhere('order_id', 'like', '%' . $request->search . '%')
                          ->orWhere('old_order_id', 'like', '%' . $request->search . '%');
                });
    
            })
            ->orderBy('created_at', 'desc') // Change 'created_at' to your desired column
            ->paginate(10)
            ->withQueryString();
    
            // Extract order_ids from booking requests
            $orderIds = $bookings->pluck('order_id')->unique();
    
            // Retrieve records grouped by order_id
            $records = Record::whereIn('order_id', $orderIds)
                ->get()
                ->groupBy('order_id'); // Group by order_id
        
                $pageName = 'All Future or Queued Jobs';

            return Inertia::render('Inspector/BookingList', [
                'bookings' => $bookings,
                'searchTerm' => $request->search,
                'records' => $records, // Now records are structured as { order_id: [records] }
                'pageName' => $pageName,
    
            ]);
    }

    // All In DOCUMENTS UPLOADED JOBS
    public function bookingUploaded(Request $request)
    {
        // Ensure the user is authenticated before serving the file
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $inspector = Auth::user();

        // Step 1: Get order_ids from the Record table where admin_review is 'pending'
        $orderIdsWithPendingReview = Record::where('admin_review', 'pending')
            ->pluck('order_id'); // Extract the order_id column

        // Step 2: Retrieve the bookings from BookingRequest where the order_id is in the list of order_ids from Record
        $bookings = BookingRequest::whereIn('order_id', $orderIdsWithPendingReview)
            ->where('assignTo', $inspector->name) // Ensure the booking is assigned to the current inspector
            ->when($request->search, function ($query) use ($request) {
                // Add search functionality
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%')
                        ->orWhere('city', 'like', '%' . $request->search . '%')
                        ->orWhere('county', 'like', '%' . $request->search . '%')
                        ->orWhere('municipality', 'like', '%' . $request->search . '%')
                        ->orWhere('InspectionType', 'like', '%' . $request->search . '%')
                        ->orWhere('methodology', 'like', '%' . $request->search . '%')
                        ->orWhere('payment_status', 'like', '%' . $request->search . '%')
                        ->orWhere('jobStatus', 'like', '%' . $request->search . '%')
                        ->orWhere('selected_date', 'like', '%' . $request->search . '%')
                        ->orWhere('order_id', 'like', '%' . $request->search . '%')
                        ->orWhere('old_order_id', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('created_at', 'desc') // Order by created date
            ->paginate(10)
            ->withQueryString();

        // Extract the order_ids from the bookings
        $orderIds = $bookings->pluck('order_id')->unique();

        // Retrieve the records related to the bookings using the order_ids
        $records = Record::whereIn('order_id', $orderIds)
            ->get()
            ->groupBy('order_id'); // Group by order_id

        $pageName = 'All where Documents (Floor plans, XRF, Chain of Custody, Photos or Description) have been Uploaded';

        // Return the result to the frontend
        return Inertia::render('Inspector/BookingList', [
            'bookings' => $bookings,
            'searchTerm' => $request->search,
            'records' => $records, // Now records are structured as { order_id: [records] }
            'pageName' => $pageName,
        ]);
    }

    // All OVERUDE JOBS
    public function bookingOverdue(Request $request)
    {
        // Ensure the user is authenticated before serving the file
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $inspector = Auth::user();

        // Step 1: Get order_ids from the Record table where admin_review is 'pending' or 'completed'
        $orderIdsWithPendingOrCompletedReview = Record::whereIn('admin_review', ['pending', 'completed'])
            ->pluck('order_id'); // Extract the order_id column

        // Step 2: Retrieve the bookings from BookingRequest where the order_id is NOT in the list of order_ids from Record
        $bookings = BookingRequest::where('assignTo', $inspector->name)
            ->whereIn('jobStatus', ['pending', 'completed']) // Filter by job status
            ->where('selected_date', '<', Carbon::now()->subDays(3)) // Filter for overdue bookings (3 days)
            ->whereNotIn('order_id', $orderIdsWithPendingOrCompletedReview) // Ensure no matching record in Record table
            ->when($request->search, function ($query) use ($request) {
                // Add search functionality
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%')
                        ->orWhere('city', 'like', '%' . $request->search . '%')
                        ->orWhere('county', 'like', '%' . $request->search . '%')
                        ->orWhere('municipality', 'like', '%' . $request->search . '%')
                        ->orWhere('InspectionType', 'like', '%' . $request->search . '%')
                        ->orWhere('methodology', 'like', '%' . $request->search . '%')
                        ->orWhere('payment_status', 'like', '%' . $request->search . '%')
                        ->orWhere('jobStatus', 'like', '%' . $request->search . '%')
                        ->orWhere('selected_date', 'like', '%' . $request->search . '%')
                        ->orWhere('order_id', 'like', '%' . $request->search . '%')
                        ->orWhere('old_order_id', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('created_at', 'desc') // Order by created date
            ->paginate(10)
            ->withQueryString();

        // Since we are looking for bookings that do not have matching records, no need to fetch records here
        $records = []; // No records to pass for this specific case

        $pageName = 'All Overdue Bookings where Documents (Floor plans, XRF, Chain of Custody, Photos or Description) have NOT been Uploaded';

        // Return the result to the frontend
        return Inertia::render('Inspector/BookingList', [
            'bookings' => $bookings,
            'searchTerm' => $request->search,
            'records' => $records, // Empty, as we only want bookings with no records
            'pageName' => $pageName,
        ]);
    }


}
