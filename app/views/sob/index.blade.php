@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Download Sales Order Booking Report</h1>
		</div>
	</div>
</div>

@include('partials.notification')

{{ Form::open(array('action' => array('SobController@generate'),'class' => 'bs-component','id' => 'myform')) }}
<div class="panel panel-default">
	<div class="panel-heading">Filter</div>
	<div class="panel-body">

		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('desc', 'Report Name', array('class' => 'control-label')) }}
						{{ Form::text('desc','',array('id' => 'desc', 'class' => 'form-control', 'placeholder' => 'Report Name' ,'maxlength' => 80)) }}
						</div>
					</div>
				</div>
			</div>
			
		</div>

		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('cy', 'TOP Cycle', array('class' => 'control-label')) }}
						{{ Form::select('cy[]', $cycles, null, array('id' => 'cy','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
			
		</div>

	</div>
</div>

<div class="form-group">
	{{ Form::submit('Download Report', array('class' => 'btn btn-primary')) }}
</div>
{{ Form::close() }}

@stop

@section('page-script')
$('#cy').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

$("#myform").validate({
	ignore: ':hidden:not(".multiselect")',
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		desc: {
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
  	},
  	invalidHandler: function(form, validator) {
        var errors = validator.numberOfInvalids();
        if (errors) {
              $("html, body").animate({ scrollTop: 0 }, "fast");
        }
    }
});


@stop