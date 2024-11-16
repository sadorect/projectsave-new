@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Notification Channel Settings</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.notification-settings.update') }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="row">
                    <div class="col-md-6">
                        <h4>Email Settings</h4>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="emailEnabled" name="channels[email][enabled]" checked>
                                <label class="custom-control-label" for="emailEnabled">Enable Email Notifications</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h4>SMS Settings</h4>
                        <div class="form-group">
                            <label>SMS Provider</label>
                            <select name="sms_provider" class="form-control">
                                <option value="twilio">Twilio</option>
                                <option value="africas_talking">Africa's Talking</option>
                            </select>
                        </div>
                        
                        <div id="twilioSettings">
                            <div class="form-group">
                                <label>Twilio Account SID</label>
                                <input type="text" name="twilio_account_sid" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Twilio Auth Token</label>
                                <input type="password" name="twilio_auth_token" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Twilio From Number</label>
                                <input type="text" name="twilio_from" class="form-control">
                            </div>
                        </div>

                        <div id="africasTalkingSettings" style="display: none;">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="at_username" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>API Key</label>
                                <input type="password" name="at_api_key" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Sender ID</label>
                                <input type="text" name="at_from" class="form-control">
                            </div>
                        </div>
                    </div>


                <!-- Add this section after the existing SMS settings -->
                <div class="card mt-4">
                  <div class="card-header">
                      <h5>Event Reminders</h5>
                  </div>
                  <div class="card-body">
                      <a href="{{ route('admin.notification-settings.event-reminders') }}" 
                        class="btn btn-primary">
                          Manage Event Reminders
                      </a>
                  </div>
                </div>

                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('select[name="sms_provider"]').change(function() {
        if ($(this).val() === 'twilio') {
            $('#twilioSettings').show();
            $('#africasTalkingSettings').hide();
        } else {
            $('#twilioSettings').hide();
            $('#africasTalkingSettings').show();
        }
    });
});
</script>
@endpush
@endsection
