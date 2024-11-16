<div class="section mb-4">
    <h5 class="border-bottom pb-2 text-primary">Spiritual Background</h5>
    <div class="card bg-light">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item mb-3">
                        <label class="text-muted">Born Again Status</label>
                        <p class="mb-1"><i class="fas fa-check-circle text-success mr-2"></i>{{ ucfirst($partner->born_again) }}</p>
                        @if($partner->born_again === 'yes')
                            <small class="text-muted">Since: {{ $partner->salvation_date->format('M d, Y') }}</small>
                            <p class="mt-1"><small>At: {{ $partner->salvation_place }}</small></p>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item mb-3">
                        <label class="text-muted">Water Baptism</label>
                        <p class="mb-1"><i class="fas fa-water text-info mr-2"></i>{{ ucfirst($partner->water_baptized) }}</p>
                        @if($partner->water_baptized === 'yes')
                            <small class="text-muted">Type: {{ ucfirst($partner->baptism_type) }}</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="info-item">
                <label class="text-muted">Holy Ghost Baptism</label>
                <p class="mb-1">
                    <i class="fas fa-fire text-warning mr-2"></i>{{ ucfirst($partner->holy_ghost_baptism) }}
                </p>
                @if($partner->holy_ghost_baptism === 'no')
                    <div class="mt-2 p-2 bg-white rounded">
                        <small>{{ $partner->holy_ghost_baptism_reason }}</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
