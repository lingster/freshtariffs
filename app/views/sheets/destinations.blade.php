<html>

<tr>
    <td>Destinations export from {{ $date }}</td>
</tr>

<tr></tr>
<tr></tr>
<tr></tr>

<tr>
    <td><strong>Prefix</strong></td>
    <td><strong>Country</strong></td>
    <td><strong>Network Name</strong></td>
    <td><strong>Interval</strong></td>
</tr>

<!--
By some reason PHPExcel does not recognize pixel values and use only numeric part of it.
To change widths, you need to resize excel column, see width and round it (see screenshot: http://)
-->
@foreach($destinations as $line)
    <tr>
        <td style="width: 9">{{ $line['prefix'] }}</td>
        <td style="width: 33">{{ $line['country'] }}</td>
        <td style="width: 33">{{ $line['network_name'] }}</td>
        <td style="width: 9">{{ $line['interval'] }}</td>
    </tr>
@endforeach
</html>