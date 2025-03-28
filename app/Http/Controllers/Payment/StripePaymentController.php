<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail; // Mail/
use App\Mail\InvoiceMail;  // Mail/
use App\Mail\ReceiptMail;  // Receipt/
use App\Services\PdfGenerator; // Services/
use PDF; // Don't forget to import the PDF facade

use App\Models\BookingRequest;
use App\Models\UnitContact;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Inertia\Inertia;
use Carbon\Carbon;

class StripePaymentController extends Controller
{
    protected $pdfGenerator;

    public function __construct(PdfGenerator $pdfGenerator) // Inject PdfGenerator
    {
        $this->pdfGenerator = $pdfGenerator;
    }

    public function getCheckout(Request $request)
    {
        return Inertia::render('Booking/Schedule');
    }

    public function handleSingleCheckout(Request $request)
        {
            // dd($request->all());      
            // Get the service from the request
            $service = $request->input('service');

            // Check if the service variable is empty
            if (empty($service)) {
                return Inertia::render('Booking/Schedule');
            }
            
            \Log::info('Received request:', $request->all()); // Log incoming data for debugging

            // Validate the incoming data 
            $validated = $request->validate([
                'selectedDate' => 'required|date', // Ensures a valid date format
                'service' => 'required|string', // Ensures the service is a string
                'price' => 'required|numeric', // Ensures price is a number
                'price_visual' => 'required|numeric', // Ensures price_visual is a number
                'price_dust' => 'required|numeric', // Ensures price_dust is a number
                'xrf' => 'required|numeric', // Ensures xrf is a number
                'xrf_reinspection_price' => 'required|numeric', // Ensures xrf_reinspection_price is a number
                'visual_reinspection_price' => 'required|numeric', // Ensures visual_reinspection_price is a number
                'dust_wipe_reinspection_price' => 'required|numeric', // Ensures dust_wipe_reinspection_price is a number
                'weekendFee' => 'nullable|numeric', // Total weekend fee is optional, but if provided, it should be a number
                'totalWeekendFee' => 'nullable|numeric', // Total weekend fee is optional, but if provided, it should be a number
                'NewSubTotal' => 'required|numeric', // Ensures NewSubTotal is a number
                'totalXrf' => 'nullable|numeric', // Ensures totalXrf is a number
                'baseXrf' => 'nullable|numeric', // Ensures baseXrf is a number
                'credSucharg' => 'required|numeric', // Ensures credSucharg is a number
                'totalPay' => 'required|numeric', // Ensures totalPay is a number
                'basePrice' => 'required|numeric', // Ensures basePrice is a number
                'totalBasePrice' => 'required|numeric', // Ensures totalBasePrice is a number
                'includeXrf' => 'required|boolean', // Ensures includeXrf is a boolean
                'InspectionType' => 'required|string', // Ensures InspectionType is a string
                'orderNumber' => 'nullable|string', // Ensures orderNumber is a string
                'dcaMunicode' => 'required|numeric', // Ensures dcaMunicode is a number
                'municipality' => 'required|string', // Ensures municipality is a string
                'county' => 'required|string', // Ensures county is a string
                'methodology' => 'required|string', // Ensures methodology is a string
                'totalCost' => 'nullable|numeric', // Total cost is optional, but if provided, it should be a number
                'subtotal' => 'required|numeric', // Ensures subtotal is a number
                'selectedTime' => 'required|string', // Ensures selectedTime is a string
                'name' => 'required|string', // Ensures name is a string
                'email' => 'required|email', // Ensures email is a valid email address
                'phone' => ['required', 'string', 'regex:/^(\+?\d{1,4}[\s-]?)?(\(?\d{1,4}\)?[\s-]?)?\d{1,4}([\s-]?)\d{1,4}([\s-]?)\d{1,4}$/'], // Ensures phone of different formats
                'address' => 'required|string', // Ensures address is a string
                'apt' => 'nullable|string', // Apt is optional, but if provided, it should be a string
                'city' => 'required|string', // Ensures city is a string
                'state' => 'required|string', // Ensures state is a string
                'block' => 'nullable|numeric', // Ensures block is a number
                'lot' => 'nullable|numeric', // Ensures lot is a number
                'units' => 'required|integer|min:1', // Ensures units is a positive integer
                'builtBefore1978' => 'required|string|in:before,after', // Ensures builtBefore1978 is either "before" or "after"
                'useSameContactForAll' => 'required|boolean', // Ensures useSameContactForAll is a boolean
                'siteContact' => 'nullable|string|max:255',
                'siteContactEmail' => 'nullable|email|max:255',
                'siteContactPhone' => 'nullable|string|max:20',
                'optionalMessage' => 'nullable|string|max:500',
                'unitsDetails' => 'required|array|min:1',
                'unitsDetails.*.siteContact' => 'nullable|string|max:255',
                'unitsDetails.*.siteContactEmail' => 'nullable|email|max:255',
                'unitsDetails.*.siteContactPhone' => 'nullable|string|max:20',
                'unitsDetails.*.unitNumber' => 'nullable|string|max:50',
                'agreedToTerms' => 'required|boolean',
                'discountCode' => 'nullable|string|max:50',
                'inspector_name' => 'nullable|string|max:225',
                'discountTotal' => 'nullable|numeric',
                'discountPercentage' => 'nullable|numeric',
                'selectedPaymentMethod' => 'string|required|max:50',

            ]);

            // After validation, you can access it safely:
            $weekendFee = $validated['weekendFee'] ?? 0; // Provide a default value if it's null
            $validated['weekendFee'] = number_format($weekendFee, 2); // Now you can safely use it

            $totalWeekendFee = $validated['totalWeekendFee']; // Provide a default value if it's null
            $validated['totalWeekendFee'] = number_format($totalWeekendFee, 2); // Now you can safely use it

            // Format the selectedDate to a standard format (e.g., Y-m-d) before storing in the database
            $selectedDateFormatted = Carbon::parse($validated['selectedDate'])->format('Y-m-d');

            // Generate a unique 7-digit order ID
            $orderId = $this->generateUniqueOrderId();

            // Ensure use_same_contact_for_all is set to a valid value (defaulting to false if not provided)
            $useSameContactForAll = $validated['useSameContactForAll'] ?? false; // Defaults to `false` if not provided

            // Add the orderId to validated data
            $validated['order_id'] = $orderId; // Add the generated order ID to the validated data

            $payment_status = 'pending'; // Set the payment status to 'pending' by default
            $validated['payment_status'] = $payment_status; // Add the payment status to the validated data
            // Start a database transaction to ensure data integrity
            \DB::beginTransaction();

            // Create the BookingRequest model and store the data
            $bookingRequest = BookingRequest::create([
                'old_order_id' => $validated['orderNumber'],
                'order_id' => $orderId,
                'service' => $validated['service'],
                'selected_date' => $selectedDateFormatted,
                'price' => $validated['price'],
                'price_visual' => $validated['price_visual'],
                'price_dust' => $validated['price_dust'],
                'xrf' => $validated['xrf'],
                'xrf_reinspection_price' => $validated['xrf_reinspection_price'],
                'visual_reinspection_price' => $validated['visual_reinspection_price'],
                'dust_wipe_reinspection_price' => $validated['dust_wipe_reinspection_price'],
                'includeXrf' => $validated['includeXrf'],
                'InspectionType' => $validated['InspectionType'],
                'dcaMunicode' => $validated['dcaMunicode'],
                'municipality' => $validated['municipality'],
                'county' => $validated['county'],
                'methodology' => $validated['methodology'],
                'selectedTime' => $validated['selectedTime'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'apt' => $validated['apt'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'block' => $validated['block'],
                'lot' => $validated['lot'],
                'units' => $validated['units'],
                'builtBefore1978' => $validated['builtBefore1978'],
                'useSameContactForAll' => $validated['useSameContactForAll'],
                'siteContact' => $validated['siteContact'],
                'siteContactEmail' => $validated['siteContactEmail'],
                'siteContactPhone' => $validated['siteContactPhone'],
                'weekendFee' => $validated['weekendFee'],
                'totalWeekendFee' => $validated['totalWeekendFee'],
                'totalCost' => $validated['totalCost'],
                'subtotal' => $validated['subtotal'],
                'NewSubTotal' => $validated['NewSubTotal'],
                'totalXrf' => $validated['totalXrf'],
                'baseXrf' => $validated['baseXrf'],
                'credSucharg' => $validated['credSucharg'],
                'totalPay' => $validated['totalPay'],
                'basePrice' => $validated['basePrice'],
                'totalBasePrice' => $validated['totalBasePrice'],
                'optionalMessage' => $validated['optionalMessage'],         
                'agreedToTerms' => $validated['agreedToTerms'],
                'assignTo' => $validated['inspector_name'] ?? null,
                'discountCode' => $validated['discountCode'] ?? null,
                'discountTotal' => $validated['discountTotal'] ?? 0,
                'discountPercentage' => $validated['discountPercentage'] ?? 0,
                'selectedPaymentMethod' => $validated['selectedPaymentMethod'],
                'payment_status' => $payment_status, // Set the payment status to 'pending' by default
            ]);
            
            // Loop through each unit's details and create a UnitContact record
            foreach ($validated['unitsDetails'] as $unitDetail) {
                $unitContact = new UnitContact();
                $unitContact->booking_request_id = $bookingRequest->id; // Foreign key to the booking request
                $unitContact->site_contact = $unitDetail['siteContact'] ?? ''; // Use an empty string if not provided
                $unitContact->site_contact_email = $unitDetail['siteContactEmail'] ?? null;
                $unitContact->site_contact_phone = $unitDetail['siteContactPhone'] ?? null;
                $unitContact->unit_number = $unitDetail['unitNumber'] ?? null;
                $unitContact->save();
            }     

            // Commit the transaction
            \DB::commit();

            // Check the selected payment method
            if ($validated['selectedPaymentMethod'] === "MANUAL") {

            // Assuming $orderId is the unique order ID you generated earlier
            $pdfFileName = "invoice_{$orderId}"; // Create a unique file name based on the order number

            // Create PDF invoice and store the path
            $pdfPath = $this->pdfGenerator->saveInvoice($validated, $pdfFileName);  // 'invoice' PDF file name

            // Send email with the invoice attached
            // \Mail::to($validated['email'])->send(new InvoiceMail($validated, $pdfPath));

            \Mail::to($validated['email'])
    ->cc(env('COMPANY_EMAIL')) // Send a copy to the company email
    ->send(new InvoiceMail($validated, $pdfPath));

            $message = 'unpaid';

            // Redirect to the success page
            return Inertia::render('Payment/Success', [
                'eData' => $validated, // Passing the validated data for Vue component to use
                'message' => $message,
                'status' => 'unpaid', // This can be modified as needed
            ]);
            } else {
                // Handle Stripe payment

            // Initialize Stripe with your secret key
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

            // Collect billing address data from the validated input
            // $billingDetails = [
            //     'name' => $validated['name'],
            //     'email' => $validated['email'],
            //     'address' => [
            //         'line1' => $validated['address'],
            //         'city' => $validated['city'],
            //         'state' => $validated['state'],
            //         // 'postal_code' => $validated['postalCode'], // Make sure to collect the postal code in the form
            //         'country' => 'US', // You can dynamically set the country if needed
            //     ],
            // ];

            // Create a Stripe customer
            $customer = \Stripe\Customer::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'], // Optional, if you'd like to include phone
                // 'address' => $billingDetails['address'], // Include address details
            ]);

            // Create a PaymentIntent for the total amount, now linked to the customer
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $validated['totalPay'] * 100, // Convert to smallest currency unit (cents)
                'currency' => 'usd',
                'description' => $validated['service'],
                'customer' => $customer->id, // Link payment to the created customer
                'metadata' => [ // Add this metadata section
        'order_id' => $orderId,
        'booking_request_id' => $bookingRequest->id
    ],
            ]);

