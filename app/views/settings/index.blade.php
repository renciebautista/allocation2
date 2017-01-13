
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
			<div class="checkbox">
				<label>
					{{ Form::checkbox('change_password', true, (($settings->change_password == 1) ? true : false)) }} Set Change Password Prompt
				</label>
			</div>
		</div>

		<div class="form-group">
			{{ Form::label('pasword_expiry', 'Password Expiry (days)', array('class' => 'control-label')) }}
			{{ Form::text('pasword_expiry', $settings->pasword_expiry ,array('class' => 'form-control', 'placeholder' => 'Password Expiry (days)')) }}
		</div>

		<div class="form-group">
			{{ Form::label('customized_preapprover', 'Customized Activity Pre Approver Department Code (use comma seperated)', array('class' => 'control-label')) }}
			{{ Form::text('customized_preapprover', $settings->customized_preapprover ,array('class' => 'form-control', 'placeholder' => 'Customized Activity Pre Approver Department Code')) }}
		</div>


		<br>
		<div class="form-group">
			{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
		</div>
	{{ Form::close() }}


	</div>
</div>

@stop

@section('page-script')
	$('#pasword_expiry').inputNumber({ allowDecimals: false });
@stop


