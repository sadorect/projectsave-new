@extends('admin.layouts.app')

@section('title', 'Celebration Statistics')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Monthly Celebration Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas
                        id="monthlyChart"
                        height="100"
                        data-celebration-chart
                        data-chart-labels='@json($monthlyChartLabels)'
                        data-chart-values='@json($monthlyChartValues)'
                    ></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Celebrations This Month</h6>
                    <h2>{{ $currentMonthCelebrationTotal }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Response Rate</h6>
                    <h2>{{ number_format($responseMetrics['rate'], 1) }}%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6>Upcoming (30 days)</h6>
                    <h2>{{ $upcomingCelebrationCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Active Well-wishers</h6>
                    <h2>{{ $activeWellwishersCount }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Upcoming Celebrations</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Days Until</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingCelebrations as $celebration)
                                    <tr>
                                        <td>{{ $celebration->name }}</td>
                                        <td>{{ $celebration->celebration_type_label }}</td>
                                        <td>{{ $celebration->next_celebration_date?->format('M d, Y') ?? 'N/A' }}</td>
                                        <td>{{ $celebration->days_until ?? 'N/A' }} days</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No upcoming celebrations in the next 30 days.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Top Well-wishers</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($topWellwishers as $wisher)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $wisher->sender->name }}
                                <span class="badge bg-primary rounded-pill">{{ $wisher->wishes_count }}</span>
                            </div>
                        @empty
                            <div class="list-group-item text-muted">No wish activity recorded yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
