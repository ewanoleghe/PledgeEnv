<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\UnitContact;
use App\Models\ServicePrice;
use App\Models\DiscountCode;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        return Inertia::render('Booking/Schedule');
    }

    public function MultipleSchedule()
    {
        return Inertia::render('Booking/MultipleSchedule');
    }

    // Single service Application
    public function RequestService(Request $request)
    {
        // Get the service from the request
        $service = $request->input('service');

        // Check if the service is empty
        if (empty($service)) {
            // If no service is provided, render the Schedule view
            return Inertia::render('Booking/Schedule');
        }

        // Fetch all selected_dates for the given service where there are 2 or more bookings for that service
        $datesWithBookings = BookingRequest::where('service', $service)  // Filter by service
                                        ->select('selected_date')    // Only select the selected_date
                                        ->groupBy('selected_date')  // Group by selected_date
                                        ->havingRaw('COUNT(*) >= 2') // Filter to only include dates with 2 or more bookings
                                        ->pluck('selected_date')    // Get the matching dates as a collection
                                        ->map(function ($date) {
                                            // Convert each selected_date to Carbon instance and format it to YYYY-MM-DD
                                            return Carbon::parse($date)->toDateString(); // Only the date part (YYYY-MM-DD)
                                        })
                                        ->toArray();

        // Render the ScheduleDate view with the service and the dates with bookings
        return Inertia::render('Booking/ScheduleDate', [
            'service' => $service,
            'datesWithBookings' => $datesWithBookings, // Pass the formatted dates to the component
        ]);
    }
        
//=================================
    public function handleSingleRequestDate(Request $request)
    {
        // Validate the service input to ensure it's not empty
        $validated = $request->validate([
            'service' => 'required', // Ensures 'service' is not empty
            'includeXrf' => 'nullable|boolean', // Ensure 'includeXrf' is a boolean (true or false)
        ]);
    
        // Retrieve the service and includeXrf value from the request
        $service = $request->input('service');
        $includeXrf = $request->input('includeXrf', false); // Default to false if not provided
    
        // Check if the service is empty
        if (empty($service)) {
            // Return the Inertia response for 'Booking/Schedule' if service is empty
            return Inertia::render('Booking/Schedule');
        }
    
        // Begin handling of specific services
        if ($service == 'Lead Inspection') {
            return $this->handleLeadInspection($includeXrf, $service);
        } elseif ($service == 'Asbestos Management') {
            return $this->handleAsbestosManagement($service); // Pass $service here
        } elseif ($service == 'Mold Assessment') {
            return $this->handleMoldAssessment($service); // if needed
        } elseif ($service == 'Radon Control') {
            return $this->handleRadonControl($service); // if needed
        } elseif ($service == 'Sewer Inspection & Cleaning') {
            return $this->handleSewerInspection($service); // if needed
        } elseif ($service == 'Residential Tank Sweep') {
            return $this->handleTankSweep($service); // if needed
        } elseif ($service == 'Remediation & Consulting') {
            return $this->handleRemediationConsulting($service); // if needed
        } elseif ($service == 'Lead in Drinking Water') {
            return $this->handleLeadInDrinkingWater($service); // if needed
        } else {
            // If the service does not match any known service
            return Inertia::render('Booking/Schedule');
        }
    
    }
    
