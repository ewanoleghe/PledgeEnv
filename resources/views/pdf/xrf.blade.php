<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> XRF inspection Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4; /* Adjust line height for better readability */
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        /* Default page setup for portrait orientation */
        @page {
            size: A4;
            margin: 10mm 20mm 20mm 10mm; /* Top 10mm, Right 20mm, Bottom 20mm, Left 10mm */
        }

        /* To ensure page breaks between sections */
        .page-break {
            page-break-before: always;
        }

        /* Landscape orientation for Lead-safe Certificate section */
        @media print {
            .landscape-page {
                page-break-before: always;
                transform: rotate(90deg);
                transform-origin: top left;
                width: 100vh;  /* Use the height of the page as the width */
                height: 100vw; /* Use the width of the page as the height */
                margin-left: 0;
                margin-top: 0;
                position: relative;
                padding: 20px; /* Optional, to provide space around the content */
            }


            /* Ensure content after the landscape page remains in portrait orientation */
            body {
                transform: none;
            }
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px 20px; /* Reduced left and right padding */
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .font-semibold {
            font-weight: 600;
        }

        .text-xs {
            font-size: 12px;
        }

        .text-2sm {
            font-size: 16px;
        }

        .text-2xl {
            font-size: 24px;
        }

        .underline {
            text-decoration: underline;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        .mb-4 {
            margin-bottom: 20px;
        }

        .mb-5 {
            margin-bottom: 30px;
        }

        .mb-8 {
            margin-bottom: 40px;
        }

        .p-5 {
            padding: 20px;
        }

        .w-full {
            width: 100%;
        }

        .max-w-4xl {
            max-width: 1200px;
            margin: 0 auto;
        }

        .border-black {
            border: 1px solid #000;
        }

        .border-t-2 {
            border-top: 2px solid #000;
        }

        .border-collapse {
            border-collapse: collapse;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed; /* Ensures equal column widths */
        }

        th, td {
            border: 1px solid black;
            padding: 2px;
            text-align: left; /* Align text to the left */
            word-wrap: break-word; /* Ensures long words wrap within the cell */
        }

        th {
            background-color: #f2f2f2;
            text-align: left; /* Align header text to the left */
        }

        td {
            word-wrap: break-word;
        }

        /* Styling the signature image to have a consistent size */
        .img.signature {
            max-width: 20px;      /* Maximum width of the signature */
            max-height: 12px;     /* Maximum height of the signature */
            width: auto;          /* Maintain aspect ratio */
            height: auto;         /* Maintain aspect ratio */
            object-fit: contain;  /* Ensure the image fits within bounds */
        }

        .prepared-by {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 300px; /* Adjust width as needed */
            border: 2px solid black;
            padding: 10px;
            text-align: left;
            background-color: #fff;
        }

        .primary-btn {
            background-color: #0066cc;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
        }

        .primary-btn:hover {
            background-color: #005bb5;
        }

        .primary-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .text-blue-500 {
            color: #0066cc;
        }

        .text-red-600 {
            color: #e53e3e;
        }

        .text-gray-300 {
            color: #d1d5db;
        }

        .text-gray-200 {
            color: #edf2f7;
        }

        /* Ensure page breaks between sections */
        .page-break {
            page-break-before: always;
        }

        /* Adjust text line height and spacing for tighter print */
        .text-left, .text-center {
            line-height: 1.4;
        }

        /* Adjust table to fit better */
        table {
            font-size: 14px;
        }

        td, th {
            padding: 10px;
        }

        /* Styling for the contact information */
        .company-info p {
            font-size: 12px; /* Smaller font */
            color: #222020; /* Grey color */
            margin: 0; /* Remove spacing */
            line-height: 1.2; /* Tight line spacing */
        }

        /* Remove spacing between Lead Visual Inspection Report and address */
        .report-header p {
            margin: 0; /* Remove margin */
            padding: 0; /* Remove padding */
        }

        /* Apply "Times New Roman" font and size 10 after the address */
        .report-content {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt; /* Set font size to 10 */
            line-height: 1.2; /* Tighten the line spacing */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="text-right">
            <div class="logo-container">
                <img src="{{ public_path('images/logo.png') }}" alt="Company Logo" style="width: 200px; height: auto;">
                <div class="company-info"><p>
                    {{ $settings->company_address }}<br>
                    {{ $settings->company_city }}, {{ $settings->company_state }} {{ $settings->company_zip }}<br>
                    Email: {{ $settings->company_email }}<br>
                    Phone: {{ $settings->company_phone }}</p>
                </div>
                <p style="margin-top: 0.5pt; margin-bottom: 0;">
                <p>Date: {{ \Carbon\Carbon::now()->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="text-center mb-4 report-header">
            <p style="font-weight: bold; font-size: 24px; margin-bottom: -1px;">Lead-Based Paint Evaluation Report Inspection</p>
            <p>
            </p>
        
            <br><br>
            <p class="text-left text-2sm leading-relaxed mb-4" style="margin-bottom: 2pt; margin-top: 20pt;"> <!-- Adjusted margin-top -->
                <strong>Performed At:</strong> <br>
                {{ $record->address }}, @if(isset($record->designation)) <br>Unit: {{ $record->designation }}, @endif <br>
                {{ $record->municipality }}, NJ {{ $record->city }}
            </p><br>
            <p class="text-left text-2sm leading-relaxed mb-4">
                <strong>Performed For:</strong> <br>
                {{ $record->property_owner_name }}<br>
                {{ $record->address }}, @if(isset($record->designation)) <br>Unit: {{ $record->designation }}, @endif <br>
                {{ $record->municipality }}, NJ {{ $record->city }}
            </p>
        </div>
        <div class="prepared-by">
            <p class="text-left text-2sm leading-relaxed">
                <strong>Prepared By:</strong> <br>
                {{ $settings->app_name }}<br>
                {{ $settings->company_address }}<br>
                {{ $settings->company_city }}, {{ $settings->company_state }} {{ $settings->company_zip }}<br>
                Email: {{ $settings->company_email }}<br>
                Phone: {{ $settings->company_phone }}<br>
                Inspection Date: {{ \Carbon\Carbon::now()->format('M d, Y') }}<br>
                Order Number: {{ $record->order_id }}
            </p>
        </div>

        <!-- Apply the 'report-content' class to apply 'Times New Roman' font and size 10 -->
        <div class="mt-3">
            <div class="page-break">
                <h2>Table of Contents</h2>
                <ul class="space-y-2">
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Contact Information</span>
                        <span class="text-gray-500 text-right w-12">3</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Executive Summary</span>
                        <span class="text-gray-500 text-right w-12">4</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Components with Lead Based Paint</span>
                        <span class="text-gray-500 text-right w-12">4</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Regulatory Requirements</span>
                        <span class="text-gray-500 text-right w-12">5</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Required Disclosure</span>
                        <span class="text-gray-500 text-right w-12">5</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Required Training for Workers</span>
                        <span class="text-gray-500 text-right w-12">5</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Controlling Lead-Based Paint</span>
                        <span class="text-gray-500 text-right w-12">5</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Abatement for Lead-Based Paint Free Certification</span>
                        <span class="text-gray-500 text-right w-12">6</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Component Removal</span>
                        <span class="text-gray-500 text-right w-12">6</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Paint Stripping</span>
                        <span class="text-gray-500 text-right w-12">6</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Procedures & Methodology</span>
                        <span class="text-gray-500 text-right w-12">6</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Location Conventions</span>
                        <span class="text-gray-500 text-right w-12">6</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Paint Testing</span>
                        <span class="text-gray-500 text-right w-12">6</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">X-Ray Fluorescence</span>
                        <span class="text-gray-500 text-right w-12">6</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Calibration Check Readings</span>
                        <span class="text-gray-500 text-right w-12">7</span>
                    </li>
                    <li class="flex items-center border-t pt-4">
                        <h2 class="flex-1">Appendices</h2>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Appendix A - Floor Plan</span>
                    </li>
                    <li class="flex items-center">
                        <span class="text-lg flex-1 text-left">Appendix B - Lead-Based Paint Evaluation Report</span>
                    </li>
                </ul>
            </div>
        </div>
    
        <div class="mt-1">
            <div class="page-break">
                <h2>Contact Information</h2>
        
                <div class="mb-2">
                    <span class="text-lg font-bold">Site</span>
                    <table class="text-left">
                        <tbody>
                            <tr>
                                <td class="border-r">Street Address</td>
                                <td>{{ $record->address }}, @if(isset($record->designation)) Unit: {{ $record->designation }}, @endif 
                                    {{ $record->municipality }}, NJ {{ $record->city }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        
                <div class="mb-2">
                    <span class="text-lg font-bold">Property Owner</span>
                    <table class="text-left">
                        <tbody>
                            <tr class="border-b">
                                <td class="border-r">Name</td>
                                <td>{{ $record->property_owner_name }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="border-r">Address</td>
                                <td>{{ $record->address }}, @if(isset($record->designation)) Unit: {{ $record->designation }}, @endif 
                                    {{ $record->municipality }}, NJ {{ $record->city }}</td>
                            </tr>
                            <tr>
                                <td class="border-r">Phone Number</td>
                                <td>(555) 123-4567</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        
                <div class="mb-2">
                    <span class="text-lg font-bold">Lead-Based Paint Inspector/Risk Assessor</span>
                    <table class="text-left">
                        <tbody>
                            <tr class="border-b">
                                <td class="border-r">Name</td>
                                <td>{{ $record->inspector_name }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="border-r">Permit Number</td>
                                <td>{{ $record->license_number }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="border-r">Instrumentation</td>
                                <td>Viken Pb200e</td>
                            </tr>
                            <tr class="border-b">
                                <td class="border-r">Signature</td>
                                <td>
                                    <img src="{{ storage_path('app/private/signature/' . $record->signature) }}" alt="Inspector Signature" 
                                        style="max-height: 40px; width: auto; height: 40px; object-fit: contain;" />

                                </td>
                            </tr>
                            <tr>
                                <td class="border-r">Date</td>
                                <td>{{ \Carbon\Carbon::parse($record->selectedDate)->format('M d, Y') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        
                <div class="mb-2">
                    <span class="text-lg font-bold">Firm</span>
                    <table class="text-left">
                        <tbody>
                            <tr class="border-b">
                                <td class="border-r">Organization</td>
                                <td>{{ $settings->app_name }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="border-r">Certification #</td>
                                <td>NJDCA {{ $settings->company_njdca }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="border-r">Street</td>
                                <td>{{ $settings->company_address }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="border-r">City, State & Zip</td>
                                <td>{{ $settings->company_city }}, {{ $settings->company_state }} {{ $settings->company_zip }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="border-r">Phone Number</td>
                                <td>{{ $settings->company_phone }}</td>
                            </tr>
                            <tr>
                                <td class="border-r">Email Address</td>
                                <td>{{ $settings->company_email }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        

        <div class="text-right page-break">
            <div class="logo-container">
                <img src="{{ public_path('images/logo.png') }}" alt="Company Logo" style="width: 200px; height: auto;">
                <div class="company-info"><p>
                    {{ $settings->company_address }}<br>
                    {{ $settings->company_city }}, {{ $settings->company_state }} {{ $settings->company_zip }}<br>
                    Email: {{ $settings->company_email }}<br>
                    Phone: {{ $settings->company_phone }}</p>
                </div>
                <p style="margin-top: 0.5pt; margin-bottom: 0;">
                <p>Date: {{ \Carbon\Carbon::now()->format('M d, Y') }}</p>
            </div>
        </div>
    
        <div class="mb-2 mt-8 text-center w-full container">
            <span class="font-bold text-2xl">Executive Summary</span><br>
            <span class="text-2sm">{{ $record->address }}, @if(isset($record->designation)) Unit: {{ $record->designation }}, @endif 
                {{ $record->municipality }}, NJ {{ $record->city }}</span><br>
            <span class="font-bold text-2sm mb-4">{{ \Carbon\Carbon::parse($record->selectedDate)->format('M d, Y') }}</span>

            <p class="text-left text-2sm leading-relaxed mb-4">
                On {{ \Carbon\Carbon::parse($record->selectedDate)->format('M d, Y') }}, {{ $settings->app_name }} [NJDCA {{ $settings->company_njdca }}] 
                condition assessment at {{ $record->address }}, @if(isset($record->designation)) Unit: {{ $record->designation }}, @endif 
                {{ $record->municipality }}, NJ {{ $record->city }}; (the “property”) in compliance with NJAC 5:28A. The lead-based paint inspection sampling protocol that was applied follows “Inspections in Single-Family Housing” Chapter 7 of the HUD Guidelines (2012 revision) and the protocol as referenced in USEPA 40 CFR Part 745.227(b). See Appendix B Lead Paint Inspection Report for the complete set of X-Ray Fluorescence data.
            </p>
            <p class="text-left text-2sm leading-relaxed mb-4">
                The tables below indicate the location of the lead-based paint found. Each positive reading applies to all similar components in the same room equivalent (room, hall, stairwell, building exterior, etc.) For a lead-based paint free certification, the lead must be stripped or the leaded component replaced and confirmation achieved. Enclosure and encapsulation are not acceptable methods for a lead-based paint free certification. If no lead-based paint was identified, the table will list “None” and the dwelling unit is considered lead-based paint free.
            </p>
            <div class="mb-8 mt-8 text-center w-full">
                @if($record->xrf_pass_fail === 'fail')
                    @if(!empty($record->xrf_data) && is_array($record->xrf_data))
                        <table class="min-w-full border-collapse text-xs">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="text-black text-center border border-black">Room Equivalent</th>
                                    <th class="text-black text-center border border-black">Component</th>
                                    <th class="text-black text-center border border-black">Substrate</th>
                                    <th class="text-black text-center border border-black">Wall</th>
                                    <th class="text-black text-center border border-black">Value (mg/cm²)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($record->xrf_data as $item)
                                    <tr>
                                        <td class="text-black text-center border border-black">{{ $item['Room'] ?? 'N/A' }}</td>
                                        <td class="text-black text-center border border-black">{{ $item['Member'] ?? 'N/A' }}</td>
                                        <td class="text-black text-center border border-black">{{ $item['Substrate'] ?? 'N/A' }}</td>
                                        <td class="text-black text-center border border-black">{{ $item['Wall'] ?? 'N/A' }}</td>
                                        <td class="text-black text-center border border-black">{{ $item['Concentration'] ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No Component above 1.0 mg/cm².</p>
                    @endif
                @endif
            </div>
            
            <p class="text-left text-2sm leading-relaxed mb-20">
                EPA 40 CFR 745.227(h) states lead-based paint is present on any surface that is tested and found to contain lead equal to or in excess of 1.0 milligrams per square centimeter or equal to or in excess of 0.5% by weight. Local thresholds may be lower than this Federal standard.
            </p>
        </div>
    
        <div class="page-break">
            <h2>Regulatory Requirements</h2>
            <span class="text-sm font-bold italic">Required Disclosure</span>
            <p class="text-left text-2sm leading-relaxed mb-4">
                A summary of this lead-based paint evaluation report must be provided to new lessees (tenants). A complete copy of this report must be provided to purchasers and owners of this property and it must be made available to new tenants under federal law (24 CFR PART 35 AND 40 CFR PART 745) before they become obligated under a lease or sales contract. Landlords (lessors) and sellers are also required to distribute an educational pamphlet approved by the U.S. Environmental Protection Agency and include standard warning language in their leases or sales contracts to ensure that parents have the information they need to protect their children from lead-based paint hazards.
            </p>

            <p class="text-left text-2sm leading-relaxed mb-4">
                Should the recipient of this report receive federal subsidy they are responsible to comply with all
                requirements of 24 CFR Part 35 Requirements for the Notification, Evaluation and Reduction of
                Lead-Based Paint Hazards in Federally Owned Residential Property and Housing Receiving Federal
                Assistance; Final Rule which, are applicable to the type of program they are participating in and the
                dollar amount of subsidy being received. If this property or any of its tenants receives financial
                federal assistance, the results of the evaluation or hazard reduction activities must be provided by
                the designated party (client) to the owner of the referenced property and the occupants within 15
                calendar days of the date when the designated party receives this report, or makes the presumption
                that lead-based paint hazards do exist.  
            </p>

            <span class="text-sm font-bold italic">Required Training for Workers</span>

            <p class="text-left text-2sm leading-relaxed mb-4">
                Should the lead-based paint and lead hazard reduction activities be part of a program which receives
                federal subsidy or a New Jersey multifamily building, all persons performing “Interim Controls” or
                “Standard Treatments” must be trained in accordance with 29 CFR 1926.59 and be supervised by an
                individual who successfully completed one of the following courses:
            </p>

            <div class="max-w-3xl mx-auto bg-white rounded-lg mb-4">
                <ol class="list-decimal list-inside space-y-2 text-gray-700">
                    <li>A lead-based paint abatement supervisors course accredited in accordance with 40 CFR 745.225</li>
                    <li>A lead-based paint abatement worker course accredited in accordance with 40 CFR 745.225</li>
                    <li>The lead-based paint Maintenance Training Program, “Work Smart, Work Wet, and Work Clean to Work Lead Safe”, prepared by the National Environmental Training Association for EPA and HUD</li>
                    <li>“The Remodeler’s and Renovator’s Lead-Based Paint Training Program,” prepared by HUD and the National Association of the Remodeling Industry</li>
                    <li>Another course approved by HUD for this purpose after consultation with EPA.</li>
                </ol>
            </div>

            <p class="text-left text-2sm leading-relaxed mb-4">
                In accordance with Section 35.1340 all Lead-Based Paint and Lead Hazard reduction activities,
                which are not exempt (see regulations) require Lead Dust Wipe Clearance testing by a 1) certified
                lead inspector, 2) certified risk assessor or 3) a dust wipe sampling technician whose work is
                reviewed by a certified risk assessor.
                <br /><br />
                If a renovation at the property is to occur, all work should comply with 40 CFR 745 Subpart EResidential Property Renovation.
            </p>

            <span class="text-lg font-bold">Controlling Lead-Based Paint</span>
            
            <p class="text-left text-2sm leading-relaxed mb-4">
                There are different options available for controlling lead-based paint. Each option has its own
                associated costs and benefits both short and long term. In most cases, a combination of the options
                can be implemented to reduce the possibility of lead contamination. {{ $settings->app_name }}. 
                strongly suggests that each option is thoroughly contemplated before beginning any activity.
                <br /><br />
                Components that are found to be positive for lead-based paint should be checked for deterioration.
                Lead-based paint in deteriorated condition is considered a paint-lead hazard. Those components
                should be address as soon as possible using lead safe work practices at a minimum. However, if any
                components are found to test positive for lead based paint, they should be considered for future
                component removal or paint stripping.
            </p>

            <span class="text-3sm font-bold italic">Abatement for Lead-Based Paint Free Certification</span>
            <br /><br />

            <span class="text-sm font-bold">Component Removal</span>
            
            <p class="text-left text-2sm leading-relaxed mb-4">
                Component removal is a permanent solution to the issue of potential exposure of lead. It requires
                taking the old lead-based painted component out and replacing it with a new non-lead painted
                component. The cost associated with this option depends mostly on the cost of the replacement
                component. Since labor is most often the more costly aspect of controlling lead issues, many owners
                choose component removal over more labor intensive methods. Components often chosen for
                removal are wood trim, windows, most doors, and exterior railings. Plaster and drywall ceilings and
                walls, fire rated doors, and wood porch components should also be considered.
            </p>

            <span class="text-sm font-bold">Paint Stripping</span>
            <p class="text-left text-2sm leading-relaxed mb-4">
                Paint stripping is a permanent solution to the issue of potential exposure of lead. The paint can be
                removed either in-place or by an off-site processing facility. In-place removal can be mechanical or
                chemical. In-place paint stripping has the issue of proper disposal of the hazardous waste
                generated.
                <br /><br />
                Mechanical stripping scrapes the paint off the substrate. Most times dry scraping is prohibited, but
                sanding or scraping can be done in conjunction with engineering controls to reduce airborne and
                settled lead dust. Power tools used to remove the paint must be equipped with a HEPA filtered
                shroud. Wetting a surface and hand scraping is also permitted. The components most often chosen
                for hand scraping are window and door jambs. Power tools are better equipped to handle lager
                surface areas.
                <br /><br />
                Chemical stripping in-place uses strong chemicals to soften the paint for easier removal from the
                substrate. The chemicals are either very acidic or very basic, so proper training and protection for
                the worker is imperative. Generally, the chemicals must remain in- place overnight, so maintaining a
                secure worksite separate from occupants is mandatory.
                <br /><br />
                Off site facilities use much stronger chemicals to remove the lead-based paint from the component.
                Components often chosen for off-site paint removal are intricate metal pieces. Sometimes this
                method is used for intricate wood work, but the stronger chemicals soften the wood and can drive
                lead into the wood while removing the paint.
            </p>

            <span class="text-3sm font-bold italic">Procedures & Methodology</span>
            <br /><br />

            <span class="text-sm font-bold">Location Conventions</span>
            
                <p class="text-left text-2sm leading-relaxed mb-4">
                    When reviewing Appendix A “Floor Plan” and Appendix B “Lead-Based Paint Evaluation Report”, you
                will notice that the letters A, B, C, and D or the numbers 1, 2, 3 and 4 are used to identify the
                location of specific components. The key to correct orientation is the location of the “A” or “1” wall.
                The “B” or “2” wall, “C” or “3” wall, and “D” or “4” wall run clockwise from the “A” or “1” wall. The
                Lead-Based Paint Evaluation Report lists this information under the “Wall” column. The “Location”
                column uses numbering of replicated components starting with “1” at left and continuing
                sequentially to right respectively to describe the location of the component while facing the wall
                identified.
                </p>
                <p class="text-sm font-bold mb-4">Paint Testing</p>
                <p class="text-sm font-bold mb-4">X-Ray Fluorescence</p>

                <p class="text-left text-2sm leading-relaxed mb-4">
                X-Ray Fluorescence (XRF) paint testing is performed to detect the presence of lead on painted
                surfaces. The XRF instrument is state-of-the art equipment. XRF testing is usually the preferred
                method of testing, because it is non-destructive, quantitative and can be performed on the spot with
                acceptable accuracy. {{ $settings->app_name }}’s evaluators follow the manufacturer’s
                suggested use and the Performance Characteristic Sheet of the XRF instrument being used. The
                results of the XRF testing are the basis for drawing conclusions and making recommendations in the
                report.
                <br /><br />
                All {{ $settings->app_name }}’s evaluators follow 40 CFR 745 and the HUD Guidelines for
                testing lead using an XRF instrument. All federal, state and city regulations are followed when
                applicable. The evaluator will test one of each and every different type of testing combination
                (component) in each room being surveyed. Each XRF reading is assigned an exclusive sample
                reference number and a measurement that is stored in the instrument. Each sample reference
                number location is logged on the XRF instrument for future reference, testing location, and report
                generation. The above described testing format is followed unless otherwise not practical or if the
                evaluator’s judgment decides to test in a different systematic approach.
                <br/><br/>
                It should be noted that detected lead levels below current levels still could create lead dust or lead
                contaminated soil hazards if the paint is turned into dust by abrasion, scraping, or sanding leading
                to possible elevated blood lead levels. Lead poisoning is a cumulative affect. Should a child or an
                adult inhale or ingest sufficient quantities of low concentrations of leaded paint, dust, or soil, it will
                accumulate in the body’s systems and could eventually cumulate to an elevated blood level of
                concern.
            </p>

                <p class="text-sm font-bold mb-4">Any untested building components should be considered lead-based paint until tested.</p>
                <p class="text-sm font-bold mb-4">Calibration Check Readings</p>

                <p class="text-left text-2sm leading-relaxed mb-4">
                In addition to the manufacturer’s recommended warm up and quality control procedures, {{ $settings->app_name }}. 
                collects quality control readings as recommended in the HUD
                Guidelines. Quality control for XRF instrumentation instruments involves readings to check
                calibration.
                <br /><br />
                For each XRF instrument, one set of XRF calibration check readings are recommended at least every
                four hours. The first is a set of three nominal-time or source decay corrected time XRF calibration
                check readings to be taken before the inspection begins for the day. The second occurs either after
                the day’s inspection work has been completed, or at least every four hours, whichever occurs first.
                {{ $settings->app_name }}’s XRF calibration check readings are taken on the Standard
                Reference Material (SRM) paint film nearest to 1.0 mg/cm<sup>2</sup>
                within the National Institute of
                Standards and Technology (NIST) SRM Used or the XRF manufacturer’s factory supplied SRM film.
                Three readings are collected on the SRM. The average of the three readings on the SRM must be
                within the acceptable plus and minus tolerances for proper calibration as detailed in the
                Performance Characteristic Sheet (PCS). All calibration checks are taken with the SRM film
                positioned at least several inches away from any potential source of lead.
                <br/><br/>
                Three readings are taken each time calibration check readings are made. The average of the
                readings are compared to the known value and if the average value is within the acceptable
                calibration check tolerance specified in the XRF Performance Characteristic Sheet the instrument is
                considered in control. If the average readings are not within the calibration check tolerance the
                instrument is not used until the instrument is brought back into control.
            </p>
            <!-- Add remaining sections as needed -->
        </div>

   </div>
</body>
</html>
