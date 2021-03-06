
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Edit User</h1>
		</div>
	</div>
</div>

	

@include('partials.notification')

<div class="row">

	<div class="col-lg-6">
	{{ Form::open(array('action' => array('UsersController@update', $user->id), 'method' => 'PUT', 'class' => 'bs-component')) }}
		<div class="form-group">
			{{ Form::label('username', 'Username', array('class' => 'control-label')) }}
			{{ Form::text('username',$user->username ,array('class' => 'form-control', 'placeholder' => 'Username')) }}
		</div>

		<div class="form-group">
			{{ Form::label('email', 'Email Address', array('class' => 'control-label')) }}
			{{ Form::text('email', $user->email,array('class' => 'form-control', 'placeholder' => 'Email Address')) }}
		</div>

		<div class="form-group">
			{{ Form::label('contact_no', 'Contact No.', array('class' => 'control-label')) }}
			{{ Form::text('contact_no', $user->contact_no,array('class' => 'form-control', 'placeholder' => 'Contact No.')) }}
		</div>

		<div class="form-group">
			{{ Form::label('first_name', 'First Name', array('class' => 'control-label')) }}
			{{ Form::text('first_name', $user->first_name,array('class' => 'form-control', 'placeholder' => 'First Name')) }}
		</div>

		<div class="form-group">
			{{ Form::label('middle_name', 'Middle Initial', array('class' => 'control-label')) }}
			{{ Form::text('middle_name', $user->middle_initial,array('class' => 'form-control', 'placeholder' => 'Middle Initial')) }}
		</div>

		<div class="form-group">
			{{ Form::label('last_name', 'Last Name', array('class' => 'control-label')) }}
			{{ Form::text('last_name', $user->last_name,array('class' => 'form-control', 'placeholder' => 'Last Name')) }}
		</div>


		<div class="form-group">
			{{ Form::label('group_id', 'Group', array('class' => 'control-label')) }}
			{{ Form::select('group_id', array('0' => 'PLEASE SELECT') + $groups, $role, array('class' => 'form-control')) }}
		</div>

		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('is_active', true, (($user->active == 1) ? true : false)) }} Active
				</label>
			</div>
		</div>


		<div class="form-group">
			{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('UsersController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
		</div>
	{{ Form::close() }}


	</div>
</div>

@include('javascript.user.edit')

@stop

@section('page-script')

@stop


