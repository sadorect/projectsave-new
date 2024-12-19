<x-layouts.lms>
    <div class="container py-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>{{ $course->title }} - Lessons</h2>
                <a href="{{ route('lessons.create', $course) }}" class="btn btn-primary">Add New Lesson</a>
            </div>
            
            <div class="card-body">
                @if($lessons->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Title</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lessons as $lesson)
                                    <tr>
                                        <td>{{ $lesson->order }}</td>
                                        <td>
                                            <a href="{{ route('lessons.show', [$course, $lesson]) }}">
                                                {{ $lesson->title }}
                                            </a>
                                        </td>
                                        <td>{{ $lesson->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('lessons.edit', [$course, $lesson]) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    Edit
                                                </a>
                                                
                                                <form action="{{ route('lessons.destroy', [$course, $lesson]) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this lesson?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $lessons->links() }}
                @else
                    <p class="text-center">No lessons available for this course yet.</p>
                @endif
            </div>
        </div>
    </div>
</x-layouts.lms>
