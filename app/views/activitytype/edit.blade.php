
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
			{{ Form::label('activity_type', 'Activity Type', array('class' => 'control-label')) }}
			{{ Form::text('activity_type', $activitytype->activity_type, array('class' => 'form-control', 'placeholder' => 'Activity Type')) }}
		</div>

		<div class="form-group">
			{{ Form::label('uom', 'Unit of Measurement', array('class' => 'control-label')) }}
			{{ Form::text('uom', $activitytype ->uom ,array('class' => 'form-control', 'placeholder' => 'Unit of Measurement')) }}
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
			{{ HTML::linkAction('ActivityTypeController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
			{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
		</div>

		
	{{ Form::close() }}
	</div>
</div>


@stop

@section('page-script')



@stop

