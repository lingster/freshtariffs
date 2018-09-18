@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <h3>Dashboard</h3>
    <hr />
    @if(Session::has('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
        <hr />
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Customers</h3>
                </div>
                <div class="panel-body">
                    You have {{ $customersCount }} customers.
                </div>
                <div class="panel-footer text-center">
                    <a class="btn btn-primary btn-sm" href="{{ URL::to('/staff/customers/create') }}">Create new customer</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title">Price lists</h3>
                </div>
                <div class="panel-body">
                    You have {{ $pricelistsCount }} price lists.
                </div>
                <div class="panel-footer text-center">
                    <a class="btn btn-pink btn-sm" href="{{ URL::to('/staff/pricelist/' . $user->user_id) }}">Create new price list</a>
                </div>
            </div>

        </div>
        <div class="col-md-4">

        </div>
    </div>

    <hr />
@endsection