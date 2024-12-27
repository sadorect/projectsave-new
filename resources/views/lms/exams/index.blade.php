<x-layouts.lms>
<div class="container py-4">
    <h2>Available Exams</h2>
    
    <div class="row mt-4">
        @foreach($exams as $exam)
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $exam->title }}</h5>
                    <p>Duration: {{ $exam->duration_minutes }} minutes</p>
                    <form action="{{ route('lms.exams.attempt.start', $exam) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Start Exam</button>
                    </form>
                </div>
            </div>
        @endforeach    
    </div>
    
    {{ $exams->links() }}
</div>
</x-layouts.lms>