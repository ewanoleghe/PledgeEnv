<x-mail::message>
<div>
<h3>{{ $data['InspectionType'] === 'reInspection' ? 'Lead-based Paint Re-Inspection' : 'Lead-based Paint Inspection' }}</h3>

<p>Thank you for your order! Attached is your receipt. We’re thrilled to have you on board. To get the most out of {{ $data['service'] }}, do this primary next step:</p>
    
<p>
    <h3>Primary Steps</h3>
    <ul>
        <li>Step 1: Thoroughly clean all painted surfaces</li>
        <li>Step 2: Visually inspect for any signs of peeling, chipping, or cracking paint</li>
        <li>Step 3: Repair any damaged areas</li>
        <li>Step 4: Remove dust and debris using a HEPA vacuum, <strong>*</strong> especially around windows and floors where inspectors often take dust wipe samples</li>
        <li>Step 5: If necessary, consult a professional to address any significant lead paint issues</li>
        <li>Step 6: For Re-Inspection (Failed house or apartment) only the area that failed will be re-tested for lead-based paint hazard.</li>
        <li>Step X: Optional For Lead-Free Designation - Get a Pre-Inspection service (for an additional fee of $100, hire our Certified Lead Inspector to Test Your Home with an X-ray fluorescence (XRF) device)</li>
    </ul>
</p>

<h3>For reference, here's your booking information:</h3>
    
<p>
    Order # {{ $data['order_id'] }}<br>
    Selected Service: {{ $data['service'] }}<br>
    Service Type: {{ $data['InspectionType'] === 'newInspection' ? 'New Inspection' : 'Re-Inspection' }} <br>
                    @if(!empty($data['orderNumber']))
                        <span class="font-bold text-xs">(order # {{ $data['orderNumber'] }})</span><br>
                    @endif
    County: {{ $data['county'] }} County<br>
    Municipality: {{ $data['municipality'] }}<br>

    Method: @if($data['includeXrf'])
    {{ $data['methodology'] }} + XRF included
            @else
    Method: {{ $data['InspectionType'] === 'reInspection' ? 
                            ($data['includeXrf'] ? 'Dust Wipe Sampling + XRF' : 'Dust Wipe Sampling') :
                            ($data['InspectionType'] === 'newInspection' ? 
                                ($data['includeXrf'] ? $data['methodology'] . ' + XRF' : $data['methodology']) : 
                            '')
                        }}
            @endif
            {{ " "}}
</p>
<p>
    Inspection Date: {{ \Carbon\Carbon::parse($data['selected_date'])->format('M d, Y') }}<br>
    Selected Time: {{ $data['selectedTime'] === 'morning' ? 'Morning 8.00 am - 12.00 pm' : 'Afternoon 12.00 pm - 5.00 pm' }}<br>
    No of Units: {{ $data['units'] }} Unit{{ $data['units'] > 1 ? 's' : '' }}<br>
</p>

<p>If you have any questions, feel free to email or call our customer success team. (We're lightning quick at replying.) We also offer live chat during business hours.</p>

<p>Thanks,<br>{{ $settings->app_name ?? config('app.name') }}<br>
    Email: {{ $settings->company_email ?? 'Email Not Set' }}<br>
    Phone: {{ $settings->company_phone ?? 'Phone Not Set' }}</p>

</div>
</x-mail::message>

