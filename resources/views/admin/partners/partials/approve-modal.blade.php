<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.partners.approve', $partner) }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="modal-header">
                    <h5 class="modal-title">Approve Application</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                
                <div class="modal-body">
                    <p>Select notification methods:</p>
                    <div class="notification-channels">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="notify_via[]" value="mail" class="custom-control-input" id="emailNotify" checked>
                            <label class="custom-control-label" for="emailNotify">Email</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="notify_via[]" value="twilio" class="custom-control-input" id="smsNotify">
                            <label class="custom-control-label" for="smsNotify">SMS</label>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>
