<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;  // Correct import for Storage

use App\Mail\DustMail;  // Dust Report/
use App\Mail\VisualMail;  // Visual Report/
use App\Mail\XRFMail;  // XRF Report/

use Illuminate\Support\Facades\Mail; // Mail/
use App\Services\ReportGenerator; // Services/
use Spatie\PdfToImage\Pdf;
// use Imagick;

use Inertia\Inertia;

use App\Models\BookingRequest;
use App\Models\DiscountCode;
use App\Models\ServicePrice;
use App\Models\UnitContact;
use App\Models\Inspector;
use App\Models\Record;
use App\Models\RecordPhoto;
use Carbon\Carbon;

class ReportController extends Controller
{
    //  Inspection Completed - Doc. Pending
    public function inspectionCompleted(Request $request){

        // Ensure the user is authenticated before serving the file
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }
        // $Auth::user();

        // Retrieve the latest 10 inspected bookings
        $bookings = BookingRequest::where('jobStatus', 'completed')
            ->whereNotIn('order_id', Record::pluck('order_id')) // Exclude order_ids found in Record
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
                          ->orWhere('old_order_id', 'like', '%' . $request->search . '%')
                          ->orWhere('assignTo', 'like', '%' . $request->search . '%');
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

            $pageName = 'Site Visited, Completed Inspection - Documents NOT Uploaded';
        
