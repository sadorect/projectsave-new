<div class="section mb-4">
    <h5 class="border-bottom pb-2 text-primary">Commitment & Status</h5>
    <div class="card bg-light">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item mb-3">
                        <label class="text-muted">Partner Type</label>
                        <h6 class="mb-0">{{ ucfirst($partner->partner_type) }} Force</h6>
                    </div>
                    <div class="info-item">
                        <label class="text-muted">Calling</label>
                        <h6 class="mb-0">{{ $partner->calling }}</h6>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item mb-3">
                        <label class="text-muted">{{ $partner->commitment_question }}</label>
                        <h6 class="mb-0">{{ $partner->commitment_answer }}</h6>
                    </div>
                    <div class="info-item">
                        <label class="text-muted">Application Status</label>
                        <div class="mt-2">
                            <span class="badge badge-{{ $partner->status === 'pending' ? 'warning' : ($partner->status === 'approved' ? 'success' : 'danger') }} badge-lg">
                                {{ ucfirst($partner->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($partner->status === 'approved')
        <div class="alert alert-success mt-3">
            <h6><i class="fas fa-check-circle mr-2"></i>Next Steps</h6>
            <ul class="mb-0 mt-2">
                <li>Join our WhatsApp group</li>
                <li>Complete orientation</li>
                <li>Attend next meeting</li>
            </ul>
        </div>
    @endif
</div>
