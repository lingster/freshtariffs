@extends('layouts.master')
@section('title', 'Integrations')

@section('content')
    <h3>Set up FreshBooks</h3>
    Enter API URL and Authentication Token for your FreshBooks account. <br />
    You can find this values in <strong>My Account -> FreshBooks API</strong> section in FreshBooks panel.
    <hr />
    @if(Session::has('message'))
        <div class="alert alert-info">{{{ Session::get('message') }}}</div>
        <hr />
    @endif

    @if(Session::has('error'))
        <div class="alert alert-warning">{{{ Session::get('error') }}}</div>
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
    <div class="form-group @if($errors->has('url')) has-error @endif">
        <label for="url" class="control-block">URL:</label>
        {{ Form::text('url', 'https://test.freshbooks.com', ['class' => 'form-control']) }}
    </div>

    <div class="form-group @if($errors->has('token')) has-error @endif">
        <label for="token" class="control-block">Authentication Token:</label>
        {{ Form::text('token', '', ['class' => 'form-control']) }}
    </div>

    <hr />
    <button type="submit" class="btn btn-primary">Save settings</button>

    {{ Form::close() }}
@endsection