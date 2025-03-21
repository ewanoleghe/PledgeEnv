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

class InspectorController extends Controller
{
    /**
     * Display a Agent Login Page.
     */
    public function index()
    {
        return Inertia::render('Inspector/Login');    
    }
    public function create()
    {
        return Inertia::render('Inspector/Register');    
    }

    
    // Inspectors Register & Login Controller
    public function handler(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ])->onlyInput('email');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input fields
        $credentials = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|lowercase|email|max:255|unique:users',
            'phone_number' => 'required|regex:/^[0-9]+$/|min:10|max:15',
            'license_number' => 'required|alpha_num|unique:inspectors',
            'team_code' => 'required|alpha_num|exists:team_codes,team_code',
            'license_copy' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'signature' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'password' => 'required|confirmed|min:3',
            'tos' => 'accepted',
        ]);

        // Handle file uploads
        if ($request->hasFile('license_copy')) {
            $file = $request->file('license_copy');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('license', $filename);
            $credentials['license_copy'] = $filename;
        }

        if ($request->hasFile('signature')) {
            $file = $request->file('signature');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('signature', $filename);
            $credentials['signature'] = $filename;
        }

        // Hash password
        $credentials['password'] = Hash::make($credentials['password']);

        // Create Inspector user
        try {
            $user = Inspector::create($credentials);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withErrors([
                    'email' => 'The email is already taken.',
                    'license_number' => 'The license number is already in use.',
                ]);
            }
            return redirect()->back()->withErrors(['error' => 'An unexpected error occurred. Please try again later.']);
        }

        // Send verification email
        event(new Registered($user));

        // Log the user in
        Auth::login($user);

        // Redirect to dashboard with success message
        return Inertia::render('Inspector/Dashboard', [
            'eUser' => $user,
            'message' => 'Account created successfully!',
        ]);
    }

    public function getFile($filename)
    {
        // Ensure the user is authenticated before serving the file
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        $path = "private/$filename";

        if (!Storage::exists($path)) {
            abort(404, 'File not found');
        }

        return response()->file(storage_path("app/$path"));
    }

    public function dashboard()
    {
        $inspector = Auth::user();

        $discountCodes = DiscountCode::where('inspector_name', $inspector->name)->get();

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
            'reInspectionBook' => $reInspectionBook,  // Reinspections
        ]);
    }


    /**
     * Display the specified resource.
     */
    // public function show(Request $request, $id = null)
    // {
    //     $inspector = Auth::user();

    //     $bookingRequest = BookingRequest::find($request->id);

    //     if ($bookingRequest->assignTo !== $inspector->name) {
    //         return redirect()->back()->withErrors(['error' => 'You are not authorized to view this inspection.']);
    //     }

    //     // Retrieve the application record along with its unit contacts
    //     $bookings = BookingRequest::with('unitContacts')->find($request->id);
        
    //     $siteContactInfo = [];
        
    //     // Check if 'useSameContactForAll' is true in the bookings object
    //     if ($bookings->useSameContactForAll) {
    //         // Use the main contact info from the booking request
    //         $siteContactInfo[] = [
    //             'unit_number' => null, // This can be set to a default or null if not applicable
    //             'site_contact' => $bookings->siteContact,
    //             'site_contact_phone' => $bookings->siteContactPhone,
    //             'site_contact_email' => $bookings->siteContactEmail,
    //         ];
    //     } else {
    //         // Loop through unit contacts if 'useSameContactForAll' is false
    //         foreach ($bookings->unitContacts as $contact) {
    //             $siteContactInfo[] = [
    //                 'unit_number' => $contact->unit_number,
    //                 'site_contact' => $contact->site_contact,
    //                 'site_contact_phone' => $contact->site_contact_phone,
    //                 'site_contact_email' => $contact->site_contact_email,
    //             ];
    //         }
    //     }

    //     // Retrieve the unique order_ids from the unitContacts relationship (assuming it has `order_id`)
    //     $orderIds = $bookingRequest->unitContacts->pluck('order_id')->unique();

    //     // Retrieve records grouped by order_id
    //     $records = Record::whereIn('order_id', $orderIds)
    //         ->get()
    //         ->groupBy('order_id'); // Group by order_id

    //     return Inertia::render('Inspector/ViewInspection', [
    //         'eUser' => $inspector,
    //         'bookings' => $bookingRequest,
    //         'siteContactInfo' => $siteContactInfo,
    //         'records' => $records, // Now records are structured as { order_id: [records] }
    //     ]);
    // }

    public function show(Request $request, $id = null)
    {
        $inspector = Auth::user();
        $bookingRequest = BookingRequest::find($request->id);

        if (!$bookingRequest || $bookingRequest->assignTo !== $inspector->name) {
            return redirect()->back()->withErrors(['error' => 'You are not authorized to view this inspection.']);
        }

        // Retrieve the application record along with its unit contacts
        $bookings = BookingRequest::with('unitContacts')->find($request->id);

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

        // Retrieve unique order_ids from unitContacts
        $unitContactOrderIds = $bookingRequest->unitContacts->pluck('order_id')->unique();

        // Get records where order_id matches either BookingRequest.order_id or unitContacts.order_id
        $orderIds = $unitContactOrderIds->merge([$bookingRequest->order_id])->unique();

        $records = Record::whereIn('order_id', $orderIds)
            ->get()
            ->groupBy('order_id');

        return Inertia::render('Inspector/ViewInspection', [
            'eUser' => $inspector,
            'bookings' => $bookingRequest,
            'siteContactInfo' => $siteContactInfo,
            'records' => $records,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function updateJob(Request $request)
    {
        $inspector = Auth::user();

        // Retrieve the booking ID from the request
        $bookingId = $request->input('bookingId');

        // Find the booking using the booking ID, along with related unit contacts
        $bookingRequest = BookingRequest::with('unitContacts')->find($bookingId);

        // Check if the bookingRequest exists
        if (!$bookingRequest) {
            return redirect()->back()->withErrors(['error' => 'Booking not found.']);
        }

        // Check authorization
        if ($bookingRequest->assignTo !== $inspector->name) {
            return redirect()->back()->withErrors(['error' => 'You are not authorized to update this inspection.']);
        }

        // Update the job status to 'completed'
        $bookingRequest->update([
            'jobStatus' => 'completed',
        ]);

        // Prepare the site contact information
        $siteContactInfo = [];
        if ($bookingRequest->useSameContactForAll) {
            $siteContactInfo[] = [
                'unit_number' => null,
                'site_contact' => $bookingRequest->siteContact,
                'site_contact_phone' => $bookingRequest->siteContactPhone,
                'site_contact_email' => $bookingRequest->siteContactEmail,
            ];
        } else {
            foreach ($bookingRequest->unitContacts as $contact) {
                $siteContactInfo[] = [
                    'unit_number' => $contact->unit_number,
                    'site_contact' => $contact->site_contact,
                    'site_contact_phone' => $contact->site_contact_phone,
                    'site_contact_email' => $contact->site_contact_email,
                ];
            }
        }

        // After the update, re-render the component with the updated data
        // Redirect to the inspection view with the booking ID
        return redirect()->route('view_inspection', ['id' => $request->bookingId])
        ->with('message', 'Job status updated successfully.')
        ->with('message_body', 'The booking status was updated to completed.');
    
    }

    /**
     * Show the same inspection after the update.
     */
    public function showInspection(Request $request, $id = null)
    {
        $inspector = Auth::user();

        $bookingRequest = BookingRequest::find($request->id);

        if ($bookingRequest->assignTo !== $inspector->name) {
            return redirect()->back()->withErrors(['error' => 'You are not authorized to view this inspection.']);
        }

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

        // Retrieve the unique order_ids from the unitContacts relationship (assuming it has `order_id`)
        $orderIds = $bookings->unitContacts->pluck('order_id')->unique();

        // Retrieve records grouped by order_id
        $records = Record::whereIn('order_id', $orderIds)
            ->get()
            ->groupBy('order_id'); // Group by order_id

        return Inertia::render('Inspector/ViewInspection', [
            'eUser' => $inspector,
            'bookings' => $bookingRequest,
            'siteContactInfo' => $siteContactInfo,
            'records' => $records, // Now records are structured as { order_id: [records] }
        ]);
    }


    /**
    * Function to submit the report and images.
    */
    public function storeRecords(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'booking_id' => 'required|integer',
            'order_id' => 'required|integer',
            'unit_number' => 'required|integer',
            'comments' => 'nullable|string|max:255',
            'pass_fail' => 'required|string|in:pass,fail',
            'selected_date' => 'required|date',
            'selected_time' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'municipality' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'block' => 'required|string|max:100',
            'lot' => 'required|string|max:100',
            'county' => 'required|string|max:100',
            'service' => 'required|string|max:100',
            'methodology' => 'required|string|max:100',
            'includeXrf' => 'required|boolean',
            'optional_message' => 'nullable|string',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'property_owner_name' => 'required|string|max:255',
            'property_owner_phone' => 'required|string|max:20',
            'property_owner_email' => 'required|email|max:255',
            'designation' => 'nullable|string|max:10',
            'xrf_pass_fail' => 'nullable|string|max:10',

            // File Uploads
            'floor_plan' => 'required|file|mimes:pdf,jpeg,png|max:10240',
            'lab_report' => 'nullable|file|mimes:pdf,jpeg,png|max:10240',
            'chain_custody' => 'nullable|file|mimes:pdf,jpeg,png|max:10240',
            'xrf_report' => 'nullable|file|mimes:pdf,jpeg,png|max:10240',
            'xrf_csv' => 'nullable|file|mimes:csv|max:10240',

            // Photo Fields
            'photos' => 'nullable|array|max:8',
            'photos.*' => 'file|mimes:jpeg,png,jpg|max:10240',
            'photo_descriptions' => 'nullable|array|min:0',
            'photo_rooms' => 'nullable|array|min:0',
            'photo_components' => 'nullable|array|min:0',
            'photo_hazards' => 'nullable|array|min:0',
            'photo_substrates' => 'nullable|array|min:0',
            'photo_values' => 'nullable|array|min:0',
        ]);

        $inspector = Auth::user();

        // Store file uploads for the main files
    $files = [];
    foreach (['floor_plan', 'lab_report', 'chain_custody', 'xrf_report'] as $fileField) {
        if ($request->hasFile($fileField)) {
            $filePath = $request->file($fileField)->store('images/inspections/' . $request->booking_id, 'public');
            $files[$fileField] = $filePath; // Store the file path
        }
    }

    // Check if xrf_csv file is uploaded and process it
    if ($request->hasFile('xrf_csv')) {
        $xrfCsvFile = $request->file('xrf_csv');
        
        // Parse the CSV file
        $csvData = array_map('str_getcsv', file($xrfCsvFile->getRealPath()));
        
        // Assuming the first row contains headers, extract them
        $headers = array_shift($csvData);

        // Initialize an array to hold filtered rows
        $filteredRows = [];

        // Loop through the data and filter rows
        foreach ($csvData as $row) {
            // Create an associative array using the headers as keys
            $rowData = array_combine($headers, $row);

            // Only keep rows where 'Concentration', 'Room', 'Structure', 'Member', 'Substrate', 'Wall' are not empty
            if (!empty($rowData['Concentration']) && !empty($rowData['Room']) && !empty($rowData['Structure']) &&
                !empty($rowData['Member']) && !empty($rowData['Substrate']) && !empty($rowData['Wall'])) {
                $filteredRows[] = $rowData; // Add valid row to the filtered array
            }
        }

        // Save the filtered rows as a JSON file
        $jsonFilePath = 'json/xrf_data/' . $request->booking_id . '_xrf_data.json';
        Storage::disk('public')->put($jsonFilePath, json_encode($filteredRows));

        // Update the 'xrf_csv' field in the record with the JSON file path
        $files['xrf_csv'] = $jsonFilePath;
    }

        // Generate a unique certificate number
        $certificateNumber = $this->generateUniqueCertificateNumber();

        // Store the main record in `records` table
        $record = new Record();
        $record->certificate_number = $certificateNumber;
        $record->booking_id = $request->booking_id;
        $record->order_id = $request->order_id;
        $record->unit_number = $request->unit_number;
        $record->comments = $request->comments;
        $record->pass_fail = $request->pass_fail;
        $record->selected_date = $request->selected_date;
        $record->selected_time = $request->selected_time;
        $record->address = $request->address;
        $record->municipality = $request->municipality;
        $record->city = $request->city;
        $record->county = $request->county;
        $record->block = $request->block;
        $record->lot = $request->lot;
        $record->service = $request->service;
        $record->methodology = $request->methodology;
        $record->includeXrf = $request->includeXrf;        
        $record->optional_message = $request->optional_message;
        $record->customer_name = $request->customer_name;
        $record->customer_email = $request->customer_email;
        $record->customer_phone = $request->customer_phone;
        $record->property_owner_name = $request->property_owner_name;
        $record->property_owner_phone = $request->property_owner_phone;
        $record->property_owner_email = $request->property_owner_email;
        $record->signature = $inspector->signature;
        $record->license_number = $inspector->license_number;
        $record->inspector_name = $inspector->name;
        //
        $record->designation = $request->designation;
        $record->xrf_pass_fail = $request->xrf_pass_fail;
        $record->floor_plan = $files['floor_plan'] ?? null;
        $record->lab_report = $files['lab_report'] ?? null;
        $record->chain_custody = $files['chain_custody'] ?? null;
        $record->xrf_report = $files['xrf_report'] ?? null;
        $record->xrf_csv = $files['xrf_csv'] ?? null;
        $record->save();

        // Handle multiple photo uploads and descriptions
        $photos = $request->file('photos'); // Get photos from the request
        $descriptions = $request->photo_descriptions ?? [];

        if ($photos && count($photos) > 0) {
            foreach ($photos as $index => $photoFile) {
                // If a description exists, process it along with the file
                $description = $descriptions[$index] ?? '';
                
                // Store the photo and get the file path
                $filePath = $photoFile->store('images/inspection_photos/' . $request->booking_id, 'public');
                
                // Make sure we are storing the correct path
                if ($filePath || $description) { // Only create RecordPhoto if a description or file exists
                    RecordPhoto::create([
                        'record_id' => $record->id,
                        'photo_path' => $filePath, // Save the correct file path
                        'description' => $description,
                        'room' => $request->photo_rooms[$index] ?? '',
                        'component' => $request->photo_components[$index] ?? '',
                        'hazard' => $request->photo_hazards[$index] ?? '',
                        'substrate' => $request->photo_substrates[$index] ?? '',
                        'value' => $request->photo_values[$index] ?? null,
                    ]);
                }
            }
        }

        // If no photos are uploaded but descriptions exist, save metadata without a photo path
        if (count($descriptions) > 0) {
            foreach ($descriptions as $index => $description) {
                // If no photo exists but a description does, set photo path as null
                $photoPath = isset($photos[$index]) ? $photos[$index]->store('images/inspection_photos/' . $request->booking_id, 'public') : null;
                
                // Create a RecordPhoto entry even if only a description exists
                if ($description && !$photoPath) { // Only save RecordPhoto if either a description or a file exists
                    RecordPhoto::create([
                        'record_id' => $record->id,
                        'photo_path' => null, // Null if no photo uploaded
                        'description' => $description,
                        'room' => $request->photo_rooms[$index] ?? '',
                        'component' => $request->photo_components[$index] ?? '',
                        'hazard' => $request->photo_hazards[$index] ?? '',
                        'substrate' => $request->photo_substrates[$index] ?? '',
                        'value' => $request->photo_values[$index] ?? null,
                    ]);
                }
            }
        }

        // Redirect to the inspection view with the booking ID
        return redirect()->route('view_inspection', ['id' => $request->booking_id])
            ->with('message', 'Inspection documents submitted successfully.');
    }

    /**
    * Function to generate a unique certificate number.
    */
    private function generateUniqueCertificateNumber()
    {
        // Generate a unique certificate number (example: PESXXXXXXX)
        do {
            // Generate a 7-character unique ID and convert to uppercase
            $certificateNumber = 'LBP'.strtoupper(substr(uniqid(), 0, 7));
        } while (Record::where('certificate_number', $certificateNumber)->exists()); // Ensure uniqueness

        return $certificateNumber;
    }

    /**
     * Showing the booking List page.
     */
    public function bookingList(Request $request) {
        $request->validate([
            'search' => 'nullable|string|max:255',
        ]);

        $inspector = Auth::user();

        $bookings = BookingRequest::where('assignTo', $inspector->name) // filter for assigned bookings only
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
    
        return Inertia::render('Inspector/BookingList', [
            'bookings' => $bookings,
            'searchTerm' => $request->search,
            'records' => $records, // Now records are structured as { order_id: [records] }

        ]);
    }
    
    /**
     * Update Job Status from Booking List.
     */
    public function updateJobStatus(Request $request) {
        $inspector = Auth::user();
    
        // Validate the incoming request
        $validated = $request->validate([
            'id' => 'required|integer|exists:booking_requests,id',
        ]);
    
        // Find the booking and toggle the job status
        $booking = BookingRequest::find($validated['id']);
        $booking->jobStatus = $booking->jobStatus === 'completed' ? 'pending' : 'completed';  // Update job status
        $booking->save();
    
        return redirect()->back()->with('message', 'Job status updated successfully!');
    }

    public function documentUpload($id)
    {
        $record = Record::find($id);

        if (!$record) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        // Generate full URLs for stored files
        $record->floor_plan = $record->floor_plan ? asset('storage/' . $record->floor_plan) : null;
        $record->xrf_report = $record->xrf_report ? asset('storage/' . $record->xrf_report) : null;
        $record->xrf_csv = $record->xrf_csv ? asset('storage/' . $record->xrf_csv) : null;
        
        return response()->json($record);
    }

    

    
    






    /**
     * Show the form for editing the specified resource.
     */
    public function editBooking(BookingRequest $bookingRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateBooking(Request $request, BookingRequest $bookingRequest)
    {
        //
    }
    
    
    
    
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inspector $inspector)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inspector $inspector)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inspector $inspector)
    {
        //
    }
}
