<div class="section mb-4">
    <h5 class="border-bottom pb-2 text-primary">Leadership Experience</h5>
    @if($partner->leadership_experience === 'yes' && $partner->leadership_details)
        <div class="leadership-timeline">
            @foreach($partner->leadership_details as $detail)
                <div class="card mb-3 border-left border-primary">
                    <div class="card-body">
                        <h6 class="card-title text-primary">{{ $detail['church_name'] }}</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><i class="fas fa-user-tie mr-2"></i>{{ $detail['post_held'] }}</p>
                                <p class="mb-1"><i class="fas fa-calendar-alt mr-2"></i>{{ $detail['year'] }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><i class="fas fa-user mr-2"></i>{{ $detail['referee_name'] }}</p>
                                <p class="mb-1"><i class="fas fa-phone mr-2"></i>{{ $detail['referee_phone'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>No leadership experience reported
        </div>
    @endif
</div>
