<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px; min-height: 100vh;">
    <a href="{{ route('home') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <span class="fs-4">You're Here!</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard
            </a>
        </li>
      <li>
          <a href="{{ route('admin.posts.index') }}" class="nav-link text-white {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
              <i class="bi bi-file-text me-2"></i>
              Posts
          </a>
      </li>
      <li>
          <a href="{{ route('admin.events.index') }}" class="nav-link text-white {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
              <i class="bi bi-calendar-event me-2"></i>
              Events
          </a>
      </li>
      <!-- Add this within your existing sidebar navigation -->
    <li class="nav-item">
        <a href="{{ route('admin.faqs.index') }}" class="nav-link text-white {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-question-circle"></i>
            <p>FAQs</p>
        </a>
    </li>
      <li>
          <a href="{{ route('admin.categories.index') }}" class="nav-link text-white {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
              <i class="bi bi-folder me-2"></i>
              Categories
          </a>
      </li>
      <li>
          <a href="{{ route('admin.tags.index') }}" class="nav-link text-white {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
              <i class="bi bi-tags me-2"></i>
              Tags
          </a>
      </li>
      <li>
        <a href="{{ route('news.index') }}" class="nav-link text-white {{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
            <i class="bi bi-newspaper me-2"></i>
            News Updates
        </a>
    </li>

    <li>
        <a href="{{ route('videos.index') }}" class="nav-link text-white {{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
            <i class="bi bi-camera-video me-2"></i>
            Video Reels
        </a>
    </li>

      <li>
          <!-- Update the users link in the sidebar -->
      <a href="{{ route('admin.users.index') }}" class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="bi bi-people me-2"></i>
        Users
      </a>

            <a href="{{ route('admin.sessions.index') }}" class="nav-link text-white {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                Sessions (Logged in)
            </a>

                            @if(auth()->check() && auth()->user()->is_admin)
                            <a href="{{ route('admin.audit.index') }}" class="nav-link text-white {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
                                <i class="bi bi-clipboard-data me-2"></i>
                                Audit Log
                            </a>
                            @endif

      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.celebrations.*') ? 'active' : '' }}" 
           href="#celebrationsSubmenu" 
           data-bs-toggle="collapse">
            <i class="bi bi-gift"></i>
            <span>Celebrations</span>
        </a>
        <div class="collapse {{ request()->routeIs('admin.celebrations.*') ? 'show' : '' }}" 
             id="celebrationsSubmenu">
            <ul class="nav flex-column pl-3">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.celebrations.calendar') }}">
                        <i class="bi bi-calendar-event"></i> Calendar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.celebrations.statistics') }}">
                        <i class="bi bi-graph-up"></i> Statistics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.celebrations.logs') }}">
                        <i class="bi bi-journal-text"></i> Wish Logs
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
            <a class="nav-link text-white" data-bs-toggle="collapse" href="#fileManagement" role="button" aria-expanded="false" aria-controls="fileManagement">
                <i class="bi bi-folder me-2"></i>
                File Management
            </a>
            <div class="collapse" id="fileManagement">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin.files.index') }}">
                            <i class="bi bi-file-earmark me-2"></i> All Files
                        </a>
                    </li>
                    
                </ul>
            </div>
        </li>
    
    <li class="nav-item">
        <a class="nav-link text-white {{ request()->routeIs('admin.forms.*') ? 'bg-primary' : '' }}" data-bs-toggle="collapse" href="#formsManagement" role="button" aria-expanded="{{ request()->routeIs('admin.forms.*') ? 'true' : 'false' }}" aria-controls="formsManagement">
            <i class="fas fa-clipboard-list me-2"></i>
            Forms Management
            @php
                $pendingSubmissions = \App\Models\FormSubmission::whereDate('created_at', today())->count();
            @endphp
            @if($pendingSubmissions > 0)
                <span class="badge bg-success ms-1">{{ $pendingSubmissions }}</span>
            @endif
        </a>
        <div class="collapse {{ request()->routeIs('admin.forms.*') || request()->routeIs('admin.submissions.*') ? 'show' : '' }}" id="formsManagement">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('admin.forms.index') ? 'active' : '' }}" href="{{ route('admin.forms.index') }}">
                        <i class="fas fa-list me-2"></i> All Forms
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('admin.forms.create') ? 'active' : '' }}" href="{{ route('admin.forms.create') }}">
                        <i class="fas fa-plus me-2"></i> Create Form
                    </a>
                </li>

            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link text-white" data-bs-toggle="collapse" href="#lms" role="button" aria-expanded="false" aria-controls="lms">
            <i class="bi bi-mortarboard me-2"></i>
            LMS Management
        </a>
        <div class="collapse" id="lms">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('admin.courses.index') }}">
                        <i class="bi bi-book me-2"></i> Courses
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('admin.lessons.index') }}">
                        <i class="bi bi-journal-text me-2"></i> Lessons
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('admin.enrollments.index') }}">
                        <i class="bi bi-people me-2"></i> Enrollments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('admin.exams.index') }}">
                        <i class="bi bi-pencil-square me-2"></i> Exams
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('admin.exam-attempts.index') }}">
                        <i class="bi bi-clock-history me-2"></i> Exam Attempts
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ route('admin.certificates.index') }}">
                        <i class="bi bi-award me-2"></i> Certificates
                        @if(\App\Models\Certificate::where('is_approved', false)->whereNull('approved_at')->count() > 0)
                            <span class="badge bg-warning ms-1">
                                {{ \App\Models\Certificate::where('is_approved', false)->whereNull('approved_at')->count() }}
                            </span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>
    </li>
    
    <li class="nav-item">
        <a href="#mailSubmenu" data-bs-toggle="collapse" class="nav-link">
            <i class="bi bi-envelope"></i>
            <span>Mail System</span>
        </a>
        <div class="collapse {{ request()->routeIs('admin.mail.*') ? 'show' : '' }}" id="mailSubmenu">
            <ul class="nav flex-column pl-3">
                <li class="nav-item">
                    <a href="{{ route('admin.mail.compose') }}" class="nav-link text-white">
                        <i class="bi bi-pencil-square me-2"></i>
                        Compose Mail
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.mail-templates.index') }}" class="nav-link text-white">
                        <i class="bi bi-file-text me-2"></i>
                        Mail Templates
                    </a>
                </li>
            </ul>
        </div>
    </li>
    
    <li>
        <a href="{{route('admin.partners.index')}}" class="nav-link text-white">
            <i class="bi bi-raised-hands me-2"></i>
            Partners ( <span style="color: yellow">{{ App\Models\Partner::where('status', 'pending')->count() }} </span> )
        </a>
        
    </li>
      </li>
        <li>
            <a href="#settingsSubmenu" class="nav-link text-white" data-bs-toggle="collapse">
                <i class="bi bi-gear me-2"></i>
                Settings
            </a>
            <div class="collapse {{ request()->routeIs('admin.notification-settings.*') ? 'show' : '' }}" id="settingsSubmenu">
                <ul class="nav flex-column pl-3">
                    <li class="nav-item">
                        <a href="{{ route('admin.notification-settings.edit') }}" class="nav-link text-white {{ request()->routeIs('admin.notification-settings.*') ? 'active' : '' }}">
                            <i class="bi bi-bell me-2"></i>
                            Notification Settings
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <div class="nav-item">
            <a href="{{ route('admin.deletion-requests.index') }}" class="nav-link">
                <i class="bi bi-trash"></i>
                <span>Deletion Requests</span>
                @if($pendingDeletions = \App\Models\DeletionRequest::where('status', 'pending')->count())
                    <span class="badge bg-danger">{{ $pendingDeletions }}</span>
                @endif
            </a>
        </div>
    </ul>




