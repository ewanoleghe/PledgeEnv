<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingRequest;
use App\Models\Inspector;
use App\Models\UnitContact;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail; // Mail/
use App\Mail\ReceiptMail;  // Receipt/
use App\Services\PdfGenerator; // Services/
use PDF; // Don't forget to import the PDF facade

use Inertia\Inertia;


class BookingController extends Controller
{
    protected $pdfGenerator;

    public function __construct(PdfGenerator $pdfGenerator) // Inject PdfGenerator
    {
        $this->pdfGenerator = $pdfGenerator;
    }

    public function indexAll(Request $request) {
        $request->validate([
            'search' => 'nullable|string|max:255',
        ]);
    
        $bookings = BookingRequest::when($request->search, function($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%')
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
                  ->orWhere('selected_date', 'like', '%' . $request->search . '%')
                  ->orWhere('order_id', 'like', '%' . $request->search . '%')
                  ->orWhere('old_order_id', 'like', '%' . $request->search . '%');
        })
        ->orderBy('created_at', 'desc') // Change 'created_at' to your desired column
        ->paginate(10)
        ->withQueryString();
    
        return Inertia::render('Admin/Pages/Bookings', [
            'bookings' => $bookings,
            'searchTerm' => $request->search,
        ]);
    }

        public function updatePaymentStatus(Request $request) {
            // Validate the incoming request
            $validated = $request->validate([
                'id' => 'required|integer|exists:booking_requests,id', 
            ]); // id is present in the request,  an integer, and exists as a valid id in the booking_requests table.

            // Find the record and toggle the payment status
            $bookings = BookingRequest::find($validated['id']);
            $bookings->payment_status = $bookings->payment_status === 'succeeded' ? 'pending' : 'succeeded';
            $bookings->save();

            return redirect()->back()->with('message', 'Payment status updated successfully!');
        }

    // View a booking application with its unit contacts
    public function viewApplication(Request $request, $id) {
        // Fetch the application
    $bookings = BookingRequest::findOrFail($id);
        
        // Retrieve the application record along with its unit contacts
        $bookings = BookingRequest::with('unitContacts')->find($request->id);
        
        // Update the 'read_unr' field to 'read'
        // $bookings->update(['read_unr' => 'read']);
        
        $siteContactInfo = [];
        
        // Check if 'useSameContactForAll' is true in the bookings object
        if ($bookings->useSameContactForAll) {
            // Use the main contact info from the booking request
            $siteContactInfo[] = [
                'unit_number' => null, // This can be set to a default or null if not applicable
                'site_contact' => $bookings->siteContact,
                'site_contact_phone' => $bookings->siteContactPhone,
                'site_contact_email' => $bookings->siteContactEmail,
            ];
        } else {
            // Loop through unit contacts if 'useSameContactForAll' is false
            foreach ($bookings->unitContacts as $contact) {
                $siteContactInfo[] = [
                    'unit_number' => $contact->unit_number,
                    'site_contact' => $contact->site_contact,
                    'site_contact_phone' => $contact->site_contact_phone,
                    'site_contact_email' => $contact->site_contact_email,
                ];
            }
        }

        // Return the view with the bookings object and site contact array
        return Inertia::render('Admin/Pages/ViewBooking', compact('bookings', 'siteContactInfo'));
    }

    // Get the Update Booking Page
    public function getUpdateBooking(Request $request, $id) {
        // Fetch the booking request
        $bookings = BookingRequest::with('unitContacts')->findOrFail($id);
    
        // Retrieve all inspectors
        $inspectors = Inspector::select('name')->get();
    
        $siteContactInfo = [];
    
        if ($bookings->useSameContactForAll) {
            $siteContactInfo[] = [
                'unit_number' => null,
                'site_contact' => $bookings->siteContact,
                'site_contact_phone' => $bookings->siteContactPhone,
                'site_contact_email' => $bookings->siteContactEmail,
            ];
        } else {
            foreach ($bookings->unitContacts as $contact) {
                $siteContactInfo[] = [
                    'unit_number' => $contact->unit_number,
                    'site_contact' => $contact->site_contact,
                    'site_contact_phone' => $contact->site_contact_phone,
                    'site_contact_email' => $contact->site_contact_email,
                ];
            }
        }
    
        return Inertia::render('Admin/Pages/UpdateBooking', [
            'bookings' => $bookings,
            'siteContactInfo' => $siteContactInfo,
            'inspectors' => $inspectors,
        ]);
    }
    
