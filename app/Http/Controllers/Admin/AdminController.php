<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; // Make sure to import this
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;  // Correct import for Storage

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\Response;

use App\Models\BookingRequest;
use App\Models\DiscountCode;
use App\Models\ServicePrice;
use App\Models\UnitContact;
use App\Models\Inspector;
use App\Models\Record;
use App\Models\TeamCode;
use Carbon\Carbon;


class AdminController extends Controller
{
    public function index()
    {
        $leadData = $this->getLeadBooking();
        return Inertia::render('Admin/Pages/Dashboard', ['leadBookingData' => $leadData]);
    }

    protected function getLeadBooking() {
        // Fetch class data from the relevant models
        $pbBooking = BookingRequest::all();
        $pbCode = DiscountCode::all();
        $pbServicePrice = ServicePrice::all();
        $pbUnitContact = UnitContact::all();

        // Fetch counts of NEW UNREAD BOOKINGS where read_unr is 'newInspection or reInspection'
        $countUnread = BookingRequest::whereNull('jobStatus')->count();

        // Fetch counts of BOOKINGS where InspectionType is 'newInspection or reInspection'
        $countNewInspection = BookingRequest::where('InspectionType', 'newInspection')->count();
        $CountReInspection = BookingRequest::where('InspectionType', 'reInspection')->count();

        // Fetch counts of INVOICE where selectedPaymentMethod is 'MANUAL' and read_unr is 'unread'
        $CountPayInvoice = BookingRequest::where('payment_status', 'pending')
        ->count();

        $CountPayElectronics = BookingRequest::where('payment_status', 'succeeded')
        ->count();

        // Fetch counts of AGENTS/INSPECTORS where discountCode is 'NOTNULL'
        $CountAgentBooking = BookingRequest::whereNotNull('discountCode')
        ->whereNotNull('assignTo')
        ->count();
        // $CountAgent = DiscountCode::where('inspector_name')->count();

        // Fetch counts of Website NO AGENTS/INSPECTORS where discountCode is 'NULL'
        $CountWebBooking = BookingRequest::whereNull('discountCode')
        ->whereNull('assignTo')
        ->whereNull('jobStatus')
        ->count();

        // Fetch counts of BOOKINGS AGENTS/INSPECTORS where assignTo is 'Not NULL'
        $CountAssignJobs = BookingRequest::whereNotNull('assignTo')
        ->whereNotNull('discountCode')->count();
        $CountPendingAssign = BookingRequest::whereNull('assignTo')
        ->whereNull('discountCode')->count();

        // Finance
        $totalPaid = BookingRequest::where('payment_status', 'succeeded')
        ->sum('NewSubTotal');
        $totalUnpaid = BookingRequest::where('payment_status', 'pending')
        ->sum('NewSubTotal');

        // Inspectors & Agents
        $CountBookingInspectors = BookingRequest::whereNotNull('discountCode')->count();
        $CountDiscountCode = DiscountCode::whereNotNull('code')->count();
        $CountInspectors = Inspector::whereNotNull('name')->count();
        $CountServices = ServicePrice::whereNotNull('service_name')->count();

        // Agent Register Code
        $CountTeamCode = TeamCode::whereNotNull('team_code')->count();

        $CountInspectors = Inspector::count();
        $CountInsComp = BookingRequest::whereNotNull('assignTo')
            ->where('jobStatus', 'completed')
            ->whereNotIn('order_id', Record::pluck('order_id')) // Exclude order_ids found in Record
            ->count();


        // Records and Certificates
        $finalReview = Record::whereIn('admin_review', ['pending', 'completed'])
            ->where('report_status', 'pending')
            ->count();
        $reportSent = Record::where('admin_review', 'completed')
            ->where('report_status', 'completed')
            ->count();
        $expire90 = Record::where('admin_review', 'completed')
            ->where('report_status', 'completed')
            ->where('date_issued', '<', Carbon::now()->subMonths(33)) // 2 years and 9 months ago
            ->count();


        // Combine and return the data
        return [
            'pbBooking' => $pbBooking,
            'pbCode' => $pbCode,
            'pbServicePrice' => $pbServicePrice,
            'pbUnitContact' => $pbUnitContact,
            'countUnread' => $countUnread,
            'countNewInspection' => $countNewInspection,
            'CountReInspection' => $CountReInspection,
            'CountPayInvoice' => $CountPayInvoice,
            'CountPayElectronics' => $CountPayElectronics,
            'CountAgentBooking' => $CountAgentBooking,
            'CountWebBooking' => $CountWebBooking,
            'CountAssignJobs' => $CountAssignJobs,
            'CountPendingAssign' => $CountPendingAssign,
            'CountBookingInspectors' => $CountBookingInspectors,


            'totalPaid' => $totalPaid,
            'totalUnpaid' => $totalUnpaid,
            'CountInspectors' => $CountInspectors,
            'CountDiscountCode' => $CountDiscountCode,
            'CountInspectors' => $CountInspectors,
            'CountServices' => $CountServices,
            'CountTeamCode' => $CountTeamCode,
            // 'totalRevenue' => $totalRevenue,
            // 'totalProfit' => $totalProfit,
            // 'totalCost' => $totalCost,

            'finalReview' => $finalReview,
            'reportSent' => $reportSent,
            'expire90' => $expire90, // This will return the count of bookings that are overdue (3 days) in the report_status 'completed' and admin_review 'completed' state.
            'CountInsComp' => $CountInsComp, // This will return the count of bookings that have been completed by the inspector and have a corresponding record
            

        ];
    }

