<x-mail::message>
# Lead Dust Wipe Report
<div>
<h3>Dear {{ $data['name'] }},</h3>
<h3>{{ $data['InspectionType'] === 'reInspection' ? 'Lead-based Paint Re-Inspection Report' : 'Lead-based Paint Inspection Report' }}</h3>

<p>We are pleased to inform you that the Lead-Based Paint Evaluation Report for the property located at {{ $data['address'] }}, 
    @if(!empty($data['unit_number'])) <span class="font-bold text-xs">(Unit # {{ $data['unit_number'] }})</span> 
    @endif
     {{ $data['municipality'] }} NJ, {{ $data['city'] }}, has been completed
</p>
    
<p> Please find the detailed report attached to this email for your review.</p>
<p> Should you have any questions or require further clarification regarding the findings, feel free to reach out, and I will be happy to assist you.</p>

<p>If you have any questions, feel free to email or call our customer success team. (We're lightning quick at replying.)</p>
<p>Thank you for trusting us with this evaluation.</p>

<p>Thanks,<br>{{ $settings->app_name ?? config('app.name') }}<br>
    Email: {{ $settings->company_email ?? 'Email Not Set' }}<br>
    Phone: {{ $settings->company_phone ?? 'Phone Not Set' }}</p>

</div>
</x-mail::message>
