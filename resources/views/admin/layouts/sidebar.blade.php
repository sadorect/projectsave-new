<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px; min-height: 100vh;">
    <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <span class="fs-4">Admin Panel</span>
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

      </li>
        <li>
            <a href="#" class="nav-link text-white">
                <i class="bi bi-gear me-2"></i>
                Settings
            </a>
        </li>

        <li>
            <a href="{{route('admin.partners.index')}}" class="nav-link text-white">
                <i class="bi bi-raised-hands me-2"></i>
                Prayer Force > {{ App\Models\Partner::where('status', 'pending')->count() }}
            </a>
            
        </li>

        <li>
            <a href="{{ route('admin.notification-settings.edit') }}" class="nav-link text-white">
                <i class="bi bi-bell me-2"></i>
                Notification Settings 
            </a>
            
        </li>

    </ul>