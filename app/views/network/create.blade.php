
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>New Network for {{ $activitytype->activity_type }}</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">

	<div class="col-lg-6">
	{{ Form::open(array('action' => array('NetworkController@store', $activitytype->id) ,'class' => 'bs-component')) }}
		<div class="form-group">
			{{ Form::label('milestone', 'Milestone', array('class' => 'control-label')) }}
			{{ Form::text('milestone','',array('class' => 'form-control', 'placeholder' => 'Milestone')) }}
		</div>

		<div class="form-group">
			{{ Form::label('task', 'Task', array('class' => 'control-label')) }}
			{{ Form::text('task','',array('class' => 'form-control', 'placeholder' => 'Task')) }}
		</div>

		<div class="form-group">
			{{ Form::label('responsible', 'Responsible Team', array('class' => 'control-label')) }}
			{{ Form::text('responsible','',array('class' => 'form-control', 'placeholder' => 'Responsible Team')) }}
		</div>

		<div class="form-group">
			{{ Form::label('start', 'Start Day', array('class' => 'control-label')) }}
			{{ Form::text('start','',array('class' => 'form-control', 'placeholder' => 'Start Day')) }}
		</div>

		<div class="form-group">
			{{ Form::label('duration', 'Duration', array('class' => 'control-label')) }}
			{{ Form::text('duration','',array('class' => 'form-control', 'placeholder' => 'Duration')) }}
		</div>

		<div class="form-group">
			{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('NetworkController@index', 'Back',  $activitytype->id, array('class' => 'btn btn-default')) }}
		</div>
	{{ Form::close() }}
	</div>
</div>


@stop

@section('page-script')



@stop


