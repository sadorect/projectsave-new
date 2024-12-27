@extends('admin.layouts.app')

@section('content')
<div class="card mt-4">
    <div class="card-header">
        <h5>Attempt Settings</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                @foreach($attempts as $attempt)
                <p>Maximum Attempts: {{ $attempt->exam->max_attempts }}</p>
                <p>Allow Retakes: {{ $attempt->exam->allow_retakes ? 'Yes' : 'No' }}</p>
            @endforeach
            </div>
            <div class="col-md-6">
                <h6>Student Attempts</h6>
                <table class="table">
                    <tr>
                        <th>Student</th>
                        <th>Attempts Used</th>
                        <th>Last Attempt</th>
                    </tr>
                    @foreach($attempts as $attempt)
                    <tr>
                        <td>{{ $attempt->user->name }}</td>
                        <td>{{ \App\Models\ExamAttempt::forUserAndExam($attempt->user_id, $attempt->exam_id)->count() }}/{{ $attempt->exam->max_attempts }}</td>
                        <td>{{ $attempt->created_at->format('M d, Y') }}</td>
                        <td>
                            <form action="{{ route('admin.exams.reset-attempts', ['exam' => $attempt->exam, 'user' => $attempt->user]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm">Reset</button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                </table>
            </div>
        </div>
    </div>
</div>
<div class="card mt-4">
    <div class="card-header">
        <h5>Attempt Management</h5>
    </div>
    <div class="card-body">
        @if($attempts->count() > 0)
        <form action="{{ route('admin.exams.reset-attempts', $attempts->first()->exam) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-warning" onclick="return confirm('Reset attempts for ALL students?')">
                Reset All Attempts
            </button>
        </form>
    @endif
    

        @foreach($attempts->groupBy('user_id') as $userId => $userAttempts)
        <div class="mt-3">
            <span>{{ $userAttempts->first()->user->name }}</span>
            <form action="{{ route('admin.exams.reset-attempts', ['exam' => $userAttempts->first()->exam, 'user' => $userId]) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-warning">Reset</button>
            </form>
        </div>
    @endforeach

    </div>
</div>

@endsection