//================specific services=================
                // $service == 'Lead Inspection'
                private function handleLeadInspection($includeXrf, $service) {
                    // Fetch the service price from the service_prices table where service_name matches the input service
                    $servicePrice = ServicePrice::where('service_name', $service)->first();
                
                    // If no service price is found, return the response
                    if (!$servicePrice) {
                        return Inertia::render('Booking/Schedule');
                    }
                
                    // Retrieve the 'bookMax' value from the service price
                    $bookMax = $servicePrice->bookMax; // Max bookings allowed for the service
                
                    // Fetch all bookings for the selected service, including selected dates
                    $bookings = BookingRequest::where('service', $service)->get();
                
                    // Initialize an array to store all selected dates
                    $selectedDates = [];
                
                    // Loop through all bookings and collect the selected dates
                    foreach ($bookings as $booking) {
                        $selectedDates[] = $booking->selected_date;
                    }
                
                    // Count the occurrences of each selected date
                    $dateCounts = array_count_values($selectedDates);
                
                    // Find the dates where the count exceeds the bookMax value
                    $datesWithBookings = array_filter($dateCounts, function ($count) use ($bookMax) {
                        return $count > $bookMax; // Filter dates with more bookings than the allowed 'bookMax'
                    });
                
                    // Get the actual dates with bookings (keys of the filtered array)
                    $datesWithBookings = array_keys($datesWithBookings);
                
                    // Retrieve the fields from the service price
                    $price = $servicePrice->price;
                    $price_visual = $servicePrice->price_visual;
                    $price_dust = $servicePrice->price_dust;
                    $xrf = $servicePrice->xrf;
                    $xrf_reinspection_price = $servicePrice->xrf_reinspection_price; // Added field
                    $visual_reinspection_price = $servicePrice->visual_reinspection_price; // Added field
                    $dust_wipe_reinspection_price = $servicePrice->dust_wipe_reinspection_price; // Added field
                    $weekendFee = $servicePrice->weekendFee; // Added field
                
                    // Return the 'Booking/ScheduleDate' view with necessary data
                    return Inertia::render('Booking/ScheduleDate', [
                        'service' => $service,
                        'price' => $price, 
                        'price_visual' => $price_visual,
                        'price_dust' => $price_dust,
                        'xrf' => $xrf,
                        'xrf_reinspection_price' => $xrf_reinspection_price, 
                        'visual_reinspection_price' => $visual_reinspection_price, 
                        'dust_wipe_reinspection_price' => $dust_wipe_reinspection_price, 
                        'weekendFee' => $weekendFee, 
                        'datesWithBookings' => $datesWithBookings, 
                        'includeXrf' => $includeXrf, 
                    ]);
                }
                
                //$service == 'Asbestos Management'
                private function handleAsbestosManagement($service) {
                    // (Implementation for Asbestos Management)
                    // Fetch the service price from the service_prices table where service_name matches the input service
                    $servicePrice = ServicePrice::where('service_name', $service)->first();
                
                    // If no service price is found, return the response
                    if (!$servicePrice) {
                        return Inertia::render('Booking/Schedule');
                    }
                
                    // Retrieve the 'bookMax' value from the service price
                    $bookMax = $servicePrice->bookMax; // Max bookings allowed for the service
                
                    // Fetch all bookings for the selected service, including selected dates
                    $bookings = BookingRequest::where('service', $service)->get();
                
                    // Initialize an array to store all selected dates
                    $selectedDates = [];
                
                    // Loop through all bookings and collect the selected dates
                    foreach ($bookings as $booking) {
                        $selectedDates[] = $booking->selected_date;
                    }
                
                    // Count the occurrences of each selected date
                    $dateCounts = array_count_values($selectedDates);
                
                    // Find the dates where the count exceeds the bookMax value
                    $datesWithBookings = array_filter($dateCounts, function ($count) use ($bookMax) {
                        return $count > $bookMax; // Filter dates with more bookings than the allowed 'bookMax'
                    });

                    return Inertia::render('Home', [
                        // Pass any necessary data
                    ]);
                }
                
                // $service == 'Mold Assessment'
                private function handleMoldAssessment($service) {
                    // (Implementation for Mold Assessment)
                    return Inertia::render('Contact', [
                        // Pass any necessary data
                    ]);
                }
                
                // $service == 'Radon Control'
                private function handleRadonControl($service) {
                    // (Implementation for Radon Control)
                    return Inertia::render('Contact', [
                        // Pass any necessary data
                    ]);
                }
                
                // $service == 'Sewer Inspection'
                private function handleSewerInspection($service) {
                    // (Implementation for Sewer Inspection)
                    return Inertia::render('Contact', [
                        // Pass any necessary data
                    ]);
                }
                
                // $service == 'Residential Tank Sweep'
                private function handleTankSweep($service) {
                    // (Implementation for Residential Tank Sweep)
                    return Inertia::render('Contact', [
                        // Pass any necessary data
                    ]);
                }
                
                // $service == 'Remediation & Consulting'
                private function handleRemediationConsulting($service) {
                    // (Implementation for Remediation & Consulting)
                    return Inertia::render('Contact', [
                        // Pass any necessary data
                    ]);
                }
                
                // $service == 'Lead in Drinking Water'
                private function handleLeadInDrinkingWater($service) {
                    // (Implementation for Lead in Drinking Water)
                    return Inertia::render('Contact', [
                        // Pass any necessary data
                    ]);
                }

