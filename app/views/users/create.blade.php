
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>New User</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">

	<div class="col-lg-6">
	{{ Form::open(array('action' => 'UsersController@store','class' => 'bs-component')) }}
		<div class="form-group">
			{{ Form::label('username', 'Username', array('class' => 'control-label')) }}
			{{ Form::text('username','',array('class' => 'form-control', 'placeholder' => 'Username')) }}
		</div>

		<div class="form-group">
			{{ Form::label('password', 'Password', array('class' => 'control-label')) }}
			{{ Form::password('password',array('class' => 'form-control', 'placeholder' => 'Password')) }}
		</div>

		<div class="form-group">
			{{ Form::label('password_confirmation', 'Password Confirmation', array('class' => 'control-label')) }}
			{{ Form::password('password_confirmation',array('class' => 'form-control', 'placeholder' => 'Password Confirmation')) }}
		</div>

		<div class="form-group">
			{{ Form::label('email', 'Email Address', array('class' => 'control-label')) }}
			{{ Form::text('email','',array('class' => 'form-control', 'placeholder' => 'Email Address')) }}
		</div>

		<div class="form-group">
			{{ Form::label('contact_no', 'Contact No.', array('class' => 'control-label')) }}
			{{ Form::text('contact_no','',array('class' => 'form-control', 'placeholder' => 'Contact No.')) }}
		</div>

		<div class="form-group">
			{{ Form::label('first_name', 'First Name', array('class' => 'control-label')) }}
			{{ Form::text('first_name','',array('class' => 'form-control', 'placeholder' => 'First Name')) }}
		</div>

		<div class="form-group">
			{{ Form::label('middle_name', 'Middle Initial', array('class' => 'control-label')) }}
			{{ Form::text('middle_name','',array('class' => 'form-control', 'placeholder' => 'Middle Initial')) }}
		</div>

		<div class="form-group">
			{{ Form::label('last_name', 'Last Name', array('class' => 'control-label')) }}
			{{ Form::text('last_name','',array('class' => 'form-control', 'placeholder' => 'Last Name')) }}
		</div>

		<div class="form-group">
			{{ Form::label('department_id', 'Department', array('class' => 'control-label')) }}
			{{ Form::select('department_id', array('0' => 'Please Select') + $departments, 'default', array('class' => 'form-control')) }}
		</div>

		<div class="form-group">
			{{ Form::label('group_id', 'Role', array('class' => 'control-label')) }}
			{{ Form::select('group_id', array('0' => 'Please Select') + $groups, 'default', array('class' => 'form-control')) }}
		</div>

		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('is_active', true,true) }} Active
				</label>
			</div>
		</div>

		<div class="form-group">
			{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('UsersController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
		</div>
	{{ Form::close() }}
	</div>
</div>


@include('javascript.user.create')

@stop

@section('page-script')

@stop
