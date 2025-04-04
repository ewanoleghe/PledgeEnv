<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lead Safe Certificate</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        body { font-family: Arial, sans-serif; }
        .certificate { 
            border: 4px solid black; 
            padding: 2px 20px; /* Increased left and right padding to 30px, reduced top and bottom to 10px */
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-blue-900 { color: #1E3A8A; }
        .text-red-600 { color: #DC2626; }
        .underline { text-decoration: underline; }
        .font-bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 10px; }
        .border-b { border-bottom: 2px solid black; }
    </style>
    
</head>
<body>
    <div class="certificate">
        <table class="w-full border-4 border-black">
            <tbody>
                <tr>
                    <td class="p-2">
                        <div style="text-align: right; margin-top: 1px;">
                            <span style="font-size: 18px; font-weight: 600;">{{ $record->certificate_number }}</span>
                        </div>
                        <div class="text-center mb-2" style="display: flex; flex-direction: column; align-items: center; margin-top: 0px;">
                            <img src="{{ public_path('images/stateLogo.png') }}" alt="NJ State Logo" style="width: 100px; height: 100px; margin-top: 0px;"> <!-- Adjusted margin-top to 0px -->
                            <h1 style="font-size: 20px; font-weight: bold; color: #160479; text-decoration: underline; margin-top: 8px;">LEAD - SAFE CERTIFICATE</h1>
                        </div>
                        <div>
                            <p style="font-size: 12px; margin-top: 1px;">
                                It is hereby certified that a lead-based paint visual inspection and/or dust wipe sampling has been performed in accordance with the protocols referenced in N.J.A.C. 5:17, and the results of which indicate that no lead-based paint hazards have been found in the dwelling unit listed below. It shall be the owner’s responsibility to perform any required ongoing evaluation and maintenance to ensure that the dwelling unit remains in a Lead-Safe condition. <strong>PURSUANT TO P.L.2003, c.311 (C.52:27D-437.1 et. seq.)</strong>
                            </p>
                        
                            <div class="text-center">
                                <p style="font-size: 12px; white-space: nowrap;">
                                    This certificate is 
                                    <span style="text-decoration: underline; font-weight: bold; color: #160479; margin-top: 0px;">VALID FOR THREE YEARS</span>
                                </p>
                            </div>
                        </div>
                        <div>
                            <table style="width: 100%; margin-top: 11px; border-collapse: collapse; border-spacing: 0;">
                                <thead>
                                    <tr>
                                        <th style="text-align: left; font-size: 14px; padding: 1px; font-weight: normal;">
                                            {{ $record->address }} <span>{{ isset($record->designation) ? 'Unit: ' . $record->designation : '' }}, </span> {{ $record->municipality }}, NJ {{ $record->city }}
                                        </th>
                                        <th style="text-align: left; font-size: 14px; padding: 1px; font-weight: normal;">{{ $record->block }}</th>
                                        <th style="text-align: left; font-size: 14px; padding: 1px; font-weight: normal;">{{ $record->lot }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="border-top: 1px solid #000; border-bottom: none; padding: 0;"></td> 
                                        <td style="border-top: 1px solid #000; border-bottom: none; padding: 0;"></td> 
                                        <td style="border-top: 1px solid #000; border-bottom: none; padding: 0;"></td> 
                                    </tr>
                                    <tr>
                                        <td style="font-size: 12px; padding: 0; margin: 0; line-height: 1;">Address</td>
                                        <td style="font-size: 12px; padding: 0; margin: 0; line-height: 1;">Block</td>
                                        <td style="font-size: 12px; padding: 0; margin: 0; line-height: 1;">Lot</td>
                                    </tr>
                                </tbody>
                            </table>
                            <p style="font-size: 14px; text-align: center; margin-bottom: 8px;">
                                Applicable Dwelling Unit: <span style="font-weight: bold; text-decoration: underline;">{{ isset($record->designation) ? 'Unit: ' . $record->designation : '' }}</span>
                            </p>
                            <p style="font-size: 14px; font-weight: bold; text-align: center; margin-bottom: 8px;">
                                (CERTIFICATE IS VALID FOR A DWELLING UNIT AND SHALL BE AFFIXED TO LEASE)
                            </p>
                        </div>
                        <div>
                            <table style="width: 100%; margin-top: 11px; border-collapse: collapse; border-spacing: 0;">
                                <thead>
                                    <tr>
                                        <th style="text-align: left; font-size: 14px; padding: 1px; font-weight: normal;">
                                            <span style="color: #d81010;">Insp/RA Name:</span> {{ $settings->company_owner }}
                                        </th>
                                        <th style="text-align: left; font-size: 14px; padding: 1px; font-weight: normal;">
                                            <span style="color: #d81010;">Evaluation Contractor #:</span> {{ $settings->company_njdca }}
                                        </th>
                                        <th style="text-align: left; font-size: 14px; padding: 1px; font-weight: normal;">
                                            <span style="color: #d81010;">Phone:</span> {{ $settings->company_phone }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <td style="border-top: 1px solid #000; border-bottom: none; padding: 0;"></td> 
                                        <td style="border-top: 1px solid #000; border-bottom: none; padding: 0;"></td> 
                                        <td style="border-top: 1px solid #000; border-bottom: none; padding: 0;"></td> 
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-top: 8px;">
                            <table style="width: 100%; border-collapse: collapse; border-spacing: 0; margin-top: 8px;">
                                <tr>
                                    <!-- Left Column: Signature Table -->
                                    <td style="width: 40%; padding: 5px; vertical-align: top;">
                                        <table style="width: 100%; border-collapse: collapse;">
                                            <thead>
                                                <tr>
                                                    <th style="width: 25%; text-align: left; font-size: 12px; padding: 1px; font-weight: normal;">
                                                        <img src="{{ public_path('images/companyOwner_sign.jpg') }}" alt="NJ State Logo" style="width: 120px; height: 60px; margin-top: 0px;">
                                                    </th>
                                                    <th style="width: 5%;"></th> <!-- Blank Middle Column -->
                                                    <th style="width: 25%; text-align: left; font-size: 14px; padding: 1px; font-weight: normal;">
                                                    {{ $settings->company_njdca }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="width: 25%; border-top: 1px solid #000; padding: 0; height: 15px; vertical-align: top;">
                                                        <span style="font-size: 12px">Signature</span>

                                                    </td>
                                                    <td style="width: 5%;"></td> <!-- Blank Middle Column -->
                                                    <td style="width: 25%; border-top: 1px solid #000; padding: 0; height: 15px; vertical-align: top;">
                                                        <span style="font-size: 12px">NJDOH ID #</span>
                                                    </td> 
                                                </tr>
                                            </tbody>
                                        </table>
                                        
                                    </td>

                                    <td style="width: 10%; padding: 5px; vertical-align: top;">
                                        <table style="width: 20%; border-collapse: collapse; margin-bottom: 8px;">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50%; text-align: left; font-size: 12px; padding: 1px; font-weight: normal;">
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="width: 50%; text-align: left; font-size: 12px; padding: 1px; font-weight: normal;">
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="width: 50%; text-align: left; font-size: 12px; padding: 1px; font-weight: normal;">
                                                    </th>
                                                </tr>
                                            </thead>
                                        </table>
                            
                                    <!-- Right Column: Contractor Info and Date Issued -->
                                    <td style="width: 50%; padding: 5px; vertical-align: top;">
                                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2px;">
                                            <thead>
                                                <tr>
                                                    <th style="width: 40%; text-align: left; font-size: 12px; padding: 6px 0; font-weight: normal;">
                                                        Contractor Name:
                                                    </th>
                                                    <th style="width: 60%; text-align: left; font-size: 14px; padding: 6px 0; font-weight: bold; border-bottom: 0.5px solid #000;">
                                                    {{ $settings->app_name }}
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="width: 40%; text-align: left; font-size: 12px; padding: 6px 0; font-weight: normal;">
                                                        Contractor Address:
                                                    </th>
                                                    <th style="width: 60%; text-align: left; font-size: 14px; padding: 6px 0; font-weight: bold; border-bottom: 0.5px solid #000;">
                                                    {{ $settings->company_address }}
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="width: 40%; text-align: left; font-size: 12px; padding: 6px 0; font-weight: normal;">
                                                    </th>
                                                    <th style="width: 60%; text-align: left; font-size: 14px; padding: 6px 0; font-weight: bold; border-bottom: 0.5px solid #000;">
                                                    {{ $settings->company_city }}, {{ $settings->company_state }} {{ $settings->company_zip }}
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="width: 40%; text-align: left; font-size: 12px; padding: 6px 0; font-weight: normal;">
                                                        Date Issued:
                                                    </th>
                                                    <th style="width: 60%; text-align: left; font-size: 12px; padding: 6px 0; font-weight: bold; border-bottom: 0.5px solid #000;">
                                                        {{ $record->created_at->format('m/d/Y') }} 
                                                    </th>
                                                </tr>
                                            </thead>
                                        </table>
                                        
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div>
                            <table style="width: 60%; margin-top: 8px; margin-bottom: 8px; border-spacing: 0;">
                                <thead>
                                    <tr>
                                        <th style="text-align: left; font-size: 11px; border-bottom: 0.1px solid black; padding-bottom: 0;">
                                            <img src="{{ $signatureUrl }}" alt="Inspector Signature" style="width: auto; height: auto; max-height: 50px; object-fit: contain;"/>
                                        </th>
                                        <th style="text-align: left; font-size: 14px; border-bottom: 0.1px solid black; padding-bottom: 0; vertical-align: top;">
                                            <br><br>{{ $record->inspector_name }}
                                        </th>
                                        <th style="text-align: left; font-size: 14px; border-bottom: 0.1px solid black; padding-bottom: 0; vertical-align: top;">
                                            <br><br>{{ $record->license_number }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="font-size: 12px;">
                                        <td style="vertical-align: top;">Technician Signature</td>
                                        <td style="text-align: left; font-size: 12px; vertical-align: top;">Lead Inspector/Risk Assessor</td>
                                        <td style="text-align: left; font-size: 12px; vertical-align: top;">Permit #</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
