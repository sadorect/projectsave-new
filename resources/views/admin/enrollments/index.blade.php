<x-admin-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Enrollment Management</h2>
            <a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Enrollment
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
                                        <a href="{{ route('admin.enrollments.create', ['course' => $course->id]) }}" 
                                           class="btn btn-sm btn-primary">
                                            Add Student
                                        </a>
                                    </td>

                                    <td>
                                      <div class="btn-group">
                                          <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                              Status
                                          </button>
                                          <ul class="dropdown-menu">
                                              <li>
                                                  <form action="{{ route('admin.enrollments.status', [$course, $student]) }}" method="POST">
                                                      @csrf
                                                      @method('PATCH')
                                                      <input type="hidden" name="status" value="active">
                                                      <button type="submit" class="dropdown-item">Active</button>
                                                  </form>
                                              </li>
                                              <li>
                                                  <form action="{{ route('admin.enrollments.status', [$course, $student]) }}" method="POST">
                                                      @csrf
                                                      @method('PATCH')
                                                      <input type="hidden" name="status" value="suspended">
                                                      <button type="submit" class="dropdown-item">Suspend</button>
                                                  </form>
                                              </li>
                                              <li>
                                                  <form action="{{ route('admin.enrollments.status', [$course, $student]) }}" method="POST">
                                                      @csrf
                                                      @method('PATCH')
                                                      <input type="hidden" name="status" value="completed">
                                                      <button type="submit" class="dropdown-item">Mark Completed</button>
                                                  </form>
                                              </li>
                                          </ul>
                                      </div>
                                      
                                      <form action="{{ route('admin.enrollments.destroy', [$course, $student]) }}" method="POST" class="d-inline">
                                          @csrf
                                          @method('DELETE')
                                          <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                              <i class="fas fa-trash"></i>
                                          </button>
                                      </form>
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
                                                            <small class="text-muted">
                                                                Enrolled: {{ $student->pivot->created_at->format('M d, Y') }}
                                                            </small>
                                                        </li>
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
</x-admin-layout>
