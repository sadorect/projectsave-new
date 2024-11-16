@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Partner Applications</h3>
            <div class="btn-group">
                <button type="button" class="btn btn-info" data-filter="all">All</button>
                <button type="button" class="btn btn-info" data-filter="prayer">Prayer Force</button>
                <button type="button" class="btn btn-info" data-filter="ground">Ground Force</button>
                <button type="button" class="btn btn-info" data-filter="skilled">Skilled Force</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Born Again</th>
                            <th>Leadership Experience</th>
                            <th>Status</th>
                            <th>Date Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partners as $partner)
                        <tr data-type="{{ $partner->partner_type }}">
                            <td>{{ $partner->name }}</td>
                            <td>{{ ucfirst($partner->partner_type) }} Force</td>
                            <td>{{ $partner->email }}</td>
                            <td>{{ $partner->phone }}</td>
                            <td>{{ $partner->born_again }}</td>
                            <td>{{ $partner->leadership_experience }}</td>
                            <td>
                                

                                <span style="color: {{ $partner->status === 'pending' ? '#000000' : ($partner->status === 'approved' ? '#28a745' : '#dc3545') }}"><strong>
                                  {{ ucfirst($partner->status) }}</strong>
                              </span>
                            </td>
                            <td>{{ $partner->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.partners.show', $partner) }}" class="btn btn-sm btn-info">View</a>
                                
                                @if($partner->status === 'pending')
                                <form action="{{ route('admin.partners.approve', $partner) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <div class="notification-channels mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="notify_via[]" value="mail" class="custom-control-input" id="email{{ $partner->id }}" checked>
                                            <label class="custom-control-label" for="email{{ $partner->id }}">Email</label>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="notify_via[]" value="twilio" class="custom-control-input" id="sms{{ $partner->id }}">
                                            <label class="custom-control-label" for="sms{{ $partner->id }}">SMS</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>

                                <form action="{{ route('admin.partners.reject', $partner) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
                            <p>Partner: {{ \App\Models\Partner::find($notification->data['partner_id'])->name }}</p>
                            <span class="badge badge-{{ $notification->data['status'] === 'approved' ? 'success' : 'danger' }}">
                                {{ ucfirst($notification->data['status']) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.btn-group button[data-filter]').click(function() {
        const filterValue = $(this).data('filter');
        
        if (filterValue === 'all') {
            $('tbody tr').show();
        } else {
            $('tbody tr').hide();
            $('tbody tr[data-type="' + filterValue + '"]').show();
        }
        
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
    });
});
</script>
@endpush
@endsection
