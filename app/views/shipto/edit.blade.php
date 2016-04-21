
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Edit Ship To</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">


	<div class="col-lg-6">
	{{ Form::open(array('action' => array('ShiptoController@update', $shipto->id), 'method' => 'PUT', 'class' => 'bs-component')) }}
		<div class="form-group">
			{{ Form::label('customer_code', 'Customer Code', array('class' => 'control-label')) }}
			{{ Form::text('customer_code',$shipto->customer_code,array('class' => 'form-control', 'placeholder' => 'Ship To Code')) }}
		</div>

		<div class="form-group">
			{{ Form::label('ship_to_code', 'Ship To Code', array('class' => 'control-label')) }}
			{{ Form::text('ship_to_code',$shipto->ship_to_code,array('class' => 'form-control', 'placeholder' => 'Ship To Code')) }}
		</div>

		<div class="form-group">
			{{ Form::label('ship_to_name', 'Ship To Name', array('class' => 'control-label')) }}
			{{ Form::text('ship_to_name',$shipto->ship_to_name,array('class' => 'form-control', 'placeholder' => 'Ship To Name')) }}
		</div>

		<div class="form-group">
			{{ Form::label('split', 'Split', array('class' => 'control-label')) }}
			{{ Form::text('split',$shipto->split,array('class' => 'form-control', 'placeholder' => 'Split')) }}
		</div>
		
		<div class="form-group">
			{{ Form::label('leadtime', 'Lead Time (days)', array('class' => 'control-label')) }}
			{{ Form::text('leadtime',$shipto->leadtime,array('class' => 'form-control', 'placeholder' => 'Lead Time (days)')) }}
		</div>

			<div class="checkbox"> <label> {{ Form::checkbox('active', 1, $shipto->active) }} Active </label> </div>


		<div class="form-group">
			{{ Form::label('loading_day', 'Loading Day/s', array('class' => 'control-label')) }}
			@foreach ($days as $day)
			<div class="checkbox"> <label> {{ Form::checkbox('days[]', $day['id'], $shipto->$day['id']) }} {{ $day['desc']}} </label> </div>
			@endforeach
		</div>

		
	

		<div class="form-group">
			{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('ShiptoController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
		</div>
	{{ Form::close() }}
	</div>
</div>

@stop

@section('page-script')
	$('#split, #leadtime').inputNumber({ allowDecimals: false });

@stop





