<div class="row mb-4">
    <div class="col-md-12">
        <h4 class="border-bottom pb-2">Leadership Experience</h4>
        @if($partner->leadership_experience === 'yes' && $partner->leadership_details)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Church/Fellowship</th>
                            <th>Position</th>
                            <th>Year</th>
                            <th>Referee</th>
                            <th>Referee Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partner->leadership_details as $detail)
                        <tr>
                            <td>{{ $detail['church_name'] }}</td>
                            <td>{{ $detail['post_held'] }}</td>
                            <td>{{ $detail['year'] }}</td>
                            <td>{{ $detail['referee_name'] }}</td>
                            <td>{{ $detail['referee_phone'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>No leadership experience reported.</p>
        @endif
    </div>
</div>
