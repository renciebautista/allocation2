
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>New Sub Task</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">

	<div class="col-lg-6">
	{{ Form::open(array('route' => array('subtasks.update', $subtask->id), 'method' => 'PUT', 'class' => 'bs-component', 'id' => 'myform')) }}
		<div class="form-group">
			{{ Form::label('task', 'Task', array('class' => 'control-label')) }}
			{{ Form::select('task', array('0' => 'Please Select') + $tasks, $subtask->task_id, array('class' => 'form-control')) }}
		</div>


		<div class="form-group">
			{{ Form::label('subtask', 'Sub Task Name', array('class' => 'control-label')) }}
			{{ Form::text('subtask',$subtask->sub_task,array('class' => 'form-control', 'placeholder' => 'Task Name')) }}
		</div>

		<div class="form-group">
			{{ Form::label('department', 'Assigned Department', array('class' => 'control-label')) }}
			{{ Form::select('department', array('0' => 'Please Select') + $departments, $subtask->department_id, array('class' => 'form-control')) }}
		</div>

		<div class="form-group">
			{{ Form::label('lead_time', 'Lead Time (days)', array('class' => 'control-label')) }}
			{{ Form::text('lead_time',$subtask->lead_time ,array('class' => 'form-control', 'placeholder' => 'Lead Time (days)')) }}
		</div>

		<div class="form-group">
			{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('SubtasksController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
		</div>
	{{ Form::close() }}
	</div>
</div>


@stop

@section('page-script')

$("#myform").validate({
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		subtask: {
			required: true,
			maxlength: 80
			},
		task: {
			required: true,
			is_natural_no_zero: true,
		}
	},
	errorPlacement: function(error, element) {               
		
	},
	highlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').addClass(errorClass).removeClass(validClass);
  	},
  	unhighlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').removeClass(errorClass).addClass(validClass);
  	}
});


@stop

