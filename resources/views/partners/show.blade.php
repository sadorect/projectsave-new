<x-layouts.app>
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>{{ ucfirst($partnerType) }} Force Application Status</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Application Details</h4>
                        <span class="badge badge-{{ $partner->status === 'pending' ? 'warning' : ($partner->status === 'approved' ? 'success' : 'danger') }}">
                            {{ ucfirst($partner->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        @include('partners.partials.show.personal-info')
                        @include('partners.partials.show.spiritual-info')
                        @include('partners.partials.show.leadership-info')
                        @include('partners.partials.show.commitment-info')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
