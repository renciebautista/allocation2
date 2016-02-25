@extends('layouts.layout')

@section('content')

<div class="row text-center">
    <div class="col-md-12">
        <div class="error-template">
            <h1>
                Oops!</h1>
            <h2>
                Scheme allocation is currently updating.</h2>
            <div class="error-details">
                Sorry for inconvenience. Updating in progress..
            </div>
            <div class="error-actions">
                <a href="{{ URL::action('DashboardController@index') }}" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-home"></span> Take Me Home </a>
                <a href="{{ URL::action('DashboardController@index') }}" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-envelope"></span> Contact Support </a>
            </div>
        </div>
    </div>
</div>
@stop



