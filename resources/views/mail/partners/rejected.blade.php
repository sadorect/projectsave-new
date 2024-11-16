@component('mail::message')
# Application Status Update

Dear {{ $partner->name }},

Thank you for your interest in joining our {{ $partner->partner_type }} Force team. After careful review, we regret to inform you that we cannot accept your application at this time.

@component('mail::button', ['url' => $url])
View Application Details
@endcomponent

Best regards,  
{{ config('app.name') }}
@endcomponent