    // Discount Code
    public function viewCode()
{
    $discountCodes = DiscountCode::orderBy('inspector_name')->get();
    return Inertia::render('Admin/Pages/DiscountCode', [
        'discountCodes' => $discountCodes,
    'message' => session('message'),]);
}

// Reset Discount code
public function resetCode(Request $request, $id)
{
    $discountCode = DiscountCode::find($id);

    if ($discountCode) {
        $discountCode->used_count = 0; // Set used_count to 0
        $discountCode->save();
    }

    return redirect()->route('admin.discount.code')->with('message', 'Discount code has been reset successfully.');
}

public function killCode(Request $request, $id)
{
    $discountCode = DiscountCode::find($id);

    if ($discountCode) {
        // Toggle the is_active field
        $discountCode->is_active = !$discountCode->is_active; // Use boolean toggle
        $discountCode->save();
    }

    return redirect()->route('admin.discount.code')->with('message', 'Discount code has been Activated/De-Activated successfully.');
}

// Create New Code
public function createCode()
    {
        $inspectors = Inspector::all();

        return Inertia::render('Admin/Pages/CreateCode', [
            'inspectors' => $inspectors,
            'message' => session('message'),
        ]);
    }

    // Store new discount code
    public function storeCode(Request $request)
    {
        // Custom validation with error handling
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:discount_codes',
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'expiration_date' => 'required|date',
            'inspector_id' => 'required|exists:inspectors,id',
        ]);

        // If validation fails, return back with errors
        if ($validator->fails()) {
            return redirect()->route('admin.code.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Find the selected inspector
        $inspector = Inspector::find($request->inspector_id);

        // Create and save discount code
        DiscountCode::create([
            'code' => $request->code,
            'discount_percentage' => $request->discount_percentage,
            'expiration_date' => $request->expiration_date,
            'inspector_name' => $inspector->name,
            'usage_limit' => 100,
            'is_active' => 1,
            'used_count' => 0,
        ]);

        // Redirect back with success message
        return redirect()->route('admin.discount.code')->with('message', 'Discount code created successfully.');
    }

    // View All Inspectors
    public function inspectorsIndex()
{
    $inspectors = Inspector::select('id', 'name', 'email', 'phone_number', 'license_number', 'license_copy', 'adminApprove')
        ->withCount([
            'discountCodes as discount_codes_count' => function ($query) {
                $query->whereColumn('inspector_name', 'inspectors.name');
            },
            'bookingRequests as booking_requests_count' => function ($query) {
                $query->whereColumn('assignTo', 'inspectors.name');
            }
        ])
        ->get();

    return Inertia::render('Admin/Pages/Inspectors', [
        'inspectors' => $inspectors,
    ]);
}

// View Inspector
public function inspectorsView(Request $request, $id)
{
    // Fetch the inspector
    $inspector = Inspector::findOrFail($id); // Use findOrFail to handle the case where the inspector isn't found

    // Load related relationships in one go
    $inspector->load('discountCodes', 'bookingRequests'); // Load the relationships

    // Get discount codes related to the inspector based on their name
    $discountCodes = DiscountCode::where('inspector_name', $inspector->name)->get();

    // Generate the correct signature URL
    $signatureUrl = Storage::url('private/signature/' . $inspector->signature);
    $licenseUrl = Storage::url('private/license/' . $inspector->license_copy);

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
    $overDue = BookingRequest::where('assignTo', $inspector->name)
        ->whereIn('jobStatus', ['pending', 'completed'])
        ->where('selected_date', '<', now()->subDays(3))
        ->count();

    $upComing = BookingRequest::where('assignTo', $inspector->name)
        ->whereIn('jobStatus', ['pending', 'completed'])
        ->where('selected_date', '>', now())
        ->count();

    $reInspectionBook = BookingRequest::where('assignTo', $inspector->name)
        ->where('jobStatus', '!=', 'completed')
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

    // Pass the counts and data to the view
    return Inertia::render('Admin/Pages/InspectorView', [
        'eUser' => $inspector,
        'discountCodes' => $discountCodes,
        'bookingRequests' => $bookingRequests,
        'records' => $records,
        'countInsBookings' => $countInsBookings,
        'countIncRec' => $countIncRec,
        'countIncCompleted' => $countIncCompleted,
        'countIncPending' => $countIncPending,
        'overDue' => $overDue,
        'upComing' => $upComing,
        'reInspectionBook' => $reInspectionBook,
        'bookings' => $bookings,
        'searchTerm' => $request->search,
        'records' => $records,
        'signatureUrl' => $signatureUrl,
        'licenseUrl' => $licenseUrl,
    ]);
}

// Suspend/Unsuspend Inspector
public function inspectorsStatus(Request $request, $id)
{
    $status = Inspector::find($id);

    if ($status) {
        // Toggle the adminApprove field
        $status->adminApprove = !$status->adminApprove; // Use boolean toggle
        $status->save();
    }

    return redirect()->route('admin.inspectors.index')->with('message', 'Inspector Activated/De-Activated successfully.');
}








}
