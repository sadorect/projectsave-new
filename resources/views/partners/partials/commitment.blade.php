<div class="section-header mt-4">
    <h4>Commitment</h4>
</div>

<div class="form-group">
    <label>What is your calling?</label>
    <input type="text" class="form-control" name="calling" required>
</div>

<div class="form-group">
    <label>{{ $commitmentQuestion }}</label>
    @if($partnerType === 'skilled')
        <input type="text" class="form-control" name="commitment_answer" required>
    @else
        <select class="form-control" name="commitment_answer" required>
            <option value="">Select...</option>
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>
    @endif
</div>
