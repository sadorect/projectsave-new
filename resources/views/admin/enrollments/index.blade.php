@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Course Enrollments</h2>
        <a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Enrollment
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Instructor</th>
                            <th>Enrolled Students</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enrollments as $course)
                            <tr>
                                <td>{{ $course->title }}</td>
                                <td>{{ $course->instructor->name }}</td>
                                <td>
                                    <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#studentsModal{{ $course->id }}">
                                        {{ $course->users->count() }} Students
                                    </button>
                                </td>
                                <td>
                                    <a href="{{ route('admin.enrollments.create', ['course_id' => $course->id]) }}" 
                                       class="btn btn-sm btn-primary">
                                        Add Student
                                    </a>
                                </td>
                            </tr>

                                                     <!-- Students Modal -->
                            <div class="modal fade" id="studentsModal{{ $course->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Enrolled Students - {{ $course->title }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="list-group">
                                                @foreach($course->users as $student)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                      {{ $student->name }}
                                                      <form action="{{ route('admin.enrollments.destroy', ['course' => $course->id, 'user' => $student->id]) }}" 
                                                            method="POST" class="d-inline">
                                                          @csrf
                                                          @method('DELETE')
                                                          <button type="submit" class="btn btn-sm btn-danger" 
                                                                  onclick="return confirm('Are you sure you want to remove this student?')">
                                                              Remove
                                                          </button>
                                                      </form>
                                                      <div class="btn-group ms-2">
                                                          <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                              {{ ucfirst($student->pivot->status) }}
                                                          </button>
                                                          <ul class="dropdown-menu">
                                                              <li>
                                                                  <form action="{{ route('admin.enrollments.status', ['course' => $course->id, 'user' => $student->id]) }}" method="POST">
                                                                      @csrf
                                                                      @method('PATCH')
                                                                      <input type="hidden" name="status" value="active">
                                                                      <button type="submit" class="dropdown-item">Active</button>
                                                                  </form>
                                                              </li>
                                                              <li>
                                                                  <form action="{{ route('admin.enrollments.status', ['course' => $course->id, 'user' => $student->id]) }}" method="POST">
                                                                      @csrf
                                                                      @method('PATCH')
                                                                      <input type="hidden" name="status" value="completed">
                                                                      <button type="submit" class="dropdown-item">Completed</button>
                                                                  </form>
                                                              </li>
                                                              <li>
                                                                  <form action="{{ route('admin.enrollments.status', ['course' => $course->id, 'user' => $student->id]) }}" method="POST">
                                                                      @csrf
                                                                      @method('PATCH')
                                                                      <input type="hidden" name="status" value="suspended">
                                                                      <button type="submit" class="dropdown-item">Suspended</button>
                                                                  </form>
                                                              </li>
                                                          </ul>
                                                      </div>                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $enrollments->links() }}
        </div>
    </div>
</div>
@endsection