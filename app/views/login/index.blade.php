@extends('layouts.login')

@section('content')

<div id="login" class="row">
	@include('partials.notification')
	{{ Form::open(array('action' => 'LoginController@dologin','class' => 'bs-component')) }}
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Sign in</h3>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('name', 'Username or Email', array('class' => 'control-label' )) }}
								{{ Form::text('name','',array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								<label class="control-label" for="password">Password</label>
								{{ Form::password('password',array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						{{ Form::submit('Sign in', array('class' => 'btn btn-primary')) }}
						{{ HTML::linkAction('LoginController@forgotpassword' , 'Forgot your password?',null,['class' => 'pull-right m-top']) }}
					</div>
				</div>
			</div>
		</div>
	</div>
	{{ Form::close() }}
</div>

@stop