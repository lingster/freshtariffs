@extends('layouts.master')
@section('title', 'Create price list')

@section('content')
    <h3>Create price list</h3>
    <p>At this page you can create price list for some customers.</p>
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
    {{ Form::open(['id' => 'mainForm']) }}

    <h4>Price list settings</h4>
    <div class="form-group">
        <label for="type" class="control-label">Type:</label>
        {{ Form::select('type', $pricelistTypesSelect, 'CLI', ['class' => 'form-control select2', 'id' => 'type-field']) }}
        <span class="help-block">This will be used as <strong>{list-type}</strong> variable in subject.</span>
    </div>

    <div class="form-group">
        <label for="note" class="control-label">Note:</label>
        {{ Form::textarea('note', null, ['class' => 'form-control', 'rows' => '5']) }}
        <span class="help-block">This will be used as <strong>{note}</strong> template in e-mail body.</span>
    </div>

    <div class="form-group">
        <label for="effective_date" class="control-label">Effective date:</label>
        <div class="input-group date col-md-2 datepicker">
            {{ Form::text('effective_date', date('d.m.Y'), ['class' => 'form-control']) }}
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
    </div>

    <hr />

    <h4>Choose destinations</h4>
    <p>Leave <i>rate</i> field empty if you don't want to suggest destination to specified user.<br />
        If you have many destinations, please use <a href="{{ URL::to('/staff/pricelist/' . $selectedUser->user_id . '/xls') }}" class="a-link">XLS uploading</a>.</p>
    <div class="form-inline" style="margin-bottom: 10px">
        <div id="destination-select-container" class="form-group">
            <label>Add destination:</label>
            <select style="width: 300px" class="form-control destination-select" multiple>
                @if(Session::has('pricelist'))
                    <?php $pricelist = Session::get('pricelist'); ?>
                    @foreach($pricelist as $destination)
                        <option value="{{ $destination['destination_id'] }}" selected>{{ sprintf('(%d) %s (%s)', $destination['prefix'], $destination['destination'], $destination['interval']) }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th class="col-xs-1">Prefix</th>
            <th class="col-xs-4 col-md-6">Destination</th>
            <th class="col-xs-1">Interval</th>
            <th class="col-xs-4 col-md-2">Rate (<a href="#" onclick="return setRates();" class="a-link">set all</a>)</th>
            <th class="col-xs-2">Actions</th>
        </tr>
        </thead>
        <tbody id="destination-container">
        @if(isset($pricelist))
            @foreach($pricelist as $destination)
                <tr id="row_{{ $destination['destination_id'] }}">
                    <td>{{ $destination['prefix'] }}</td>
                    <td>{{ $destination['destination'] }}</td>
                    <td>{{ $destination['interval'] }}</td>
                    <td>{{ Form::number('rate_' . $destination['destination_id'], $destination['value'], ['class' => 'form-control rate-field']) }}</td>
                    <td class="text-center">
                        <a data-toggle="tooltip" href="#" alt="Delete row" title="Delete row" onclick="return deleteRow({{ $destination['destination_id'] }});"><i class="glyphicon glyphicon-remove"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>

    <hr />
    <h4>Choose customers</h4>
    <div class="form-group">
        {{ Form::select('users[]', $companyUsersSelect, $selectedUser->user_id, ['class' => 'form-control select2', 'multiple' => 'multiple']) }}
        <span class="help-block">Price list will be generated and sent for specified users.</span>
    </div>
    <hr />
    <button type="button" class="btn btn-primary" id="createPricelistBtn">Create price list</button>
    {{ Form::close() }}

    <div class="modal fade" id="setRateModal" tabindex="-1" role="dialog" aria-labelledby="setRateModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="setRateModalLabel">Set rate</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" id="setRateModalError" style="display: none"></div>
                    This rate will be set for all selected destinations.
                    <form>
                        <div class="form-group">
                            <label class="control-label">Rate: </label>
                            <input type="number" class="form-control" value="0.03" name="rate" id="setRateModalRate" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-pink btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" onclick="return setRatesSubmit(this);" class="btn btn-primary btn-sm" id="addDestinationModalBtn">Set rate</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="/js/select2.min.js"></script>

    <script id="row-template" type="text/x-custom-template">
        <tr id="row_{id}">
            <td>{prefix}</td>
            <td>{destination}</td>
            <td>{interval}</td>
            <td>{{ Form::number('rate_{id}', '{rate}', ['class' => 'form-control rate-field']) }}</td>
            <td class="text-center">
                <a data-toggle="tooltip" href="#" alt="Delete row" title="Delete row" onclick="return deleteRow({id});"><i class="glyphicon glyphicon-remove"></i></a>
            </td>
        </tr>
    </script>
    <script type="text/javascript">
        if (!String.prototype.format) {
            String.prototype.format = function() {
                var str = this.toString();
                if (!arguments.length)
                    return str;
                var args = typeof arguments[0],
                        args = (("string" == args || "number" == args) ? arguments : arguments[0]);
                for (arg in args)
                    str = str.replace(RegExp("\\{" + arg + "\\}", "gi"), args[arg]);
                return str;
            }
        }

        var destinationSelect = $('.destination-select');
        var destinationContainer = $('#destination-container');
        var rowTemplate = $('#row-template');
        var typeField = $('#type-field');
        var alreadyAdded = {};

        $('.select2').select2();

        function formatSearch(e) {
            if (typeof(e.meta) === 'undefined' || typeof(e.meta.s) === 'undefined') {
                return e.text;
            }

            return 'Add all <b>{country}</b> ({num} items)'.format({
                'country': e.meta.c,
                'num': e.meta.n
            });
        }

        function getFirstSelectedUser() {
            var users = $('form select[name="users[]"]').val();
            if (users && users.length > 0) {
                return users[0];
            } else {
                return null;
            }
        }
        destinationSelect.select2({
            'placeholder': 'Add destination',
            'closeOnSelect': false,
            'minimumInputLength': 3,
            'ajax': {
                'url': '{{ URL::to('/') }}/api/destinations',
                'dataType': "json",
                'delay': 250,
                'data': function(params) {
                    return {
                        'q': params.term,
                        'u': getFirstSelectedUser(),
                        't': typeField.val(),
                        '_token': '{{ csrf_token() }}'
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: data.items
                    };
                }
            },
            'cache': true,
            'escapeMarkup': function (markup) { return markup; },
            'templateResult': formatSearch
        });

        destinationSelect.on('select2:select', function(e) {
            var data = e.params.data;
            if (typeof(data.meta.s) === 'undefined') {
                var template = rowTemplate.html().format({
                    'prefix': data.meta.p,
                    'destination': data.meta.d,
                    'interval': data.meta.i,
                    'id': data.id,
                    'rate': (data.meta.r != null) ? data.meta.r : 0.01
                });
                alreadyAdded[data.id] = true;
                destinationContainer.append(template);
            } else {
                $.getJSON(
                        '/api/destinations/country?_token={{ csrf_token() }}&q=' + data.meta.c + '&u=' + getFirstSelectedUser() + '&t=' + typeField.val(),
                        function(response) {
                            for (var k in response.items) {
                                var data = response.items[k];
                                if (alreadyAdded[data.id] === true) {
                                    console.log('skipping ', data.id);
                                    continue;
                                }

                                var template = rowTemplate.html().format({
                                    'prefix': data.meta.p,
                                    'destination': data.meta.d,
                                    'interval': data.meta.i,
                                    'id': data.id,
                                    'rate': (data.meta.r != null) ? data.meta.r : 0.01
                                });
                                alreadyAdded[data.id] = true;
                                destinationContainer.append(template);
                            }
                        }
                );
                destinationSelect.select2("close");
                deleteRow(data.meta.c);
            }
        });

        destinationSelect.on('select2:unselect', function(e) {
            var data = e.params.data;
            if (typeof(data.meta.s) === 'undefined') {
                deleteRow(data.id);
            }
        });

        function deleteRow(destinationId) {
            alreadyAdded[destinationId] = false;
            $('#row_' + destinationId).remove();
            var selectedDestinations = destinationSelect.val();
            var index;
            if (selectedDestinations && (index = selectedDestinations.indexOf(destinationId.toString())) !== -1) {
                delete selectedDestinations[index];
                destinationSelect.val(selectedDestinations);
            }
            return false;
        }

        function setRates() {
            $('#setRateModalError').hide();
            $('#setRateModal').modal();
            return false;
        }

        function setRatesSubmit(e) {
            var rate = $('#setRateModalRate').val();
            if (!parseFloat(rate)) {
                $('#setRateModalError').val('Rate is not a number.');
                $('#setRateModalError').fadeIn();
                return false;
            }

            $('.rate-field').val(rate);
            $('#setRateModal').modal('hide');
        }

        var createPricelistBtn = $('#createPricelistBtn');
        createPricelistBtn.click(function() {
            createPricelistBtn.text('Creating may take some time, please wait...');
            createPricelistBtn.attr('disabled', 'disabled');
            $('#mainForm').submit();
            return false;
        });
    </script>
@endsection