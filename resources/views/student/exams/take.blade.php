<x-layouts.lms>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>{{ $exam->title }}</h3>
        <div id="examTimer" class="h4"></div>
    </div>

    <form id="examForm" action="{{ route('lms.exams.submit', [$exam, $attempt]) }}" method="POST">
        @csrf
        @foreach($exam->questions as $index => $question)
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Question {{ $index + 1 }}</h5>
                    <p class="mb-3">{{ $question->question_text }}</p>
                    
                    <div class="options-list">
                        @foreach(json_decode($question->options) as $optionIndex => $option)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" 
                                       name="answers[{{ $question->id }}]" 
                                       value="{{ $option }}"
                                       id="q{{ $question->id }}_{{ $optionIndex }}">
                                <label class="form-check-label" for="q{{ $question->id }}_{{ $optionIndex }}">
                                    {{ chr(65 + $optionIndex) }}. {{ $option }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">Submit Exam</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Timer functionality
    const endTime = new Date().getTime() + ({{ $exam->duration_minutes }} * 60 * 1000);
    
    const timer = setInterval(function() {
        const now = new Date().getTime();
        const distance = endTime - now;
        
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById("examTimer").innerHTML = 
            minutes + "m " + seconds + "s ";
            
        if (distance < 0) {
            clearInterval(timer);
            document.getElementById("examForm").submit();
        }
    }, 1000);
</script>
@endpush
</x-layouts.lms>