//------------------------------
public function ScheduleDate(Request $request)
    {
        // Get the service, to avoid url hack uses a get route
        $service = $request->input('service');

        // Check if the service variable is empty
        if (empty($service)) {
            return Inertia::render('Booking/Schedule');
        }

        // If service is not empty, render ScheduleTime view with the data
        return Inertia::render('Booking/ScheduleTime', [
            'service' => $request->input('service'), // Assuming this comes from the request
        ]);
    }
    
    public function handleSingleRequestTime(Request $request)
    {
        \Log::info('Received request:', $request->all()); // Log incoming data for debugging

        // Validate the incoming request
        $validated = $request->validate([
            'selectedDate' => 'required|date',
            'service' => 'required|string',
            'price' => 'required|numeric',
            'price_visual' => 'required|numeric',
            'price_dust' => 'required|numeric',
            'xrf' => 'required|numeric',
            'xrf_reinspection_price' => 'nullable|numeric',
            'visual_reinspection_price' => 'nullable|numeric',
            'dust_wipe_reinspection_price' => 'nullable|numeric',
            'weekendFee' => 'nullable|numeric',
            'includeXrf' => 'nullable|boolean',
            'InspectionType' => 'required|string',
            'orderNumber' => 'nullable|string|max:7',
            'dcaMunicode' => 'nullable|string',
            'municipality' => 'required|string',
            'county' => 'nullable|string',
            'methodology' => 'nullable|string',
            'totalCost' => 'required|numeric',
        ]);

        // Retrieve inspection type
        $inspectionType = $validated['InspectionType'];

        // Initialize bookingRequest variable
        $bookingRequest = null;

        // If the InspectionType is 'reInspection'
        if ($inspectionType === 'reInspection') {
            // Set the methodology to 'Dust Wipe Sampling'
            $validated['methodology'] = 'Dust Wipe Sampling';
            // Check for a valid order number or return an error if it's not valid
            if (empty($validated['orderNumber']) || strlen($validated['orderNumber']) < 7) {
                return $this->handleInvalidOrderNumber($validated);
            }

            // Check if the order number exists in the database
            $bookingRequest = \DB::table('booking_requests')->where('order_id', $validated['orderNumber'])->first();

            if (!$bookingRequest) {
                return $this->handleNonExistentOrderNumber($validated);
            }
        }

        // Common logic for both newInspection and reInspection
        $selectedDate = new \Carbon\Carbon($validated['selectedDate']);
        $isWeekend = $selectedDate->isWeekend();
        $subtotal = $validated['totalCost'] + ($isWeekend && isset($validated['weekendFee']) ? $validated['weekendFee'] : 0);
        
        // Prepare data to be returned based on whether a bookingRequest exists
        $eData = $bookingRequest ? $this->getBookingData($bookingRequest, $validated, $subtotal) : $this->getNewInspectionData($validated, $subtotal);
        
        return Inertia::render('Booking/ScheduleTime', $eData);
    }

    // Handling functions for different responses
    protected function handleInvalidOrderNumber($validated)
    {
        $errorMessage = 'Order number must be exactly 7 digits.';
        return Inertia::render('Booking/ScheduleDate', $this->prepareDataForRender($validated, $errorMessage));
    }

    protected function handleNonExistentOrderNumber($validated)
    {
        $errorMessage = "Order number \"{$validated['orderNumber']}\" does not exist.";
        return Inertia::render('Booking/ScheduleDate', $this->prepareDataForRender($validated, $errorMessage));
    }

    protected function prepareDataForRender($validated, $errorMessage)
    {
        // Prepare other booking data logic (for example, date conflicts)...
        return [
            'service' => $validated['service'],
            'price' => $validated['price'],
            'price_visual' => $validated['price_visual'],
            'price_dust' => $validated['price_dust'],
            'xrf' => $validated['xrf'],
            'xrf_reinspection_price' => $validated['xrf_reinspection_price'],
            'visual_reinspection_price' => $validated['visual_reinspection_price'],
            'dust_wipe_reinspection_price' => $validated['dust_wipe_reinspection_price'],
            'weekendFee' => $validated['weekendFee'],
            'includeXrf' => $validated['includeXrf'],
            'error' => ['orderNumber' => $errorMessage],
            // Add any additional data needed for rendering...
        ];
    }

    protected function getBookingData($bookingRequest, $validated, $subtotal)
    {
        return [
            'service' => $bookingRequest->service,
            'price' => $bookingRequest->price,
            'price_visual' => $bookingRequest->price_visual,
            'price_dust' => number_format($validated['price_dust'], 2),
            'xrf' => $bookingRequest->xrf,
            'xrf_reinspection_price' => $validated['xrf_reinspection_price'] !== null ? number_format($validated['xrf_reinspection_price'], 2) : null,
            'visual_reinspection_price' => $bookingRequest->visual_reinspection_price,
            'dust_wipe_reinspection_price' => $validated['dust_wipe_reinspection_price'] !== null ? number_format($validated['dust_wipe_reinspection_price'], 2) : null,
            'weekendFee' => $validated['weekendFee'] !== null ? number_format($validated['weekendFee'], 2) : null,
            'totalWeekendFee' => $bookingRequest->totalWeekendFee,
            'NewSubTotal' => $bookingRequest->NewSubTotal,
            'totalXrf' => $bookingRequest->totalXrf,
            'baseXrf' => $bookingRequest->baseXrf,
            'credSucharg' => $bookingRequest->credSucharg,
            'totalPay' => $bookingRequest->totalPay,
            'basePrice' => $bookingRequest->basePrice,
            'totalBasePrice' => $bookingRequest->totalBasePrice,
            'includeXrf' => $validated['includeXrf'],
            'orderNumber' => $validated['orderNumber'],
            'InspectionType' => $validated['InspectionType'],
            'dcaMunicode' => $bookingRequest->dcaMunicode,
            'municipality' => $bookingRequest->municipality,
            'county' => $bookingRequest->county,
            'methodology' => $validated['methodology'],
            'totalCost' => $validated['totalCost'],
            'subtotal' => number_format($subtotal, 2),
            'selectedDate' => $validated['selectedDate'],
            'selectedTime' => $bookingRequest->selectedTime,
            'name' => $bookingRequest->name,
            'email' => $bookingRequest->email,
            'phone' => $bookingRequest->phone,
            'address' => $bookingRequest->address,
            'apt' => $bookingRequest->apt,
            'city' => $bookingRequest->city,
            'state' => $bookingRequest->state,
            'block' => $bookingRequest->block,
            'lot' => $bookingRequest->lot,
            'units' => $bookingRequest->units,
            'builtBefore1978' => $bookingRequest->builtBefore1978,
            'useSameContactForAll' => $bookingRequest->useSameContactForAll,
            'siteContact' => $bookingRequest->siteContact,
            'siteContactEmail' => $bookingRequest->siteContactEmail,
            'siteContactPhone' => $bookingRequest->siteContactPhone,
            'optionalMessage' => $bookingRequest->optionalMessage,
        ];
    }

    protected function getNewInspectionData($validated, $subtotal)
    {
        return [
            'service' => $validated['service'],
            'price' => $validated['price'],
            'price_visual' => $validated['price_visual'],
            'price_dust' => $validated['price_dust'],
            'xrf' => number_format($validated['xrf'], 2),
            'xrf_reinspection_price' => number_format($validated['xrf_reinspection_price'] ?? 0, 2),
            'visual_reinspection_price' => number_format($validated['visual_reinspection_price'] ?? 0, 2),
            'dust_wipe_reinspection_price' => number_format($validated['dust_wipe_reinspection_price'] ?? 0, 2),
            'weekendFee' => number_format($validated['weekendFee'] ?? 0, 2),
            'selectedDate' => $validated['selectedDate'],
            'includeXrf' => $validated['includeXrf'],
            'InspectionType' => $validated['InspectionType'],
            'orderNumber' => $validated['orderNumber'],
            'dcaMunicode' => $validated['dcaMunicode'],
            'municipality' => $validated['municipality'],
            'county' => $validated['county'],
            'methodology' => $validated['methodology'],
            'totalCost' => $validated['totalCost'],
            'subtotal' => number_format($subtotal, 2),
        ];
    }

