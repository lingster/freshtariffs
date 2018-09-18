@extends('layouts.master')
@section('title', 'Integrations')

@section('content')
    <h3>Import contacts from Freshbooks</h3>
    <hr />
    <div class="alert alert-info">Choose contacts for importing.</div>
    <hr />
    {{ Form::open() }}
    {{ Form::hidden('finish', 'true') }}
    <table class="table table-bordered">
        <thead>
        <tr>
            <th><input type="checkbox" id="checkAll"></th>
            <th>Username</th>
            <th>E-Mail</th>
        </tr>
        </thead>
        <tbody>
        @foreach($importResults as $key => $result)
            <tr>
                <td style="width: 50px">
                    {{ Form::checkbox('users[]', $key) }}
                </td>
                <td>{{{ $result['username'] }}}</td>
                <td>{{{ $result['email'] }}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr />
    <button type="submit" class="btn btn-primary">Import contacts</button>
    {{ Form::close() }}
@endsection

@section('bottom_scripts')
    <script type="text/javascript">
        var users = $('input[name="users[]"]');
        var checkAll = $('#checkAll');
        checkAll.change(function () {
            users.each(function (i, el) {
                $(el).prop('checked', checkAll.prop('checked'));
            });
        });
    </script>
@endsection