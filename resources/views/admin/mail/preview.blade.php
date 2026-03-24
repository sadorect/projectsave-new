@extends('admin.layouts.app')

@section('title', 'Mail Preview')
@section('page_kicker', 'Communications')
@section('page_subtitle', 'Validate template variables, recipient context, and optional custom text before you queue the final delivery.')

@section('content')
<div class="admin-page-shell">
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="d-grid gap-4">
                <x-ui.panel title="Delivery Summary" subtitle="Preview details for the current outbound message.">
                    <div class="admin-definition-grid admin-definition-grid-single">
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Template</span>
                            <strong>{{ $template->name }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Subject</span>
                            <strong>{{ $template->subject }}</strong>
                        </div>
                        <div class="admin-definition-item">
                            <span class="admin-definition-label">Recipient count</span>
                            <strong>{{ number_format($totalRecipients) }}</strong>
                        </div>
                    </div>
                </x-ui.panel>

                <x-ui.panel title="Recipients Sample" subtitle="The first recipients included in this preview payload.">
                    @if(! empty($recipientDetails))
                        <div class="admin-stack-list">
                            @foreach($recipientDetails as $recipient)
                                <div class="admin-stack-item">
                                    <span>{{ $recipient['name'] }}</span>
                                    <strong>{{ $recipient['email'] }}</strong>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No valid recipient records were resolved from the selected options.</p>
                    @endif
                </x-ui.panel>
            </div>
        </div>

        <div class="col-xl-8">
            <x-ui.panel title="Rendered Email" subtitle="This is how the template body and optional note will be delivered.">
                <div class="admin-mail-preview">
                    {!! $content !!}
                </div>

                @if(! empty($customMessage))
                    <div class="admin-mail-preview-note">
                        <span class="admin-field-label">Custom appended note</span>
                        <div>{!! $customMessage !!}</div>
                    </div>
                @endif

                <div class="admin-action-row mt-4">
                    <form action="{{ route('admin.mail.send') }}" method="POST" class="d-inline-flex flex-wrap gap-2">
                        @csrf
                        @foreach($selectedRecipients as $recipient)
                            <input type="hidden" name="recipients[]" value="{{ $recipient }}">
                        @endforeach
                        <input type="hidden" name="template_id" value="{{ $template->id }}">
                        <textarea name="custom_message" class="d-none">{{ $customMessage }}</textarea>
                        <button type="submit" class="surface-button-primary">Queue this email</button>
                    </form>

                    <a href="{{ route('admin.mail.compose') }}" class="surface-button-secondary">Back to compose</a>
                </div>
            </x-ui.panel>
        </div>
    </div>
</div>
@endsection
