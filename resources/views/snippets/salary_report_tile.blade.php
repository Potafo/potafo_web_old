<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>Date</th>
        <!-- <th>Login Time</th>
        <th>Logout TIme</th> -->
        <th style="width: 150px">Time splits</th>
        <th>Duration</th>
        <th>5 Star amount</th>
        <th>Normal Order count</th>
        <th>Normal Order amount</th>
        <th>Special order count</th>
        <th>Special order amount</th>
        <th>Bonus hours</th>
        <th>Bonus time amount</th>
        <th>Bonus amount</th>
        <th>Shortage amount</th>
        <th>Final amount</th>
    </tr>
    </thead>

    @foreach($staff_data as $data)
        <tr>
            <td><b>{{ $data['name'] }}</b></td>
        </tr>
        @foreach($data['data'] as $report)
        <tr>
            <td>{{ $report['date'] }}</td>
            <!-- <td>{{ $report['login_time'] }}</td>
                <td>{{ $report['logout_time'] }}</td> -->
            <td>{!! $report['time_splits'] !!}</td>
            <td>{{ $report['total_duration'] }}</td>
            <td>{{ $report['star_amount'] }}</td>
            <td>{{ $report['normal_order_count'] }}</td>
            <td>{{ $report['normal_order_earnings'] }}</td>
            <td>{{ $report['special_order_count'] }}</td>
            <td>{{ $report['special_order_earnings'] }}</td>
            <td>{{ $report['bonus_hour'] }}</td>
            <td>{{ $report['hour_bonus_amount'] }}</td>
            <td style="color:green;">{{ $report['extra_bonus'] }}</td>
            <td style="color:red;">{{ $report['shortage'] }}</td>
            <td>{{ $report['final_amount'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td><b></b></td>
            <td><b></b></td>
            <td><b></b></td>
            <td><b></b></td>
            <td><b></b></td>
            <td><b></b></td>
            <td><b></b></td>
            <td><b></b></td>
            <td><b></b></td>
            <td><b></b></td>
            <td><b>{{ $data['total'] }}</b></td>
        </tr>
    @endforeach

</table>