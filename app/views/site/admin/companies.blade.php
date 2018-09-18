@extends('layouts.master')
@section('title', 'Companies')

@section('content')
    <h3>Companies</h3>
    <p>Here is a list of companies that are registered in the system.</p>
    <hr />
    @if(Session::has('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
        <hr />
    @endif
    @if(count($companies) > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Website</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($companies as $company)
                    <tr>
                        <td>{{ $company->company_id }}</td>
                        <td>{{{ $company->name }}}</td>
                        <td>{{{ $company->address }}}</td>
                        <td>{{{ $company->phone }}}</td>
                        <td>{{{ $company->website  }}}</td>
                        <td class="text-center">
                            <a data-toggle="tooltip" href="{{ URL::to('/admin/companies/' . $company->company_id . '/edit') }}" alt="Edit company" title="Edit company"><i class="glyphicon glyphicon-edit"></i></a>
                            <a data-toggle="tooltip" href="{{ URL::to('/admin/users/' . $company->company_id . '?staff=true') }}" alt="Manage staff" title="Manage staff"><i class="glyphicon glyphicon-briefcase"></i></a>
                            <a data-toggle="tooltip" href="{{ URL::to('/admin/users/' . $company->company_id) }}" alt="Manage customers" title="Manage customers"><i class="glyphicon glyphicon-user"></i></a>
                            <a data-toggle="tooltip" href="{{ URL::to('/admin/companies/' . $company->company_id . '/delete') }}?_token={{ csrf_token() }}" alt="Delete company" title="Delete company" onclick="return confirm('Are you sure? This operation cannot be undone.');"><i class="glyphicon glyphicon-remove"></i></a>
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
    <a href="{{ URL::to('/admin/companies/create') }}" class="btn btn-primary">Create new company</a>
@endsection