            return Inertia::render('Admin/Pages/Bookings', [
                'bookings' => $bookings,
                'searchTerm' => $request->search,
                'pageName' => $pageName,
            ]);    
    }

    //  Inspection Completed - Doc. Uploaded
    public function reportReview(Request $request){

        // Ensure the user is authenticated before serving the file
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }
        // $Auth::user();

        // Retrieve the latest 10 inspected bookings
        $bookings = Record::where('admin_review', ['pending', 'completed'])
            ->where('report_status', 'pending')
            ->whereIn('order_id', BookingRequest::pluck('order_id'))
            ->when($request->search, function($query) use ($request) {
                $query->where(function ($query) use ($request) { // group the search conditions
                    $query->where('certificate_number', 'like', '%' . $request->search . '%')
                          ->orWhere('order_id', 'like', '%' . $request->search . '%')
                          ->orWhere('pass_fail', 'like', '%' . $request->search . '%')
                          ->orWhere('city', 'like', '%' . $request->search . '%')
                          ->orWhere('county', 'like', '%' . $request->search . '%')
                          ->orWhere('municipality', 'like', '%' . $request->search . '%')
                        //   ->orWhere('InspectionType', 'like', '%' . $request->search . '%')
                          ->orWhere('methodology', 'like', '%' . $request->search . '%')
                          ->orWhere('property_owner_name', 'like', '%' . $request->search . '%')
                          ->orWhere('property_owner_phone', 'like', '%' . $request->search . '%')
                          ->orWhere('property_owner_email', 'like', '%' . $request->search . '%')
                          ->orWhere('license_number', 'like', '%' . $request->search . '%')
                          ->orWhere('inspector_name', 'like', '%' . $request->search . '%')
                          ->orWhere('date_issued', 'like', '%' . $request->search . '%');
                });
    
            })
            ->orderBy('created_at', 'desc') // Change 'created_at' to your desired column
            ->paginate(10)
            ->withQueryString();
    
            // Extract order_ids from bookings
            $orderIds = $bookings->pluck('order_id')->unique();

            // Retrieve corresponding BookingRequest records based on order_id
            $bookingRequests = BookingRequest::whereIn('order_id', $orderIds)->get();

            $pageName = 'Admin Review Needed - Documents Uploaded';
        
            return Inertia::render('Admin/Pages/Records', [
                'bookings' => $bookings,
                'bookingRequests' => $bookingRequests,
                'searchTerm' => $request->search,
                'pageName' => $pageName,
    
            ]);
    }

    //  Inspection Completed - Report Sent
    public function reportSent(Request $request){
        // Ensure the user is authenticated before serving the file
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }
        // $Auth::user();

        // Retrieve the latest 10 inspected bookings
        $bookings = Record::where('report_status', 'completed')
            ->where('admin_review', 'completed')
            ->whereIn('order_id', BookingRequest::pluck('order_id'))
            ->when($request->search, function($query) use ($request) {
                $query->where(function ($query) use ($request) { // group the search conditions
                    $query->where('certificate_number', 'like', '%' . $request->search . '%')
                          ->orWhere('order_id', 'like', '%' . $request->search . '%')
                          ->orWhere('pass_fail', 'like', '%' . $request->search . '%')
                          ->orWhere('city', 'like', '%' . $request->search . '%')
                          ->orWhere('county', 'like', '%' . $request->search . '%')
                          ->orWhere('municipality', 'like', '%' . $request->search . '%')
                          //   ->orWhere('InspectionType', 'like', '%' . $request->search . '%')
                          ->orWhere('methodology', 'like', '%' . $request->search . '%')
                          ->orWhere('property_owner_name', 'like', '%' . $request->search . '%')
                          ->orWhere('property_owner_phone', 'like', '%' . $request->search . '%')
                          ->orWhere('property_owner_email', 'like', '%' . $request->search . '%')
                          ->orWhere('license_number', 'like', '%' . $request->search . '%')
                          ->orWhere('inspector_name', 'like', '%' . $request->search . '%')
                          ->orWhere('date_issued', 'like', '%' . $request->search . '%');
                });

            })
            ->orderBy('created_at', 'desc') // Change 'created_at' to your desired column
            ->paginate(10)
            ->withQueryString();

            // Extract order_ids from bookings
            $orderIds = $bookings->pluck('order_id')->unique();

            // Retrieve corresponding BookingRequest records based on order_id
            $bookingRequests = BookingRequest::whereIn('order_id', $orderIds)->get();
            $pageName = 'Documents Uploaded, - Admin Review Completed - Report Sent';
            return Inertia::render('Admin/Pages/Records', [
                'bookings' => $bookings,
                'bookingRequests' => $bookingRequests,
               'searchTerm' => $request->search,
                'pageName' => $pageName,
            ]);

    }

    //  Certificates expiring in 90 days
    public function certificatesExpiring(Request $request){
        // Ensure the user is authenticated before serving the file
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }
        // $Auth::user();

        // Retrieve the latest 10 inspected bookings
        $bookings = Record::where('admin_review', 'completed')
            ->where('report_status', 'completed')
            ->where('date_issued', '<', Carbon::now()->subMonths(33)) // 2 years and 9 months ago
            ->whereIn('order_id', BookingRequest::pluck('order_id'))
            ->when($request->search, function($query) use ($request) {
                $query->where(function ($query) use ($request) { // group the search conditions
                    $query->where('certificate_number', 'like', '%' . $request->search . '%')
                          ->orWhere('order_id', 'like', '%' . $request->search . '%')
                          ->orWhere('pass_fail', 'like', '%' . $request->search . '%')
                          ->orWhere('city', 'like', '%' . $request->search . '%')
                          ->orWhere('county', 'like', '%' . $request->search . '%')
                          ->orWhere('municipality', 'like', '%' . $request->search . '%')
                          //   ->orWhere('InspectionType', 'like', '%' . $request->search . '%')
                          ->orWhere('methodology', 'like', '%' . $request->search . '%')
                          ->orWhere('property_owner_name', 'like', '%' . $request->search . '%')
                          ->orWhere('property_owner_phone', 'like', '%' . $request->search . '%')
                          ->orWhere('property_owner_email', 'like', '%' . $request->search . '%')
                          ->orWhere('license_number', 'like', '%' . $request->search . '%')
                          ->orWhere('inspector_name', 'like', '%' . $request->search . '%')
                          ->orWhere('date_issued', 'like', '%' . $request->search . '%');
                });

            })
            ->orderBy('created_at', 'desc') // Change 'created_at' to your desired column
            ->paginate(10)
            ->withQueryString();

            // Extract order_ids from bookings
            $orderIds = $bookings->pluck('order_id')->unique();

            // Retrieve corresponding BookingRequest records based on order
            $bookingRequests = BookingRequest::whereIn('order_id', $orderIds)->get();
            $pageName = 'Certificates Expiring in 90 Days';
            return Inertia::render('Admin/Pages/Records', [
                'bookings' => $bookings,
                'bookingRequests' => $bookingRequests,
                'searchTerm' => $request->search,
                'pageName' => $pageName,
            ]);
    }

    //  Re-Send Report Status
    public function reSendReportStatus(Request $request, $orderId){
        // Ensure the user is authenticated before serving the file
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }
        // $Auth::user();

        // Retrieve the booking record based on the provided order_id
        $booking = Record::where('order_id', $orderId)->first();

        if($booking){
            // Send email to the property owner notifying them about the report status re-send
            // Implement email sending logic here

            // Update the report_status field to 'pending'
            $booking->report_status = 'pending';
            $booking->save();

            // Redirect back to the records page with a success message
            return redirect()->route('admin.records.index')->with('success', 'Report status re-sent successfully.');
        } else {
            return redirect()->route('admin.records.index')->with('error', 'Record not found.');
        }
    }

    //  View Report Resources
    public function viewResources(Request $request, $id = null)
    {
        // Ensure the user is authenticated before serving the file
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        // Retrieve the single record by its ID, including related photos and inspector
        $record = Record::with(['photos', 'inspector'])->findOrFail($id);

        // Generate the signature URL if the inspector exists and has a signature
        $signatureUrl = $record->inspector && $record->inspector->signature
            ? route('admin.signature.show', ['filename' => $record->inspector->signature])
            : null;

        // Load and process the XRF data from a JSON file
        $jsonFilePath = storage_path('app/public/json/xrf_data/1_xrf_data.json');
        
        // Check if the JSON file exists
        if (file_exists($jsonFilePath)) {
            $jsonData = json_decode(file_get_contents($jsonFilePath), true);

            // Filter rows where 'Concentration' > 1.0
            $filteredData = array_filter($jsonData, function ($row) {
                return isset($row['Concentration']) && $row['Concentration'] > 1.0;
            });

            // Select the desired columns
            $selectedColumns = array_map(function ($row) {
                return [
                    'Concentration' => $row['Concentration'],
                    'Room' => $row['Room'],
                    'Structure' => $row['Structure'],
                    'Member' => $row['Member'],
                    'Substrate' => $row['Substrate'],
                    'Wall' => $row['Wall'],
                ];
            }, $filteredData);
        } else {
            $selectedColumns = [];
        }

        // Return the view with all relevant data
        $pageName = 'View Report Resources';

        return Inertia::render('Admin/Pages/ViewRecord', [
            'record' => $record,
            'pageName' => $pageName,
            'signatureUrl' => $signatureUrl,
            'xrfData' => $selectedColumns,
        ]);
    }

