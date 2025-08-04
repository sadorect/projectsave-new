<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 5px 5px 0 0;
            text-align: center;
        }
        .content {
            background: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .form-data {
            background: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .data-row {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .data-row:last-child {
            border-bottom: none;
        }
        .field-label {
            font-weight: bold;
            color: #495057;
            display: inline-block;
            min-width: 150px;
        }
        .field-value {
            color: #212529;
        }
        .footer {
            background: #6c757d;
            color: white;
            padding: 15px;
            border-radius: 0 0 5px 5px;
            text-align: center;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>📋 New Form Submission</h2>
        <p>{{ $form->title ?? 'Form Submission' }}</p>
    </div>

    <div class="content">
        <p><strong>Hello,</strong></p>
        <p>You've received a new form submission. Here are the details:</p>

        <div class="form-data">
            <h3>📝 Form Information</h3>
            <div class="data-row">
                <span class="field-label">Form Title:</span>
                <span class="field-value">{{ $form->title ?? 'Unknown Form' }}</span>
            </div>
            @if($form->description)
            <div class="data-row">
                <span class="field-label">Description:</span>
                <span class="field-value">{{ $form->description }}</span>
            </div>
            @endif
            <div class="data-row">
                <span class="field-label">Submitted At:</span>
                <span class="field-value">{{ $submission->created_at->format('M d, Y \a\t H:i:s') }}</span>
            </div>
            <div class="data-row">
                <span class="field-label">Submitted By:</span>
                <span class="field-value">
                    @if($submission->user)
                        {{ $submission->user->name }} ({{ $submission->user->email }})
                    @else
                        Guest User
                    @endif
                </span>
            </div>
        </div>

        <div class="form-data">
            <h3>📊 Submitted Data</h3>
            @if($submission->data && is_array($submission->data))
                @foreach($submission->data as $key => $value)
                    @php
                        // Try to find the field label from form fields
                        $fieldLabel = $key;
                        if ($form->fields) {
                            foreach ($form->fields as $field) {
                                if (isset($field['name']) && $field['name'] === $key) {
                                    $fieldLabel = $field['label'] ?? $key;
                                    break;
                                }
                            }
                        }
                        $fieldLabel = ucfirst(str_replace('_', ' ', $fieldLabel));
                    @endphp
                    <div class="data-row">
                        <span class="field-label">{{ $fieldLabel }}:</span>
                        <span class="field-value">
                            @if(is_array($value))
                                {{ implode(', ', $value) }}
                            @elseif(filter_var($value, FILTER_VALIDATE_EMAIL))
                                <a href="mailto:{{ $value }}">{{ $value }}</a>
                            @elseif(filter_var($value, FILTER_VALIDATE_URL))
                                <a href="{{ $value }}">{{ $value }}</a>
                            @else
                                {{ $value ?: '(No response)' }}
                            @endif
                        </span>
                    </div>
                @endforeach
            @else
                <p style="color: #6c757d; font-style: italic;">No form data was submitted.</p>
            @endif
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ route('admin.forms.submissions', $form) }}" class="btn">
                View All Submissions
            </a>
        </div>

        <p style="color: #6c757d; font-size: 14px;">
            <strong>Note:</strong> This is an automated notification from your form management system. 
            You can manage notification settings from your admin dashboard.
        </p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'Your App') }}. All rights reserved.</p>
        <p>Submission ID: #{{ $submission->id }}</p>
    </div>
</body>
</html>
