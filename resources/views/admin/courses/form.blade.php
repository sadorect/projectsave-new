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
    <label for="objectives">Course Objectives</label>
    <textarea name="objectives" id="objectives" rows="5" class="form-control @error('objectives') is-invalid @enderror">{{ old('objectives', $course->objectives ?? '') }}</textarea>
</div>

<div class="form-group mb-3">
    <label for="outcomes">Learning Outcomes</label>
    <textarea name="outcomes" id="outcomes" rows="5" class="form-control @error('outcomes') is-invalid @enderror">{{ old('outcomes', $course->outcomes ?? '') }}</textarea>
</div>

<div class="form-group mb-3">
    <label for="evaluation">Evaluation Method</label>
    <textarea name="evaluation" id="evaluation" rows="5" class="form-control @error('evaluation') is-invalid @enderror">{{ old('evaluation', $course->evaluation ?? '') }}</textarea>
</div>

<div class="form-group mb-3">
    <label for="recommended_books">Recommended Books</label>
    <textarea name="recommended_books" id="recommended_books" rows="5" class="form-control @error('recommended_books') is-invalid @enderror">{{ old('recommended_books', $course->recommended_books ?? '') }}</textarea>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('document-container');

    // Add new document input
    container.addEventListener('click', function(e) {
        if (e.target.closest('.add-document')) {
            const newGroup = document.createElement('div');
            newGroup.className = 'document-input-group mb-2';
            newGroup.innerHTML = `
                <div class="input-group">
                    <input type="file" name="documents[]" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                    <button type="button" class="btn btn-success add-document"><i class="fas fa-plus"></i></button>
                    <button type="button" class="btn btn-danger remove-document"><i class="fas fa-trash"></i></button>
                </div>
                <div class="document-preview mt-1"></div>
            `;
            container.appendChild(newGroup);
        }
        
        if (e.target.closest('.remove-document')) {
            const group = e.target.closest('.document-input-group');
            if (container.children.length > 1) {
                group.remove();
            }
        }
    });

    // Preview files
    container.addEventListener('change', function(e) {
        if (e.target.type === 'file') {
            const preview = e.target.closest('.document-input-group').querySelector('.document-preview');
            const file = e.target.files[0];
            if (file) {
                preview.innerHTML = `
                    <div class="document-item">
                        <i class="fas fa-file"></i>
                        <span>${file.name}</span>
                        <small class="text-muted">(${(file.size/1024/1024).toFixed(2)} MB)</small>
                    </div>
                `;
            }
        }
    });
});
</script>
<script>
    const editors = ['description','objectives', 'outcomes', 'evaluation', 'recommended_books'];
    editors.forEach(field => {
        ClassicEditor
            .create(document.querySelector(`#${field}`))
            .catch(error => {
                console.error(error);
            });
    });
    </script>
<style>
.document-item {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f8f9fa;
}
</style>


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
