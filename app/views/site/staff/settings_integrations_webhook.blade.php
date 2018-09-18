@extends('layouts.master')
@section('title', 'Integrations')

@section('content')
    <h3>Set up a webhook</h3>
    Enter URL to receive notifications. Read <a href="#" class="a-link">API documentation</a> for more information.
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
    <div class="form-group @if($errors->has('webhook_url')) has-error @endif">
        <label for="webhook_url" class="control-block">Webhook URL:</label>
        {{ Form::text('webhook_url', 'http://', ['class' => 'form-control']) }}
    </div>
    <hr />
    <button type="submit" class="btn btn-primary">Save settings</button>

    {{ Form::close() }}
@endsection