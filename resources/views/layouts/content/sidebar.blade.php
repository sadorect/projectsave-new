<div class="sidebar bg-dark text-white" style="width: 250px; min-height: 100vh;">
    <div class="p-3">
        <div class="text-center mb-4">
            <img src="{{ auth()->user()->avatar ?? asset('frontend/img/default-avatar.png') }}" 
                 alt="Profile" 
                 class="rounded-circle mb-3" 
                 style="width: 80px; height: 80px;">
            <h6 class="mb-0">{{ auth()->user()->name }}</h6>
            <small class="text-muted">Content Editor</small>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link text-white" href="{{ route('user.dashboard') }}">
                <i class="bi bi-house me-2"></i> User Dashboard
            </a>
            
            <a class="nav-link text-white" data-bs-toggle="collapse" href="#contentManagement">
                <i class="bi bi-pencil-square me-2"></i> Content Management
                <i class="bi bi-chevron-down float-end"></i>
            </a>
            
            <div class="collapse show" id="contentManagement">
                <nav class="nav flex-column ms-3">
                    <a class="nav-link text-white {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}" 
                       href="{{ route('admin.posts.index') }}">
                        <i class="bi bi-file-text me-2"></i> Posts
                    </a>
                    <!-- Other content management links -->
                </nav>
            </div>
        </nav>
    </div>
</div>
