@extends('admin.layouts.app')

@section('title', 'Celebration Statistics')

@section('content')
<div class="container-fluid">
    <!-- Monthly Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Monthly Celebration Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Celebrations This Month</h6>
                    <h2>{{ $monthlyStats->where('month', now()->month)->sum('count') }}</h2>
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
                    <h2>{{ $upcomingCelebrations->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6>Active Well-wishers</h6>
                    <h2>{{ $topWellwishers->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

       <!-- Upcoming Celebrations -->
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
                              @foreach($upcomingCelebrations as $celebration)
                              <tr>
                                  <td>{{ $celebration->name }}</td>
                                  <td>
                                      @if($celebration->birthday && Carbon\Carbon::parse($celebration->birthday)->format('m-d') >= now()->format('m-d'))
                                          🎂 Birthday
                                      @elseif($celebration->wedding_anniversary)
                                          💑 Wedding Anniversary
                                      @endif
                                  </td>
                                  <td>{{ $celebration->next_celebration_date ? $celebration->next_celebration_date->format('M d, Y') : 'N/A' }}</td>
                                  <td>{{ $celebration->next_celebration_date ? $celebration->next_celebration_date->diffInDays(now()) : 'N/A' }} days</td>
                              </tr>
                          @endforeach
                          
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Well-wishers -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Top Well-wishers</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($topWellwishers as $wisher)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $wisher->sender->name }}
                                <span class="badge bg-primary rounded-pill">{{ $wisher->wishes_count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Celebrations per Month',
                data: @json($monthlyStats->pluck('count')),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush
@endsection
