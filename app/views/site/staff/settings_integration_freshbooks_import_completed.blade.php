@extends('layouts.master')
@section('title', 'Integrations')

@section('content')
    <h3>Import contacts from Freshbooks</h3>
    <hr />
    <div class="alert alert-success">Import completed.</div>
    <hr />
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Username</th>
            <th>E-Mail</th>
            <th>Status</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody>
        @foreach($importResults['success'] as $result)
            <tr>
                <td>{{{ $result->username }}}</td>
                <td>{{{ $result->email }}}</td>
                <td><span style="color: green; font-weight: bold;">Success</span></td>
                <td>User imported successfully. Password for account sent by e-mail.</td>
            </tr>
        @endforeach
        @foreach($importResults['failed'] as $result)
            <tr>
                <td>{{{ $result->username }}}</td>
                <td>{{{ $result->email }}}</td>
                <td><span style="color: red; font-weight: bold;">Error</span></td>
                <td>Account is not imported: e-mail already exists.</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection