@extends('admin.layouts.app')

@section('title', 'Create Post')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Create New Post</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Scripture</label>
                            <input type="text" name="scripture" class="form-control @error('scripture') is-invalid @enderror" value="{{ old('scripture') }}">
                            @error('scripture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bible Text</label>
                            <textarea name="bible_text" class="form-control @error('bible_text') is-invalid @enderror" rows="3">{{ old('bible_text') }}</textarea>
                            @error('bible_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subtitle</label>
                            <input type="text" name="subtitle" class="form-control @error('subtitle') is-invalid @enderror" value="{{ old('subtitle') }}">
                            @error('subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                      
<div class="mb-3">
    <label class="form-label">Details</label>
    <textarea id="details" name="details" class="form-control @error('details') is-invalid @enderror" rows="5">{{ old('details') }}</textarea>
    @error('details')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

                        <div class="mb-3">
                            <label class="form-label">Action Point</label>
                            <textarea name="action_point" class="form-control @error('action_point') is-invalid @enderror" rows="3">{{ old('action_point') }}</textarea>
                            @error('action_point')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Author</label>
                            <input type="text" name="author" class="form-control @error('author') is-invalid @enderror" value="{{ old('author') }}">
                            @error('author')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Post Date</label>
                            <input type="date" name="post_date" class="form-control @error('post_date') is-invalid @enderror" value="{{ old('post_date', now()->format('Y-m-d')) }}">
                            @error('post_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Post Time</label>
                            <input type="time" name="post_time" class="form-control @error('post_time') is-invalid @enderror" value="{{ old('post_time', now()->format('H:i')) }}">
                            @error('post_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Featured Image</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                       <!-- Update multiple select for categories and tags -->
<div class="mb-3">
    <label class="form-label">Categories</label>
    <select name="category_ids[]" class="form-select" multiple size="5">
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', [])) ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Tags</label>
    <select name="tag_ids[]" class="form-select" multiple size="5">
        @foreach($tags as $tag)
            <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tag_ids', [])) ? 'selected' : '' }}>
                {{ $tag->name }}
            </option>
        @endforeach
    </select>
</div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="shareToFacebook" name="share_to_facebook">
                                <label class="custom-control-label" for="shareToFacebook">Share to Facebook</label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


