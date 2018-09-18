@extends('layouts.master')
@section('title', 'Application settings')

@section('content')
    <h3>Application settings</h3>
    <p>At this page you can edit application settings.</p>
    <hr />
    @if(Session::has('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
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

    {{ Form::open() }}
    <div class="form-group @if($errors->has('footer_template')) has-error @endif">
        <label for="footer_template" class="control-block">E-mail footer template:</label>
        {{ Form::textarea('footer_template', $footer_template, ['class' => 'form-control', 'rows' => '10']) }}
        <span class="help-block">This text will be appended to all price lists letters.</span>
    </div>

    <hr />

    <div class="form-group @if($errors->has('default_email_subject')) has-error @endif">
        <label for="default_email_subject" class="control-block">Default e-mail subject:</label>
        {{ Form::text('default_email_subject', $default_email_subject, ['class' => 'form-control']) }}
        <span class="help-block">This e-mail subject will be used as default for new users.</span>
    </div>

    <div class="form-group @if($errors->has('default_email_template')) has-error @endif">
        <label for="default_email_template" class="control-block">Default e-mail template:</label>
        {{ Form::textarea('default_email_template', $default_email_template, ['class' => 'form-control', 'rows' => '10']) }}
        <span class="help-block">This e-mail template will be used as default for new users.</span>
    </div>

    <hr />

    <div class="form-group @if($errors->has('registered_email_subject')) has-error @endif">
        <label for="registered_email_subject" class="control-block">Registered user e-mail subject:</label>
        {{ Form::text('registered_email_subject', $registered_email_subject, ['class' => 'form-control']) }}
        <span class="help-block">This e-mail subject will be used in letters which sent after user creation.</span>
    </div>

    <div class="form-group @if($errors->has('registered_email_template')) has-error @endif">
        <label for="registered_email_template" class="control-block">Registered user e-mail template:</label>
        {{ Form::textarea('registered_email_template', $registered_email_template, ['class' => 'form-control', 'rows' => '10']) }}
        <span class="help-block">This e-mail template will be used in letters which sent after user creation.</span>
    </div>

    <hr />

    <button type="submit" class="btn btn-primary">Edit settings</button>

    {{ Form::close() }}
@endsection