//------------------------------
    public function getSingleRequestData(Request $request)
    {
        // Get the service, to avoid url hack uses a get route
        $service = $request->input('service');

        // Check if the service variable is empty
        if (empty($service)) {
            return Inertia::render('Booking/Schedule');
        }

        // If service is not empty, render ScheduleTime view with the data
        return Inertia::render('Booking/TermsServices', [
            'service' => $request->input('service'), // Assuming this comes from the request
        ]);
    }
    
    public function handleSingleRequestData(Request $request)
    {
        // Get the service, to avoid url hack uses a get route
        $service = $request->input('service');

        // Check if the service variable is empty
        if (empty($service)) {
            return Inertia::render('Booking/Schedule');
        }
        
        \Log::info('Received request:', $request->all()); // Log incoming data for debugging

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'selectedDate' => 'required|date',
            'service' => 'required|string',
            'price' => 'required|numeric',
            'price_visual' => 'required|numeric',
            'price_dust' => 'required|numeric',
            'xrf' => 'required|numeric',
            'xrf_reinspection_price' => 'nullable|numeric',
            'visual_reinspection_price' => 'nullable|numeric',
            'dust_wipe_reinspection_price' => 'nullable|numeric',
            'weekendFee' => 'nullable|numeric',
            'includeXrf' => 'nullable|boolean',
            'InspectionType' => 'required|string',
            'orderNumber' => 'nullable|string',
            'dcaMunicode' => 'nullable|numeric',
            'municipality' => 'required|string',
            'county' => 'nullable|string',
            'methodology' => 'nullable|string',
            'totalCost' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'selectedTime' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => ['required', 'string', 'regex:/^(\+?\d{1,4}[\s-]?)?(\(?\d{1,4}\)?[\s-]?)?\d{1,4}([\s-]?)\d{1,4}([\s-]?)\d{1,4}$/'],
            'address' => 'required|string|max:255',
            'apt' => 'nullable|string|max:50',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:11',
            'block' => 'nullable|numeric|min:3',
            'lot' => 'nullable|numeric|min:1',
            'units' => 'required|integer|min:1|max:10',
            'builtBefore1978' => 'nullable|string|max:255',
            'useSameContactForAll' => 'required|boolean',
            'siteContact' => 'nullable|string|max:255',
            'siteContactEmail' => 'nullable|email|max:255',
            'siteContactPhone' => 'nullable|string|max:20',
            'optionalMessage' => 'nullable|string',
            'unitsDetails' => 'required|array|min:1|max:6',
            'unitsDetails.*.siteContact' => 'nullable|string|max:255',
            'unitsDetails.*.siteContactEmail' => 'nullable|email|max:255',
            'unitsDetails.*.siteContactPhone' => 'nullable|string|max:20',
            'unitsDetails.*.unitNumber' => 'nullable|string|max:50',
        ]);

        // Check if validation fails
