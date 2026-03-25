<div class="public-divider mt-5 pt-5"></div>

<div class="public-section-heading mb-4">
    <div class="public-kicker">Step 3</div>
    <h3 class="mb-0 text-2xl font-semibold">Leadership Experience</h3>
    <p class="public-section-description mb-0">Share any leadership responsibility you have held in church or fellowship settings.</p>
</div>

<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold">Have you held any leadership position in the church or fellowship?</label>
        <select class="form-select" name="leadership_experience" required>
            <option value="">Select...</option>
            <option value="yes" @selected(old('leadership_experience') === 'yes')>Yes</option>
            <option value="no" @selected(old('leadership_experience') === 'no')>No</option>
        </select>
    </div>
</div>

<div id="leadershipDetails" class="d-none mt-4" aria-hidden="true">
    <div class="leadership-entry public-card p-4 position-relative">
        <button type="button" class="remove-leadership-entry btn btn-sm btn-outline-danger position-absolute d-none"
                style="top: 0.75rem; right: 0.75rem;" aria-label="Remove this entry">
            <i class="bi bi-trash"></i> Remove
        </button>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Name of Church or Fellowship</label>
                <input type="text" class="form-control" name="church_name[]">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Post Held</label>
                <input type="text" class="form-control" name="post_held[]">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Year</label>
                <input type="text" class="form-control" name="leadership_year[]">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Name of Referee</label>
                <input type="text" class="form-control" name="referee_name[]">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Phone Number of Referee</label>
                <input type="tel" class="form-control" name="referee_phone[]">
            </div>
        </div>
    </div>

    <button type="button" class="surface-button-secondary mt-3" id="addMoreLeadership">Add More Experience</button>
</div>
