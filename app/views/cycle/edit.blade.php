
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
			{{ Form::label('submission_deadline', 'Circular Submission Deadline', array('class' => 'control-label')) }}
			{{ Form::text('submission_deadline', date_format(date_create($cycle->submission_deadline),'m/d/Y'),array('class' => 'form-control', 'placeholder' => 'Circular Submission Deadline')) }}
		</div>

		<div class="form-group">
			{{ Form::label('approval_deadline', 'Approval Deadline', array('class' => 'control-label')) }}
			{{ Form::text('approval_deadline', date_format(date_create($cycle->approval_deadline),'m/d/Y'),array('class' => 'form-control', 'placeholder' => 'Approval Deadline')) }}
		</div>
		
		<div class="form-group">
			{{ Form::label('pdf_deadline', 'PDF and Attachment Creation Date', array('class' => 'control-label')) }}
			{{ Form::text('pdf_deadline', date_format(date_create($cycle->pdf_deadline),'m/d/Y'),array('class' => 'form-control', 'placeholder' => 'PDF and Attachment Creation Date')) }}
		</div>

		<div class="form-group">
			{{ Form::label('release_date', 'Release Date', array('class' => 'control-label')) }}
			{{ Form::text('release_date', date_format(date_create($cycle->release_date),'m/d/Y'),array('class' => 'form-control', 'placeholder' => 'Release Date')) }}
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
			<div class="checkbox">
				<label>
					{{ Form::checkbox('launch', 1,$cycle->launch) }} Launch
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




