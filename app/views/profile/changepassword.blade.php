
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Change Password</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">

	<div class="col-lg-6">
	{{ Form::open(array('action' => 'ProfileController@updatepassword','class' => 'bs-component')) }}
		<div class="form-group">
			{{ Form::label('old_password', 'Old Password', array('class' => 'control-label')) }}
			{{ Form::password('old_password',array('class' => 'form-control', 'placeholder' => 'Old Password')) }}
		</div>


		<div class="form-group">
			{{ Form::label('password', 'New Password', array('class' => 'control-label')) }}
			{{ Form::password('password',array('class' => 'form-control', 'placeholder' => 'New Password')) }}
		</div>

		<div class="form-group">
			{{ Form::label('password_confirmation', 'Password Confirmation', array('class' => 'control-label')) }}
			{{ Form::password('password_confirmation',array('class' => 'form-control', 'placeholder' => 'Password Confirmation')) }}
		</div>

		<div class="form-group">
			{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
		</div>
	{{ Form::close() }}
	</div>
</div>
@stop


@section('page-script')
$("form").validate({
	ignore: null,
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		old_password : {
			required: true
        },
        password : {
			required: true,
            minlength : 6
        },
        password_confirmation : {
        	required: true,
            minlength : 6,
            equalTo : "#password"
        }

	},
	errorPlacement: function(error, element) {   
		console.log(element.parent());        
		error.appendTo(element.parent().find("label"));    
	},
	highlight: function( element, errorClass, validClass ) {
		$(element.closest('div')).addClass(errorClass).removeClass(validClass);
	},
	unhighlight: function( element, errorClass, validClass ) {
		$(element.closest('div')).removeClass(errorClass).addClass(validClass);
	}
});
@stop
