@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Exam Management</h2>
        <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Create New Exam
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Course</th>
                            <th>Duration</th>
                            <th>Questions</th>
                            <th>Passing Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($exams as $exam)
                        <tr>
                            <td>{{ $exam->title }}</td>
                            <td>{{ $exam->course->title }}</td>
                            <td>{{ $exam->duration_minutes }} mins</td>
                            <td>{{ $exam->questions->count() }}</td>
                            <td>{{ $exam->passing_score }}%</td>
                            <td>
                              <a href="{{ route('admin.exams.preview', $exam) }}" class="btn btn-sm btn-info">
                                  <i class="bi bi-eye"></i>
                              </a>
                              <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-sm btn-primary">
                                  <i class="bi bi-pencil"></i>
                              </a>
                              <a href="{{ route('admin.questions.create', $exam) }}" class="btn btn-sm btn-success">
                                  <i class="bi bi-plus"></i> Questions
                              </a>
                              <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this exam and all its questions?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                          </td>
                          
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $exams->links() }}
        </div>
    </div>
</div>
@endsection
