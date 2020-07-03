<!doctype html>
<html lang="en-US">
<head>
</head>

<body>
<table id="keywords"  class="tablesorter"  cellspacing="0" cellpadding="0">
    <thead>
    <tr>
                    <th width="20px"><span>Name</span></th>
                    <th width="20px"><span>Category</span></th>
                    <th width="20px"><span>Sub_category</span></th>
                    <th width="20px"><span>Diet</span></th>
                    <th width="20px"><span>Portion</span></th>
                    <th width="20px"><span>Rate</span></th>
                    <th width="20px"><span>Packing_Charge</span></th>
    </tr>
    </thead>
    <tbody>
    @if(count($menus)>0)
        @foreach($menus as $item)
    <tr>
        <td>{{$item->name}}</td>
        <td>{{ json_decode($item->category) }}</td>
        <td>{{json_decode($item->subcategory)}}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @endforeach
    @endif
    <tbody>
</table>
</body>
</html>
