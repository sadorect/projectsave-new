@extends('admin.layouts.app')

@section('title', 'Exam Attempt Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt me-2"></i>
                        Exam Attempt Details
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.exam-attempts.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Back to All Attempts
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Attempt Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-user me-2"></i>Student Information
                                    </h5>
                                    <p><strong>Name:</strong> {{ $attempt->user->name }}</p>
                                    <p><strong>Email:</strong> {{ $attempt->user->email }}</p>
                                    <p class="mb-0"><strong>User ID:</strong> #{{ $attempt->user->id }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-clipboard-list me-2"></i>Exam Information
                                    </h5>
                                    <p><strong>Title:</strong> {{ $attempt->exam->title }}</p>
                                    <p><strong>Course:</strong> {{ $attempt->exam->course->title ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Passing Score:</strong> {{ $attempt->exam->passing_score }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attempt Details -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="card-title text-{{ $attempt->score >= $attempt->exam->passing_score ? 'success' : 'danger' }}">
                                        {{ number_format($attempt->score, 1) }}%
                                    </h3>
                                    <p class="card-text">Final Score</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="card-title">
                                        <span class="badge bg-{{ $attempt->score >= $attempt->exam->passing_score ? 'success' : 'danger' }} fs-6 text-white">
                                            {{ $attempt->score >= $attempt->exam->passing_score ? 'PASSED' : 'FAILED' }}
                                        </span>
                                    </h3>
                                    <p class="card-text">Result</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="card-title">{{ $attempt->started_at->format('M d, Y') }}</h3>
                                    <p class="card-text">Date Taken</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="card-title">
                                        @if($attempt->completed_at && $attempt->started_at)
                                            {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }}m
                                        @else
                                            N/A
                                        @endif
                                    </h3>
                                    <p class="card-text">Time Taken</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Pass Check -->
                    @if(isset($attempt->answers['manual_pass']) && $attempt->answers['manual_pass'])
                        <div class="alert alert-info mb-4">
                            <h5><i class="fas fa-hand-paper me-2"></i>Manual Pass Record</h5>
                            <p><strong>Administrator:</strong> {{ $attempt->answers['admin_name'] ?? 'Unknown' }}</p>
                            @if(isset($attempt->answers['admin_notes']) && $attempt->answers['admin_notes'])
                                <p><strong>Notes:</strong> {{ $attempt->answers['admin_notes'] }}</p>
                            @endif
                            <p class="mb-0"><small class="text-muted">This is a manually created passing record by an administrator.</small></p>
                        </div>
                    @endif

                    <!-- Detailed Answers (only for actual exam attempts, not manual passes) -->
                    @if(!isset($attempt->answers['manual_pass']) && is_array($attempt->answers) && count($attempt->answers) > 0)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-list-alt me-2"></i>
                                    Detailed Answers
                                </h5>
                            </div>
                            <div class="card-body">
                                @foreach($attempt->exam->questions as $index => $question)
                                    @php
                                        $userAnswer = $attempt->answers[$question->id] ?? null;
                                        // Ensure we're comparing strings
                                        $userAnswerString = is_string($userAnswer) ? $userAnswer : (is_array($userAnswer) ? json_encode($userAnswer) : (string)$userAnswer);
                                        $isCorrect = $userAnswerString === $question->correct_answer;
                                        $questionNumber = $index + 1;
                                    @endphp
                                    
                                    <div class="mb-4 pb-3 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0">
                                                <span class="badge bg-secondary text-white me-2">Q{{ $questionNumber }}</span>
                                                {{ $question->question_text }}
                                            </h6>
                                            <span class="badge bg-{{ $isCorrect ? 'success' : 'danger' }} text-white">
                                                {{ $isCorrect ? 'Correct' : 'Incorrect' }}
                                            </span>
                                        </div>
                                        
                                        @php
                                            $options = json_decode($question->options, true);
                                        @endphp
                                        
                                        @if(is_array($options))
                                            <div class="row">
                                                @foreach($options as $optionKey => $optionText)
                                                    <div class="col-md-6 mb-2">
                                                        <div class="p-2 rounded 
                                                            @if($optionKey === $question->correct_answer) 
                                                                bg-success text-white
                                                            @elseif($optionKey === $userAnswerString && !$isCorrect)
                                                                bg-danger text-white
                                                            @else
                                                                bg-light
                                                            @endif
                                                        ">
                                                            <strong>{{ strtoupper($optionKey) }}.</strong> {{ $optionText }}
                                                            
                                                            @if($optionKey === $question->correct_answer)
                                                                <i class="fas fa-check ms-2" title="Correct Answer"></i>
                                                            @endif
                                                            
                                                            @if($optionKey === $userAnswerString)
                                                                <i class="fas fa-arrow-left ms-2" title="Student's Answer"></i>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted">No options available for this question.</div>
                                        @endif
                                        
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <strong>Student's Answer:</strong> 
                                                @if($userAnswer)
                                                    @if(is_string($userAnswer))
                                                        {{ strtoupper($userAnswer) }} 
                                                        @if(isset($options[$userAnswer]))
                                                            - {{ $options[$userAnswer] }}
                                                        @endif
                                                    @else
                                                        {{ json_encode($userAnswer) }}
                                                    @endif
                                                @else
                                                    <em>Not answered</em>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif(!isset($attempt->answers['manual_pass']))
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            No detailed answers available for this attempt.
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.exam-attempts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to All Attempts
                        </a>
                        
                        <form action="{{ route('admin.exam-attempts.destroy', $attempt) }}" method="POST" class="d-inline ms-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this exam attempt? This action cannot be undone.')">
                                <i class="fas fa-trash me-1"></i>Delete Attempt
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-light {
    background-color: #f8f9fa !important;
    color: #6c757d !important;
}
.badge {
    font-size: 0.75em;
}
</style>
@endpush
