
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Settings</h1>
		</div>
	</div>
</div>

	

@include('partials.notification')

<div class="row">

	<div class="col-lg-6">
	{{ Form::open(array('action' => array('SettingsController@update'), 'class' => 'bs-component')) }}
		<div class="form-group">
			{{ Form::label('new_user_email', 'New user approver email', array('class' => 'control-label')) }}
			{{ Form::text('new_user_email', $settings->new_user_email ,array('class' => 'form-control', 'placeholder' => 'New user approver email')) }}
		</div>


		<div class="form-group">
			{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
		</div>
	{{ Form::close() }}


	</div>
</div>

@stop

@section('page-script')

@stop


