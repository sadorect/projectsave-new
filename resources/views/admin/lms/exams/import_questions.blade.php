@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h3>Import Questions for: {{ $exam->title }}</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('skipped'))
        <div class="alert alert-danger">
            @if(is_array(session('skipped')))
                <ul class="mb-0">
                    @foreach(session('skipped') as $skipped)
                        @if(is_array($skipped))
                            <li>
                                @foreach($skipped as $item)
                                    {{ is_array($item) ? json_encode($item) : (is_object($item) ? json_encode($item) : $item) }},
                                @endforeach
                            </li>
                        @elseif(is_object($skipped))
                            <li>{{ json_encode($skipped) }}</li>
                        @else
                            <li>{{ $skipped }}</li>
                        @endif
                    @endforeach
                </ul>
            @else
                {{ session('skipped') }}
            @endif
        </div>
    @endif
    <form action="{{ route('admin.exams.import-preview', $exam->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="docx_file" class="form-label">Upload DOCX File</label>
            <input type="file" name="docx_file" id="docx_file" class="form-control" accept=".docx" required>
        </div>

        <button class="btn btn-primary" type="submit">Import Questions</button>
    </form>

    <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary mt-3">Back to Exams</a>
</div>
@endsection
