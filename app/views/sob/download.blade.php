@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Export SOB File</h1>
		</div>
	</div>
</div>

@include('partials.notification')

{{ Form::open(array('action' => array('SobController@downloadreport'),'class' => 'bs-component','id' => 'myform')) }}
<div class="panel panel-default">
	<div class="panel-heading">Filter</div>
	<div class="panel-body">

		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('filename', 'Filename', array('class' => 'control-label')) }}
						{{ Form::text('filename','',array('id' => 'filename', 'class' => 'form-control', 'placeholder' => 'Filename' ,'maxlength' => 80)) }}
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('type', 'Activity Type', array('class' => 'control-label')) }}
						{{ Form::select('type', array('0' => 'PLEASE SELECT') + $types, null, array('class' => 'form-control')) }}
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
						{{ Form::label('brand', 'SOB Allocation Brand', array('class' => 'control-label')) }}
						{{ Form::select('brand', array('0' => 'PLEASE SELECT') + $brands, null, array('class' => 'form-control')) }}
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('cycles', 'TOP Cycle', array('class' => 'control-label')) }}
						{{ Form::select('cycles[]', $cycles, null, array('id' => 'cycles','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
			
		</div>
		

	</div>
</div>

<div class="form-group">
	{{ Form::submit('Download File', array('class' => 'btn btn-primary')) }}
</div>
{{ Form::close() }}

@stop

@section('page-script')
$('#cycles').multiselect({
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