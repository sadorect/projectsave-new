@component('mail::message')
# Application Approved!

Dear {{ $partner->name }},

Congratulations! Your {{ $partner->partner_type }} Force application has been approved. We're excited to have you join our team.

@component('mail::button', ['url' => $url])
View Application Details
@endcomponent

Next Steps:
- Join our WhatsApp group for updates
- Complete the orientation process
- Attend our next team meeting

Welcome aboard!

Best regards,  
{{ config('app.name') }}
@endcomponent