            // Save the payment intent in the BookingRequest
        $bookingRequest->payment_intent = $paymentIntent->id; // Save the payment intent ID
        $bookingRequest->save(); // Commit the change to the database

            // Return Inertia response with payment details
            return Inertia::render('Payment/Payment', [
                'eData' => $validated, // Passing the validated data for Vue component to use
                'clientSecret' => $paymentIntent->client_secret, // Pass clientSecret for Stripe
                'bookingRequestId' => $bookingRequest->id, // Pass the BookingRequest ID for tracking
                'orderId' => $orderId, // Pass the orderId to the next page
            ]);
        }
}

    // Helper function to generate a unique 7-digit order ID
    private function generateUniqueOrderId()
    {
        do {
            // Generate a random 7-digit number
            $orderId = rand(1000000, 9999999);
        } while (BookingRequest::where('order_id', $orderId)->exists()); // Ensure it's unique

        return $orderId;
    }
//----------------------------------
    // public function success()
    // {
    //     return Inertia::render('Payment/Success');
    // }

    public function success(Request $request)
    {
        $paymentIntentId = $request->input('payment_intent'); 

        if (!$paymentIntentId) {
            return Inertia::render('Payment/Error', [
                'message' => 'Payment Intent ID is missing.',
            ]);
        }

        $bookingRequests = BookingRequest::where('payment_intent', $paymentIntentId)->get();

        if ($bookingRequests->isEmpty()) {
            return Inertia::render('Payment/Error', [
                'message' => 'No associated booking requests found for this payment intent.',
            ]);
        }

        foreach ($bookingRequests as $bookingRequest) {
            $bookingRequest->update(['payment_status' => 'succeeded']);
            $dataArray = $bookingRequest->toArray();
            $pdfFileName = "receipt_{$dataArray['order_id']}.pdf"; 
            $pdfPath = $this->pdfGenerator->saveInvoice($dataArray, $pdfFileName);
            // \Mail::to($dataArray['email'])->send(new ReceiptMail($dataArray, $pdfPath));

            \Mail::to($dataArray['email'])
    ->cc(env('COMPANY_EMAIL')) // Send a copy to the company email
    ->send(new ReceiptMail($dataArray, $pdfPath));
        }

        return Inertia::render('Payment/Success', [
            'message' => 'Payment was successful and receipts have been sent!',
        ]);
    }

    
    
    public function handleWebhook(Request $request)
    {
        // Set your Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        // Retrieve the raw body of the request
        $payload = @file_get_contents('php://input');
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET'); // Set your webhook secret key

        try {
            // Verify the webhook signature to ensure the request is from Stripe
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

            // Handle the payment_intent.succeeded event
            if ($event->type == 'payment_intent.succeeded') {
                $paymentIntent = $event->data->object; // Get the payment intent object

                // Extract the booking request ID from the metadata
                $bookingRequestId = $paymentIntent->metadata->booking_request_id;

                // Find the booking request and update its payment status to 'paid'
                $bookingRequest = BookingRequest::find($bookingRequestId);
                if ($bookingRequest) {
                    $bookingRequest->payment_status = 'paid';
                    $bookingRequest->save();
                }
            }

            // Respond to Stripe that the webhook was received successfully
            return response()->json(['status' => 'success']);
        } catch (\UnexpectedValueException $e) {
            // Return a 400 error if webhook payload is invalid
            return response()->json(['status' => 'failure', 'error' => $e->getMessage()], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Return a 400 error if webhook signature verification fails
            return response()->json(['status' => 'failure', 'error' => $e->getMessage()], 400);
        }
    }
}




    
