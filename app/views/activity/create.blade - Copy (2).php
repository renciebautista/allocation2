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
	<div class="col-lg-12">
		<div class="form-group">
			{{ HTML::linkRoute('activity.index', 'Back', array(), array('class' => 'btn btn-default')) }}
		</div>
	</div>

</div>


<ul class="nav nav-tabs">
	<li class="active"><a aria-expanded="true" href="#activty" data-toggle="tab">Activity Details</a></li>
	<li class=""><a aria-expanded="false" href="#budget" data-toggle="tab">Budget Details</a></li>
	<li class=""><a aria-expanded="false" href="#customer" data-toggle="tab">Customer Details</a></li>
	<li class=""><a aria-expanded="false" href="#schemes" data-toggle="tab">Schemes</a></li>
	<li class=""><a aria-expanded="false" href="#timings" data-toggle="tab">Timings Details</a></li>
</ul>
<div id="myTabContent" class="tab-content">
  	<div class="tab-pane fade active in" id="activty">
  		<br>
  		<div class="well">
  			<div class="row">
  				<div class="col-lg-6">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('scope', 'Scope', array('class' => 'control-label')) }}
								{{ Form::select('scope', $scope_types, null, array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Approver -->
			<div class="row">
  				<div class="col-lg-6">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('planner', 'PMOG Planner', array('class' => 'control-label')) }}
								{{ Form::select('planner', $users, null, array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								
								{{ Form::label('approver', 'Activity Approver', array('class' => 'control-label')) }}
								{{ Form::select('approver', $users, null, array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Approver -->

			<!-- Cycle -->
			<div class="row">
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div id="multiselect" class="col-lg-12">
								{{ Form::label('activity_type', 'Activity Type', array('class' => 'control-label')) }}
								{{ Form::select('activity_type', $activity_types, null, array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('lead_time', 'Activity Leadtime (days)', array('class' => 'control-label')) }}
								{{ Form::text('lead_time','',array('class' => 'form-control', 'placeholder' => '0')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('download_date', 'Expected Download Date ', array('class' => 'control-label')) }}
								{{ Form::text('download_date','',array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('implementation_date', 'Expected Implementation Date', array('class' => 'control-label')) }}
								{{ Form::text('implementation_date','',array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy')) }}
							</div>
						</div>
					</div>
				</div>
				
				
			</div>
			<!-- End Cycle -->

			<div class="row">
  				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('circular_name', 'Activity Title', array('class' => 'control-label')) }}
								{{ Form::text('circular_name','',array('class' => 'form-control', 'placeholder' => 'Activity Title')) }}
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
								{{ Form::label('division', 'Division', array('class' => 'control-label')) }}
								{{ Form::select('division', $divisions, null, array('class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div id="multiselect" class="col-lg-12">
								{{ Form::label('category', 'Category', array('class' => 'control-label')) }}
								<select class="form-control" data-placeholder="SELECT CATEGORY" id="category" name="category[]" multiple="multiple" ></select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('brand', 'Brand', array('class' => 'control-label')) }}
								<select class="form-control" data-placeholder="SELECT BRAND" id="brand" name="brand[]" multiple="multiple" ></select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('objective', 'SKU/s Involved', array('class' => 'control-label' )) }}
								{{ Form::select('objective[]', $objectives, null, array('id' => 'objective', 'class' => 'form-control', 'multiple' => 'multiple')) }}
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
									{{ Form::textarea('background','',array('class' => 'form-control', 'placeholder' => 'Background and Rationale')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						{{ HTML::linkRoute('activity.index', 'Save', array(), array('class' => 'btn btn-primary')) }}
						{{ HTML::linkRoute('activity.index', 'Next', array(), array('class' => 'btn btn-primary')) }}
					</div>
				</div>
			</div>
  		</div>
  	</div>

  	<div class="tab-pane fade" id="budget">
		<br>
  		<div class="well">
  			<div class="row">
  				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								<table id="budget_table" class="table table-striped table-hover ">
								  	<thead>
								    	<tr>
								      		<th>Type (TTS/PE)</th>
								      		<th>IO</th>
								      		<th>Amount</th>
								      		<th>Start Date</th>
								      		<th>End Date</th>
								      		<th>Remarks</th>
								      		<th>Action</th>
								    	</tr>
								  	</thead>
								</table> 
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
								{{ Form::label('scope', 'Billing Deadline', array('class' => 'control-label')) }}
								{{ Form::text('circular_name','',array('class' => 'form-control', 'placeholder' => 'Activity Title')) }}
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
								
								{{ Form::label('approver', 'Billing Requirements', array('class' => 'control-label')) }}
								{{ Form::textarea('background','',array('class' => 'form-control', 'placeholder' => 'Background and Rationale' , 'size' => '50x5')) }}
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
			<div class="col-lg-12">
					<div class="form-group">
						{{ HTML::linkRoute('activity.index', 'Back', array(), array('class' => 'btn btn-default')) }}
						{{ HTML::linkRoute('activity.index', 'Save', array(), array('class' => 'btn btn-primary')) }}
						{{ HTML::linkRoute('activity.index', 'Next', array(), array('class' => 'btn btn-primary')) }}
					</div>
				</div>
				</div>
  		</div>
  	</div>

  	<div class="tab-pane fade" id="timings">
		<br>
  		<div class="well">
  			<div class="row">
  				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
							<table class="table table-striped table-hover ">
							  <thead>
							    <tr>
							      <th>#</th>
							      <th>Column heading</th>
							      <th>Column heading</th>
							      <th>Column heading</th>
							    </tr>
							  </thead>
							  <tbody>
							    <tr>
							      <td>1</td>
							      <td>Column content</td>
							      <td>Column content</td>
							      <td>Column content</td>
							    </tr>
							    <tr>
							      <td>2</td>
							      <td>Column content</td>
							      <td>Column content</td>
							      <td>Column content</td>
							    </tr>
							    <tr class="info">
							      <td>3</td>
							      <td>Column content</td>
							      <td>Column content</td>
							      <td>Column content</td>
							    </tr>
							    <tr class="success">
							      <td>4</td>
							      <td>Column content</td>
							      <td>Column content</td>
							      <td>Column content</td>
							    </tr>
							    <tr class="danger">
							      <td>5</td>
							      <td>Column content</td>
							      <td>Column content</td>
							      <td>Column content</td>
							    </tr>
							    <tr class="warning">
							      <td>6</td>
							      <td>Column content</td>
							      <td>Column content</td>
							      <td>Column content</td>
							    </tr>
							    <tr class="active">
							      <td>7</td>
							      <td>Column content</td>
							      <td>Column content</td>
							      <td>Column content</td>
							    </tr>
							  </tbody>
							</table> 


							</div>
						</div>
					</div>
				</div>
			</div>
  		</div>
  	</div>

  	<div class="tab-pane fade" id="customer">
		<br>
  		<div class="well">
  			<div class="row">
  				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('channel', 'Customers Involved', array('class' => 'control-label' )) }}
							<div id="tree3"></div>
							{{ Form::hidden('customers', null, array('id' => 'customers')) }}
							</div>
						</div>
					</div>
				</div>
			</div>
  			<div class="row">
  				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-6">
								{{ Form::label('channel', 'DT Channels Involved', array('class' => 'control-label' )) }}
							{{ Form::select('channel[]', $channels, null, array('id' => 'channel', 'class' => 'form-control', 'multiple' => 'multiple')) }}
							</div>
						</div>
					</div>
				</div>
			</div>
  		</div>
  	</div>

  	<div class="tab-pane fade" id="schemes">
		<br>
  		<div class="well">
  			<div class="row">
  				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
							<table class="table table-striped table-hover ">
							  <thead>
							    <tr>
							      <th>#</th>
							      <th>Column heading</th>
							      <th>Column heading</th>
							      <th>Column heading</th>
							    </tr>
							  </thead>
							  <tbody>
							    <tr>
							      <td>1</td>
							      <td>Column content</td>
							      <td>Column content</td>
							      <td>Column content</td>
							    </tr>
							    <tr>
							      <td>2</td>
							      <td>Column content</td>
							      <td>Column content</td>
							      <td>Column content</td>
							    </tr>
							    <tr class="info">
							      <td>3</td>
							      <td>Column content</td>
							      <td>Column content</td>
							      <td>Column content</td>
							    </tr>
							    <tr class="success">
							      <td>4</td>
							      <td>Column content</td>
							      <td>Column content</td>
							      <td>Column content</td>
							    </tr>
							    <tr class="danger">
							      <td>5</td>
							      <td>Column content</td>
							      <td>Column content</td>
							      <td>Column content</td>
							    </tr>
							    <tr class="warning">
							      <td>6</td>
							      <td>Column content</td>
							      <td>Column content</td>
							      <td>Column content</td>
							    </tr>
							    <tr class="active">
							      <td>7</td>
							      <td>Column content</td>
							      <td>Column content</td>
							      <td>Column content</td>
							    </tr>
							  </tbody>
							</table> 


							</div>
						</div>
					</div>
				</div>
			</div>
  		</div>
  	</div>
</div>


@stop

@section('page-script')
<!-- $('textarea#background').ckeditor(); -->
<!-- activity details -->

$('#download_date').datetimepicker({
	pickTime: false,
	calendarWeeks: true,
	minDate: moment()
});

$('#download_date').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});

function duration(value){

	$.ajax({
		type: "GET",
		url: "../activitytype/"+value+"/network/totalduration",
		success: function(msg){
			$('#lead_time').val(msg);
		},
		error: function(){
			alert("failure");
		}
	});
}

$('select#activity_type').on("change",function(){
	duration($(this).val());
});


<!-- Budget details -->

$('#budget_table').ajax_table({
	columns: [
		{ type: "select", id: "io_ttstype", placeholder: "Enter First Name"},
    	{ type: "text", id: "io_no", placeholder: "Enter Last Name" },
    	{ type: "text", id: "io_amount", placeholder: "Amount" },
    	{ type: "text", id: "io_startdate", placeholder: "mm/dd/yyyy" },
    	{ type: "text", id: "io_enddate", placeholder: "mm/dd/yyyy" },
    	{ type: "text", id: "io_remarks", placeholder: "Enter Last Name" },
	],
	onInitRow: function() {
        $('#io_startdate, #io_enddate').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
        $('#io_startdate, #io_enddate').datetimepicker({
			pickTime: false,
			calendarWeeks: true,
			minDate: moment()
		});
		$('#io_amount').inputNumber();
		$("#io_no").mask("aa99999999");

		$.ajax({
			type: "GET",
			url: "../api/budgettype",
			success: function(data){
				$('select#io_ttstype').empty();
				$.each(data, function(i, text) {
					$('<option />', {value: i, text: text}).appendTo($('select#io_ttstype')); 
				});
		   }
		});
    }
});




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

$().on('click', function() {
    $.ajax({
        url: 'http://10.100.12.145:8080/somePage/123',
        success: function (response) {
            // add response to the container you want: $(/* selector */).html(response);
        }
    });
});

@stop


