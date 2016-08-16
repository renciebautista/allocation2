@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			
			<h2>Create Job Order</h2>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="form-group">
			<a id="scheme_back" class="btn btn-default" href="{{action('ActivityController@edit', $activity->id);}}#jo">Back to Activity Details</a>
		</div>
	</div>

</div>


@include('partials.notification')

<div class="well ">
	{{ Form::open(array('action' => array('ActivityController@storejo', $activity->id) ,'class' => 'bs-component' ,'id' => 'myform')) }}

	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-6">
						{{ Form::label('task', 'Task', array('class' => 'control-label')) }}
						{{ Form::select('task', array('0' => 'PLEASE SELECT') + $tasks, null, array('id' => 'task', 'class' => 'form-control')) }}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-6">
						{{ Form::label('sub_task', 'Sub Task', array('class' => 'control-label')) }}
						<select class="form-control" data-placeholder="SELECT SUB TASK" id="sub_task" name="sub_task">
							<option value="0">PLEASE SELECT</option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-3">
						{{ Form::label('start_date', 'Start Date', array('class' => 'control-label')) }}
						{{ Form::text('start_date','',array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy', 'id' => 'start_date')) }}
					</div>
					<div class="col-lg-3">
						{{ Form::label('end_date', 'End Date', array('class' => 'control-label')) }}
						{{ Form::text('end_date','',array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy', 'id' => 'end_date')) }}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('details', 'Details', array('class' => 'control-label')) }}
						{{ Form::textarea('details','',array('class' => 'form-control multiselect', 'placeholder' => 'Details', 'id' => 'details')) }}
					</div>
					
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						{{ Form::label('files', 'Attach Files', array('class' => 'control-label')) }}
						<input type="file" name="files[]" id="filer_input" multiple="multiple">
					</div>
					
				</div>
			</div>
		</div>
	</div>

	
	<br>
	<div class="row">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
					<a class="btn btn-default" id="back" href="{{action('ActivityController@edit', $activity->id);}}#jo">Back to Activity Details</a>
					{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
					</div>
				</div>
			</div>
		</div>
	</div>
	{{ Form::close() }}
</div>

@stop

@section('add-script')
	{{ HTML::script('assets/js/joborder.js') }}
@stop