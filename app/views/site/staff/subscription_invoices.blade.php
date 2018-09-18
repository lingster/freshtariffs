@extends('layouts.master')
@section('title', 'Invoices')

@section('content')
    <h3>Invoices</h3>
    <p></p>
    <hr />
    @if(count($invoices) > 0)
        <table class="table table-bordered table-responsive">
        <thead>
        <tr>
            <th class="col-xs-7">ID</th>
            <th class="col-xs-3">Date</th>
            <th class="col-xs-2">Download</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoices as $invoice)
            <tr>
                <td>{{ $invoice->id }}</td>
                <td>{{ $invoice->dateString() }}</td>
                <td>
                    <a href="{{ URL::to('/staff/subscription/invoices/' . $invoice->id . '/download') }}">
                        <i class="glyphicon glyphicon-download-alt"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @else
        <p>No data to show.</p>
    @endif
@endsection