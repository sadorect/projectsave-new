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
                    @foreach($exam->attempts->groupBy('user_id') as $userId => $attempts)
                        <tr>
                            <td>{{ $attempts->first()->user->name }}</td>
                            <td>{{ $attempts->count() }}/{{ $attempt->exam->max_attempts }}</td>
                            <td>{{ $attempts->last()->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
@endsection