<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Visual inspection Report</title>
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
            <p style="font-weight: bold; font-size: 24px; margin-bottom: -1px;">Visual Paint Assessment Report</p>
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
            On {{ \Carbon\Carbon::parse($record->selectedDate)->format('M d, Y') }}, {{ $settings->app_name }} [NJDCA {{ $settings->company_njdca }}] 
                condition assessment at {{ $record->address }}, @if(isset($record->designation)) Unit: {{ $record->designation }}, @endif 
                {{ $record->municipality }}, NJ {{ $record->city }}; (the “property”) in compliance with NJAC 5:28A.  
                Only the dwelling and common area interiors were included. Exteriors were not part of 
                The assessment focused on identifying deteriorated paint, paint chips, dust from painting activities, and paint residue on floors. Only the dwelling unit and interior common areas were inspected; exterior surfaces were not included in the evaluation.
            </p>
            {{-- <p class="text-left leading-relaxed mb-4">
                The maintenance and the environmental conditions of the property are the responsibility of the
                property’s management, principals, or owners. If the property, program, or any of its tenants receive
                financial federal assistance, the results of this evaluation must be provided by the designated party
                (client) to the owner of the referenced property and the occupants within 15 calendar days of the
                date when the designated party receives this report or makes the presumption that lead-based paint
                hazards exist, per the Department of Housing and Urban Development 24 CFR Part 35.125.
            </p> --}}

            <!-- Pass condition -->
            @if($record->pass_fail === 'pass')
                <p class="text-left">
                    The visual assessment followed the protocols outlined in the United States Department of Housing and Urban Development’s Visual Assessment Training course. Based on these findings, the property qualifies for a Lead-Safe Certificate under NJAC 5:28A. A copy of the NJ Lead-Safe Certificate is attached.
                </p>
                <p class="text-left">
                    Additionally, on 09/12/2024, Governor Murphy signed Bill S-3368, extending the validity of the Lead-Safe Certificate to three years.
                </p>
            @endif

            <!-- Fail condition -->
            @if($record->pass_fail === 'fail')
                <p class="text-left">
                    Deteriorated paint, chips or dust from painting activities and paint residue on floors were observed
                    on the following components/surfaces.
                </p>

                <div class="mb-8 mt-8 text-center w-full">
                    <table class="min-w-full border-collapse">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="text-black border">Room</th>
                                <th class="text-black border">Component</th>
                                <th class="text-black border">Wall</th>
                                <th class="text-black border">Hazard</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($record->photos as $eData)
                                <tr>
                                    <td class="border">{{ $eData['room'] }}</td>
                                    <td class="border">{{ $eData['component'] }}</td>
                                    <td class="border">{{ $eData['description'] }}</td>
                                    <td class="border">{{ $eData['hazard'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="text-left leading-relaxed">
                    Identified hazards shall be remediated using either abatement (permanent) or interim controls
                    (temporary). Abatement is permanent but has a higher cost, as Interim controls are temporary but
                    can be much more affordable. Abatement shall be performed by a NJ Licensed lead abatement firm
                    using licensed lead abatement supervisors and workers. You can find the list of NJ licensed lead
                    abatement contractors at 
                    <a href="https://cdxapps.epa.gov/ocspp-oppt-lead/firm-location-search" target="_blank" class="text-blue-500 underline">
                        https://cdxapps.epa.gov/ocspp-oppt-lead/firm-location-search
                    </a>. Select RRP Contractor, then NJ, then enter your zip code to search for contractors. After completion of interim control work,
                    a lead-based paint post-remediation with dust wipes will be required. The post-remediation dust
                    wipe inspection will need to be scheduled and paid for separately from this initial inspection.
                </p>
            @endif
        </div>
        
        <p class="leading-relaxed text-center mb-5">
            <span class="text-xs underline">
                <strong>
                    No furniture, artwork or other large objects were moved during this visual inspection. If those
                    objects are moved during after this inspection occurred and deteriorated paint is observed, it should
                    also be included in the repair scope of work. If those areas are present during the reinspection, it
                    would result in a failure.
                </strong>
            </span>
        </p>
        
        <p class="text-left pt-4 pb-20">
            Respectfully Submitted,<br /><br />
            <img src="{{ storage_path('app/private/signature/' . $record->signature) }}" alt="Inspector Signature" 
            style="width: auto; height: auto; max-height: 3rem; object-fit: contain;">            <br />
            {{ $record->inspector_name }}<br>
            Lead Inspector/Risk Assessor,<br>
            Permit No.: {{ $record->license_number }}<br>
            Encl: Floor Plan,
            @if($record->pass_fail === 'pass') Lead-safe Certificate; @else Photos; @endif
        </p>

       <!-- Photos Section -->
        @if($record->pass_fail === 'fail')
        <div class="grid grid-cols-2 gap-4 mb-20">
            @forelse($record->photos as $photo)
                <div class="flex flex-col items-center justify-center">
                    @if($photo->photo_path)
                        <img src="{{ Storage::url($photo->photo_path) }}" 
                            alt="Inspection Photo" 
                            class="w-full h-72 object-cover" 
                            loading="lazy" 
                            onerror="this.onerror=null; this.src='/images/no-image-available.png';" />
                    @else
                        <p>No photo available</p>
                    @endif

                    <!-- Description below the image -->
                    <p class="mt-2 text-left text-sm text-gray-900">
                        {{ $photo->description }}: {{ $photo->room }} | {{ $photo->component }} | {{ $photo->hazard }}
                    </p>
                    <hr class="border-t-1 border-black w-full mb-2 mt-2">
                </div>
            @empty
                <p class="text-center">No photos available for this inspection.</p>
            @endforelse
        </div>
        @endif

   </div>
</body>
</html>