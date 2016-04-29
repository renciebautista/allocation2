
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>New Ship To Holiday</h1>
		</div>
	</div>
</div>

@include('partials.notification')

{{ Form::open(array('action' => 'SobholidaysController@store','class' => 'bs-component', 'id' => 'myform')) }}
<div class="row">

	<div class="col-lg-6">
	
		<div class="form-group">
			{{ Form::label('desc', 'Description', array('class' => 'control-label')) }}
			{{ Form::text('desc','',array('class' => 'form-control', 'placeholder' => 'Description')) }}
		</div>
		<div class="form-group">
			{{ Form::label('date', 'Date', array('class' => 'control-label')) }}
			{{ Form::text('date','',array('class' => 'form-control', 'placeholder' => 'Date')) }}
		</div>
		
		<div class="form-group">
			{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('SobholidaysController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
		</div>
	
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table id="shipto" class="table table-striped table-hover">
				<thead>
					<tr>
						<th>{{ Form::checkbox('all', 1,false, ['id' => 'all']) }}</th>
						<!-- <th>Group</th> -->
						<!-- <th>Area</th> -->
						<th>Ship To Code</th>
						<th>Ship To</th>
					</tr>
				</thead>
				<tbody>
					@if(count($shiptos) == 0)
					<tr>
						<td colspan="2">No record found!</td>
					</tr>
					@else
					@foreach($shiptos as $shipto)
					<tr>
						<td>
							{{ Form::checkbox("shiptos[]", $shipto->ship_to_code,false) }}
						</td>
						<!-- <td>{{ $shipto->group_name }}</td> -->
						<!-- <td>{{ $shipto->area_name }}</td> -->
						<td>{{ $shipto->ship_to_code }}</td>
						<td>{{ $shipto->ship_to_name }}</td>
					</tr>
					@endforeach
					@endif
				</tbody>
			</table> 
		</div>
	</div>
</div>
{{ Form::close() }}

@stop

@section('page-script')

$('#all').click(function() {
  	var checkedStatus = this.checked;
  	$('#shipto tbody tr').find('td:first :checkbox').each(function() {
    	$(this).prop('checked', checkedStatus);
  	});
});

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


