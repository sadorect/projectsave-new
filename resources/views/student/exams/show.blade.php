<x-layouts.lms>
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h3>{{ $exam->title }}</h3>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Exam Details</h5>
                    <ul class="list-unstyled">
                        <li>Course: {{ $exam->course->title }}</li>
                        <li>Duration: {{ $exam->duration_minutes }} minutes</li>
                        <li>Total Questions: {{ $exam->questions->count() }}</li>
                        <li>Passing Score: {{ $exam->passing_score }}%</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Instructions</h5>
                    <ul>
                        <li>Complete all questions within the time limit</li>
                        <li>Each question has only one correct answer</li>
                        <li>You cannot pause or resume the exam once started</li>
                    </ul>
                </div>
            </div>
            
            <form action="{{ route('lms.exams.start', $exam) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg">Start Exam</button>
            </form>
        </div>
    </div>
</div>
</x-layouts.lms>
