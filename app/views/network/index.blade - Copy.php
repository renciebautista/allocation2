@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>{{ $activitytype->activity_type }} Network</h1>
	  	</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		{{ Form::open(array('method' => 'get','class' => 'form-inline')) }}
		 	<div class="form-group">
		 		<label class="sr-only" for="s">Search</label>
		 		{{ Form::text('s',Input::old('s'),array('class' => 'form-control', 'placeholder' => 'Search')) }}
		  	</div>
		  	<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
		  	<a href="{{ URL::action('NetworkController@create', $activitytype->id) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Network</a>
		{{ Form::close() }}
	</div>
</div>
<br>
<div class="row">

	<div class="col-lg-12">
	{{ Form::open(array('action' => array('NetworkController@store', $activitytype->id) ,'class' => 'well bs-component')) }}

		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					{{ Form::label('milestone', 'Milestone', array('class' => 'control-label')) }}
					{{ Form::text('milestone','',array('class' => 'form-control', 'placeholder' => 'Milestone')) }}
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					{{ Form::label('task', 'Task', array('class' => 'control-label')) }}
					{{ Form::text('task','',array('class' => 'form-control', 'placeholder' => 'Task')) }}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					{{ Form::label('responsible', 'Responsible Team', array('class' => 'control-label')) }}
					{{ Form::text('responsible','',array('class' => 'form-control', 'placeholder' => 'Responsible Team')) }}
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					{{ Form::label('start', 'Start Day', array('class' => 'control-label')) }}
					{{ Form::text('start','',array('class' => 'form-control', 'placeholder' => 'Start Day')) }}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					{{ Form::label('duration', 'Duration', array('class' => 'control-label')) }}
					{{ Form::text('duration','',array('class' => 'form-control', 'placeholder' => 'Duration')) }}
				</div>
			</div>
		</div>


		<div class="form-group">
			{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('NetworkController@index', 'Cancel',  $activitytype->id, array('class' => 'btn btn-default')) }}
		</div>
	{{ Form::close() }}
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Network</th>
						<th>Task</th>
						<th>Team Responsible</th>
						<th>Start Day</th>
						<th>End Day</th>
						<th>Duration</th>
						<th>Running Days</th>
						<th>Remaing Days</th>
						<th colspan="3" style="text-align:center;">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($networks) == 0)
					<tr>
						<td colspan="4">No record found!</td>
					</tr>
					@else
					@foreach($networks as $network)
					<tr>
						<td>{{ $network->network }}</td>
						<td>{{ $network->task }}</td>
						<td>{{ $network->responsible }}</td>
						<td>{{ $network->start }}</td>
						<td></td>
						<td>{{ $network->duration }}</td>
						<td></td>
						<td></td>
					</tr>
					@endforeach
					@endif
				</tbody>
			</table> 
		</div>
	</div>
</div>

@stop