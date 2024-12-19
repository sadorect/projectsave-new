@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Lesson Management</h2>
        <a href="{{ route('admin.lessons.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add New Lesson
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Course</th>
                            <th>Order</th>
                            <th>Video</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lessons as $lesson)
                            <tr>
                                <td>{{ $lesson->title }}</td>
                                <td>{{ $lesson->course->title }}</td>
                                <td>{{ $lesson->order }}</td>
                                <td>
                                    @if($lesson->video_url)
                                        <i class="bi bi-camera-video text-success"></i>
                                    @else
                                        <i class="bi bi-camera-video-off text-muted"></i>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.lessons.edit', $lesson) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No lessons found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $lessons->links() }}
        </div>
    </div>
</div>
@endsection