<x-layouts.lms>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>{{ $exam->title }}</h2>
            <div id="timer" class="h4"></div>
        </div>

        <form id="examForm" action="{{ route('lms.exams.attempt.submit', $exam) }}" method="POST">
            @csrf
            @foreach($exam->questions as $question)
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">{{ $question->question_text }}</h5>
                    @foreach(json_decode($question->options) as $option)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" 
                               name="answers[{{ $question->id }}]" 
                               value="{{ $option }}" required>
                        <label class="form-check-label">{{ $option }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            <button type="submit" class="btn btn-primary">Submit Exam</button>
        </form>
    </div>

    <script>
        // Timer functionality
        const duration = {{ $exam->duration_minutes * 60 }};
        let timeLeft = duration;
        
        const timer = setInterval(() => {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById('timer').textContent = 
                `${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeft <= 0) {
                clearInterval(timer);
                document.getElementById('examForm').submit();
            }
        }, 1000);
    </script>
</x-layouts.lms>
