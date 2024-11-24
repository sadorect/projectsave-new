<div class="sidebar bg-dark text-white" style="width: 250px; min-height: 100vh;">
    <div class="p-3">
        <div class="text-center mb-4">
            <img src="{{ auth()->user()->avatar ?? asset('frontend/img/default-avatar.png') }}" 
                 alt="Profile" 
                 class="rounded-circle mb-3" 
                 style="width: 80px; height: 80px;">
            <h6 class="mb-0">{{ auth()->user()->name }}</h6>
            <small class="text-muted">Member</small>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link text-white {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" 
               href="{{ route('user.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
                    <a class="nav-link text-white {{ request()->routeIs('user.profile') ? 'active' : '' }}" 
                                    href="{{ route('user.profile') }}">
                        <i class="bi bi-person me-2"></i> My Profile
                    </a>

                    <!-- Add to sidebar menu -->
            <li class="nav-item">
              <a href="{{ route('user.account.deletion') }}" class="nav-link">
                  <i class="bi bi-trash"></i>
                  <span>Delete Account</span>
              </a>
            </li>
            
            <a class="nav-link text-white {{ request()->routeIs('user.partnerships.*') ? 'active' : '' }}" 
               href="#partnerships">
                <i class="bi bi-people me-2"></i> Partnerships
            </a>
            
            <a class="nav-link text-white {{ request()->routeIs('user.notifications.*') ? 'active' : '' }}" 
               href="#notifications">
                <i class="bi bi-bell me-2"></i> Notifications
            </a>
            
            <a class="nav-link text-white {{ request()->routeIs('user.settings.*') ? 'active' : '' }}" 
               href="#settings">
                <i class="bi bi-gear me-2"></i> Settings
            </a>
        </nav>

        <div class="mt-auto pt-3 border-top">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light w-100">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </div>

    </div>
</div>