<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dust Wipe Report</title>
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
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .signature img {
            max-height: 20px;
            object-fit: contain;
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

        /* Remove spacing between Lead Dust Wipe Report and address */
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
                    {{-- {{ env('APP_NAME') }}<br> --}}
                    {{ env('COMPANY_ADDRESS') }}<br>
                    {{ env('COMPANY_CITY') }}, {{ env('COMPANY_STATE') }} {{ env('COMPANY_ZIP') }}<br>
                    Email: {{ env('COMPANY_EMAIL') }}<br>
                    Phone: {{ env('COMPANY_PHONE') }}</p>
                </div>
                <p style="margin-top: 0.5pt; margin-bottom: 0;">
                <p>Date: {{ \Carbon\Carbon::now()->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="text-center mb-4 report-header">
            <p style="font-weight: bold; font-size: 24px; margin-bottom: -1px;">Lead Dust Wipe Report</p>
            <p>
                {{ $record->address }},
                @if(isset($record->designation)) Unit: {{ $record->designation }}, @endif
                {{ $record->municipality }}, NJ {{ $record->city }}
            </p>
            <p>
                {{ \Carbon\Carbon::parse($record->selectedDate)->format('M d, Y') }}
            </p>
        </div>

        <!-- Apply the 'report-content' class to apply 'Times New Roman' font and size 10 -->
        <div class="report-content">
            <p class="text-left leading-relaxed mb-2">
                On {{ \Carbon\Carbon::parse($record->selectedDate)->format('M d, Y') }}, {{ env('APP_NAME') }} [NJDCA {{ env('COMPANY_NJDCA') }}] 
                performed a dust wipe sampling for the presence of lead at {{ $record->address }}, @if(isset($record->designation)) 
                Unit: {{ $record->designation }}, @endif {{ $record->municipality }}, NJ {{ $record->city }}; (the “property”) in 
                compliance with NJAC 5:28A. Only the dwelling and common area interiors were included. Exteriors were not part of 
                the assessment. We conducted our inspection in accordance with all federal, state, and municipal regulations. The 
                regulations and inspection standards require that we plan and perform the inspection to obtain a reasonable assurance 
                about whether or not the property is at risk from lead dust.
            </p>
            <p class="text-left leading-relaxed mb-4">
                The maintenance and the environmental conditions of the property are the responsibility of the
                property’s management, principals, or owners. If the property, program, or any of its tenants receive
                financial federal assistance, the results of this evaluation must be provided by the designated party
                (client) to the owner of the referenced property and the occupants within 15 calendar days of the
                date when the designated party receives this report or makes the presumption that lead-based paint
                hazards exist, per the Department of Housing and Urban Development 24 CFR Part 35.125.
            </p>

            <!-- Pass condition -->
            @if($record->pass_fail === 'pass')
                <p class="text-left">All dust wipes tested are not considered actionable for lead dust. The property may still contain lead painted components, and lead dust re-accumulation is possible.</p>
            @endif

            <!-- Fail condition -->
            @if($record->pass_fail === 'fail')
                <p class="text-left">The following surfaces are considered actionable for the lead dust.</p>

                <div class="mb-8 mt-8 text-center w-full">
                    <table class="min-w-full border-collapse">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="text-black border">Room</th>
                                <th class="text-black border">Component</th>
                                <th class="text-black border">Wall</th>
                                <th class="text-black border">Value (mg/cm²)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($record->photos as $eData)
                                <tr>
                                    <td class="border">{{ $eData['room'] }}</td>
                                    <td class="border">{{ $eData['component'] }}</td>
                                    <td class="border">{{ $eData['description'] }}</td>
                                    <td class="border">{{ $eData['value'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="text-left leading-relaxed">
                    Identified dust hazards shall be remediated by lead dust cleaning. Cleaning shall be performed by
                    EPA RRP certified firms using an EPA certified RRP Renovator. You can find certified EPA RRP
                    contractors at 
                    <a href="https://cdxapps.epa.gov/ocspp-oppt-lead/firm-location-search" target="_blank" class="text-blue-500 underline">
                        https://cdxapps.epa.gov/ocspp-oppt-lead/firm-location-search
                    </a>. Select RRP Contractor, then NJ, then enter your zip code to search for contractors. After completion of interim control work,
                    a lead-based paint post-remediation with dust wipes will be required. The post-remediation dust
                    wipe inspection will need to be scheduled and paid for separately from this initial inspection.
                </p>
            @endif
        </div>
        
        <p class="leading-relaxed text-center mb-5">
            <span class="text-xs underline"><strong>Lead Dust Hazard Standards</strong><br>
            Floors (Bare and Carpeted) &lt; 10 ug/ft<sup>2</sup><br>
            Window Sill &lt; 100 ug/ft<sup>2</sup></span>
        </p>
        
        <p class="text-left pt-4 pb-20">
            Respectfully Submitted,<br /><br />
            <img src="{{ storage_path('app/private/signature/' . $record->signature) }}" alt="Inspector Signature" 
            style="width: auto; height: auto; max-height: 3rem; object-fit: contain;">            <br />
            {{ $record->inspector_name }}<br>
            Lead Inspector/Risk Assessor,<br>
            Permit No.: {{ $record->license_number }}<br>
            Encl: Floor Plan,
            @if($record->pass_fail === 'pass') Laboratory Results, Lead-safe Certificate; @else Laboratory Results; @endif
        </p>
   </div>
</body>
</html>