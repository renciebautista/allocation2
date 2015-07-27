@extends('layouts.login')

@section('content')

<div id="login" class="row">
	@include('partials.notification')
	{{ Form::open(array('action' => 'LoginController@forgotpassword','class' => 'bs-component')) }}
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Forgot Password</h3>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('email', 'Email', array('class' => 'control-label' )) }}
								{{ Form::text('email','',array('class' => 'form-control', 'placeholder' => 'Enter your email address')) }}
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