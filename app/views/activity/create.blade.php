@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>New Activity</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">
	{{ Form::open(array('route' => 'activity.store','class' => 'bs-component')) }}
	<div class="col-lg-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Activity</h3>
			</div>
			<div class="panel-body">
				<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										{{ Form::label('circular_name', 'Activity Name', array('class' => 'control-label')) }}
										{{ Form::text('circular_name','',array('class' => 'form-control', 'placeholder' => 'Circular No.' ,'readonly' => '')) }}
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										{{ Form::label('scope', 'Scope Type', array('class' => 'control-label')) }}
										{{ Form::select('scope', $scope_types, null, array('class' => 'form-control')) }}
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
										{{ Form::label('cycle', 'TOP Cycle', array('class' => 'control-label')) }}
										{{ Form::select('cycle', $cycles, null, array('class' => 'form-control')) }}
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										
										{{ Form::label('activity_type', 'Activity Type', array('class' => 'control-label')) }}
										{{ Form::select('activity_type', $activity_types, null, array('class' => 'form-control')) }}
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										{{ Form::label('division', 'Division', array('class' => 'control-label')) }}
										{{ Form::select('division', $divisions, null, array('class' => 'form-control')) }}
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
					
			</div>
		</div>
	</div>

	<div class="col-lg-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Budget</h3>
			</div>
			<div class="panel-body">
				<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										{{ Form::label('budget_tts', 'Budget I/O TTS', array('class' => 'control-label')) }}
										{{ Form::text('budget_tts','',array('class' => 'form-control', 'placeholder' => 'Budget I/O TTS')) }}
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										{{ Form::label('budget_pe', 'Budget I/O PE', array('class' => 'control-label')) }}
										{{ Form::text('budget_pe','',array('class' => 'form-control', 'placeholder' => 'Budget I/O PE')) }}
									</div>
								</div>
							</div>
						</div>
					</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Details</h3>
			</div>
			<div class="panel-body">
				<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										{{ Form::label('objective', 'Objectives', array('class' => 'control-label' )) }}
										{{ Form::select('objective[]', $objectives, null, array('id' => 'objective', 'class' => 'form-control', 'multiple' => 'multiple')) }}
									</div>
								</div>
							</div>
						</div>
					</div>

				<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										{{ Form::label('background', 'Background and Rationale', array('class' => 'control-label')) }}
										{{ Form::textarea('background','',array('class' => 'form-control', 'placeholder' => 'Background and Rationale' , 'size' => '50x13')) }}
									</div>
								</div>
							</div>
						</div>
					</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Customers</h3>
			</div>
			<div class="panel-body">
				<div id="tree3"></div>
				{{ Form::hidden('customers', null, array('id' => 'customers')) }}

			</div>

			<!-- <div>Selected keys: <span id="echoSelection3">-</span></div>
  			<div>Selected root keys: <span id="echoSelectionRootKeys3">-</span></div>
  			<div>Selected root nodes: <span id="echoSelectionRoots3">-</span></div> -->

		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Channels</h3>
			</div>
			<div class="panel-body">
			<div class="col-lg-12">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							{{ Form::label('channel', 'Channels', array('class' => 'control-label' )) }}
							{{ Form::select('channel[]', $channels, null, array('id' => 'channel', 'class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>

			</div>
		</div>
	</div>

	
	<div class="col-lg-12">
		<div class="form-group">
			{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkRoute('activity.index', 'Back', array(), array('class' => 'btn btn-default')) }}
		</div>
	</div>
	{{ Form::close() }}
</div>
@stop

@section('page-script')

$('select#division').on("change",function(){
	suggest_name();
	$.ajax({
			type: "POST",
			data: {q: $(this).val()},
			url: "../api/category",
			success: function(data){
				$('select#category').empty();
				$.each(data, function(i, text) {
					$('<option />', {value: i, text: text}).appendTo($('select#category')); 
				});
			$('select#category').multiselect('rebuild');
		   }
		});
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
		suggest_name();
	}
});

$('select#brand').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true,
	onDropdownHide: function(event) {
		suggest_name();
	}
});

$('select#objective').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

$('select#channel').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

$('select#channel').multiselect('disable');
 


function suggest_name(){
	$scope = $("#scope option:selected").text();
	$cycle = $("#cycle option:selected").text();
	$activity_type = $("#activity_type option:selected").text();
	$division = $("#division option:selected").text();
	$category = GetSelectValue($("#category :selected"));
	if(!$category){
		$cat='';
	}else{
		$cat='_'+$category;
	}
	$brand = GetSelectValue($("#brand :selected"));
	if(!$brand){
		$brd='';
	}else{
		$brd='_'+$brand;
	}
	$('#circular_name').val($scope+'_'+$cycle+'_'+$activity_type+'_'+$division+$cat+$brd);
}

$('select#scope,select#cycle,select#activity_type').on("change",function(){
	suggest_name();
});

// fancy tree
$("#tree3").fancytree({
	extensions: [],
	checkbox: true,
	selectMode: 3,
	source: {
		url: "../api/customers"
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
		console.log(keys);
		if($.inArray('E1397', keys) != -1){
			$('select#channel').multiselect('enable');
		}else{
			$('select#channel').multiselect('deselectAll', false);
			$('select#channel').multiselect('updateButtonText')
			$('select#channel').multiselect('disable');
		}

		$("#customers").val(selRootKeys.join(", "));
	  },
});
$('#budget_tts, #budget_pe').inputNumber();
@stop


