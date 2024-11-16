@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Dashboard</h1>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text display-6">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Active Users</h5>
                    <p class="card-text display-6">{{ $stats['active_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">New Users Today</h5>
                    <p class="card-text display-6">{{ $stats['new_users_today'] }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Pending Partners</h5>
                    <p class="card-text display-6">{{ $stats['pending_partners'] }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Recent Activity Section -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Recent Activity
                </div>
                <div class="card-body">
                    @if($recent_activity->count() > 0)
                        <div class="list-group">
                            @foreach($recent_activity as $activity)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $activity->title }}</h6>
                                        <small>{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">Created by {{ $activity->author}}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p>No recent activity</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Quick Actions
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">Add New User</a>
                        <a href="#" class="list-group-item list-group-item-action">View Reports</a>
                        <a href="#" class="list-group-item list-group-item-action">System Settings</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection