@extends('layouts.login')

@section('content')

<div id="login" class="row">
	@include('partials.notification')
	{{ Form::open(array('action' => 'LoginController@doResetPassword','class' => 'bs-component')) }}
	{{ Form::hidden('token', $token) }}
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Reset Password</h3>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								<label class="control-label" for="password">New Password</label>
								{{ Form::password('password',array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								<label class="control-label" for="password_confirmation">Password Confirmation</label>
								{{ Form::password('password_confirmation',array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
					</div>
				</div>
			</div>
		</div>
	</div>
	{{ Form::close() }}
</div>

@stop