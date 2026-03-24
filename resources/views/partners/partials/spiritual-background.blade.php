<div class="public-divider mt-5 pt-5"></div>

<div class="public-section-heading mb-4">
    <div class="public-kicker">Step 2</div>
    <h3 class="mb-0 text-2xl font-semibold">Spiritual Background</h3>
    <p class="public-section-description mb-0">Help us understand your walk with Christ and your ministry formation.</p>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Are you born again?</label>
        <select class="form-select" name="born_again" required>
            <option value="">Select...</option>
            <option value="yes" @selected(old('born_again') === 'yes')>Yes</option>
            <option value="no" @selected(old('born_again') === 'no')>No</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Are you baptized in water?</label>
        <select class="form-select" name="water_baptized" required>
            <option value="">Select...</option>
            <option value="yes" @selected(old('water_baptized') === 'yes')>Yes</option>
            <option value="no" @selected(old('water_baptized') === 'no')>No</option>
        </select>
    </div>

    <div class="col-12 born-again-details d-none" aria-hidden="true">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">If yes, when?</label>
                <input type="date" class="form-control" name="salvation_date" value="{{ old('salvation_date') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Where?</label>
                <input type="text" class="form-control" name="salvation_place" value="{{ old('salvation_place') }}">
            </div>
        </div>
    </div>

    <div class="col-12 baptism-type-group d-none" aria-hidden="true">
        <label class="form-label fw-semibold">Baptism Type</label>
        <select class="form-select" name="baptism_type">
            <option value="">Select...</option>
            <option value="immersion" @selected(old('baptism_type') === 'immersion')>Immersion</option>
            <option value="sprinkling" @selected(old('baptism_type') === 'sprinkling')>Sprinkling</option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Are you baptized in the Holy Ghost with evidence of speaking in tongues?</label>
        <select class="form-select" name="holy_ghost_baptism" required>
            <option value="">Select...</option>
            <option value="yes" @selected(old('holy_ghost_baptism') === 'yes')>Yes</option>
            <option value="no" @selected(old('holy_ghost_baptism') === 'no')>No</option>
        </select>
    </div>

    <div class="col-12 holy-ghost-reason d-none" aria-hidden="true">
        <label class="form-label fw-semibold">If no, tell us why</label>
        <textarea class="form-control" name="holy_ghost_baptism_reason" rows="4">{{ old('holy_ghost_baptism_reason') }}</textarea>
    </div>
</div>
