<x-layouts.app>
    <div class="container py-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h2>Edit Lesson: {{ $lesson->title }}</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('lessons.update', [$course, $lesson]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group mb-4">
                        <label for="title">Lesson Title</label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title', $lesson->title) }}" 
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="video">Video (Leave empty to keep current video)</label>
                        <input type="file" 
                               name="video" 
                               id="video" 
                               class="form-control @error('video') is-invalid @enderror" 
                               accept="video/mp4,video/mov,video/ogg,video/webm">
                        @error('video')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if($lesson->video_url)
                            <div class="mt-2">
                                <small class="text-muted">Current video: {{ basename($lesson->video_url) }}</small>
                            </div>
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <label for="order">Lesson Order</label>
                        <input type="number" 
                               name="order" 
                               id="order" 
                               class="form-control @error('order') is-invalid @enderror" 
                               value="{{ old('order', $lesson->order) }}" 
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
                                  required>{{ old('content', $lesson->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Lesson</button>
                        <a href="{{ route('lessons.show', [$course, $lesson]) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
