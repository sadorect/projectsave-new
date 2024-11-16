<div class="row mb-4">
    <div class="col-md-12">
        <h4 class="border-bottom pb-2">Personal Information</h4>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th width="200">Name:</th>
                    <td>{{ $partner->name }}</td>
                    <th width="200">Date of Birth:</th>
                    <td>{{ $partner->dob->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td>{{ $partner->email }}</td>
                    <th>Phone:</th>
                    <td>{{ $partner->phone }}</td>
                </tr>
                <tr>
                    <th>Profession:</th>
                    <td>{{ $partner->profession }}</td>
                    <th>Application Date:</th>
                    <td>{{ $partner->created_at->format('M d, Y H:i A') }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
