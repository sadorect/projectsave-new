<x-layouts.app>
    <div class="container py-8">
        <div class="card">
            <div class="card-header">
                <h2>Edit Course</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('courses.update', $course) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group mb-4">
                        <label for="title">Course Title</label>
                        <input type="text" name="title" id="title" 
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $course->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="5" required>{{ old('description', $course->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="featured_image">Featured Image</label>
                        <input type="file" name="featured_image" id="featured_image" 
                               class="form-control @error('featured_image') is-invalid @enderror">
                        @error('featured_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="draft" {{ $course->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ $course->status === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ $course->status === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Course</button>
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
