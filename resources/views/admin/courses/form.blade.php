<div class="form-group mb-3">
    <label for="title">Course Title</label>
    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
           value="{{ old('title', $course->title ?? '') }}" required>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="description">Description</label>
    <textarea name="description" id="description" rows="5" 
              class="form-control @error('description') is-invalid @enderror"
              data-admin-rich-text
              >{{ old('description', $course->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="objectives">Course Objectives</label>
    <textarea name="objectives" id="objectives" rows="5" class="form-control @error('objectives') is-invalid @enderror" data-admin-rich-text>{{ old('objectives', $course->objectives ?? '') }}</textarea>
</div>

<div class="form-group mb-3">
    <label for="outcomes">Learning Outcomes</label>
    <textarea name="outcomes" id="outcomes" rows="5" class="form-control @error('outcomes') is-invalid @enderror" data-admin-rich-text>{{ old('outcomes', $course->outcomes ?? '') }}</textarea>
</div>

<div class="form-group mb-3">
    <label for="evaluation">Evaluation Method</label>
    <textarea name="evaluation" id="evaluation" rows="5" class="form-control @error('evaluation') is-invalid @enderror" data-admin-rich-text>{{ old('evaluation', $course->evaluation ?? '') }}</textarea>
</div>

<div class="form-group mb-3">
    <label for="recommended_books">Recommended Books</label>
    <textarea name="recommended_books" id="recommended_books" rows="5" class="form-control @error('recommended_books') is-invalid @enderror" data-admin-rich-text>{{ old('recommended_books', $course->recommended_books ?? '') }}</textarea>
</div>

<div class="form-group mb-3">
    <label>Course Documents</label>
    <div id="document-container">
        <div class="document-input-group mb-2">
            <div class="input-group">
                <input type="file" name="documents[]" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                <button type="button" class="btn btn-success add-document"><i class="fas fa-plus"></i></button>
                <button type="button" class="btn btn-danger remove-document"><i class="fas fa-trash"></i></button>
            </div>
            <div class="document-preview mt-1"></div>
        </div>
    </div>
</div>

<div class="form-group mb-3">
  <label for="featured_image">Featured Image</label>
  <input type="file" name="featured_image" id="featured_image" 
         class="form-control @error('featured_image') is-invalid @enderror">
  @if(isset($course->featured_image) && $course->featured_image)
    <img id="image_preview" src="{{ Storage::disk('s3')->url($course->featured_image )}}" 
         class="mt-2" style="max-height: 200px; display: block"
         onerror="this.src='{{ asset('frontend/img/course-placeholder.jpg') }}'; this.onerror=null;">
  @else
    <img id="image_preview" src="{{ asset('frontend/img/course-placeholder.jpg') }}" 
         class="mt-2" style="max-height: 200px; display: none">
  @endif
  @error('featured_image')
      <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>


<div class="form-group mb-3">
    <label for="status">Status</label>
    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
        <option value="draft" {{ (old('status', $course->status ?? '') == 'draft') ? 'selected' : '' }}>Draft</option>
        <option value="published" {{ (old('status', $course->status ?? '') == 'published') ? 'selected' : '' }}>Published</option>
        <option value="archived" {{ (old('status', $course->status ?? '') == 'archived') ? 'selected' : '' }}>Archived</option>
    </select>
    @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

