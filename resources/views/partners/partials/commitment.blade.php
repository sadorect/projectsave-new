<div class="public-divider mt-5 pt-5"></div>

<div class="public-section-heading mb-4">
    <div class="public-kicker">Step 4</div>
    <h3 class="mb-0 text-2xl font-semibold">Commitment</h3>
    <p class="public-section-description mb-0">Tell us how you sense God leading you to serve through Projectsave.</p>
</div>

<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold">What is your calling?</label>
        <input type="text" class="form-control" name="calling" value="{{ old('calling') }}" required>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">{{ $commitmentQuestion }}</label>
        @if($partnerType === 'skilled')
            <input type="text" class="form-control" name="commitment_answer" value="{{ old('commitment_answer') }}" required>
        @else
            <select class="form-select" name="commitment_answer" required>
                <option value="">Select...</option>
                <option value="yes" @selected(old('commitment_answer') === 'yes')>Yes</option>
                <option value="no" @selected(old('commitment_answer') === 'no')>No</option>
            </select>
        @endif
    </div>
</div>
