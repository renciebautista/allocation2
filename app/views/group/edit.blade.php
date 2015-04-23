
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Edit Group</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">

	<div class="col-lg-6">
	{{ Form::open(array('route' => array('group.update', $group->id), 'method' => 'PUT', 'class' => 'bs-component', 'id' => 'myform')) }}
		<div class="form-group">
			{{ Form::label('name', 'Group Name', array('class' => 'control-label')) }}
			{{ Form::text('name', $group->name ,array('class' => 'form-control', 'placeholder' => 'Group Name')) }}
		</div>

		<div class="form-group">
			{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('GroupController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
		</div>
	{{ Form::close() }}
	</div>
</div>


@stop

@section('page-script')

$("#myform").validate({
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		name: {
			required: true,
			maxlength: 80
			}
	},
	errorPlacement: function(error, element) {               
		
	},
	highlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').addClass(errorClass).removeClass(validClass);
  	},
  	unhighlight: function( element, errorClass, validClass ) {
    	$(element).closest('div').removeClass(errorClass).addClass(validClass);
  	}
});




@stop