if ($validator->fails()) {
    // Return the user to the 'Booking/ScheduleTime' view along with validation errors and the input data
    return Inertia::render('Booking/ScheduleTime', [
        'service' => $request->input('service'),
        'price' => $request->input('price'),
        'price_visual' => $request->input('price_visual'),
        'price_dust' => $request->input('price_dust'),
        'xrf' => $request->input('xrf'),
        'xrf_reinspection_price' => $request->input('xrf_reinspection_price'),
        'visual_reinspection_price' => $request->input('visual_reinspection_price'),
        'dust_wipe_reinspection_price' => $request->input('dust_wipe_reinspection_price'),
        'weekendFee' => $request->input('weekendFee'),
        'selectedDate' => $request->input('selectedDate'),
        'includeXrf' => $request->input('includeXrf'),
        'InspectionType' => $request->input('InspectionType'),
        'orderNumber' => $request->input('orderNumber'),
        'dcaMunicode' => $request->input('dcaMunicode'),
        'municipality' => $request->input('municipality'),
        'county' => $request->input('county'),
        'methodology' => $request->input('methodology'),
        'totalCost' => $request->input('totalCost'),
        'subtotal' => $request->input('subtotal'),
        'reinspection_price' => $request->input('reinspection_price'),
        'errors' => $validator->errors(),  // Pass validation errors here
    ]);
}

        // Retrieve the validated data
        $validated = $validator->validated();

        // Check if selectedDate is a weekend (Saturday or Sunday)
        $selectedDate = \Carbon\Carbon::parse($validated['selectedDate']);

        if ($selectedDate->isWeekend()) {
            $validated['weekendFee'] = $validated['weekendFee'] ?? 0; // Retain the provided fee if it's a weekend
        } else {
            $validated['weekendFee'] = 0.00; // Update weekend fee to 0 if not a weekend
        }

        // Initialize variables
        $baseXrf = 0;  // This will hold the base XRF value
        $totalXrf = 0;  // This will hold the total XRF fee
        $totalCost = 0;

        // Base price depends on InspectionType and methodology
        if ($validated['InspectionType'] === 'newInspection') {
            if ($validated['methodology'] === 'Visual Inspection') {
                $basePrice = $validated['price_visual']; // Base price for visual inspection
            } elseif ($validated['methodology'] === 'Dust Wipe Sampling') {
                $basePrice = $validated['price_dust']; // Base price for dust wipe sampling
            } else {
                // Handle unexpected methodology for new inspections
                return 'Price Not Available: Invalid methodology for new inspection';
            }
        } elseif ($validated['InspectionType'] === 'reInspection') {
            // Set methodology to 'Dust Wipe Sampling' for reInspection
            $validated['methodology'] = 'Dust Wipe Sampling';
            
            // Always use dust wipe reinspection price
            $basePrice = $validated['dust_wipe_reinspection_price'];  // Base price for dust wipe reinspection
        } else {
            // Handle unexpected InspectionType
            return 'Price Not Available: Invalid inspection type';
        }

        // Calculate the subTotal (base price * units)
        $NewSubTotal = $basePrice * $validated['units']; // Base price * units
        $totalWeekendFee = $validated['weekendFee'] * $validated['units']; // Weekend fee * units

        // Calculate baseXrf based on InspectionType
        if ($validated['InspectionType'] === 'newInspection') {
            // For new inspections, baseXrf equals xrf value
            $baseXrf = $validated['xrf'];
        } elseif ($validated['InspectionType'] === 'reInspection') {
            // For reinspections, baseXrf equals xrf_reinspection_price
            $baseXrf = $validated['xrf_reinspection_price'];
        }

        // Now calculate totalXrf as baseXrf * units
        $totalXrf = $baseXrf * $validated['units'];

        // Add weekend fee to subTotal
        $NewSubTotal += $totalWeekendFee;

        // If XRF service is included, add the XRF fee * units
        if ($validated['includeXrf']) {
            $NewSubTotal += $totalXrf;
        }

        // Calculate credit surcharge (4% of subTotal)
        $credSucharg = $NewSubTotal * 0.04;

        // Calculate totalPay = NewSubTotal + credit surcharge
        $totalPay = $NewSubTotal + $credSucharg;

        // Calculate totalBasePrice: basePrice * units
        $totalBasePrice = $basePrice * $validated['units']; // Calculate total base price

        // Add additional calculated fields to the validated data
        $validated['basePrice'] = $basePrice; // Add base price to validated data
        $validated['totalBasePrice'] = $totalBasePrice; // Add totalBasePrice to validated data
        $validated['totalWeekendFee'] = $totalWeekendFee; // Add totalWeekendFee to validated data  
        $validated['totalCost'] = $totalCost;
        $validated['NewSubTotal'] = $NewSubTotal;
        $validated['totalXrf'] = $totalXrf;
        $validated['baseXrf'] = $baseXrf;
        $validated['credSucharg'] = $credSucharg;
        $validated['totalPay'] = $totalPay;



        // Return the data via Inertia to the frontend
        return Inertia::render('Booking/TermsServices', [
            'bookDate' => $validated,
        ]);
    }

