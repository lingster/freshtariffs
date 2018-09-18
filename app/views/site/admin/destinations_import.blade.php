@extends('layouts.master')
@section('title', 'Import destinations from file')

@section('content')
    <h3>Import destinations from file</h3>
    <p> At this page you can upload destinations from file. </p>
    <hr />
    <p>
        File requirements:
        <ul>
            <li>Each destination must be at new line</li>
            <li>Line must contain values in following order: <code>Prefix | Destination | Interval</code></li>
            <li>You can use own separator (for example "," or ";" for CSV-files).<br />
                Note that separator can not be used in values, so avoid characters like "-", "/", " ".</li>
        </ul>
        Example of valid file: <br />
        <pre><code>20;Egypt - Fixed;0/1/1
2010;Egypt - Mobile - Vodafone;0/1/1
21377;Algeria - Mobile - Orascom;0/1/1
21378;Algeria - Mobile - Orascom;0/1/1
21379;Algeria - Mobile - Orascom;0/1/1
234703;Nigeria - Mobile - MTN;0/1/1
234706;Nigeria - Mobile - MTN;0/1/1</code></pre>
    </p>
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

    <hr />
    {{ Form::open(['files' => true]) }}
    <div class="form-group">
        <label for="file">Choose a file:</label>
        {{ Form::file('file', ['class' => 'form-control']) }}
    </div>

    <div class="form-group">
        <label for="separator">Choose a separator:</label>
        {{ Form::text('separator', ';', ['class' => 'form-control']) }}
        <span class="help-block">Must be 1 character.</span>
    </div>
    <hr />
    <input type="submit" value="Import" class="btn btn-primary">
    {{ Form::close() }}
@endsection