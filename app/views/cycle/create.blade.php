
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>New Cycle</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">


	<div class="col-lg-6">
	{{ Form::open(array('action' => 'CycleController@store','class' => 'bs-component')) }}
		<div class="form-group">
			{{ Form::label('cycle_name', 'Cycle Name', array('class' => 'control-label')) }}
			{{ Form::text('cycle_name','',array('class' => 'form-control', 'placeholder' => 'Cycle Name')) }}
		</div>

		<div class="form-group">
			{{ Form::label('month', 'Month', array('class' => 'control-label')) }}
			{{ Form::select('month', array('0' => 'Please Select') + $months, 'default', array('class' => 'form-control')) }}
		</div>

		<div class="form-group">
			{{ Form::label('vetting_deadline', 'Vetting Deadline', array('class' => 'control-label')) }}
			{{ Form::text('vetting_deadline','',array('class' => 'form-control', 'placeholder' => 'Vetting Deadline')) }}
		</div>

		<div class="form-group">
			{{ Form::label('replyback_deadline', 'Replyback Deadline', array('class' => 'control-label')) }}
			{{ Form::text('replyback_deadline','',array('class' => 'form-control', 'placeholder' => 'Replyback Deadline')) }}
		</div>

		<div class="form-group">
			{{ Form::label('submission_deadline', 'Regular Approval/Circular Submission Deadline', array('class' => 'control-label')) }}
			{{ Form::text('submission_deadline','',array('class' => 'form-control', 'placeholder' => 'Regular Approval/Circular Submission Deadline')) }}
		</div>

		<div class="form-group">
			{{ Form::label('release_date', 'Regular Release Date', array('class' => 'control-label')) }}
			{{ Form::text('release_date','',array('class' => 'form-control', 'placeholder' => 'Regular Release Date')) }}
		</div>

		<div class="form-group">
			{{ Form::label('emergency_deadline', 'Emergency Approval/Circular Submission Deadline', array('class' => 'control-label')) }}
			{{ Form::text('emergency_deadline','',array('class' => 'form-control', 'placeholder' => 'Emergency Approval/Circular Submission Deadline')) }}
		</div>

		<div class="form-group">
			{{ Form::label('emergency_release_date', 'Emergency Release Deadline', array('class' => 'control-label')) }}
			{{ Form::text('emergency_release_date','',array('class' => 'form-control', 'placeholder' => 'Emergency Release Deadline')) }}
		</div>

		<div class="form-group">
			{{ Form::label('implemintation_date', 'Implementation Date', array('class' => 'control-label')) }}
			{{ Form::text('implemintation_date','',array('class' => 'form-control', 'placeholder' => 'Implementation Date')) }}
		</div>

		<div class="form-group">
			{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('CycleController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
		</div>
	{{ Form::close() }}
	</div>
</div>


@stop

@section('page-script')

$('#vetting_deadline, #replyback_deadline, #submission_deadline, #release_date, #emergency_deadline, #emergency_release_date, #implemintation_date').datetimepicker({
		pickTime: false
	});

$('#vetting_deadline, #replyback_deadline, #submission_deadline, #release_date, #emergency_deadline, #emergency_release_date, #implemintation_date').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});

;
@stop


