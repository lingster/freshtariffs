@extends('layouts.master')
@section('title', 'Integrations')

@section('content')
    <h3>Import contacts from Freshbooks</h3>
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
    {{ Form::hidden('import', 'true') }}
    Click this button to start importing process. <br />
    <hr />
    <button type="submit" class="btn btn-primary">Import contacts</button>
    {{ Form::close() }}
@endsection