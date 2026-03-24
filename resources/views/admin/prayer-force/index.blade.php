@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Prayer Force Applications</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Born Again</th>
                            <th>Leadership Experience</th>
                            <th>Leadership Details</th>
                            <th>Status</th>
                            <th>Date Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partners as $partner)
                            <tr>
                                <td>{{ $partner->name }}</td>
                                <td>{{ $partner->email }}</td>
                                <td>{{ $partner->phone }}</td>
                                <td>{{ $partner->born_again }}</td>
                                <td>{{ $partner->leadership_experience }}</td>
                                <td>
                                    @if($partner->leadership_details)
                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="{{ route('admin.prayer-force.show', $partner) }}" class="btn btn-sm btn-info">View Details</a>
                                            <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#leadershipModal{{ $partner->id }}">
                                                Leadership
                                            </button>
                                        </div>
                                    @else
                                        <a href="{{ route('admin.prayer-force.show', $partner) }}" class="btn btn-sm btn-info">View Details</a>
                                    @endif
                                </td>
                                <td>
                                    <span style="color: {{ $partner->status === 'pending' ? '#000000' : ($partner->status === 'approved' ? '#28a745' : '#dc3545') }}">
                                        {{ ucfirst($partner->status) }}
                                    </span>
                                </td>
                                <td>{{ $partner->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.prayer-force.approve', $partner) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <div class="notification-channels mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" name="notify_via[]" value="mail" class="form-check-input" id="email{{ $partner->id }}" checked>
                                                <label class="form-check-label" for="email{{ $partner->id }}">Email</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="notify_via[]" value="twilio" class="form-check-input" id="sms{{ $partner->id }}">
                                                <label class="form-check-label" for="sms{{ $partner->id }}">SMS (Twilio)</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="notify_via[]" value="africas_talking" class="form-check-input" id="at{{ $partner->id }}">
                                                <label class="form-check-label" for="at{{ $partner->id }}">SMS (Africa's Talking)</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>

                                    <form action="{{ route('admin.prayer-force.reject', $partner) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<div class="notifications-panel mt-4">
    <h4>Recent Notifications</h4>
    <div class="list-group">
        @foreach(auth()->user()->notifications as $notification)
            <div class="list-group-item">
                <div class="d-flex justify-content-between">
                    <h6>{{ $notification->data['message'] }}</h6>
                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                </div>
                <p>Partner: {{ \App\Models\PrayerForcePartner::find($notification->data['partner_id'])->name }}</p>
                <span class="badge bg-{{ $notification->data['status'] === 'approved' ? 'success' : 'danger' }}">
                    {{ ucfirst($notification->data['status']) }}
                </span>
            </div>
        @endforeach
    </div>
</div>

@foreach($partners as $partner)
    @if($partner->leadership_details)
        <div class="modal fade" id="leadershipModal{{ $partner->id }}" tabindex="-1" aria-labelledby="leadershipModalLabel{{ $partner->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="leadershipModalLabel{{ $partner->id }}">Leadership Details - {{ $partner->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @foreach($partner->leadership_details as $detail)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p><strong>Church:</strong> {{ $detail['church_name'] }}</p>
                                    <p><strong>Position:</strong> {{ $detail['post_held'] }}</p>
                                    <p><strong>Year:</strong> {{ $detail['year'] }}</p>
                                    <p><strong>Referee:</strong> {{ $detail['referee_name'] }}</p>
                                    <p><strong>Referee Phone:</strong> {{ $detail['referee_phone'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection
