@extends('admin.layouts.app')

@section('content')
<div class="container">
  <h1 class="mb-4">Submissions List</h1>

  @if(isset($submissions) && $submissions->count())
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Form</th>
          <th>Submitted At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($submissions as $submission)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $submission->user->name ?? 'N/A' }}</td>
            <td>{{ $submission->form->title ?? 'N/A' }}</td>
            <td>{{ $submission->created_at->format('Y-m-d H:i') }}</td>
            <td>
              <a href="{{ route('admin.submissions.show', $submission->id) }}" class="btn btn-sm btn-primary">View</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <div class="alert alert-info">
      No submissions found.
    </div>
  @endif
</div>
@endsection