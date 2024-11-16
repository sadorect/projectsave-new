@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>{{ ucfirst($partner->partner_type) }} Force Application Details</h3>
            <div>
                <a href="{{ route('admin.partners.index') }}" class="btn btn-secondary">Back to List</a>
                @if($partner->status === 'pending')
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#approveModal">
                        Approve Application
                    </button>
                @endif
            </div>
        </div>
        
        <div class="card-body">
            @include('admin.partners.partials.personal-info')
            @include('admin.partners.partials.spiritual-info')
            @include('admin.partners.partials.leadership-info')
            @include('admin.partners.partials.commitment-info')
        </div>
    </div>
</div>

@include('admin.partners.partials.approve-modal')
@endsection
