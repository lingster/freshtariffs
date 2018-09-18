@extends('layouts.master')
@section('title', 'Create a price list custom type')

@section('content')
    <h3>Create a price list custom type</h3>
    <p>At this page you can create a price list custom type.</p>
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
    <div class="form-group @if($errors->has('name')) has-error @endif">
        <label for="type" class="control-block">Type:</label>
        {{ Form::text('type', null, ['class' => 'form-control']) }}
    </div>

    <hr />
    <button type="submit" class="btn btn-primary">Create new type</button>

    {{ Form::close() }}
@endsection