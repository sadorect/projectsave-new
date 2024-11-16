<div class="section-header mt-4">
    <h4>Leadership Experience</h4>
</div>

<div class="form-group">
    <label>Have you held any leadership position in the church/fellowship?</label>
    <select class="form-control" name="leadership_experience" required>
        <option value="">Select...</option>
        <option value="yes">Yes</option>
        <option value="no">No</option>
    </select>
</div>
<div id="leadershipDetails" style="display: none;">
    <div class="leadership-entry card mb-3">
        <div class="card-body">
            <div class="form-group">
                <label>Name of Church/Fellowship</label>
                <input type="text" class="form-control" name="church_name[]">
            </div>
            <div class="form-group">
                <label>Post Held</label>
                <input type="text" class="form-control" name="post_held[]">
            </div>
            <div class="form-group">
                <label>Year</label>
                <input type="text" class="form-control" name="leadership_year[]">
            </div>
            <div class="form-group">
                <label>Name of Referee</label>
                <input type="text" class="form-control" name="referee_name[]">
            </div>
            <div class="form-group">
                <label>Phone Number of Referee</label>
                <input type="tel" class="form-control" name="referee_phone[]">
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-secondary" id="addMoreLeadership">Add More Experience</button>
</div>
