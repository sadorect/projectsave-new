@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h4>Preview Questions for: {{ $exam->title }}</h4>

    @if(count($questions))
        <form action="{{ route('admin.exams.import-confirm', $exam->id) }}" method="POST">
            @csrf
            <div class="alert alert-info">Total Questions to Import: <strong>{{ count($questions) }}</strong></div>

            <ul class="list-group mb-4">
                @foreach($questions as $index => $q)
                    <li class="list-group-item">
                        <strong>Q{{ $index + 1 }}:</strong> {{ $q['question'] }}
                        <ul class="mt-2">
                            @foreach($q['options'] as $key => $option)
                                <li>
                                    <strong>{{ $key }}.</strong>
                                    @if($q['answer'] === $key)
                                        <span class="text-success">{{ $option }} ✅</span>
                                    @else
                                        {{ $option }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>

            <button type="submit" class="btn btn-success">Confirm Import</button>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    @else
        <div class="alert alert-warning">No valid questions were parsed from the file.</div>
    @endif

    @if(count($skipped))
        <h5 class="mt-5">⚠️ Skipped Questions ({{ count($skipped) }})</h5>
        <ul class="list-group">
            @foreach($skipped as $skip)
                <li class="list-group-item text-muted">
                    <strong>Question:</strong> {{ $skip['question'] }}
                    <br>
                    <small>Reason: {{ $skip['reason'] }}</small>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
