@extends('layouts.master')
@section('title', 'Edit company settings')

@section('content')
    <h3>Edit company settings</h3>
    <p>
        At this page you can create edit your company settings. <br />
        To integrate third-party services, visit <a class="a-link" href="{{ URL::to('/staff/settings/integrations') }}">integrations page</a>.
    </p>
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
    <div class="form-group @if($errors->has('name')) has-error @endif">
        <label for="name" class="control-block">Company name:</label>
        {{ Form::text('name', $company->name, ['class' => 'form-control']) }}
    </div>

    <div class="form-group @if($errors->has('email')) has-error @endif">
        <label for="email" class="control-block">Company e-mail:</label>
        {{ Form::email('email', $company->email, ['class' => 'form-control']) }}
    </div>

    <div class="form-group @if($errors->has('phone')) has-error @endif">
        <label for="phone" class="control-block">Company phone:</label>
        {{ Form::text('phone', $company->phone, ['class' => 'form-control']) }}
    </div>

    <div class="form-group @if($errors->has('website')) has-error @endif">
        <label for="website" class="control-block">Company website:</label>
        {{ Form::text('website', $company->website, ['class' => 'form-control']) }}
    </div>

    <div class="form-group @if($errors->has('address')) has-error @endif">
        <label for="address" class="control-block">Company address:</label>
        {{ Form::textarea('address', $company->address, ['class' => 'form-control', 'rows' => '3']) }}
    </div>

    <div class="form-group @if($errors->has('date_format')) has-error @endif">
        <label for="address" class="control-block">Date format:</label>
        {{ Form::select('date_format', Company::getDateFormats(), $company->date_format, ['class' => 'form-control', 'rows' => '3']) }}
    </div>

    <div class="form-group @if($errors->has('')) has-error @endif">
        <label for="email_subject" class="control-block">E-Mail subject:</label>
        {{ Form::text('email_subject', $company->email_subject, ['class' => 'form-control']) }}
        <span class="help-block">
            You can use variables in subject of e-mail.
            <ul>
                <li><strong>{username}</strong> - customer name</li>
                <li><strong>{company}</strong> - company name</li>
                <li><strong>{list-type}</strong> - price list type</li>
                <li><strong>{date}</strong> - current date</li>
            </ul>
        </span>
    </div>

    <div class="form-group @if($errors->has('email_template')) has-error @endif">
        <label for="email_template" class="control-block">E-mail template:</label>
        {{ Form::textarea('email_template', $company->email_template, ['class' => 'form-control', 'rows' => '10']) }}
        <span class="help-block">
            You can use HTML tags and variables in body of e-mail.
            <ul>
                <li><strong>{username}</strong> - customer name</li>
                <li><strong>{company}</strong> - company name</li>
                <li><strong>{phone}</strong> - company phone</li>
                <li><strong>{address}</strong> - company address</li>
                <li><strong>{date}</strong> - current date</li>
                <li><strong>{note}</strong> - note for price list</li>
            </ul>
        </span>
    </div>

    <div class="form-group @if($errors->has('email_reply_to')) has-error @endif">
        <label for="email_reply_to" class="control-label">E-mail reply to:</label>
        {{ Form::text('email_reply_to', $company->email_reply_to, ['class' => 'form-control']) }}
        <span class="help-block">Replies to new price list letter will be sent to this address.</span>
    </div>

    <hr />
    <button type="submit" class="btn btn-primary">Edit company</button>

    {{ Form::close() }}
@endsection