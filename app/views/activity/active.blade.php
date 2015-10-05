
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Edit Activity</h1>
		</div>
	</div>
</div>

	

@include('partials.notification')

<div class="row">

	<div class="col-lg-6">
	{{ Form::open(array('action' => array('ActivityController@setactive', $activity->id), 'method' => 'PUT', 'class' => 'bs-component')) }}
		<div class="form-group">
			{{ Form::label('status', 'Status', array('class' => 'control-label')) }}
			{{ Form::text('status',$activity->status->status ,array('class' => 'form-control', 'readonly' => '')) }}
		</div>

		<div class="form-group">
			{{ Form::label('cycle', 'TOP Cycle', array('class' => 'control-label')) }}
			{{ Form::text('cycle',$activity->cycle->cycle_name ,array('class' => 'form-control', 'readonly' => '')) }}
		</div>

		<div class="form-group">
			{{ Form::label('scope', 'Scope', array('class' => 'control-label')) }}
			{{ Form::text('scope',$activity->scope->scope_name ,array('class' => 'form-control', 'readonly' => '')) }}
		</div>

		<div class="form-group">
			{{ Form::label('type', 'Activity Type', array('class' => 'control-label')) }}
			{{ Form::text('type',$activity->activitytype->activity_type ,array('class' => 'form-control', 'readonly' => '')) }}
		</div>

		<div class="form-group">
			{{ Form::label('title', 'Activity Title', array('class' => 'control-label')) }}
			{{ Form::text('title',$activity->circular_name ,array('class' => 'form-control', 'readonly' => '')) }}
		</div>
		

		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('deactivated', true, (($activity->disable == 1) ? true : false)) }} Deactivated
				</label>
			</div>
		</div>


		<div class="form-group">
			{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('ActivityController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
		</div>
	{{ Form::close() }}


	</div>
</div>

@include('javascript.user.edit')

@stop

@section('page-script')

@stop


