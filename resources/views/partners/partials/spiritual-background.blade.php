<div class="section-header mt-4">
    <h4>Spiritual Background</h4>
</div>

<div class="form-group">
    <label>Are you born-again?</label>
    <select class="form-control" name="born_again" required>
        <option value="">Select...</option>
        <option value="yes">Yes</option>
        <option value="no">No</option>
    </select>
</div>

<div class="born-again-details" style="display: none;">
    <div class="form-group">
        <label>If yes, when?</label>
        <input type="date" class="form-control" name="salvation_date">
    </div>
    <div class="form-group">
        <label>Where?</label>
        <input type="text" class="form-control" name="salvation_place">
    </div>
</div>

<div class="form-group">
    <label>Are you baptized in water?</label>
    <select class="form-control" name="water_baptized" required>
        <option value="">Select...</option>
        <option value="yes">Yes</option>
        <option value="no">No</option>
    </select>
</div>

<div class="baptism-type-group form-group" style="display: none;">
    <label>Baptism Type</label>
    <select class="form-control" name="baptism_type">
        <option value="">Select...</option>
        <option value="immersion">Immersion</option>
        <option value="sprinkling">Sprinkling</option>
    </select>
</div>

<div class="form-group">
    <label>Are you baptized in the Holy Ghost with evidence of speaking in tongues?</label>
    <select class="form-control" name="holy_ghost_baptism" required>
        <option value="">Select...</option>
        <option value="yes">Yes</option>
        <option value="no">No</option>
    </select>
</div>

<div class="holy-ghost-reason form-group" style="display: none;">
    <label>If No, why?</label>
    <textarea class="form-control" name="holy_ghost_baptism_reason" rows="3"></textarea>
</div>