//------------------------------
    public function getTerms(Request $request)
    {
        // Get the service, to avoid url hack uses a get route
        $service = $request->input('service');

        // Check if the service variable is empty
        if (empty($service)) {
            return Inertia::render('Booking/Schedule');
        }

        // If service is not empty, render ScheduleTime view with the data
        return Inertia::render('Booking/TermsServices', [
            'service' => $service, // Pass the service data to the view
        ]);
    }

    public function handleSingleTOS(Request $request)
    {
        // Get the service from the request
        $service = $request->input('service');

        // Check if the service variable is empty
        if (empty($service)) {
            return Inertia::render('Booking/Schedule');
        }

        // Validate the incoming request data
        $validated = $request->validate([
            'service' => 'required|string|max:255',
            'selectedDate' => 'required|date',
            'price' => 'required|numeric|between:0,999999.99',
            'xrf' => 'nullable|numeric|between:0,999999.99',
            'reinspection_price' => 'nullable|numeric|between:0,999999.99',
            'includeXrf' => 'nullable|boolean',
            'selectedTime' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|lowercase|email|max:255',
            'phone' => ['required', 'string', 'regex:/^(\+?\d{1,4}[\s-]?)?(\(?\d{1,4}\)?[\s-]?)?\d{1,4}([\s-]?)\d{1,4}([\s-]?)\d{1,4}$/'],
            'address' => 'required|string|max:255',
            'apt' => 'nullable|string|max:50',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'block' => 'nullable|string|max:50',
            'lot' => 'nullable|string|max:50',
            'units' => 'required|integer|min:1',
            'builtBefore1978' => 'nullable|string|max:255',
            'useSameContactForAll' => 'required|boolean',
            'siteContact' => 'nullable|string|max:255',
            'siteContactEmail' => 'nullable|email|max:255',
            'siteContactPhone' => 'nullable|string|max:20',
            'optionalMessage' => 'nullable|string',
            'unitsDetails' => 'required|array|min:1',
            'unitsDetails.*.siteContact' => 'nullable|string|max:255',
            'unitsDetails.*.siteContactEmail' => 'nullable|email|max:255',
            'unitsDetails.*.siteContactPhone' => 'nullable|string|max:20',
            'unitsDetails.*.unitNumber' => 'nullable|string|max:50',
            'discountCode' => 'nullable|string|max:50', // Assuming the discount code is passed as a string
            'discountTotal' => 'nullable|numeric|between:0,999999.99' // Assuming the discount total is passed as a number
        ]);

        // Get the service price details from the ServicePrice model
        $servicePrice = ServicePrice::where('service_name', $service)->first();

        if (!$servicePrice) {
            // If no service price is found, return an error
            return Inertia::render('Booking/Error', ['message' => 'Service price not found.']);
        }

        // Calculate the base price from the ServicePrice model
        $basePrice = $servicePrice->price;

        // Parse the selected date
        $selectedDate = Carbon::parse($validated['selectedDate']);

        // Set weekend fee based on selected date
        $weekendFee = 0;
        if ($selectedDate->isWeekend()) {
            // Only query the model if it's a weekend
            $weekendFee = $servicePrice->weekendFee ?? 0;
        }

        // Calculate the subTotal (base price + weekend fee if applicable)
        $subTotal = $basePrice * $validated['units']; // Base price * units
        $totalWeekendFee = $weekendFee * $validated['units']; // weekend fee * units
        $totalCost = $validated['price'] * $validated['units']; // Total cost = provided price * units
        $totalXrf = ($validated['includeXrf'] && $validated['xrf'] !== null) ? $validated['xrf'] * $validated['units'] : 0; // Total XRF fee if applicable

        // Add weekend fee to subTotal if the selected date is a weekend (Saturday or Sunday)
        $subTotal += $totalWeekendFee;

        // If XRF service is included, add the XRF fee * units
        if ($validated['includeXrf']) {
            $subTotal += $totalXrf;
        }

        // Calculate credit surcharge (4% of subTotal)
        $credSucharg = $subTotal * 0.04;

        // Calculate totalAmount = subTotal + credSucharg
        $totalAmount = $subTotal + $credSucharg;

        // Format the amounts to 2 decimal places
        $validated['weekendFee'] = number_format($weekendFee, 2);
        $validated['subTotal'] = number_format($subTotal, 2);
        $validated['totalWeekendFee'] = number_format($totalWeekendFee, 2);
        $validated['totalCost'] = number_format($totalCost, 2);
        $validated['totalXrf'] = number_format($totalXrf, 2);
        $validated['credSucharg'] = number_format($credSucharg, 2);
        $validated['totalAmount'] = number_format($totalAmount, 2);

        // Return the Terms and Services view with validated data
        return Inertia::render('Booking/TermsServices', [
            'bookDate' => $validated,
        ]);
    }

//-----------------------DISCOUNT CODE-------
public function getDiscountCode(Request $request)
{
    // Get the service, to avoid url hack uses a get route
    $service = $request->input('service');

    // Check if the service variable is empty
    if (empty($service)) {
        return Inertia::render('Booking/Schedule');
    }

    // If service is not empty, render ScheduleTime view with the data
    return Inertia::render('Booking/ScheduleDate', [
        'service' => $request->input('service'), // Assuming this comes from the request
    ]);
}

