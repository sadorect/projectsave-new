<x-mail::message>
# Welcome to the Prayer Force Team

Dear {{ $partner->name }},

Your application to join our Prayer Force has been approved! We're excited to have you as part of our intercessory team.

<x-mail::button :url="$url">
Access Prayer Force Portal
</x-mail::button>

Next Steps:
- Join our WhatsApp group
- Check your email for prayer schedules
- Complete your profile

Blessings,<br>
{{ config('app.name') }}
</x-mail::message>
