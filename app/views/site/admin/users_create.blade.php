@extends('layouts.master')
@section('title', 'Create user')

@section('content')
    <h3>Create user</h3>
    <p>At this page you can create a new user.</p>
    <hr />
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

    {{ Form::open() }}
    <div class="form-group">
        <label for="name" class="control-block">Company name:</label>
        <p class="form-control-static">{{ $company->name }}</p>
    </div>

    <div class="form-group @if($errors->has('username')) has-error @endif">
        <label for="username" class="control-block">User full name:</label>
        {{ Form::text('username', null, ['class' => 'form-control']) }}
    </div>

    <div class="form-group @if($errors->has('email')) has-error @endif">
        <label for="email" class="control-block">User e-mail:</label>
        {{ Form::email('email', null, ['class' => 'form-control']) }}
        <span class="help-block">Will be used for sending e-mails and login.</span>
    </div>

    <div class="form-group @if($errors->has('email_cc')) has-error @endif">
        <label for="email" class="control-block">User e-mail CC:</label>
        {{ Form::email('email_cc', null, ['class' => 'form-control']) }}
        <span class="help-block">All price lists will be also sent to this e-mail.</span>
    </div>

    <div class="form-group @if($errors->has('email_bcc')) has-error @endif">
        <label for="email" class="control-block">User e-mail BCC:</label>
        {{ Form::email('email_bcc', null, ['class' => 'form-control']) }}
        <span class="help-block">Same as e-mail CC, but recipient will not know another e-mail.</span>
    </div>


    <div class="form-group @if($errors->has('password')) has-error @endif">
        <label for="password" class="control-block">User password:</label>
        {{ Form::password('password', ['class' => 'form-control']) }}
        <span class="help-block">Temporary, can be changed by user after login</span>
    </div>

    <div class="form-group @if($errors->has('role')) has-error @endif">
        <label for="role" class="control-block">Role:</label>
        {{
            Form::select('role',
                [
                    User::ROLE_ADMIN => 'Administrator',
                    User::ROLE_COMPANY => 'Manager (staff)',
                    User::ROLE_CUSTOMER => 'Customer'
                ],
                Input::has('staff') ? User::ROLE_COMPANY : User::ROLE_CUSTOMER, // value by default
                ['class' => 'form-control']
            )
        }}
    </div>


    <hr />
    <button type="submit" class="btn btn-primary">Create new user</button>

    {{ Form::close() }}
@endsection