@extends('layouts.master')
@section('title', '404')

@section('content')
    <h3>404</h3>
    <p>The page are you looking for is not found.</p>
    <hr />
    <a class="btn btn-primary" href="{{ URL::to('/') }}">Go to main page</a>
@endsection