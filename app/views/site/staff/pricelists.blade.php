@extends('layouts.master')
@section('title', 'Price Lists')

@section('content')
    <h3>Price Lists</h3>
    <p>At this page you can view history of sent price lists.</p>
    <hr />
    @if(Session::has('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
        <hr />
    @endif
    {{ Form::open() }}
    @if(count($pricelists) > 0)
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="col-xs-1">#</th>
                <th class="col-xs-4">Customer</th>
                <th class="col-xs-2">Type</th>
                <th class="col-xs-2">Created At</th>
                <th class="col-xs-2">Actions</th>
            </tr>
            </thead>
            <tbody id="newDestinationsContainer">
            @foreach($pricelists as $pricelist)
                <tr>
                    <td>{{ $pricelist->id }}</td>
                    <td>{{{ $pricelist->user->username }}}</td>
                    <td>{{{ $pricelist->price_type }}}</td>
                    <td>{{ $pricelist->created_at }}</td>
                    <td class="text-center"><a href="{{ URL::to('/pricelists/' . $pricelist->filename) }}" data-toggle="tooltip" alt="Download file" title="Download file"><i class="glyphicon glyphicon-download"></i></a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $pricelists->links() }}
    @else
        <p>No data to display.</p>
    @endif
    <hr />
@endsection