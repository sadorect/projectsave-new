@extends('admin.layouts.app')

@section('title', 'Edit Post')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Post: {{ $post->title }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $post->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Scripture</label>
                            <input type="text" name="scripture" class="form-control @error('scripture') is-invalid @enderror" value="{{ old('scripture', $post->scripture) }}">
                            @error('scripture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bible Text</label>
                            <textarea name="bible_text" class="form-control @error('bible_text') is-invalid @enderror" rows="3">{{ old('bible_text', $post->bible_text) }}</textarea>
                            @error('bible_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        

                        <div class="mb-3">
                            <label class="form-label">Subtitle</label>
                            <input type="text" name="subtitle" class="form-control @error('subtitle') is-invalid @enderror" value="{{ old('subtitle', $post->subtitle) }}">
                            @error('subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Details</label>
                            <textarea name="details" id="details" class="form-control @error('details') is-invalid @enderror" rows="5" required>{{ old('details', $post->details) }}</textarea>
                            @error('details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Action Point</label>
                            <textarea name="action_point" class="form-control @error('action_point') is-invalid @enderror" rows="3">{{ old('action_point', $post->action_point) }}</textarea>
                            @error('action_point')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Author</label>
                            <input type="text" name="author" class="form-control @error('author') is-invalid @enderror" value="{{ old('author', $post->author) }}">
                            @error('author')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label class="form-label">Publication Date & Time</label>
                            <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}">
                            @error('published_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        
                        
                        <div class="mb-3">
                            <label class="form-label">Featured Image</label>
                            @if($post->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $post->image) }}" alt="Current image" class="img-thumbnail" style="max-height: 200px">
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="card border-light-subtle mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                    <div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" value="1" id="featuredImageGenerationEnabled" name="featured_image_generation_enabled" {{ old('featured_image_generation_enabled', $post->featured_image_generation_enabled) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="featuredImageGenerationEnabled">Generate featured image automatically with AI</label>
                                        </div>
                                        <div class="small text-muted">
                                            Source: {{ $post->featured_image_source === 'manual' ? 'manual image' : ($post->featured_image_source === 'ai' ? 'ai image' : 'none') }}
                                            · Generation: {{ $post->featured_image_generation_status ?? 'not queued' }}
                                            @if($post->featured_image_approval_status)
                                                · Review: {{ $post->featured_image_approval_status }}
                                            @endif
                                            @if($post->featured_image_generated_at)
                                                · Last generated {{ $post->featured_image_generated_at->diffForHumans() }}
                                            @endif
                                        </div>
                                        @if($post->featured_image_generation_error)
                                            <div class="small text-danger mt-1">{{ $post->featured_image_generation_error }}</div>
                                        @endif
                                    </div>
                                    @if(config('ai-images.enabled'))
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('generate-featured-image-form').submit();">Generate Candidate</button>
                                    @endif
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">AI Provider</label>
                                        <select name="featured_image_provider" class="form-select @error('featured_image_provider') is-invalid @enderror">
                                            @foreach($aiProviders as $providerKey => $provider)
                                                <option value="{{ $providerKey }}" {{ old('featured_image_provider', $post->featured_image_provider ?: $defaultAiProvider) === $providerKey ? 'selected' : '' }}>
                                                    {{ $provider['label'] ?? $providerKey }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('featured_image_provider')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Style Preset</label>
                                        <select name="featured_image_preset" class="form-select @error('featured_image_preset') is-invalid @enderror">
                                            @foreach($aiPresets as $presetKey => $preset)
                                                <option value="{{ $presetKey }}" {{ old('featured_image_preset', $post->featured_image_preset ?: $defaultAiPreset) === $presetKey ? 'selected' : '' }}>
                                                    {{ $preset['label'] ?? $presetKey }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('featured_image_preset')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Prompt Override</label>
                                        <textarea name="featured_image_prompt" class="form-control @error('featured_image_prompt') is-invalid @enderror" rows="3">{{ old('featured_image_prompt', $post->featured_image_prompt) }}</textarea>
                                        @error('featured_image_prompt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Advanced Options JSON</label>
                                        <textarea name="featured_image_options" class="form-control @error('featured_image_options') is-invalid @enderror" rows="3">{{ old('featured_image_options', $post->featured_image_options ? json_encode($post->featured_image_options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea>
                                        <div class="form-text">Keep using presets now and add provider-specific options later as JSON overrides.</div>
                                        @error('featured_image_options')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Categories</label>
                            <select name="category_ids[]" class="form-select" multiple>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, $post->categories->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tags</label>
                            <select name="tag_ids[]" class="form-select" multiple>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" {{ in_array($tag->id, $post->tags->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Post</button>
                        </div>
                    </form>

                    @if(config('ai-images.enabled'))
                        <form id="generate-featured-image-form" action="{{ route('admin.posts.generate-featured-image', $post) }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endif

                    @if($post->featured_image_candidate_path)
                        <div class="card border-warning-subtle mt-4" id="ai-review">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Generated Candidate Awaiting Review</h6>
                                <span class="badge bg-warning text-dark">{{ $post->featured_image_approval_status ?? 'pending' }}</span>
                            </div>
                            <div class="card-body">
                                <div class="row g-4 align-items-start">
                                    <div class="col-md-6">
                                        <div class="small text-muted mb-2">Candidate Preview</div>
                                        <img src="{{ asset('storage/' . $post->featured_image_candidate_path) }}" alt="Generated candidate image" class="img-fluid rounded border">
                                    </div>
                                    <div class="col-md-6">
                                        @if($post->image)
                                            <div class="small text-muted mb-2">Current Live Image</div>
                                            <img src="{{ asset('storage/' . $post->image) }}" alt="Current live image" class="img-fluid rounded border mb-3">
                                        @endif
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('admin.posts.approve-featured-image', $post) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success">Approve Candidate</button>
                                            </form>
                                            <form action="{{ route('admin.posts.reject-featured-image', $post) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger">Reject Candidate</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection