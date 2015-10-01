@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>{{ $template->name }}</h1>
		</div>
	</div>
</div>

@include('partials.notification')

{{ Form::open(array('action' => array('AllocationReportController@update',$template->id), 'method' => 'PUT', 'class' => 'bs-component')) }}
<div class="panel panel-default">
	<div class="panel-heading">General Filters</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-lg-12">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							{{ Form::label('name', 'Report Template Name', array('class' => 'control-label')) }}
							{{ Form::text('name',$template->name ,array('id' => 'name', 'class' => 'form-control', 'placeholder' => 'Report Template Name' ,'maxlength' => 80)) }}
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
						{{ Form::label('st', 'Status', array('class' => 'control-label')) }}
						{{ Form::select('st[]', $statuses, $sel_status, array('id' => 'st','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('scope', 'Scope', array('class' => 'control-label')) }}
						{{ Form::select('scope[]', $scopes, $sel_scopes, array('id' => 'scope','class' => 'form-control', 'multiple' => 'multiple')) }}
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
						{{ Form::label('planner', 'PMOG Planner', array('class' => 'control-label')) }}
						{{ Form::select('planner[]', $planners, $sel_planners, array('id' => 'planner','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('app', 'Activity Approver', array('class' => 'control-label')) }}
						{{ Form::select('app[]', $approvers, $approvers, array('id' => 'app','class' => 'form-control', 'multiple' => 'multiple')) }}
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
						{{ Form::label('type', 'Activity Type', array('class' => 'control-label')) }}
						{{ Form::select('type[]', $activitytypes,$sel_activitytypes, array('id' => 'type','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							{{ Form::label('division', 'Division', array('class' => 'control-label')) }}
							{{ Form::select('division[]',  $divisions,$sel_divisions, array('id' => 'division', 'class' => 'form-control multiselect' ,'multiple' => 'multiple' ,'data-placeholder' => 'SELECT DIVISION')) }}
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div id="multiselect" class="col-lg-12">
							{{ Form::label('category', 'Category', array('class' => 'control-label')) }}
							{{ Form::select('category[]',  $categories, $sel_categories, array('id' => 'category', 'class' => 'form-control multiselect' ,'multiple' => 'multiple' ,'data-placeholder' => 'SELECT CATEGORY')) }}
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							{{ Form::label('brand', 'Brand', array('class' => 'control-label')) }}
							{{ Form::select('brand[]',  $brands, $sel_brands, array('id' => 'brand', 'class' => 'form-control multiselect' ,'multiple' => 'multiple' ,'data-placeholder' => 'SELECT BRAND')) }}
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Customer Details</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-4">
									{{ Form::label('tree3', 'Select Customers', array('class' => 'control-label' )) }}<br>
									<a href="#" id="btnCSelectAll">Select all</a> |
									<a href="#" id="btnCDeselectAll">Deselect all</a>
									<div id="tree3"></div>
									{{ Form::hidden('customers', null, array('id' => 'customers')) }}
								</div>
								<div class="col-lg-4">
									{{ Form::label('tree4', 'Select Outlets', array('class' => 'control-label' )) }}<br>
									<div id="chOtlets">
										<a href="#" id="btnOutSelectAll">Select all</a> |
										<a href="#" id="btnOutDeselectAll">Deselect all</a>
									</div>
									
									<div id="tree4"></div>
									{{ Form::hidden('outlets_involved', null, array('id' => 'outlets_involved')) }}
								</div>

								<div class="col-lg-4">
									{{ Form::label('tree5', 'Select DT Channels', array('class' => 'control-label' )) }}<br>
									<div id="chSel">
										<a href="#" id="btnChSelectAll">Select all</a> |
										<a href="#" id="btnChDeselectAll">Deselect all</a>
									</div>
									
									<div id="tree5"></div>
									{{ Form::hidden('channels_involved', null, array('id' => 'channels_involved')) }}
								</div>
							</div>	
							
							
						</div>
					</div>
				</div>
			</div>
		</div>

<div class="filters panel panel-default">
	<div class="panel-heading">Show Fields</div>
	<div class="panel-body min-height">
		@foreach($schemefields as $field)
		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('field[]', $field->id,(in_array($field->id,$sel_schemefields)) ? true:false) }} {{ $field->desc_name}}
				</label>
			</div>
		</div>
		@endforeach
	</div>
</div>

<div class="form-group">
	{{ Form::submit('Update', array('class' => 'btn btn-primary disable-button')) }}
	{{ HTML::linkAction('AllocationReportController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
</div>
{{ Form::close() }}
@include('javascript.allocationreport.create')

@stop

@section('page-script')

$(".disable-button").disableButton();

$("#tree3").fancytree({
	checkbox: true,
	selectMode: 3,
	source: {
		url: "{{ URL::action('AllocationReportController@customer') }}"
	},
	select: function(event, data) {
		// Get a list of all selected nodes, and convert to a key array:
		var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
			 return node.key;
		});
		selectedkeys = selKeys;
		//console.log(selKeys);
		// $("#echoSelection3").text(selKeys.join(", "));


		// Get a list of all selected TOP nodes
		var selRootNodes = data.tree.getSelectedNodes(true);
		// ... and convert to a key array:
		var selRootKeys = $.map(selRootNodes, function(node){
		  return node.key;
		});

		var keys = selRootKeys.join(".").split(".");
		
		$("#customers").val(selRootKeys.join(", "));
	},
	cookieId: "fancytree-tree3",
    idPrefix: "fancytree-tree3-"
});

function getCustomer(){
	$.ajax({
		type: "GET",
		url: "{{ URL::action('AllocationReportController@customerselected', array('id' => $template->id)) }}",
		success: function(data){
			$.each(data, function(i, node) {
				$("#tree3").fancytree("getTree").getNodeByKey(node).setSelected(true);
			});
		}
	});
}

$("#btnCDeselectAll").click(function(){

  	$("#tree3").fancytree("getTree").visit(function(node){
    	node.setSelected(false);
  	});
  	return false;
});
$("#btnCSelectAll").click(function(){
  	$("#tree3").fancytree("getTree").visit(function(node){
    	node.setSelected(true);
  	});
  	return false;
});


$("#tree4").fancytree({
	checkbox: true,
	selectMode: 3,
	source: {
		url: "{{ URL::action('AllocationReportController@outlets') }}"
	},
	select: function(event, data) {
		// Get a list of all selected nodes, and convert to a key array:
		var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
			 return node.key;
		});
		selectedkeys = selKeys;
		//console.log(selKeys);
		// $("#echoSelection3").text(selKeys.join(", "));


		// Get a list of all selected TOP nodes
		var selRootNodes = data.tree.getSelectedNodes(true);
		// ... and convert to a key array:
		var selRootKeys = $.map(selRootNodes, function(node){
		  return node.key;
		});

		var keys = selRootKeys.join(".").split(".");
		
		$("#outlets_involved").val(selRootKeys.join(", "));
	},
	cookieId: "fancytree-tree4",
    idPrefix: "fancytree-tree4-"
});


function getOutlets(){
	$.ajax({
		type: "GET",
		url: "{{ URL::action('AllocationReportController@outletsselected', array('id' => $template->id)) }}",
		success: function(data){
			$.each(data, function(i, node) {
				$("#tree4").fancytree("getTree").getNodeByKey(node).setSelected(true);
			});
		}
	});
}

$("#btnOutDeselectAll").click(function(){

  	$("#tree4").fancytree("getTree").visit(function(node){
    	node.setSelected(false);
  	});
  	return false;
});
$("#btnOutSelectAll").click(function(){
  	$("#tree4").fancytree("getTree").visit(function(node){
    	node.setSelected(true);
  	});
  	return false;
});

$("#tree5").fancytree({
	checkbox: true,
	selectMode: 3,
	source: {
		url: "{{ URL::action('AllocationReportController@channels') }}"
	},
	select: function(event, data) {
		// Get a list of all selected nodes, and convert to a key array:
		var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
			 return node.key;
		});
		selectedkeys = selKeys;
		//console.log(selKeys);
		// $("#echoSelection3").text(selKeys.join(", "));


		// Get a list of all selected TOP nodes
		var selRootNodes = data.tree.getSelectedNodes(true);
		// ... and convert to a key array:
		var selRootKeys = $.map(selRootNodes, function(node){
		  return node.key;
		});

		var keys = selRootKeys.join(".").split(".");
		
		$("#channels_involved").val(selRootKeys.join(", "));
	},
	cookieId: "fancytree-tree5",
    idPrefix: "fancytree-tree5-"
});

function getChannels(){
	$.ajax({
		type: "GET",
		url: "{{ URL::action('AllocationReportController@channelsselected', array('id' => $template->id)) }}",
		success: function(data){
			$.each(data, function(i, node) {
				$("#tree5").fancytree("getTree").getNodeByKey(node).setSelected(true);
			});
		}
	});
}

$("#btnChDeselectAll").click(function(){

  	$("#tree5").fancytree("getTree").visit(function(node){
    	node.setSelected(false);
  	});
  	return false;
});
$("#btnChSelectAll").click(function(){
  	$("#tree5").fancytree("getTree").visit(function(node){
    	node.setSelected(true);
  	});
  	return false;
});

getCustomer();
getOutlets();
getChannels();

@stop