    // 
    // Update the Booking
    public function updateBooking(Request $request, $id)
    {
        $validated = $request->validate([
            'selected_date' => 'required|date',
            'selectedTime' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'optionalMessage' => 'nullable|string|max:500',
            'assignTo' => 'nullable|string|max:255',
        ]);

        $booking = BookingRequest::findOrFail($id);
        $booking->update($validated);

        $updatedBooking = BookingRequest::with('unitContacts')->findOrFail($id);
        $inspectors = Inspector::select('name')->get();
        $siteContactInfo = [];

        if ($updatedBooking->useSameContactForAll) {
            $siteContactInfo[] = [
                'unit_number' => null,
                'site_contact' => $updatedBooking->siteContact,
                'site_contact_phone' => $updatedBooking->siteContactPhone,
                'site_contact_email' => $updatedBooking->siteContactEmail,
            ];
        } else {
            foreach ($updatedBooking->unitContacts as $contact) {
                $siteContactInfo[] = [
                    'unit_number' => $contact->unit_number,
                    'site_contact' => $contact->site_contact,
                    'site_contact_phone' => $contact->site_contact_phone,
                    'site_contact_email' => $contact->site_contact_email,
                ];
            }
        }

        return redirect()->route('admin.viewBooking', ['id' => $id])->with('message', 'Booking updated successfully');
    }

    // Handles the Pending Payment :: Set payment to suceeded if pending and reload the page
    public function updatePaymentStatusBookingView(Request $request) {
        // Validate the incoming request
        $validated = $request->validate([
            'id' => 'required|integer|exists:booking_requests,id', 
        ]); // id is present in the request,  an integer, and exists as a valid id in the booking_requests table.

        // Find the record and toggle the payment status
        $bookings = BookingRequest::find($validated['id']);
        $bookings->payment_status = $bookings->payment_status === 'succeeded' ? 'pending' : 'succeeded';
        $bookings->save();

        // Retrieve the application record along with its unit contacts
        $bookings = BookingRequest::with('unitContacts')->find($request->id);
        
        // Update the 'read_unr' field to 'read'
        // $bookings->update(['read_unr' => 'read']);
        
        $siteContactInfo = [];
        
        // Check if 'useSameContactForAll' is true in the bookings object
        if ($bookings->useSameContactForAll) {
            // Use the main contact info from the booking request
            $siteContactInfo[] = [
                'unit_number' => null, // This can be set to a default or null if not applicable
                'site_contact' => $bookings->siteContact,
                'site_contact_phone' => $bookings->siteContactPhone,
                'site_contact_email' => $bookings->siteContactEmail,
            ];
        } else {
            // Loop through unit contacts if 'useSameContactForAll' is false
            foreach ($bookings->unitContacts as $contact) {
                $siteContactInfo[] = [
                    'unit_number' => $contact->unit_number,
                    'site_contact' => $contact->site_contact,
                    'site_contact_phone' => $contact->site_contact_phone,
                    'site_contact_email' => $contact->site_contact_email,
                ];
            }
        }

        // Send the receipt email to the site owner
        $dataArray = $bookings->toArray();  // Convert the bookings object to an array
        $pdfFileName = "receipt_{$dataArray['order_id']}.pdf"; 
        $pdfPath = $this->pdfGenerator->saveInvoice($dataArray, $pdfFileName);
        // \Mail::to($dataArray['email'])->send(new ReceiptMail($dataArray, $pdfPath));

        \Mail::to($dataArray['email'])
        ->cc(env('COMPANY_EMAIL')) // Send a copy to the company email
        ->send(new ReceiptMail($dataArray, $pdfPath));

        // Return the view with the bookings object and site contact array
        return Inertia::render('Admin/Pages/ViewBooking', compact('bookings', 'siteContactInfo'));
    }
        
    public function deleteApplication(Request $request)
    {
        // Validate the incoming request
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer|exists:booking_requests,id',
        ]);

        if ($validated->fails()) {
            return redirect()->back()->withErrors($validated)->withInput();
        }

        // Perform the deletion
        BookingRequest::destroy($request->id);

        // Optionally, redirect back with a success message
        return redirect()->route('admin.bookings')->with('message', 'Application deleted successfully.');
    }

    
}
