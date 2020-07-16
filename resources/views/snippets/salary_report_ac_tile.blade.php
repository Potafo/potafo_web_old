<table class="table table-striped table-bordered">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Final amount</th>
    </tr>

    @foreach($staff_data as $data)
        <tr>
            <td>{{ $data['user_id'] }}</td>
            <td>{{ $data['name'] }}</td>
            <td>{{ $data['total'] }}</td>
        </tr>
    @endforeach

</table>