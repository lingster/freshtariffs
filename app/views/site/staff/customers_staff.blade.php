@extends('layouts.master')
@section('title', 'Customers')

@section('content')
    <h3>Customers</h3>
    <p>At this page you can modify existing customers or create new.</p>
    <hr />
    @if(Session::has('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
        <hr />
    @endif

    @if(count($users) > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>E-Mail</th>
                    <th>Last login</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $userInfo)
                    <tr>
                        <td>{{ $userInfo->user_id }}</td>
                        <td>{{{ $userInfo->username }}}</td>
                        <td>{{{ $userInfo->email }}}</td>
                        <td>{{ $userInfo->last_login ? $userInfo->last_login : '(not logged in)' }}</td>
                        <td class="text-center">
                            <a data-toggle="tooltip" href="{{ URL::to('/staff/customers/' . $userInfo->user_id . '/edit') }}" alt="Edit user" title="Edit user"><i class="glyphicon glyphicon-edit"></i></a>
                            <a data-toggle="tooltip" href="{{ URL::to('/admin/price/' . $userInfo->user_id) }}" alt="Create price list" title="Create price list"><i class="glyphicon glyphicon-list-alt"></i></a>
                            <a data-toggle="tooltip" href="{{ URL::to('/staff/customers/' . $userInfo->user_id . '/delete') }}?_token={{ csrf_token() }}" alt="Delete user" title="Delete user" onclick="return confirm('Are you sure? This operation cannot be undone.');"><i class="glyphicon glyphicon-remove"></i></a>
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
    <a class="btn btn-primary" href="{{ URL::to('/staff/customers/create') }}">Create new customer</a>
@endsection