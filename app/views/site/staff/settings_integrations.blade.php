@extends('layouts.master')
@section('title', 'Integrations')

@section('content')
    <h3>Integrations</h3>
    <p>At this page you can integrate your account with third-party services.</p>
    <hr />
    @if(Session::has('message'))
        <div class="alert alert-info">{{{ Session::get('message') }}}</div>
        <hr />
    @endif
    
    @if(Session::has('error'))
        <div class="alert alert-warning">{{{ Session::get('error') }}}</div>
        <hr />
    @endif
    
    @if(count($services) > 0)
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Service</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($services as $service)
                <tr>
                    <td class="col-lg-2" style="line-height: 50px">
                        <img src="{{ $service->logo }}" style="max-width: 50px; max-height: 50px;" />
                        <strong style="margin-left: 10px"> {{ $service->name }}</strong>
                    </td>
                    <td class="col-lg-6">
                        {{ $service->description }}
                    </td>
                    <td class="col-lg-4">
                        @if ($service->isIntegrated($user->company->company_id))
                            <p>
                                Connected: <br />
                                <strong>{{{ $service->getIntegration($user->company->company_id)->account }}}</strong>.
                                <br />
                                <a class="btn btn-primary btn-sm" href="{{ URL::to('/staff/settings/integrations/disconnect/' . $service->service_id) }}?_token={{ csrf_token() }}">Disconnect</a>
                                @if($service->settings_url)
                                    <a class="btn btn-primary btn-sm" href="{{ URL::to($service->settings_url) }}">Settings</a>
                                @endif
                            </p>
                        @else
                            <p>
                                Not connected. <br />
                                <a class="btn btn-primary btn-sm" href="{{ URL::to('/staff/settings/integrations/connect/' . $service->service_id) }}?_token={{ csrf_token() }}">Connect</a>
                            </p>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>No data to show.</p>
    @endif
@endsection