
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>New Holiday</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">

	<div class="col-lg-6">
	{{ Form::open(array('action' => 'HolidaysController@store','class' => 'bs-component', 'id' => 'myform')) }}
		<div class="form-group">
			{{ Form::label('desc', 'Description', array('class' => 'control-label')) }}
			{{ Form::text('desc','',array('class' => 'form-control', 'placeholder' => 'Description')) }}
		</div>
		<div class="checkbox">
	        <label>
	          	<input type="checkbox"> Annually
	        </label>
      	</div>
		<div class="form-group">
			{{ Form::label('date', 'Date', array('class' => 'control-label')) }}
			{{ Form::text('date','',array('class' => 'form-control', 'placeholder' => 'Date')) }}
		</div>
		
		<div class="form-group">
			{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('HolidaysController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
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
		desc: {
			required: true,
			maxlength: 80
			},
		date: {
			required: true
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

$('#date').datetimepicker({
		pickTime: false
	});
$('#date').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});

@stop


