<x-layouts.app>
    <div class="container py-4">
        <h2>Create New Exam</h2>
        <form action="{{ route('lms.exams.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label">Course</label>
              <select name="course_id" class="form-select" required>
                  <option value="">Select a course</option>
                  @foreach($courses as $course)
                      <option value="{{ $course->id }}">{{ $course->title }}</option>
                  @endforeach
              </select>
          </div>
          
            
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control"  required>
            </div>

            <div class="mb-3">
                <label class="form-label">Duration (minutes)</label>
                <input type="number" name="duration_minutes" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Passing Score (%)</label>
                <input type="number" name="passing_score" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Exam</button>
        </form>
    </div>
</x-layouts.app>
