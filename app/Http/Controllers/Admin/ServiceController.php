<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

use App\Models\ServicePrice;
use App\Models\TeamCode;

class ServiceController extends Controller
{
    // Get all Services & Price
    public function serviceIndex()
    {
        $services = ServicePrice::all();
        return Inertia::render('Admin/Pages/Services', [
            'services' => $services,
            'message' => session('message'),
        ]);
    
    }

    // Get VIEW Service by ID
    public function getService(Request $request, $id)
    {
        $service = ServicePrice::find($id);
        return Inertia::render('Admin/Pages/ServicesView', [
            'service' => $service
        ]);
    }

    // Create Service Get
    public function createServiceGet()
    {
        return Inertia::render('Admin/Pages/ServicesCreate');
    }

    // Create Service Store
    public function storeService(Request $request)
{
    // Validation
    $validator = Validator::make($request->all(), [
        'service_name' => 'required|string|max:255|unique:service_prices',
        'price' => 'required|numeric|min:0',
        'price_visual' => 'required|numeric|min:0',
        'price_dust' => 'required|numeric|min:0',
        'xrf' => 'required|numeric|min:0',
        'xrf_reinspection_price' => 'required|numeric|min:0',
        'visual_reinspection_price' => 'required|numeric|min:0',
        'dust_wipe_reinspection_price' => 'required|numeric|min:0',
        'weekendFee' => 'required|numeric|min:0',
        'description' => 'required|string|max:1000',
        'bookMax' => 'required|integer|min:1',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        // Return the user back to the form with validation errors and old input
        return back()->withErrors($validator)->withInput();
    }

    // Store the service data in the database
    ServicePrice::create($request->all());

    // Flash a success message to the session
    session()->flash('message', 'Service Created Successfully');

    // Get all services and return the response
    $services = ServicePrice::all();

    return Inertia::render('Admin/Pages/Services', [
        'services' => $services,
        'message' => session('message'),
    ]);
}


    // Get Update service
    public function getUpdateService(Request $request, $id)
    {
        $service = ServicePrice::find($id);
        return Inertia::render('Admin/Pages/ServicesUpdate', [
            'service' => $service,
            'message' => session('message'),]);
    }
    
    // Update Service
    public function updateService(Request $request, $id)
    {
        $service = ServicePrice::find($id);
        $service->update($request->all());
        return redirect()->back()->with('message', 'Service Updated Successfully');
    }

    // Delete Service
    public function deleteService($id)
    {
        $service = ServicePrice::find($id);
        $service->delete();
        return redirect()->back()->with('message', 'Service Deleted Successfully');
    }

    // Agent Sign up code
    public function codeIndex()
    {
        $teamCode = TeamCode::all();
        return Inertia::render('Admin/Pages/RegisterCode', [
            'teamCode' => $teamCode,
            'message' => session('message'),
        ]);
    }

    // Create Agent Sign up code
    public function codeCreate()
    {
        return Inertia::render('Admin/Pages/RegisterCodeCreate');
    }

    // Store Agent Sign up code
    public function storeCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_code' => 'required|string|max:255|unique:team_codes',
            'team_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
    TeamCode::create($request->all());
    
        // Flash a success message to the session
    session()->flash('message', 'Service Created Successfully');
    
    // Get all existing codes
    $teamCode = TeamCode::all();

    return Inertia::render('Admin/Pages/RegisterCode', [
        'teamCode' => $teamCode,
        'message' => session('message'),
    ]);

    }

    // Delete Agent Sign up code
    public function DestroyCode($id)
    {
        $teamCode = TeamCode::find($id);
        
        if (!$teamCode) {
            return redirect()->back()->with('message', 'Code Not Found');
        }
        $teamCode->delete();
        return redirect()->back()->with('message', 'Code Deleted Successfully');
    }

}
