@extends('layouts.master')
@section('title', 'Upload price list')

@section('content')
    <h3>XLS price list uploading</h3>
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
    <h4>Choose destinations</h4>
    <ol>
        <li>Download our price list template file: <a href="{{ URL::to('/static/PriceTemplate.xlsx') }}" class="a-link">PriceTemplate.xlsx (779 KB)</a></li>
        <li>Modify <i>Rate</i> column (you can leave fields empty to not include some items into price list).</li>
        <li>Upload result file into this form.</li>
    </ol>
    {{ Form::open(['files' => true]) }}
    <div class="form-group">
        <label for="file" class="control-label">Price list file: </label>
        {{ Form::file('file', ['class' => 'form-control']) }}
    </div>

    <hr />
    <h4>Choose customers</h4>
    <div class="form-group">
        {{ Form::select('users[]', $companyUsersSelect, $selectedUser->user_id, ['class' => 'form-control select2', 'multiple' => 'multiple']) }}
        <span class="help-block">Price list will be generated and sent for specified users.</span>
    </div>
    <hr />
    <h4>Price list settings</h4>
    <div class="form-group">
        <label for="type" class="control-label">Type:</label>
        {{ Form::select('type', $pricelistTypesSelect, 'CLI', ['class' => 'form-control select2']) }}
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
    <button type="submit" class="btn btn-primary" id="createPricelistBtn">Create price list</button>
    {{ Form::close() }}
@endsection

@section('bottom_scripts')
    <script src="/js/select2.min.js"></script>
    <script type="text/javascript">
        $('.select2').select2();
        $(document).ready(function() {
            $('form').submit(function (e) {
                var createPricelistBtn = $('#createPricelistBtn');
                createPricelistBtn.text('Creating may take some time, please wait...');
                createPricelistBtn.attr('disabled', 'disabled');
                return true;
            });
        });
    </script>
@endsection