@extends('layouts.master')
@section('title', 'Log In')

@section('content')
    <div class="col-xs-12">
        <h3>Log in</h3>
        <hr />
        @if(Session::has('message'))
            <div class="alert alert-danger">{{ Session::get('message') }}</div>
            <hr />
        @endif
        {{ Form::open(['class' => 'form-horizontal']) }}
        @if(isset($companyId))
            {{ Form::hidden('company_id', $companyId) }}
        @endif
        <div class="form-group">
            <label class="col-sm-4 control-label" for="email">E-Mail: </label>
            <div class="col-sm-5">
                {{ Form::email('email', null, ['class' => 'form-control', 'id' => 'email']) }}
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label" for="password">Password: </label>
            <div class="col-sm-5">
                {{ Form::password('password', ['class' => 'form-control', 'id' => 'password']) }}
            </div>
        </div>

        <hr />
        <div class="row">
            <button type="submit" class="btn btn-primary col-xs-4 col-xs-offset-4">Login</button>
        </div>
        <hr />
        <div class="row">
            <div class="col-xs-4 col-xs-offset-4">
                Don't have an account? <a href="{{ URL::to('/users/create') }}" class="a-link">Click here to create new.</a>
            </div>
        </div>
        {{ Form::close() }}

    </div>
@endsection