// Get the signature URL
public function showSignature($filename)
{
    // Path to the signature file
    $path = storage_path("app/private/signature/{$filename}");

        // Check if the file exists, otherwise return a 404 error
    if (!file_exists($path)) {
        return response()->file(public_path('/images/default-signature.png')); // Fallback
    }

    // Return the private file as a response
    return response()->file($path);
}

//-----------------------Report--------------------------
// Approve & Email the Report or Cancel for Inspectors review
public function approveReport(Request $request)
{
    $action = $request->input('action');
    $id = $request->input('id');
    $record = Record::find($id);

    if (!$record) {
        return redirect()->back()->with('error', 'Record not found.');
    }

    try {
        switch ($action) {
            case 'reset':
                return $this->resetRecord($record);
            case 'approve':
                $this->approveRecordActions($record);
                $signatureUrl = $this->generateSignatureUrl($record);
                $pdfPath = $this->generatePdf($record, $signatureUrl);
                $this->sendEmailWithPdf($record, $pdfPath, $signatureUrl);
                return redirect()->back()->with('message', 'Report approved and email sent.');
            default:
                return redirect()->back()->with('error', 'Invalid action.');
        }
    } catch (\Exception $e) {
        \Log::error('Error approving report: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error approving report: ' . $e->getMessage());
    }
}

protected function approveRecordActions(Record $record)
{
    $record->date_issued = now();
    $record->admin_review = 'completed';
    $record->report_status = 'completed';
    $record->save();
}

protected function generateSignatureUrl(Record $record)
{
    return $record->signature
        ? storage_path('app/private/signature/' . $record->signature)
        : asset('images/default-signature.png');
}

protected function generatePdf(Record $record, $signatureUrl)
{
    $pdfFileName = "report_{$record->id}_" . time();
    $pdfPath = (new ReportGenerator)->saveReport(['record' => $record, 'signatureUrl' => $signatureUrl], $pdfFileName);
    if (!$pdfPath) {
        throw new \Exception("PDF generation failed for Record ID: {$record->id}");
    }
    return $pdfPath;
}

protected function convertPdfToImage(Record $record)
{
    $files = [
        'floor_plan',
        'lab_report',
        'chain_custody',
        'xrf_report'
    ];
    $imagePaths = [];

    foreach ($files as $file) {
        $filePath = storage_path("app/public/{$record->$file}");
        
        // If the file is a PDF
        if (file_exists($filePath) && strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) == 'pdf') {
            $imagePath = $this->pdfToImage($filePath);
            $imagePaths[$file] = $imagePath;

            // Replace the PDF with the image path in the database
            $record->$file = $imagePath;
            $record->save();
        }
    }

    return $imagePaths;
}

// protected function pdfToImage($pdfFilePath)
// {
//     $imagePath = storage_path('app/public/images/' . uniqid() . '.png');
//     $pdf = new Pdf($pdfFilePath);
//     $pdf->setOutputFormat('png')
//         ->setResolution(300)
//         ->saveImage($imagePath);
//     return $imagePath;
// }

