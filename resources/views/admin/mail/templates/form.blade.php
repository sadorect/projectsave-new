<div class="mb-3">
    <label>Template Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $mailTemplate->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Subject</label>
    <input type="text" name="subject" class="form-control" value="{{ old('subject', $mailTemplate->subject ?? '') }}" required>
</div>

<div class="mb-3">
    <label>Body</label>
    <textarea name="body" class="form-control rich-editor" rows="10" required>{{ old('body', $mailTemplate->body ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label>Variables (comma-separated)</label>
    <input type="text" name="variables" class="form-control" value="{{ old('variables', isset($mailTemplate) ? implode(',', $mailTemplate->variables ?? []) : '') }}">
    <small class="text-muted">Example: {name}, {email}, {course}</small>
</div>
