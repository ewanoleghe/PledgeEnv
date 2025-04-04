<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lead Free Certificate</title>
    <style>
        @page { size: A4 landscape; margin: 14mm; }
        body { font-family: Arial, sans-serif; }
        .certificate { 
            border: 4px solid black; 
            padding: 4px 40px;
            width: 244mm;
            height: 172mm;
            position: relative;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        h1 { color: #006DC1; font-family: Cambria, serif; font-size: 22pt; text-align: center; margin-bottom: 4px; }
        h2 { color: #FA0807; font-family: Cambria, serif; font-size: 16.5pt; text-align: center; margin-bottom: 12px; }
        p { color: black; font-family: Cambria, serif; font-size: 11pt; margin: 0; line-height: 1.5; }
        h4 { color: #FA0807; font-family: Cambria, serif; font-size: 9.5pt; margin: 0; }
        
        .content {
            display: flex;
            flex-direction: row;
            gap: 20px;
            flex-grow: 1;
        }
        .main-table {
            width: auto;
            border-collapse: collapse;
            margin-left: 0;
        }
        .main-table td,
        .right-table td {
            font-family: Cambria, serif;
            font-size: 11pt;
            color: black;
            line-height: 1.5;
        }

        .checkbox {
            margin-right: 4px;
        }
        .right-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .right-table td {
            padding: 4px 8px;
            vertical-align: top;
            border-bottom: 0.5px solid #000;
        }
        .signature img {
            width: auto;
            height: auto;
            max-height: 50px;
            object-fit: contain;
        }
        .footer-text {
            position: absolute;
            bottom: 12px; /* Move it up */
            left: 50%; /* Center horizontally */
            transform: translateX(-50%); /* Adjust for centering */
            width: 100%; /* Ensure it stretches across the width */
            text-align: center; /* Center the text */
            padding-bottom: 10px; /* Increase padding from the bottom */
        }


    </style>
</head>
<body>
    <div class="certificate">
        <div class="content">
            <div>
                <h1>Lead - Free Certificate</h1>
                <h2>Lead - Free (Interior Only)</h2>
                <p>
                    It is hereby certified that a lead-based paint inspection has been performed, and the results of this inspection indicate that no lead in the amount greater than or equal to 1.0 mg/cm<span style="font-size: 7pt;">2</span> or greater than 0.5% by weight in paint was found on any building component using the protocols outlined in <b>N.J.A.C. 5:17-3.2(c)</b>. Therefore, the dwelling(s) identified below qualify for the following exemption.
                </p>

                <!-- Checklist -->
                <table class="main-table" style="margin-top: 16px; font-size: 10pt;">
                    <tr>
                        <td>
                            <input type="checkbox" class="checkbox" style="width: 10px; height: 10px; vertical-align: 10px; margin-right: 8px;" />
                            <b>N.J.A.C. 5:10-1.12(h)4</b> (Additional Lead Paint Fee)
                        </td>
                        <td>BHI Registration Number:</td>
                        <td style="border-bottom: 0.5px solid #000;">NA</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" class="checkbox" style="width: 10px; height: 10px; vertical-align: 10px; margin-right: 8px;" />
                            <b>N.J.A.C. 5:10-6.6</b> (Lead-Safe Maintenance)
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" class="checkbox" style="width: 10px; height: 10px; vertical-align: 10px; margin-right: 8px;" />
                            <b>N.J.A.C. 5:27-4.10(a)1 OR N.J.A.C. 5:15-4.2(c)</b> (Rooming & Boarding OR Emergency Shelters)
                        </td>
                        <td style="text-align: right;">Facility ID:</td>
                        <td style="border-bottom: 0.5px solid #000;">NA</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" class="checkbox" checked style="width: 10px; height: 10px; vertical-align: 10px; margin-right: 8px;" />
                            <b>N.J.A.C. 5:28-2.1(a)</b> State Housing Code
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" class="checkbox" style="width: 10px; height: 10px; vertical-align: 10px; margin-right: 8px;" />
                            <b>CHILD OCCUPIED FACILITY (Daycare Centers, Preschools, etc.) PURSUANT TO N.J.A.C. 5:17</b>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                
                <!-- Property Details -->
                <table style="width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 10pt;">
                    <tr>
                        <td style="text-align: left; padding: 4px 8px;">Site Address:</td>
                        <td style="text-align: left; padding: 4px 8px; border-bottom: 0.5px solid #000;">
                            {{ $record->address }} {{ isset($record->designation) ? 'Unit: ' . $record->designation : '' }}, {{ $record->municipality }}, NJ {{ $record->city }}
                        </td>
                        <td style="text-align: left; padding: 4px 8px;">County:</td>
                        <td style="text-align: left; padding: 4px 8px; border-bottom: 0.5px solid #000;">
                            {{ $record->county }}
                        </td>
                        <td style="text-align: left; padding: 4px 8px;">Block:</td>
                        <td style="text-align: left; padding: 4px 8px; border-bottom: 0.5px solid #000;">
                            {{ $record->block }}
                        </td>
                        <td style="text-align: left; padding: 4px 8px;">Lot:</td>
                        <td style="text-align: left; padding: 4px 8px; border-bottom: 0.5px solid #000;">
                            {{ $record->lot }}
                        </td>
                    </tr>
                </table>
                <table style="width: 50%; border-collapse: collapse; margin-top: 12px; font-size: 10pt;">
                    <tr>
                        <td style="text-align: left; padding: 4px 8px; width: 50%;">Applicable Units or Common Areas:</td>
                        <td style="text-align: left; padding: 4px 8px; border-bottom: 0.5px solid #000; width: 50%;"></td>
                    </tr>
                </table>
                
                <!-- Inspector Details -->
                <table style="width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 10pt;">
                    <tr>
                        <td style="text-align: left; padding: 4px 8px; width: 20%;">Name of Inspector/Risk Assessor:</td>
                        <td style="text-align: left; padding: 4px 8px; border-bottom: 0.5px solid #000; width: 23%;">{{ $record->inspector_name }}</td>
                        <td style="width: 2%;"></td> <!-- Empty column for spacing -->
                        <td style="text-align: right; padding: 4px 8px; width: 20%;">NJDOH ID:</td>
                        <td style="text-align: left; padding: 4px 8px; border-bottom: 0.5px solid #000; width: 20%;">{{ $record->license_number }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left; padding: 4px 8px;">Name of Evaluation Contractor:</td>
                        <td style="text-align: left; padding: 4px 8px; border-bottom: 0.5px solid #000;">{{ $settings->app_name }}</td>
                        <td></td> <!-- Empty column for spacing -->
                        <td style="text-align: right; padding: 4px 8px;">NJDCA CERT. #:</td>
                        <td style="text-align: left; padding: 4px 8px; border-bottom: 0.5px solid #000;">{{ $settings->company_njdca }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left; padding: 4px 8px;">Address of Evaluation Contractor:</td>
                        <td style="text-align: left; padding: 4px 8px; border-bottom: 0.5px solid #000;">
                            {{ $settings->company_address }} {{ $settings->company_city }}, {{ $settings->company_state }} {{ $settings->company_zip }}
                        </td>
                        <td></td> <!-- Empty column for spacing -->
                        <td style="text-align: right; padding: 4px 8px;">Phone:</td>
                        <td style="text-align: left; padding: 4px 8px; border-bottom: 0.5px solid #000;">{{ $settings->company_phone }}</td>
                    </tr>
                </table>
                <table style="width: 50%; border-collapse: collapse; margin-top: 12px; font-size: 10pt;">
                    <tr>
                        <!-- Remove padding from the first cell to eliminate spacing -->
                        <td style="text-align: left; padding: 4px 8px; width: 25%;">Date(s) of Inspection:</td>
                    
                        <!-- Apply border only to the first date -->
                        <td style="text-align: left; padding: 2px 0; width: 10%; border-bottom: 0.5px solid #000;">
                            {{ \Carbon\Carbon::parse($record->selected_date)->format('m/d/Y') }}
                        </td>
                    
                        <!-- Center the "TO" text between the two date columns -->
                        <td style="text-align: center; padding: 2px 0; width: 5%; border-bottom: none;">
                            TO
                        </td>
                    
                        <!-- Apply border only to the second date -->
                        <td style="text-align: right; padding: 2px 0; width: 10%; border-bottom: 0.5px solid #000;">
                            {{ \Carbon\Carbon::parse($record->selected_date)->format('m/d/Y') }}
                        </td>
                    </tr>
                    
                </table>
                
                <table style="width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 10pt;">
                    <tr>
                        <td style="text-align: left; padding: 4px 8px; width: 25%;">Date Certificate Issue:</td>
                        <td style="text-align: left; padding: 4px 8px; border-bottom: 0.5px solid #000; width: 10%;">{{ $record->created_at->format('m/d/Y') }}</td>
                        <td style="width: 18%;"></td> <!-- Empty column for spacing -->
                        <td style="text-align: left; padding: 4px 8px; width: 27%;">Signature of Inspector/Risk Assessor:</td>
                        <td style="text-align: left; padding: 4px 8px; border-bottom: 0.5px solid #000; " class="signature">
                            <span style="display: inline-block; margin-left: 12px;">
                                <img src="{{ $signatureUrl }}" alt="Inspector Signature" style="width: 90px; height: auto;" />
                            </span>
                        </td>
                        
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <h4 class="footer-text">
            THIS CERTIFICATE SHOULD BE KEPT BY THE OWNER AND TRANSFERRED TO ALL FUTURE OWNERS FOR LIFE OF STRUCTURE<br>
        </h4>
    </div>
</body>
</html>