public function DiscountCode(Request $request)
    {
        // Get the service, to avoid url hack uses a get route
        $service = $request->input('service');

        // Check if the service variable is empty
        if (empty($service)) {
            return Inertia::render('Booking/Schedule');
        }
        
        \Log::info('Received request:', $request->all()); // Log incoming data for debugging

        // Validate the incoming request
        $validated = $request->validate([
            'selectedDate' => 'required|date',
            'service' => 'required|string',
            'price' => 'required|numeric',
            'price_visual' => 'required|numeric',
            'price_dust' => 'required|numeric',
            'xrf' => 'required|numeric',
            'xrf_reinspection_price' => 'nullable|numeric',
            'visual_reinspection_price' => 'nullable|numeric',
            'dust_wipe_reinspection_price' => 'nullable|numeric',
            'weekendFee' => 'nullable|numeric',
            'includeXrf' => 'nullable|boolean',
            'InspectionType' => 'required|string',
            'orderNumber' => 'nullable|string',
            'dcaMunicode' => 'nullable|numeric',
            'municipality' => 'required|string',
            'county' => 'nullable|string',
            'methodology' => 'nullable|string',
            'totalCost' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'selectedTime' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => ['required', 'string', 'regex:/^(\+?\d{1,4}[\s-]?)?(\(?\d{1,4}\)?[\s-]?)?\d{1,4}([\s-]?)\d{1,4}([\s-]?)\d{1,4}$/'],
            'address' => 'required|string|max:255',
            'apt' => 'nullable|string|max:50',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:11',
            'block' => 'nullable|numeric|min:3',
            'lot' => 'nullable|numeric|min:1',
            'units' => 'required|integer|min:1|max:10',
            'builtBefore1978' => 'nullable|string|max:255',
            'useSameContactForAll' => 'required|boolean',
            'siteContact' => 'nullable|string|max:255',
            'siteContactEmail' => 'nullable|email|max:255',
            'siteContactPhone' => 'nullable|string|max:20',
            'optionalMessage' => 'nullable|string',
            'unitsDetails' => 'required|array|min:1|max:6',
            'unitsDetails.*.siteContact' => 'nullable|string|max:255',
            'unitsDetails.*.siteContactEmail' => 'nullable|email|max:255',
            'unitsDetails.*.siteContactPhone' => 'nullable|string|max:20',
            'unitsDetails.*.unitNumber' => 'nullable|string|max:50',
            'discountCode' => 'required|string|max:50'
        ]);

        // Check if selectedDate is a weekend (Saturday or Sunday)
        $selectedDate = \Carbon\Carbon::parse($validated['selectedDate']);

        if ($selectedDate->isWeekend()) {
            $validated['weekendFee'] = $validated['weekendFee'] ?? 0; // Retain the provided fee if it's a weekend
        } else {
            $validated['weekendFee'] = 0.00; // Update weekend fee to 0 if not a weekend
        }

        // Initialize variables
        $baseXrf = 0;  // This will hold the base XRF value
        $totalXrf = 0;  // This will hold the total XRF fee
        $totalCost = 0;

        // Base price depends on InspectionType and methodology
        if ($validated['InspectionType'] === 'newInspection') {
            if ($validated['methodology'] === 'Visual Inspection') {
                $basePrice = $validated['price_visual']; // Base price for visual inspection
            } elseif ($validated['methodology'] === 'Dust Wipe Sampling') {
                $basePrice = $validated['price_dust']; // Base price for dust wipe sampling
            } else {
                // Handle unexpected methodology for new inspections
                return 'Price Not Available: Invalid methodology for new inspection';
            }
        } elseif ($validated['InspectionType'] === 'reInspection') {
            // Set methodology to 'Dust Wipe Sampling' for reInspection
            $validated['methodology'] = 'Dust Wipe Sampling';
            
            // Always use dust wipe reinspection price
            $basePrice = $validated['dust_wipe_reinspection_price'];  // Base price for dust wipe reinspection
        } else {
            // Handle unexpected InspectionType
            return 'Price Not Available: Invalid inspection type';
        }

        $totalWeekendFee = $validated['weekendFee'] * $validated['units']; // Weekend fee * units

        // Calculate baseXrf based on InspectionType
        if ($validated['InspectionType'] === 'newInspection') {
            // For new inspections, baseXrf equals xrf value
            $baseXrf = $validated['xrf'];
        } elseif ($validated['InspectionType'] === 'reInspection') {
            // For reinspections, baseXrf equals xrf_reinspection_price
            $baseXrf = $validated['xrf_reinspection_price'];
        }

        // Now calculate totalXrf as baseXrf * units
        $totalXrf = $baseXrf * $validated['units'];

        // Calculate totalBasePrice: basePrice * units
        $totalBasePrice = $basePrice * $validated['units']; // Calculate total base price// Calculate the subTotal (base price * units)

        // Initialize discountTotal
        $discountTotal = 0;

        // Validate the discount code if it exists
        if (!empty($validated['discountCode'])) {
            $discountCode = $validated['discountCode'];
            $discount = DiscountCode::where('code', $discountCode)
                ->where('is_active', true)
                ->where('expiration_date', '>=', now())
                ->first();

            if ($discount) {
                // Check usage limits
                if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
                    $errorMessage = "Coupon code limit has been reached.";

                    return Inertia::render('Booking/TermsServices', [
                        'bookDate' => $validated,
                        'message' => $errorMessage,
                        'success' => false,
                    ]);
                }

                // Calculate discount based on the totalBasePrice only
                $discountPercentage = $discount->discount_percentage;
                $discountAmount = ($totalBasePrice * ($discountPercentage / 100)); // Apply discount to base price x units
                $inspector_name = $discount->inspector_name; // The Code Owners Name

                // Reduce totalBasePrice by the discount from base price
                $totalBasePrice -= $discountAmount; 
                $discountTotal = $discountAmount; // Set discountTotal
                
                // Update the used_count
                $discount->increment('used_count');
            } else {
                $errorMessage = "Invalid or expired discount code.";
                return Inertia::render('Booking/bookDate', [
                    'bookDate' => $validated,
                    'message' => $errorMessage,
                    'success' => false,
                ]);
            }
        }

        $NewSubTotal = $totalBasePrice; // Base price * units
        // Add weekend fee to subTotal
        $NewSubTotal += $totalWeekendFee;

        // If XRF service is included, add the XRF fee * units
        if ($validated['includeXrf']) {
            $NewSubTotal += $totalXrf;
        }

        // Calculate credit surcharge (4% of subTotal)
        $credSucharg = $NewSubTotal * 0.04;

        // Calculate totalPay = NewSubTotal + credit surcharge
        $totalPay = $NewSubTotal + $credSucharg;

        // Add additional calculated fields to the validated data
        $validated['basePrice'] = $basePrice; // Add base price to validated data
        $validated['totalBasePrice'] = $totalBasePrice; // Add totalBasePrice to validated data
        $validated['totalWeekendFee'] = $totalWeekendFee; // Add totalWeekendFee to validated data  
        $validated['totalCost'] = $totalCost;
        $validated['NewSubTotal'] = $NewSubTotal;
        $validated['totalXrf'] = $totalXrf;
        $validated['baseXrf'] = $baseXrf;
        $validated['credSucharg'] = $credSucharg;
        $validated['totalPay'] = $totalPay;
        $validated['discountTotal'] = number_format($discountTotal, 2); // Add discountTotal to the validated array
        $validated['inspector_name'] = $inspector_name; // Include the Inspector/Agent
        $validated['discountCode'] = $validated['discountCode']; // Include the discount code in the response
        $validated['discountPercentage']  = number_format($discountPercentage, 0); // Include the discount % in the response

        // dd($validated);
        // Return the data via Inertia to the frontend
        return Inertia::render('Booking/TermsServices', [
            'bookDate' => $validated,
        ]);
    }

