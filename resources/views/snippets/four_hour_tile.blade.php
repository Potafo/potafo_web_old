<table class="table table-striped table-bordered">
    <tr>
        <th>ID</th>
        <th>Name</th>
    </tr>

    @foreach($staff_data as $data)
        <tr>
            <td>{{ $data['id'] }}</td>
            <td>{{ $data['name'] }}</td>
            <td>{{ $data['days'] }}</td>
        </tr>
    @endforeach

</table>