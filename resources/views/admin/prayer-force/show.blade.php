@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Prayer Force Application Details</h3>
            <div>
              <form action="{{ route('admin.prayer-force.approve', $partner) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-sm btn-success">Approve</button>
            </form>
            
            <form action="{{ route('admin.prayer-force.reject', $partner) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
            </form>
                
              </div>
        <a href="{{ route('admin.prayer-force.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>Personal Information</h4>
                    <table class="table">
                        <tr>
                            <th>Name:</th>
                            <td>{{ $partner->name }}</td>
                        </tr>
                        <tr>
                            <th>Date of Birth:</th>
                            <td>{{ $partner->dob->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Profession:</th>
                            <td>{{ $partner->profession }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $partner->phone }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $partner->email }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h4>Spiritual Background</h4>
                    <table class="table">
                        <tr>
                            <th>Born Again:</th>
                            <td>{{ ucfirst($partner->born_again) }}</td>
                        </tr>
                        @if($partner->born_again === 'yes')
                        <tr>
                            <th>Salvation Date:</th>
                            <td>{{ $partner->salvation_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Salvation Place:</th>
                            <td>{{ $partner->salvation_place }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Water Baptized:</th>
                            <td>{{ ucfirst($partner->water_baptized) }}</td>
                        </tr>
                        @if($partner->water_baptized === 'yes')
                        <tr>
                            <th>Baptism Type:</th>
                            <td>{{ ucfirst($partner->baptism_type) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Holy Ghost Baptism:</th>
                            <td>{{ ucfirst($partner->holy_ghost_baptism) }}</td>
                        </tr>
                        @if($partner->holy_ghost_baptism === 'no')
                        <tr>
                            <th>Reason:</th>
                            <td>{{ $partner->holy_ghost_baptism_reason }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($partner->leadership_experience === 'yes')
            <div class="row mt-4">
                <div class="col-12">
                    <h4>Leadership Experience</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Church/Fellowship</th>
                                    <th>Position</th>
                                    <th>Year</th>
                                    <th>Referee</th>
                                    <th>Referee Contact</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($partner->leadership_details as $detail)
                                <tr>
                                    <td>{{ $detail['church_name'] }}</td>
                                    <td>{{ $detail['post_held'] }}</td>
                                    <td>{{ $detail['year'] }}</td>
                                    <td>{{ $detail['referee_name'] }}</td>
                                    <td>{{ $detail['referee_phone'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="row mt-4">
                <div class="col-md-6">
                    <h4>Additional Information</h4>
                    <table class="table">
                        <tr>
                            <th>Calling:</th>
                            <td>{{ $partner->calling }}</td>
                        </tr>
                        <tr>
                            <th>Prayer Commitment:</th>
                            <td>{{ ucfirst($partner->prayer_commitment) }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge badge-{{ $partner->status === 'pending' ? 'warning' : ($partner->status === 'approved' ? 'success' : 'danger') }}">
                                    {{ ucfirst($partner->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Application Date:</th>
                            <td>{{ $partner->created_at->format('M d, Y H:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
