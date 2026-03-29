@extends('admin.layouts.app')

@section('title', 'Edit Post')

@section('content')
<div class="container-fluid">
    @include('components.alerts')

    <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">Edit Post</h1>
                <p class="text-muted mb-0">{{ $post->title }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">Back</a>
                <button type="submit" name="submit_action" value="draft" class="btn btn-outline-primary">Save Draft</button>
                <button type="submit" name="submit_action" value="publish" class="btn btn-primary">Update Post</button>
            </div>
        </div>

        @include('admin.posts._form', ['allowCategoryCreation' => false])
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
@endsection
