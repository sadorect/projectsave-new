<x-admin-layout>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Edit Course: {{ $course->title }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.courses.form')
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Course</button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
