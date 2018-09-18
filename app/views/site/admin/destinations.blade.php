@extends('layouts.master')
@section('title', 'Destinations')

@section('content')
    <h3>Destinations</h3>
    <p>
        At this page you can modify existing destinations or create new.<br />
        You can also <a class="a-link" href="/admin/destinations/download">download all destinations as .csv file.</a>
    </p>
    <hr />
    @if(Session::has('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
        <hr />
    @endif

    {{ Form::open(['url' => '/admin/destinations/search', 'method' => 'GET']) }}
    <div class="form-group">
        <label for="">Enter prefix, country, network name: </label>
        <div class="row">
            <div class="col-md-4">{{ Form::text('query', Input::get('query', ''), ['class' => 'form-control']) }}</div>
            <div class="col-md-2"><button type="submit" class="btn btn-pink btn-sm">Search</button></div>
        </div>
    </div>
    {{ Form::close() }}
    <hr />
    {{ Form::open(['url'=> '/admin/destinations']) }}
    {{ Form::hidden('query', Input::get('query')) }}
    {{ Form::hidden('page', Input::get('page')) }}
    @if(count($destinations) > 0)
    <table class="table table-bordered">
        <thead>
        <tr>
            <th class="col-xs-2">Prefix</th>
            <th class="col-xs-4">Country</th>
            <th class="col-xs-4">Network name</th>
            <th class="col-xs-2">Interval</th>
        </tr>
        </thead>
        <tbody id="newDestinationsContainer">
        @foreach($destinations as $destination)
            @include('site.admin.destinations__row', ['$destination' => $destination])
        @endforeach
        </tbody>
    </table>
    {{ $destinations->appends(Input::except('page'))->links() }}
    @else
        <p>No data to display.</p>
    @endif
    <div class="row">
        <div class="col-md-offset-9 col-md-3">
            <button type="button" class="btn btn-pink btn-sm" onclick="addNewDestination(this)" id="addDestinationBtn" style="width: 100%">Add new destination</button>
        </div>
    </div>
    <hr />
    <input type="submit" class="btn btn-primary btn-lg" value="Save changes" />
    {{ Form::close() }}

    <div class="modal fade" id="addDestinationModal" tabindex="-1" role="dialog" aria-labelledby="addDestinationModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addDestinationModalLabel">Add destination</h4>
                </div>
                <div class="modal-body">
                    <div id="addDestinationModalErrors" class="alert alert-danger" style="display: none"></div>
                    <form>
                        <div class="form-group">
                            <label class="control-label">Prefix: </label>
                            <input type="text" class="form-control" name="prefix" id="prefix" />
                        </div>

                        <div class="form-group">
                            <label class="control-label">Country: </label>
                            <input type="text" class="form-control" name="country" id="country" value="" />
                        </div>

                        <div class="form-group">
                            <label class="control-label">Network name: </label>
                            <input type="text" class="form-control" name="network_name" id="network_name" value="" />
                        </div>

                        <div class="form-group">
                            <label class="control-label">Interval: </label>
                            <input type="text" class="form-control" name="interval" id="interval" value="0/1/1" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="pull-left">
                        <a class="a-link" href="{{ URL::to('/admin/destinations/import') }}"><i class="glyphicon glyphicon-upload"></i> Upload destinations from file</a>
                    </div>
                    <button type="button" class="btn btn-pink btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" onclick="return addNewDestinationSubmit(this);" class="btn btn-primary btn-sm" id="addDestinationModalBtn">Add destination</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('bottom_scripts')
    <script type="text/javascript">
        var csrf_token = '{{ csrf_token() }}';
        var addDestinationModal = $('#addDestinationModal');
        var prefix = $('#prefix');
        var country = $('#country');
        var networkName = $('#network_name');
        var interval = $('#interval');

        var addDestinationModalBtn = $('#addDestinationModalBtn');
        var addDestinationModalErrorsContainer = $('#addDestinationModalErrors');
        var newDestinationsContainer = $('#newDestinationsContainer');

        function toggleButtonState(disabled) {
            if (disabled) {
                addDestinationModalBtn.text('Waiting...').attr('disabled', 'disabled');
            } else {
                addDestinationModalBtn.text('Add destination').removeAttr('disabled');
            }
        }

        function addNewDestination(el) {
            [prefix, country, networkName].forEach(function(e) {
                e.val('');
            });
            interval.val('0/1/1');
            addDestinationModalErrorsContainer.hide();
            addDestinationModal.modal();
            return false;
        }

        function addNewDestinationSubmit(el) {
            toggleButtonState(true);
            addDestinationModalErrorsContainer.hide();

            var data = {
                'prefix': prefix.val(),
                'country': country.val(),
                'network_name': networkName.val(),
                'interval': interval.val()
            };

            $.post('/admin/destinations/add?_token=' + csrf_token, data, function(response) {
                toggleButtonState(false);
                if (response.status === 'error') {
                    console.assert(typeof(response.errors) !== 'undefined', 'Error responses must contain error descriptions.');
                    var errors = [];
                    for (var i in response.errors) {
                        for(var j in response.errors[i]) {
                            errors.push(response.errors[i][j]);
                        }
                    }

                    addDestinationModalErrorsContainer.html('<ul><li>' + errors.join('</li><li>') + '</li></ul>');
                    addDestinationModalErrorsContainer.fadeIn();
                    return false;
                } else {
                    console.assert(typeof(response.template) !== 'undefined', 'Success response must contain template.');
                    var template = response.template;
                    newDestinationsContainer.append(template);
                    addDestinationModal.modal('hide');
                }
            }).fail(function(e) {
                alert('There was an error while sending request to create form. Please, check your internet connection.');
                toggleButtonState(false);
                return false;
            });

            return false;
        }
    </script>
@endsection