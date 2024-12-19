<x-layouts.app>
    <div class="container py-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h2>Create New Lesson for {{ $course->title }}</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('lessons.store', $course) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group mb-4">
                        <label for="title">Lesson Title</label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title') }}" 
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="video">Video</label>
                        <input type="file" 
                               name="video" 
                               id="video" 
                               class="form-control @error('video') is-invalid @enderror" 
                               accept="video/mp4,video/mov,video/ogg,video/webm">
                        @error('video')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="order">Lesson Order</label>
                        <input type="number" 
                               name="order" 
                               id="order" 
                               class="form-control @error('order') is-invalid @enderror" 
                               value="{{ old('order', $course->lessons()->count() + 1) }}" 
                               required>
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="content">Lesson Content</label>
                        <textarea name="content" 
                                  id="content" 
                                  class="form-control @error('content') is-invalid @enderror" 
                                  rows="10" 
                                  required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Create Lesson</button>
                        <a href="{{ route('lessons.index', $course) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
