<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingRequest;
use App\Models\UnitContact;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarViewController extends Controller
{
    //
    public function indexAll(Request $request) {
        $request->validate([
            'search' => 'nullable|string|max:255',
        ]);

        // Retrieve all bookings
        $bookingsQuery = BookingRequest::query();

        // Filter by search term if provided
        if ($request->filled('search')) {
            $bookingsQuery->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%')
                      ->orWhere('phone', 'like', '%' . $request->search . '%')
                      ->orWhere('selected_date', 'like', '%' . $request->search . '%')
                    ->orWhere('order_id', 'like', '%' . $request->search . '%')
                    ->orWhere('old_order_id', 'like', '%' . $request->search . '%');
            });
        }

        $bookings = $bookingsQuery->orderBy('selected_date')->get();

        // Group bookings by selected_date
        $groupedBookings = $bookings->groupBy('selected_date')->map(function ($dateBookings) {
            return $dateBookings->pluck('order_id'); // Extract only order_id for each date
        });

        return Inertia::render('Admin/Pages/CalendarBookings', [
            'bookings' => $bookings->toArray(), // Ensure it's an array
            'searchTerm' => $request->search,
        ]);
    }

    public function viewDatesBooking(Request $request) {
        $request->validate([
            'selected_date' => 'required|date',
            'search' => 'nullable|string|max:255',
        ]);
    
        $bookings = BookingRequest::whereDate('selected_date', $request->selected_date) // Filter by selected_date
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%')
                      ->orWhere('phone', 'like', '%' . $request->search . '%')
                      ->orWhere('city', 'like', '%' . $request->search . '%')
                      ->orWhere('county', 'like', '%' . $request->search . '%')
                      ->orWhere('municipality', 'like', '%' . $request->search . '%')
                      ->orWhere('InspectionType', 'like', '%' . $request->search . '%')
                      ->orWhere('methodology', 'like', '%' . $request->search . '%')
                      ->orWhere('payment_status', 'like', '%' . $request->search . '%')
                      ->orWhere('assignTo', 'like', '%' . $request->search . '%')
                      ->orWhere('jobStatus', 'like', '%' . $request->search . '%')
                      ->orWhere('order_id', 'like', '%' . $request->search . '%')
                      ->orWhere('old_order_id', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('created_at', 'desc') // Change 'created_at' to your desired column
            ->paginate(10)
            ->withQueryString();
    
        return Inertia::render('Admin/Pages/DateModel', [
            'bookings' => [
                'data' => $bookings->items(),
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'total' => $bookings->total(),
            ],
            'searchTerm' => $request->search,
            'selectedDate' => $request->selected_date,
        ]);
    }

    public function updatePaymentStatusC(Request $request) {
        // Validate the incoming request
        $validated = $request->validate([
            'id' => 'required|integer|exists:booking_requests,id', 
        ]); // id is present in the request,  an integer, and exists as a valid id in the booking_requests table.

        // Find the record and toggle the payment status
        $bookings = BookingRequest::find($validated['id']);

        $bookings->payment_status = $bookings->payment_status === 'succeeded' ? 'pending' : 'succeeded';
        $bookings->save();

        $bookings = BookingRequest::whereDate('selected_date', $bookings->selected_date) // Filter by selected_date
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%')
                      ->orWhere('phone', 'like', '%' . $request->search . '%')
                      ->orWhere('city', 'like', '%' . $request->search . '%')
                      ->orWhere('county', 'like', '%' . $request->search . '%')
                      ->orWhere('municipality', 'like', '%' . $request->search . '%')
                      ->orWhere('InspectionType', 'like', '%' . $request->search . '%')
                      ->orWhere('methodology', 'like', '%' . $request->search . '%')
                      ->orWhere('payment_status', 'like', '%' . $request->search . '%')
                      ->orWhere('assignTo', 'like', '%' . $request->search . '%')
                      ->orWhere('jobStatus', 'like', '%' . $request->search . '%')
                      ->orWhere('order_id', 'like', '%' . $request->search . '%')
                      ->orWhere('old_order_id', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('created_at', 'desc') // Change 'created_at' to your desired column
            ->paginate(10)
            ->withQueryString();
    
        return Inertia::render('Admin/Pages/DateModel', [
            'bookings' => [
                'data' => $bookings->items(),
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'total' => $bookings->total(),
            ],
            'searchTerm' => $request->search,
            'selectedDate' => $request->selected_date,
        ])->with('message', 'Payment status updated successfully!');
    }


        

    


}
