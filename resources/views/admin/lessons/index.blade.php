<x-admin-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Lesson Management</h2>
            <a href="{{ route('admin.lessons.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Lesson
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
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lessons as $lesson)
                                <tr>
                                    <td>{{ $lesson->title }}</td>
                                    <td>{{ $lesson->course->title }}</td>
                                    <td>{{ $lesson->order }}</td>
                                    <td>{{ $lesson->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.lessons.edit', $lesson) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
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
            </div>
        </div>
    </div>
</x-admin-layout>
