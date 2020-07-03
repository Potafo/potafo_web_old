<!doctype html>
<html lang="en-US">
<head>
</head>

<body>
<table id="keywords"  class="tablesorter"  cellspacing="0" cellpadding="0">
    <thead>
    <tr>
        @if(count($columns)>0)
            @foreach($columns as $key=>$column)
                @if($key!=0)
                    <th width="20px"><span>{{ $column->column_name }}</span></th>
                @endif
            @endforeach
        @endif   
    
    </tr>
    
    </thead>
     
    <tbody>
    <tbody>
</table>
</body>
</html>
