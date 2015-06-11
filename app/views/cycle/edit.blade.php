
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Edit Cycle</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">


	<div class="col-lg-6">
	{{ Form::open(array('route' => array('cycle.update', $cycle->id), 'method' => 'PUT', 'class' => 'bs-component')) }}
		<div class="form-group">
			{{ Form::label('cycle_name', 'Cycle Name', array('class' => 'control-label')) }}
			{{ Form::text('cycle_name', $cycle->cycle_name, array('class' => 'form-control', 'placeholder' => 'Cycle Name')) }}
		</div>

		
		<div class="form-group">
			{{ Form::label('month_year', 'Month/Year', array('class' => 'control-label')) }}
			{{ Form::text('month_year',$cycle->month_year,array('class' => 'form-control', 'placeholder' => 'Month/Year')) }}
		</div>

		<div class="form-group">
			{{ Form::label('vetting_deadline', 'Vetting Deadline', array('class' => 'control-label')) }}
			{{ Form::text('vetting_deadline', date_format(date_create($cycle->vetting_deadline),'m/d/Y'), array('class' => 'form-control', 'placeholder' => 'Vetting Deadline')) }}
		</div>

		<div class="form-group">
			{{ Form::label('replyback_deadline', 'Replyback Deadline', array('class' => 'control-label')) }}
			{{ Form::text('replyback_deadline', date_format(date_create($cycle->replyback_deadline),'m/d/Y'), array('class' => 'form-control', 'placeholder' => 'Replyback Deadline')) }}
		</div>

		<div class="form-group">
			{{ Form::label('submission_deadline', 'Regular Approval/Circular Submission Deadline', array('class' => 'control-label')) }}
			{{ Form::text('submission_deadline', date_format(date_create($cycle->submission_deadline),'m/d/Y'), array('class' => 'form-control', 'placeholder' => 'Regular Approval/Circular Submission Deadline')) }}
		</div>

		<div class="form-group">
			{{ Form::label('release_date', 'Regular Release Date', array('class' => 'control-label')) }}
			{{ Form::text('release_date', date_format(date_create($cycle->release_date),'m/d/Y'), array('class' => 'form-control', 'placeholder' => 'Regular Release Date')) }}
		</div>

		<div class="form-group">
			{{ Form::label('emergency_deadline', 'Emergency Approval/Circular Submission Deadline', array('class' => 'control-label')) }}
			{{ Form::text('emergency_deadline', ($cycle->emergency_deadline == '0000-00-00') ? '' : date_format(date_create($cycle->emergency_deadline),'m/d/Y'), array('class' => 'form-control', 'placeholder' => 'Emergency Approval/Circular Submission Deadline')) }}
		</div>

		<div class="form-group">
			{{ Form::label('emergency_release_date', 'Emergency Release Deadline', array('class' => 'control-label')) }}
			{{ Form::text('emergency_release_date', ($cycle->emergency_release_date == '0000-00-00') ? '' : date_format(date_create($cycle->emergency_release_date),'m/d/Y'), array('class' => 'form-control', 'placeholder' => 'Emergency Release Deadline')) }}
		</div>

		<div class="form-group">
			{{ Form::label('implemintation_date', 'Implementation Date', array('class' => 'control-label')) }}
			{{ Form::text('implemintation_date', date_format(date_create($cycle->implemintation_date),'m/d/Y'), array('class' => 'form-control', 'placeholder' => 'Implementation Date')) }}
		</div>

		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('emergency', 1,$cycle->emergency) }} Emergency
				</label>
			</div>
		</div>

		<div class="form-group">
			{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('CycleController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
		</div>
	{{ Form::close() }}
	</div>
</div>



@include('javascript.cycle.edit')

@stop

@section('page-script')

@stop




