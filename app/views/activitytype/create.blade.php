
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>New Activity Type</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">

	<div class="col-lg-6">
	{{ Form::open(array('action' => 'ActivityTypeController@store','class' => 'bs-component')) }}

		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('with_scheme', 1,true) }} With Scheme
				</label>
			</div>
		</div>


		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('with_msource', 1,true) }} With Material Sourcing
				</label>
			</div>
		</div>

		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('with_sob', 1,false) }} With SOB
				</label>
			</div>
		</div>
		
		<div class="form-group">
			{{ Form::label('activity_type', 'Activity Type', array('class' => 'control-label')) }}
			{{ Form::text('activity_type','',array('class' => 'form-control', 'placeholder' => 'Activity Type')) }}
		</div>

		<div class="form-group">
			{{ Form::label('uom', 'Unit of Measurement', array('class' => 'control-label')) }}
			{{ Form::text('uom','',array('class' => 'form-control', 'placeholder' => 'Unit of Measurement')) }}
		</div>

		<div class="form-group">
			{{ Form::label('prefix', 'Prefix', array('class' => 'control-label')) }}
			{{ Form::text('prefix','',array('class' => 'form-control', 'placeholder' => 'Prefix')) }}
		</div>

		<div class="form-group">
			{{ Form::label('default_loading', 'SOB Default Loading', array('class' => 'control-label')) }}
			{{ Form::select('default_loading', $loading, 1, array('class' => 'form-control')) }}
		</div>

		<div class="form-group">
			{{ Form::label('budget_type', 'Required Budget Type', array('class' => 'control-label')) }}

			@foreach ($budget_types as $row)
			<div class="checkbox">
				<label>
					{{ Form::checkbox('budget_types[]',$row->id, null, array('id' => 'budget_types')) }}
					{{ $row->budget_type }}
				</label>
			</div>
			@endforeach
		</div>

		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('active', 1,true) }} Active
				</label>
			</div>
		</div>

		

		<div class="form-group">
			{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('ActivityTypeController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
			
		</div>

		
	{{ Form::close() }}
	</div>
</div>


@include('javascript.activitytype.create')

@stop

@section('page-script')

@stop







