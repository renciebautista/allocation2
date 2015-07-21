
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
			{{ Form::text('middle_name', $user->middle_name,array('class' => 'form-control', 'placeholder' => 'Middle Initial')) }}
		</div>

		<div class="form-group">
			{{ Form::label('last_name', 'Last Name', array('class' => 'control-label')) }}
			{{ Form::text('last_name', $user->last_name,array('class' => 'form-control', 'placeholder' => 'Last Name')) }}
		</div>

		

		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('active', 1, (bool)$user->active) }} Active
				</label>
			</div>
		</div>

		{{ $user->active }}
		<div class="form-group">
			{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
			<a href="{{$url}}" class="btn btn-default">Back</a>
		</div>
	{{ Form::close() }}
	</div>
</div>

@include('javascript.user.edit')

@stop

@section('page-script')

@stop


