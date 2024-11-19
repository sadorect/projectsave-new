<div class="notification-preview-card">
    <div class="card">
        <div class="card-body">
            <h6 class="card-title">{{ $notification['title'] }}</h6>
            <p class="card-text">{{ $notification['message'] }}</p>
            <div class="notification-meta">
                <small class="text-muted">
                    <i class="bi bi-calendar"></i> 
                    {{ \Carbon\Carbon::parse($notification['date'])->format('M d, Y') }}
                </small>
            </div>
        </div>
    </div>
</div>
