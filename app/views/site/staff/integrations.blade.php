@extends('layouts.master')
@section('title', 'Integrations')
@endsection
@section('content')
    <h3>Third Party Apps</h3>
    <p class ="lead">At this page you can modify existing cloud app connections or create new.</p>
    <hr />


        <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>Cloud Service</th>
                <th>Status</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
                    <tr>
                    <td>1</td>               
    			<td>Twilio</td>
			<td>Coming Soon</td>
			<td>Send SMS alerts or Voice Calls using this integration</td>
			<td><a <button type="button" class="btn btn-danger btn-sm">Connect</button></a></td>>

</tr>
		                 <tr>
	                    <td>1</td>               
                        <td>TelecomsXChange</td>
                        <td>Coming soon</td>
                        <td>Publish Price list right into your TelecomsXChange Account</td>
                        <td><a <button type="button" class="btn btn-danger btn-sm">Connect</button></a></td>>

</tr>

                                    <tr>
                    <td>1</td>               
                        <td>FreshBooks</td>
                        <td>Coming soon</td>
                        <td>Import FreshBooks Customers into FreshTariffs account.</td>
                        <td> <a <button type="button" class="btn btn-danger btn-sm">Connect</button></a> </td>>

</tr>



                  <tr>
                    <td>1</td>               
                        <td>Google Drive</td>
                        <td>Coming Soon</td>
                        <td>Save sent price lists right into your Google Drive Storage</td>
                        <td><a <button type="button" class="btn btn-danger btn-sm">Connect</button></a></td>>

</tr>


@endsection
