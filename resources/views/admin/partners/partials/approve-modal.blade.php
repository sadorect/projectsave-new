<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.partners.approve', $partner) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p>Select notification methods:</p>
                    <div class="notification-channels">
                        <div class="form-check">
                            <input type="checkbox" name="notify_via[]" value="mail" class="form-check-input" id="emailNotify" checked>
                            <label class="form-check-label" for="emailNotify">Email</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="notify_via[]" value="twilio" class="form-check-input" id="smsNotify">
                            <label class="form-check-label" for="smsNotify">SMS</label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>
