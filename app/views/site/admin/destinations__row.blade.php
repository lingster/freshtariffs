<tr>
    <td>
        {{ Form::text('prefix_' . $destination->destination_id, $destination->prefix, ['class' => 'form-control']) }}
    </td>
    <td>
        {{ Form::text('country_' . $destination->destination_id, $destination->country, ['class' => 'form-control']) }}
    </td>
    <td>
        {{ Form::text('networkname_' . $destination->destination_id, $destination->network_name, ['class' => 'form-control']) }}
    </td>
    <td>
        {{ Form::text('interval_' . $destination->destination_id, $destination->interval, ['class' => 'form-control']) }}
    </td>
</tr>