@extends('layouts.master')
@section('title', 'Price List Custom Types')

@section('content')
    <h3>Price list custom types</h3>
    <p>Here is a list of price list custom types.</p>
    <hr />
    @if(Session::has('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
        <hr />
    @endif
    @if(count($custom_types) > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($custom_types as $custom_type)
                    <tr>
                        <td>{{ $custom_type->global_custom_type_id }}</td>
                        <td>{{{ $custom_type->value }}}</td>
                        <td class="text-center">
                            <a data-toggle="tooltip" href="{{ URL::to('/admin/customtypes/' . $custom_type->global_custom_type_id . '/edit') }}" alt="Edit type" title="Edit type"><i class="glyphicon glyphicon-edit"></i></a>
                            <a data-toggle="tooltip" href="{{ URL::to('/admin/customtypes/' . $custom_type->global_custom_type_id . '/delete') }}?_token={{ csrf_token() }}" alt="Delete type" title="Delete type" onclick="return confirm('Are you sure? This operation cannot be undone.');"><i class="glyphicon glyphicon-remove"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p>No data to display.</p>
    @endif
    <hr />
    <a href="{{ URL::to('/admin/customtypes/create') }}" class="btn btn-primary">Create new type</a>
@endsection