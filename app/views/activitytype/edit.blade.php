
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Edit Activity Type</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">

	<div class="col-lg-6">
	{{ Form::open(array('route' => array('activitytype.update', $activitytype->id), 'method' => 'PUT', 'class' => 'bs-component')) }}

		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('with_scheme', 1,$activitytype->with_scheme) }} With Scheme
				</label>
			</div>
		</div>

		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('with_msource', 1,$activitytype->with_msource) }} With Material Sourcing
				</label>
			</div>
		</div>

		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('with_sob', 1,$activitytype->with_sob) }} With SOB
				</label>
			</div>
		</div>

		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('with_tradedeal', 1,$activitytype->with_tradedeal) }} With Trade Deal
				</label>
			</div>
		</div>

		<div class="form-group">
			{{ Form::label('activity_type', 'Activity Type', array('class' => 'control-label')) }}
			{{ Form::text('activity_type', $activitytype->activity_type, array('class' => 'form-control', 'placeholder' => 'Activity Type')) }}
		</div>

		<div class="form-group">
			{{ Form::label('uom', 'Unit of Measurement', array('class' => 'control-label')) }}
			{{ Form::text('uom', $activitytype->uom ,array('class' => 'form-control', 'placeholder' => 'Unit of Measurement')) }}
		</div>

		<div class="form-group">
			{{ Form::label('prefix', 'SOB Prefix', array('class' => 'control-label')) }}
			{{ Form::text('prefix', $activitytype->prefix ,array('class' => 'form-control', 'placeholder' => 'SOB Prefix')) }}
		</div>

		<div class="form-group">
			{{ Form::label('default_loading', 'SOB Default Loading', array('class' => 'control-label')) }}
			{{ Form::select('default_loading', $loading, $activitytype->default_loading, array('class' => 'form-control')) }}
		</div>


		<div class="form-group">
			{{ Form::label('budget_type', 'Required Budget Type', array('class' => 'control-label')) }}

			@foreach ($budget_types as $row)
			<div class="checkbox">
				<label>
					{{ Form::checkbox('budget_types[]',$row->id, in_array($row->id, $required), array('id' => 'budget_types')) }}
					{{ $row->budget_type }}
				</label>
			</div>
			@endforeach
		</div>

		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('active', 1,$activitytype->active) }} Active
				</label>
			</div>
		</div>

		<div class="form-group">
			{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('ActivityTypeController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
			
		</div>

		
	{{ Form::close() }}
	</div>
</div>



@include('javascript.activitytype.edit')

@stop

@section('page-script')

@stop


