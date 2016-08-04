@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
    <div class="row">
        <div class="col-lg-12 col-md-7 col-sm-6">
            <h1>Trade Deal : {{ $scheme->name }}</h1>
        </div>
    </div>
</div>


<div class="panel panel-primary">
    <div class="panel-heading">Scheme Details</div>
        <div class="panel-body">

            {{ Form::open(array('action' => array('SchemeController@update', $scheme->id), 'files'=>true, 'method' => 'PUT', 'id' => 'updatescheme', 'class' => 'bs-component')) }}
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-12">
                                {{ Form::label('scheme_name', 'Scheme Name', array('class' => 'control-label')) }}
                                {{ Form::text('scheme_name', $scheme->name, array('id' => 'scheme_name', 'class' => 'form-control', 'readonly' => '')) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            {{ Form::close() }}
    </div>
</div>
@stop