protected function updateRecordFiles(Record $record, $imagePaths)
    {
        foreach ($imagePaths as $file => $imagePath) {
            $record->$file = str_replace(storage_path('app/public'), 'storage', $imagePath);
        }
        $record->save();
    }

    protected function sendEmailWithPdf(Record $record, $mergedPdfPath, $signatureUrl)
{
    $email = $record->property_owner_email;
    $xrfData = $record->xrf_data ?? 'default-xrf-data';
    $mailClasses = [];

    // Normalize includeXrf to boolean
    $includeXrf = filter_var($record->includeXrf, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;

    // Debug log
    \Log::debug("Evaluating email conditions for Record ID: {$record->id}", [
        'methodology' => $record->methodology,
        'includeXrf' => $includeXrf,
        'xrf_pass_fail' => $record->xrf_pass_fail,
        'pass_fail' => $record->pass_fail,
    ]);

    // Condition 1: Visual Inspection, No XRF
    if ($record->methodology === 'Visual Inspection' && !$includeXrf) {
        \Log::debug("Condition 1 matched: Visual Inspection, No XRF");
        $mailClasses[] = new VisualMail($record, $mergedPdfPath, $signatureUrl, $xrfData);
    }

    // Condition 2: Dust Wipe Sampling, No XRF
    if ($record->methodology === 'Dust Wipe Sampling' && !$includeXrf) {
        \Log::debug("Condition 2 matched: Dust Wipe Sampling, No XRF");
        $mailClasses[] = new DustMail($record, $mergedPdfPath, $signatureUrl, $xrfData);
    }

    // Condition 3: XRF Pass
    if (($record->methodology === 'Visual Inspection' || $record->methodology === 'Dust Wipe Sampling') && 
        $includeXrf && 
        $record->xrf_pass_fail === 'pass') {
        \Log::debug("Condition 3 matched: XRF Pass");
        $mailClasses[] = new XRFMail($record, $mergedPdfPath);
    }

    // Condition 4: XRF Fail
    if ($includeXrf && $record->xrf_pass_fail === 'fail') {
        if ($record->methodology === 'Visual Inspection') {
            \Log::debug("Condition 4 matched: Visual Inspection, XRF Fail");
            $mailClasses[] = new VisualMail($record, $mergedPdfPath, $signatureUrl, $xrfData);
        } elseif ($record->methodology === 'Dust Wipe Sampling') {
            \Log::debug("Condition 4 matched: Dust Wipe Sampling, XRF Fail");
            $mailClasses[] = new DustMail($record, $mergedPdfPath, $signatureUrl, $xrfData);
        }
    }

    // Fallback for unhandled cases
    if (empty($mailClasses)) {
        \Log::warning("No specific condition matched, falling back to methodology-based email");
        if ($record->methodology === 'Visual Inspection') {
            $mailClasses[] = new VisualMail($record, $mergedPdfPath, $signatureUrl, $xrfData);
        } elseif ($record->methodology === 'Dust Wipe Sampling') {
            $mailClasses[] = new DustMail($record, $mergedPdfPath, $signatureUrl, $xrfData);
        } else {
            $mailClasses[] = new VisualMail($record, $mergedPdfPath, $signatureUrl, $xrfData);
        }
    }

    foreach ($mailClasses as $mailClass) {
        Mail::to($email)
            ->cc(env('COMPANY_EMAIL')) // Send a copy to the company email
            ->send($mailClass);
    
        \Log::info("Email Sent Successfully to: " . $email . " - " . get_class($mailClass) . " (CC: " . env('COMPANY_EMAIL') . ")");
    }
    
}

private function resetRecord($record)
    {
        try {
            foreach ($record->photos as $photo) {
                Storage::disk('public')->delete($photo->photo_path);
                $photo->delete();
            }

            foreach (['floor_plan', 'lab_report', 'chain_custody', 'xrf_report', 'xrf_csv'] as $fileField) {
                Storage::disk('public')->delete($record->$fileField);
            }

            $record->delete();
            return redirect()->back()->with('message', 'Record and files reset successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error resetting record: ' . $e->getMessage());
        }
    }


//------------------------------------








    private function sendApprovalEmail(Record $record)
    {
        // Get the property owner's email
        $propertyOwnerEmail = $record->property_owner_email;

        // Send an email notification
        Mail::to($propertyOwnerEmail)->send(new ReportApprovedMail($record));
    }

    private function handleResetAction(Record $record)
    {
        // Handle any additional reset logic if needed
        return $this->resetRecord($record);
    }

    
}