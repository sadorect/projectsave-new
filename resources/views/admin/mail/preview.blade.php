@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Email Preview</h3>
            <a href="{{ route('admin.mail.compose') }}" class="btn btn-secondary">Back to Compose</a>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <strong>Recipients:</strong>
            <ul>
                @foreach($recipientDetails as $recipient)
                    <li>{{ $recipient['name'] }} ({{ $recipient['email'] }})</li>
                @endforeach
            </ul>
        </div>
        
            <div class="mb-3">
                <strong>Template:</strong> {{ $template->name }}
            </div>
            <div class="mb-3">
                <strong>Subject:</strong> {{ $template->subject }}
            </div>
            <div class="email-preview-content border p-4">
                {!! $content !!}
            </div>
            @if($customMessage)
                <div class="mt-3">
                    <strong>Custom Message:</strong>
                    <div class="border p-3">
                        {!! $customMessage !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="card-footer">
      <form action="{{ route('admin.mail.send') }}" method="POST">
          @csrf
          <input type="hidden" name="template_id" value="{{ $template->id }}">
          <input type="hidden" name="custom_message" value="{{ $customMessage }}">
          <input type="hidden" name="recipient" value="{{ json_encode($recipient['email']) }}">
          <button type="submit" class="btn btn-primary">Send This Email</button>
          <a href="{{ route('admin.mail.compose') }}" class="btn btn-secondary">Edit Email</a>
      </form>
  </div>
  
</div>
@endsection
