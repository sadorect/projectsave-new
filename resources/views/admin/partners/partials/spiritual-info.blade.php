<div class="row mb-4">
    <div class="col-md-12">
        <h4 class="border-bottom pb-2">Spiritual Background</h4>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th width="200">Born Again:</th>
                    <td>{{ ucfirst($partner->born_again) }}</td>
                    @if($partner->born_again === 'yes')
                        <th width="200">Salvation Date:</th>
                        <td>{{ $partner->salvation_date->format('M d, Y') }}</td>
                    @endif
                </tr>
                @if($partner->born_again === 'yes')
                <tr>
                    <th>Salvation Place:</th>
                    <td colspan="3">{{ $partner->salvation_place }}</td>
                </tr>
                @endif
                <tr>
                    <th>Water Baptized:</th>
                    <td>{{ ucfirst($partner->water_baptized) }}</td>
                    @if($partner->water_baptized === 'yes')
                        <th>Baptism Type:</th>
                        <td>{{ ucfirst($partner->baptism_type) }}</td>
                    @endif
                </tr>
                <tr>
                    <th>Holy Ghost Baptism:</th>
                    <td>{{ ucfirst($partner->holy_ghost_baptism) }}</td>
                    @if($partner->holy_ghost_baptism === 'no')
                        <th>Reason:</th>
                        <td>{{ $partner->holy_ghost_baptism_reason }}</td>
                    @endif
                </tr>
            </table>
        </div>
    </div>
</div>
