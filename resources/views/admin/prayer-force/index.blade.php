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
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#leadershipModal{{ $partner->id }}">
                                      <a href="{{ route('admin.prayer-force.show', $partner) }}" class="btn btn-sm btn-info">View Details</a>
                                    </button>
                                @else
                                    N/A
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
                      <!-- Add notification channels here -->
                      <div class="notification-channels mb-2">
                          <div class="custom-control custom-checkbox">
                              <input type="checkbox" name="notify_via[]" value="mail" class="custom-control-input" id="email{{ $partner->id }}" checked>
                              <label class="custom-control-label" for="email{{ $partner->id }}">Email</label>
                          </div>
                          <div class="custom-control custom-checkbox">
                              <input type="checkbox" name="notify_via[]" value="twilio" class="custom-control-input" id="sms{{ $partner->id }}">
                              <label class="custom-control-label" for="sms{{ $partner->id }}">SMS (Twilio)</label>
                          </div>
                          <div class="custom-control custom-checkbox">
                              <input type="checkbox" name="notify_via[]" value="africas_talking" class="custom-control-input" id="at{{ $partner->id }}">
                              <label class="custom-control-label" for="at{{ $partner->id }}">SMS (Africa's Talking)</label>
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

               <!-- Add this after the table -->
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
                            <span class="badge badge-{{ $notification->data['status'] === 'approved' ? 'success' : 'danger' }}">
                                {{ ucfirst($notification->data['status']) }}
                            
                            </span>
              
                        </div>
                    @endforeach
                </div>
              </div>

  <!-- Leadership Details Modals -->
@foreach($partners as $partner)
    @if($partner->leadership_details)
    <div class="modal fade" id="leadershipModal{{ $partner->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Leadership Details - {{ $partner->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
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


