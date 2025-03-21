<x-mail::message>
# Lead-Based Paint Evaluation Report Inspection
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

<p>Thanks,<br>{{ config('app.name') }}<br>
    Email: {{ env('COMPANY_EMAIL') }}<br>
    Phone: {{ env('COMPANY_PHONE') }}</p>

</div>
</x-mail::message>