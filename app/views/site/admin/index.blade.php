@extends('layouts.master')
@section('title', 'Admin Panel')

@section('content')
    <h3>Welcome to Admin Panel</h3>
    <hr />
    <div class="list-group">
        <a href="{{ URL::to('/admin/companies') }}" class="list-group-item">Manage companies</a>
        <a href="{{ URL::to('/admin/destinations') }}" class="list-group-item">Manage destinations</a>
        <a href="{{ URL::to('/admin/customtypes') }}" class="list-group-item">Manage price lists custom types</a>
        <a href="{{ URL::to('/admin/settings') }}" class="list-group-item">Application settings</a>
    </div>
@endsection