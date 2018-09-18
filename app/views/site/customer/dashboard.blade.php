@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')
    <h3>Dashboard</h3>
    <hr />
    @if(Session::has('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
        <hr />
    @endif
    @if($latestPriceFilename)
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Price list</h3>
                    </div>
                    <div class="panel-body">
                        Click "Download" button to download your latest price list.
                    </div>
                    <div class="panel-footer text-center">
                        <a class="btn btn-primary btn-sm" target="_blank" href="{{ URL::to('/pricelists/' . $latestPriceFilename) }}">Download</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">There are no price lists for you yet. Contact your manager if you think this is an error.</div>
    @endif

    <hr />

@endsection