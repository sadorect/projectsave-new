@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Video Reels</h3>
            <a href="{{ route('videos.create') }}" class="btn btn-primary">
                Add Video
            </a>
        </div>
        <div class="card-body">
            <div class="video-list" id="sortableVideos">
                @foreach($videos as $video)
                <div class="card mb-3" data-id="{{ $video->id }}">
                    <div class="row no-gutters">
                        <div class="col-md-4">
                            <img src="https://img.youtube.com/vi/{{ $video->youtube_id }}/0.jpg" class="card-img">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title">{{ $video->title }}</h5>
                                <p class="card-text">
                                    <small class="text-muted">Order: {{ $video->display_order }}</small>
                                    <span class="badge badge-{{ $video->is_active ? 'success' : 'secondary' }}">
                                        {{ $video->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                                
                                <div class="btn-group">
                                    <a href="{{ route('videos.edit', $video->id) }}" class="btn btn-sm btn-info">Edit</a>
                                    <form action="{{ route('videos.destroy', $video) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this video?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

    

