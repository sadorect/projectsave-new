<div class="section mb-4">
    <h5 class="border-bottom pb-2">Personal Information</h5>
    <div class="row">
        <div class="col-md-6">
            <p><strong>Name:</strong> {{ $partner->name }}</p>
            <p><strong>Email:</strong> {{ $partner->email }}</p>
            <p><strong>Phone:</strong> {{ $partner->phone }}</p>
        </div>
        <div class="col-md-6">
            <p><strong>Date of Birth:</strong> {{ $partner->dob->format('M d, Y') }}</p>
            <p><strong>Profession:</strong> {{ $partner->profession }}</p>
            <p><strong>Application Date:</strong> {{ $partner->created_at->format('M d, Y') }}</p>
        </div>
    </div>
</div>
