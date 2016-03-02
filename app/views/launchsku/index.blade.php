@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Launch SKU List</h1>
	  	</div>
	</div>
</div>

@include('partials.notification')

<div class="row">
	<div class="col-lg-12">
		{{ Form::open(array('method' => 'get','class' => 'form-inline')) }}
		 	<div class="form-group">
		 		<label class="sr-only" for="s">Search</label>
		 		{{ Form::text('s',Input::old('s'),array('class' => 'form-control', 'placeholder' => 'Search')) }}
		  	</div>
		  	<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
		  	{{ HTML::linkAction('LaunchSkuController@upload','Import SKU/s',null, array('class' => 'btn btn-info')) }}
		  	{{ HTML::linkAction('LaunchSkuController@export','Export Launch SKU/s Template',null, array('class' => 'btn btn-info')) }}
		{{ Form::close() }}
	</div>
</div>
<br>

{{ Form::open(array('action' => 'LaunchSkuController@assignaccess','class' => 'bs-component')) }}
<div class="panel panel-default">
	<div class="panel-heading">Proponents</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::select('users[]', $proponents, null, array('id' => 'users','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<button id="assign" type="button" class="btn btn-info"> Assign Access</button>
		  		<button id="remove" type="button" class="btn btn-danger"> Remove Access</button>
		  		<button id="delete" type="button" class="btn btn-danger"> Delete SKU</button>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th></th>
						<th class="center">Sap Code</th>
						<th class="center">Description</th>
						
						<th class="center">Division</th>
						<th class="center">Category</th>
						<th class="center">Brand</th>
						<th class="center">CPG Description</th>
						<th class="center">Proponents</th>
					</tr>
				</thead>
				<tbody>
					@if(count($launchskus) == 0)
					<tr>
						<td colspan="9">No record found!</td>
					</tr>
					@else
					@foreach($launchskus as $launch)
					<tr>
						<td class="center">{{ Form::checkbox('skus[]',  $launch->sap_code) }}</td>
						<td>{{ $launch->sap_code }}</td>
						<td>{{ $launch->sap_desc }}</td>
						<td>{{ $launch->division_desc }}</td>
						<td>{{ $launch->category_desc }}</td>
						<td>{{ $launch->brand_desc }}</td>
						<td>{{ $launch->cpg_desc }}</td>
						<td>{{ $launch->users }}</td>
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
$('#users').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

$("#assign").click(function(e){
	bootbox.dialog({
	  message: "Do you want to assign user access to this sku/s?",
	  title: "ETOP",
	  buttons: {
	    success: {
	      	label: "Yes",
	      	className: "btn btn-primary",
	      	callback: function() {
	        	var skus = new Array();
				$("input[name='skus[]']:checked").each(function(i) {
					skus.push($(this).val());
				});
				var users = GetSelectValues($('select#users :selected'));
				
				if((skus.length == 0) || (users.length == 0)){
					alert("No users or sku selected");
				}else{
					$.ajax({
					    type: 'POST',
					    url: "{{ URL::action('LaunchSkuController@assignaccess') }}",
					    data: { skus: skus, users:users },
					    success: function(data) {
						    if(data.success == 1){
						    	location.reload();
							}else{
								alert("Error updating records!");
							}
					        
					    }
					});
				}
	      }
	    },
	    danger: {
	      	label: "No",
	      	className: "btn btn-default"
	    },
	  }
	});
});

$("#remove").click(function(e){
	bootbox.dialog({
	  message: "Do you want to remove user access to this sku/s?",
	  title: "ETOP",
	  buttons: {
	    success: {
	      	label: "Yes",
	      	className: "btn btn-primary",
	      	callback: function() {
	        	var skus = new Array();
				$("input[name='skus[]']:checked").each(function(i) {
					skus.push($(this).val());
				});
				var users = GetSelectValues($('select#users :selected'));
				
				if((skus.length == 0) || (users.length == 0)){
					alert("No users or sku selected");
				}else{
					$.ajax({
					    type: 'POST',
					    url: "{{ URL::action('LaunchSkuController@removeaccess') }}",
					    data: { skus: skus, users:users },
					    success: function(data) {
						    if(data.success == 1){
						    	location.reload();
							}else{
								alert("Error updating records!");
							}
					        
					    }
					});
				}
	      }
	    },
	    danger: {
	      	label: "No",
	      	className: "btn btn-default"
	    },
	  }
	});
});

$("#delete").click(function(e){
	bootbox.dialog({
	  message: "Do you want to remove this sku/s?",
	  title: "ETOP",
	  buttons: {
	    success: {
	      	label: "Yes",
	      	className: "btn btn-primary",
	      	callback: function() {
	        	var skus = new Array();
				$("input[name='skus[]']:checked").each(function(i) {
					skus.push($(this).val());
				});
				
				if(skus.length == 0){
					alert("No sku selected");
				}else{
					$.ajax({
					    type: 'POST',
					    url: "{{ URL::action('LaunchSkuController@removeskus') }}",
					    data: { skus: skus },
					    success: function(data) {
						    if(data.success == 1){
						    	location.reload();
							}else{
								alert("Error updating records!");
								location.reload();
							}
					        
					    }
					});
				}
	      }
	    },
	    danger: {
	      	label: "No",
	      	className: "btn btn-default"
	    },
	  }
	});
});

@stop
