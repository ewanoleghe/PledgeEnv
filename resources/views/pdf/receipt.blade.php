<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #333;
        }
        .centered-text {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start; /* Align items at the start */
            margin-bottom: 20px;
        }

        .header img {
            max-width: 150px; /* Adjust logo size */
            height: auto;
        }

        .logo-container {
            text-align: right; /* Align the logo and company info to the right */
            margin-left: auto; /* Push the logo container to the right */
        }

        /* .status-badge {
            background-color: #aafc3f; /* Yellow background for unpaid status */
            color: #070707; /* Dark text color */
            padding: 5px 10px; /* Padding around the text */
            border-radius: 20px; /* Rounded edges */
            font-size: 20px; /* Font size for the badge */
            font-weight: bold; /* Make the badge text bold */
        } */
        .company-info {
            font-size: 12px; /* Adjust font size for company info */
            color: #555; /* Change text color */
        }

        .company-info p {
            margin: 0; /* Remove margin */
            padding: 0; /* Optional: Remove padding as well */
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
        }
        .header h2 {
            font-size: 18px;
            color: #555;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th, .table td {
            padding: 8px; /* Reduced padding */
            border: 1px solid #ddd;
            text-align: left;
        }

        .table th {
            background-color: #f4f4f4;
            color: #333;
        }

        .table td {
            font-size: 14px;
            color: #555;
        }

        .table .bold {
            font-weight: bold;
        }

        .table .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #777;
            font-size: 12px;
        }
        .unit-details {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            font-size: 12px;
            margin-top: 10px;
        }
        .unit-details p {
            margin: 0;
        }
        .indent-right {
            margin-left: 20px;  /* adjust this value as needed for the desired indentation */
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <!-- Add your logo here -->
        <div class="logo-container">
            <img src="{{ public_path('images/logo.png') }}" alt="Company Logo" style="width: 600px; height: auto;">
            <div class="company-info"><p>
                {{ $settings->company_address }}<br>
                {{ $settings->company_city }}, {{ $settings->company_state }} {{ $settings->company_zip }}<br>
                Email: {{ $settings->company_email }}<br>
                Phone: {{ $settings->company_phone }}</p>
            </div>
            <p>Date: {{ \Carbon\Carbon::now()->format('M d, Y') }}</p>
        </div>
        <h2 class="centered-text">
            {{ $data['InspectionType'] === 'reInspection' ? 'Lead-based Paint Re-Inspection' : 'Lead-based Paint Inspection' }}
        </h2>
        <h3>Payment Receipt # {{ $data['order_id'] }}</h3>
    </div>

    <!-- Billing Details -->
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 70%; vertical-align: top; border: none;"> <!-- Left Column -->
                <strong>Property Address</strong><br>
                <p class="indent-right">
                    <strong>{{ $data['name'] }}</strong><br>
                    {{ $data['address'] }}, {{ $data['apt'] }}<br>
                    {{ $data['municipality'] }}, {{ $data['state'] }}<br>
                    {{ $data['city'] }} - {{ $data['county'] }} County
                </p>
            </td>
            {{-- <td style="width: 30%; text-align: right; border: none;"> <!-- Right Column -->
                <span class="status-badge">Status: Paid</span>
            </td> --}}
        </tr>
    </table>

    <hr>

    <!-- Order Summary Table -->
    <h3 class="bold">Order Summary</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Details</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <!-- Service Information -->
            <tr>
                <td class="bold">Selected Service:</td>
                <td>{{ $data['service'] }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="bold">Inspection Type:</td>
                <td>{{ $data['InspectionType'] === 'reInspection' ? "Re-Inspection ({$data['old_order_id']})" : 'New Inspection' }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="bold">Method:</td>
                <td>{{ $data['methodology'] }} <span class="text-xs">({{ $data['county'] }} County)</span></td>
                <td></td>
            </tr>
            <tr>
                <td class="bold">Time Slot:</td>
                <td>{{ $data['selectedTime'] === 'morning' ? 'Morning 8.00 am - 12.00 pm' : 'Afternoon 12.00 pm - 5.00 pm' }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="bold">Date:</td>
                <td>{{ \Carbon\Carbon::parse($data['selected_date'])->format('M d, Y') }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="bold">No of Units:</td>
                <td>{{ $data['units'] }} Units</td>
                <td></td>
            </tr>
           <!-- Weekend Fee -->
            @if ($data['totalWeekendFee'] > 0)
                <tr>
                    <td class="bold">Weekend Fee:</td>
                    <td>${{ $data['weekendFee'] }} fee per unit</td>
                    <td>${{ $data['totalWeekendFee'] }}</td>
                </tr>
            @endif

            <!-- Service Cost -->
            <tr>
                <td class="bold">{{ $data['service'] }} Cost:</td>
                <td>${{ $data['basePrice'] }} per unit
                    @if (isset($data['discountPercentage']) && $data['discountPercentage'])
                    <span>
                        <sup class="text-xs font-bold">({{ $data['discountPercentage'] }}% off)</sup>
                    </span>
                    @endif
                </td>
                <td>${{ $data['totalBasePrice'] }}</td>
            </tr>

            <!-- XRF Service -->
            @if ($data['includeXrf'])
                <tr>
                    <td class="bold">XRF Service (Yes):</td>
                    <td>${{ $data['baseXrf'] }} fee per unit</td>
                    <td>${{ $data['totalXrf'] }}</td>
                </tr>
            @endif

            <!-- Subtotal -->
            <tr class="total-row">
                <td class="bold">Subtotal:</td>
                <td></td>
                <td>${{ $data['NewSubTotal'] }}</td>
            </tr>

            <!-- Credit Card Surcharge -->
            <tr class="total-row">
                <td class="bold">Surcharge:</td>
                <td></td>
                <td>${{ $data['credSucharg'] }}</td>
            </tr>

            <!-- Total Amount -->
            <tr class="total-row">
                <td class="bold"><h3>Total Amount:</h3></td>
                <td></td>
                <td><h3>${{ $data['totalPay'] }}</h3></td>
            </tr>
        </tbody>
    </table>
    <hr>

    <!-- Footer -->
    <div class="footer">
       
        <p>Thank you for your business!</p>
    </div>
</div>

</body>
</html>
