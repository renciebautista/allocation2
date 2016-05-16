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

			<div class="col-lg-3">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('year', 'Year', array('class' => 'control-label')) }}
						{{ Form::select('year', array('0' => 'PLEASE SELECT') + $years, null, array('class' => 'form-control')) }}
						</div>
					</div>
				</div>
			</div>
			

			<div class="col-lg-3">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('week', 'Week', array('class' => 'control-label')) }}
						{{ Form::hidden('week_id', '',array('id' => 'week_id')) }}
						<select class="form-control" data-placeholder="SELECT WEEK" id="week" name="week" >
							<option value="">SELECT WEEK</option>
						</select>
						</div>
					</div>
				</div>
			</div>
			
		</div>


		<div class="row">

			<div class="col-lg-3">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('type', 'Activity Type', array('class' => 'control-label')) }}
						{{ Form::hidden('type_id', '',array('id' => 'type_id')) }}
						<select class="form-control" data-placeholder="SELECT ACTIVITY TYPE" id="activity_type" name="activity_type" >
							<option value="">SELECT ACTIVITY TYPE</option>
						</select>
						</div>
					</div>
				</div>
			</div>
			

			<div class="col-lg-3">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('brand', 'SOB Allocation Brand', array('class' => 'control-label')) }}
						{{ Form::hidden('brand_id', '',array('id' => 'brand_id')) }}
						<select class="form-control" data-placeholder="SELECT BRAND" id="brand" name="brand" >
							<option value="">SELECT BRAND</option>
						</select>
						</div>
					</div>
				</div>
			</div>
			
		</div>

		<div class="row">

			<div class="col-lg-3">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('exporttype', 'Export Type', array('class' => 'control-label')) }}
						{{ Form::select('exporttype', $exporttypes, null, array('class' => 'form-control')) }}
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

$("#week").depdrop({
    url: "{{action('api\SobController@weeks')}}",
    depends: ['year'],
    params: ['week_id']
});

$("#activity_type").depdrop({
    url: "{{action('api\SobController@weekactivitytype')}}",
    depends: ['week','year'],
    params: ['type_id']
});

$("#brand").depdrop({
    url: "{{action('api\SobController@weekbrand')}}",
    depends: ['activity_type', 'week','year'],
    initDepends: ['year'], 
    initialize: true,
    params: ['brand_id']
});

$( "#week" ).change(function() {
  	$('#week_id').val($(this).val());
});

$( "#activity_type" ).change(function() {
  	$('#type_id').val($(this).val());
});

$( "#brand" ).change(function() {
  	$('#brand_id').val($(this).val());
});

@stop