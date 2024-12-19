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
              required>{{ old('description', $course->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-3">
  <label for="featured_image">Featured Image</label>
  <input type="file" name="featured_image" id="featured_image" 
         class="form-control @error('featured_image') is-invalid @enderror">
  <img id="image_preview" src="{{ $course->featured_image ?? '' }}" 
       class="mt-2" style="max-height: 200px; display: {{ isset($course->featured_image) ? 'block' : 'none' }}">
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

<script>
  document.getElementById('featured_image').onchange = function(evt) {
      const [file] = this.files;
      if (file) {
          const preview = document.getElementById('image_preview');
          preview.src = URL.createObjectURL(file);
          preview.style.display = 'block';
      }
  }
</script>
