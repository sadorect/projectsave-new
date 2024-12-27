<x-layouts.lms>
<div class="container py-4">
    <h2>Available Exams</h2>
    
    <div class="row mt-4">
        @foreach($exams as $exam)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $exam->title }}</h5>
                        <p class="card-text">Course: {{ $exam->course->title }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-info">{{ $exam->duration_minutes }} minutes</span>
                                <span class="badge bg-primary">{{ $exam->questions->count() }} questions</span>
                                <span class="badge bg-success">{{ $exam->passing_score }}% to pass</span>
                            </div>
                            <a href="{{ route('lms.exams.show', $exam) }}" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    {{ $exams->links() }}
</div>
</x-layouts.lms>