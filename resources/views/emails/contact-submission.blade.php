@component('mail::message')
<div>
  <h2>New Contact Form Submission</h2>

  <p><strong>Name:</strong> {{ $contact->name }}</p>
  <p><strong>Email:</strong> {{ $contact->email }}</p>

  <p><strong>Message:</strong><br>
  {{ $contact->message }}</p>
</div>
@endcomponent