//----------------PAYMENT OPTIONS--------------
public function payOptions(Request $request)
{
     // Get the service, to avoid url hack uses a get route
     $service = $request->input('service');

     // Check if the service variable is empty
     if (empty($service)) {
         return Inertia::render('Booking/Schedule');
     }
 
     // If service is not empty, render ScheduleTime view with the data
     return Inertia::render('Booking/PaymentOptions', [
         'service' => $request->input('service'), // Assuming this comes from the request
     ]);
}

public function selectPayment(Request $request)
{
    // Dump and die for debugging
    // dd($request->all()); 
    
    // Get the service from request
    $service = $request->input('service');

    // Check if the service variable is empty
    if (empty($service)) {
        return Inertia::render('Booking/Schedule');
    }
    
    // Log incoming data for debugging
    \Log::info('Received request:', $request->all());

    // Validate the incoming request
    $validated = $request->validate([
        'discountCode' => 'string|nullable',
        'inspector_name' => 'string|nullable',
        'discountTotal' => 'numeric|nullable',
        'discountPercentage' => 'numeric|nullable',
        'selectedDate' => 'required|date',
        'service' => 'required|string',
        'price' => 'required|numeric',
        'price_visual' => 'required|numeric',
        'price_dust' => 'required|numeric',
        'xrf' => 'required|numeric',
        'xrf_reinspection_price' => 'nullable|numeric',
        'visual_reinspection_price' => 'nullable|numeric',
        'dust_wipe_reinspection_price' => 'nullable|numeric',
        'weekendFee' => 'nullable|numeric',
        'totalWeekendFee' => 'nullable|numeric',
        'NewSubTotal' => 'numeric|required',
        'totalXrf' => 'numeric|nullable',
        'baseXrf' => 'numeric|nullable',
        'credSucharg' => 'numeric|required',
        'totalPay' => 'numeric|required',
        'basePrice' => 'numeric|required',
        'totalBasePrice' => 'numeric|required',
        'includeXrf' => 'boolean|required',
        'InspectionType' => 'required|string',
        'orderNumber' => 'string|nullable',
        'dcaMunicode' => 'nullable|numeric',
        'municipality' => 'string|required',
        'county' => 'string|required',
        'methodology' => 'string|nullable',
        'totalCost' => 'numeric|required',
        'subtotal' => 'numeric|required',
        'selectedTime' => 'required|string|max:255',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => ['required', 'string', 'regex:/^(\+?\d{1,4}[\s-]?)?(\(?\d{1,4}\)?[\s-]?)?\d{1,4}([\s-]?)\d{1,4}([\s-]?)\d{1,4}$/'],
        'address' => 'required|string|max:255',
        'apt' => 'nullable|string|max:50',
        'city' => 'required|string|max:100',
        'state' => 'required|string|max:11',
        'block' => 'nullable|numeric|min:3',
        'lot' => 'nullable|numeric|min:1',
        'units' => 'required|integer|min:1|max:10',
        'builtBefore1978' => 'nullable|string|max:255',
        'useSameContactForAll' => 'required|boolean',
        'siteContact' => 'nullable|string|max:255',
        'siteContactEmail' => 'nullable|email|max:255',
        'siteContactPhone' => 'nullable|string|max:20',
        'optionalMessage' => 'nullable|string',
        'unitsDetails' => 'required|array|min:1|max:6',
        'unitsDetails.*.siteContact' => 'nullable|string|max:255',
        'unitsDetails.*.siteContactEmail' => 'nullable|email|max:255',
        'unitsDetails.*.siteContactPhone' => 'nullable|string|max:20',
        'unitsDetails.*.unitNumber' => 'nullable|string|max:50',
        'agreedToTerms' => 'boolean|required'
    ]);
    
    
    // Proceed to render the payment options view after validation
    return Inertia::render('Booking/PaymentOptions', ['paySelectData' => $validated]);
}
    
}
