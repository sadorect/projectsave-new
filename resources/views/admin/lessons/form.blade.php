<div class="form-group mb-3">
    <label for="course_id">Course</label>
    <select name="course_id" id="course_id" class="form-control @error('course_id') is-invalid @enderror" required>
        <option value="">Select Course</option>
        @foreach($courses as $id => $title)
            <option value="{{ $id }}" {{ (old('course_id', $lesson->course_id ?? '') == $id) ? 'selected' : '' }}>
                {{ $title }}
            </option>
        @endforeach
    </select>
    @error('course_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="title">Lesson Title</label>
    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
           value="{{ old('title', $lesson->title ?? '') }}" required>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="content">Content</label>
    <textarea name="content" id="content" rows="5" 
              class="form-control @error('content') is-invalid @enderror" 
              required>{{ old('content', $lesson->content ?? '') }}</textarea>
    @error('content')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="video_url">Video URL</label>
    <input type="url" name="video_url" id="video_url" 
           class="form-control @error('video_url') is-invalid @enderror"
           value="{{ old('video_url', $lesson->video_url ?? '') }}">
    @error('video_url')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="order">Order</label>
    <input type="number" name="order" id="order" 
           class="form-control @error('order') is-invalid @enderror"
           value="{{ old('order', $lesson->order ?? '') }}" required min="1">
    @error('order')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
