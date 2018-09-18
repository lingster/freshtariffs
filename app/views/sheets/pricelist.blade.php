<html>

<tr>
    <td>{{ $type }} Price List from {{ $date }} for {{ $username }}</td>
</tr>
<tr>
    <td>Your technical prefix: (none)</td>
</tr>

<tr></tr>
<tr></tr>
<tr></tr>

<tr>
    <td><strong>Prefix</strong></td>
    <td><strong>Destination</strong></td>
    <td><strong>Rate</strong></td>
    <td><strong>Effective date</strong></td>
    <td><strong>Interval</strong></td>
    <td><strong>Comment</strong></td>
</tr>

<!--
By some reason PHPExcel does not recognize pixel values and use only numeric part of it.
To change widths, you need to resize excel column, see width and round it (see screenshot: http://)
-->
@foreach($pricelist as $line)
    <tr>
        <td style="width: 9px">{{ $line['prefix'] }}</td>
        <td style="width: 33px">{{ $line['destination'] }}</td>
        <td style="width: 9px">{{ $line['rate'] }}</td>
        <td style="width: 26px">{{ $line['effective_date'] }}</td>
        <td style="width: 9px">{{ $line['interval'] }}</td>
        <td style="width: 10px">{{ $line['comment'] }}</td>
    </tr>
@endforeach
</html>