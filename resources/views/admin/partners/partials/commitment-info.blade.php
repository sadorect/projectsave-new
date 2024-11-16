<div class="row mb-4">
    <div class="col-md-12">
        <h4 class="border-bottom pb-2">Commitment Information</h4>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th width="200">Partner Type:</th>
                    <td>{{ ucfirst($partner->partner_type) }} Force</td>
                </tr>
                <tr>
                    <th>Calling:</th>
                    <td>{{ $partner->calling }}</td>
                </tr>
                <tr>
                    <th>{{ $partner->commitment_question }}</th>
                    <td>{{ $partner->commitment_answer }}</td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td>
                        <span class="badge badge-{{ $partner->status === 'pending' ? 'warning' : ($partner->status === 'approved' ? 'success' : 'danger') }}">
                            {{ ucfirst($partner->status) }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
