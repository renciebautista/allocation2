@extends('layouts.layout')

@section('content')

<div class="row text-center">
    <div class="col-md-12">
        <div class="error-template">
            <h1>
                Oops!</h1>
            <h2>
                No record found</h2>
            <div class="error-actions">
                <a href="{{ URL::action('CycleController@index') }}" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-home"></span> Back </a>
            </div>
        </div>
    </div>
</div>
@stop



