<div class="public-section-heading mb-4">
    <div class="public-kicker">Step 1</div>
    <h3 class="mb-0 text-2xl font-semibold">Personal Information</h3>
    <p class="public-section-description mb-0">Tell us who you are and how we can reach you.</p>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label fw-semibold">Name</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
    </div>

    <div class="col-md-6">
        <label for="dob" class="form-label fw-semibold">Date of Birth</label>
        <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob') }}" required>
    </div>

    <div class="col-md-6">
        <label for="profession" class="form-label fw-semibold">Profession</label>
        <input type="text" class="form-control" id="profession" name="profession" value="{{ old('profession') }}" required>
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label fw-semibold">Phone Number</label>
        <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
    </div>

    <div class="col-12">
        <label for="email" class="form-label fw-semibold">Email Address</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
    </div>
</div>
