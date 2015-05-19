
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Activity Filter Settings</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="panel panel-default">
	<div class="panel-heading">Filters</div>
	<div class="panel-body">
		{{ Form::open(array('method' => 'get','class' => 'bs-component')) }}
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							{{ Form::label('division', 'Divisions', array('class' => 'control-label')) }}
							{{ Form::select('division[]', $divisions, null, array('id' => 'division','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						<div id="multiselect" class="col-lg-12">
							{{ Form::label('category', 'Category', array('class' => 'control-label')) }}
							<select class="form-control" data-placeholder="SELECT CATEGORY" id="category" name="category[]" multiple="multiple" ></select>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							{{ Form::label('brand', 'Brand', array('class' => 'control-label')) }}
							<select class="form-control" data-placeholder="SELECT BRAND" id="brand" name="brand[]" multiple="multiple" ></select>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-6">
				{{ Form::label('tree3', 'Select Customers', array('class' => 'control-label' )) }}
				<div id="tree3"></div>
				{{ Form::hidden('customers', null, array('id' => 'customers')) }}
			</div>
		</div>	
		<br>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					{{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
					{{ HTML::linkAction('DashboardController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
				</div>
			</div>
		</div>
		{{ Form::close() }}
	</div>
</div>


@stop

@section('page-script')

$('select#division').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true,
	onDropdownHide: function(event) {
		$.ajax({
			type: "POST",
			data: {q: GetSelectValues($('select#division :selected'))},
			url: "../api/categories",
			success: function(data){
				$('select#category').empty();
				$.each(data, function(i, text) {
					$('<option />', {value: i, text: text}).appendTo($('select#category')); 
				});
			$('select#category').multiselect('rebuild');
		   }
		});
	}
});


$('select#category').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true,
	onDropdownHide: function(event) {
		$.ajax({
			type: "POST",
			data: {categories: GetSelectValues($('select#category :selected'))},
			url: "../api/brand",
			success: function(data){
				$('select#brand').empty();
				$.each(data, function(i, text) {
					$('<option />', {value: i, text: text}).appendTo($('select#brand')); 
				});
			$('select#brand').multiselect('rebuild');
		   }
		});
	}
});

$('select#brand').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true,
	onDropdownHide: function(event) {
	}
});

$("#tree3").fancytree({
	extensions: [],
	checkbox: true,
	selectMode: 3,
	source: {
		url: "../../api/customers"
	},
	select: function(event, data) {
		// Get a list of all selected nodes, and convert to a key array:
		// var selKeys = $.map(data.tree.getSelectedNodes(), function(node){
		//  return node.key;
		// });
		// $("#echoSelection3").text(selKeys.join(", "));


		// Get a list of all selected TOP nodes
		var selRootNodes = data.tree.getSelectedNodes(true);
		// ... and convert to a key array:
		var selRootKeys = $.map(selRootNodes, function(node){
		  return node.key;
		});

		// $("#echoSelectionRootKeys3").text(selRootKeys.join("."));
		// $("#echoSelectionRootKeys3").text(selRootKeys.join(", "));

		var keys = selRootKeys.join(".").split(".");
		// console.log(keys);
		if($.inArray('E1397', keys) != -1){
			$('select#channel').multiselect('enable');
			updatechannel();
		}else{
			$('select#channel').multiselect('deselectAll', false);
			$('select#channel').multiselect('updateButtonText')
			$('select#channel').multiselect('disable');
		}
		$("#customers").val(selRootKeys.join(", "));
	},
	click: function(event, data) {
        $("#updateCustomer").addClass("dirty");
    },
});
@stop


