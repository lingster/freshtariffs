@extends('layouts.master')
@section('title', 'Create an account')

@section('content')
    <div class="col-xs-12">
        <h4>Create Free Account & Check your email inbox for access details.</h4>
        <hr />
        @if(Session::has('message'))
            <div class="alert alert-danger">{{ Session::get('message') }}</div>
            <hr />
        @endif
        @if($errors->has())
            <div class="alert alert-danger">
                <strong>There was an errors while submitting a form:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{ Form::open(['class' => 'form-horizontal']) }}
        <div class="form-group">
            <label class="col-sm-4 control-label" for="company">Company Name: </label>
            <div class="col-sm-5">
                {{ Form::text('company', null, ['class' => 'form-control', 'id' => 'company']) }}
            </div>
        </div>

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
            <button type="submit" class="btn btn-primary col-xs-4 col-xs-offset-4">Create an account</button>
        </div>
        {{ Form::close() }}

    </div>
@endsection
