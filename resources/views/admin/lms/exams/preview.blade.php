@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>{{ $exam->title }} - Preview</h3>
            <div>
                <span class="badge bg-info me-2">Duration: {{ $exam->duration_minutes }} minutes</span>
                <span class="badge bg-success">Passing Score: {{ $exam->passing_score }}%</span>
                <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-secondary ms-3">Back to Edit</a>
            </div>
        </div>
        
        <div class="card-body">
            @if($exam->questions->count() > 0)
                <form id="previewForm">
                    @foreach($exam->questions as $index => $question)
                        <div class="question-card mb-4">
                            <h5>Question {{ $index + 1 }} <span class="float-end text-muted">({{ $question->points }} points)</span></h5>
                            <p class="mb-3">{{ $question->question_text }}</p>
                            
                            <div class="options-list">
                                @foreach(json_decode($question->options) as $optionIndex => $option)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" 
                                               name="question_{{ $question->id }}" 
                                               value="{{ $option }}"
                                               id="q{{ $question->id }}_{{ $optionIndex }}">
                                        <label class="form-check-label" for="q{{ $question->id }}_{{ $optionIndex }}">
                                            {{ chr(65 + $optionIndex) }}. {{ $option }}
                                        </label>
                                        @if($option === $question->correct_answer)
                                            <span class="badge bg-success ms-2">Correct Answer</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </form>
            @else
                <div class="alert alert-warning">
                    No questions have been added to this exam yet.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
