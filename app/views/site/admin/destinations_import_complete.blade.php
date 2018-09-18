@extends('layouts.master')
@section('title', 'Import destinations from file')

@section('content')
    <h3>Import destinations from file</h3>
    <p>At this page you can upload destinations from file.</p>
    <hr />
    <div class="alert alert-info">
        <strong>Import completed.</strong><br />
        Results of import:
        <ul>
            <li>Records imported: <strong>{{ $result['imported'] }}</strong></li>
            <li>Records ignored: <strong>{{ count($result['ignored']) }}</strong></li>
        </ul>
        @if(count($result['ignored']) > 0)
            <hr />
            <strong>Additional info:</strong>
            <ul>
            @foreach($result['ignored'] as $value)
                <li>Prefix "{{ $value['prefix'] }}" already exists at line {{ $value['line'] }}, ignored.</li>
            @endforeach
            </ul>
        @endif
    </div>
    <hr />
    <a class="btn btn-primary" href="{{ URL::to('/admin/destinations') }}">Go to destinations</a>
@endsection