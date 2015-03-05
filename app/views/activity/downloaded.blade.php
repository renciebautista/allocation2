@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Edit {{ $activity->circular_name }}</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">
	<div class="col-lg-12">
		<div class="form-group">
			{{ HTML::linkRoute('activity.index', 'Back To Activity List', array(), array('class' => 'btn btn-default')) }}

			<button class="btn btn-primary disabled">Download to PMOG</button>

			{{ HTML::linkAction('ActivityController@recall','Recall', $activity->id, array('class' => 'btn btn-warning')) }}
		</div>
	</div>

</div>


<ul class="nav nav-tabs">
	<li class="active"><a aria-expanded="true" href="#activty">Activity Details</a></li>
	<li class=""><a aria-expanded="false" href="#customer">Customer Details</a></li>
	<li class=""><a aria-expanded="false" href="#schemes">Schemes</a></li>
	<li class=""><a aria-expanded="false" href="#budget">Budget Details</a></li>
	<li class=""><a aria-expanded="false" href="#timings">Timings Details</a></li>
	<li class=""><a aria-expanded="false" href="#materials">Material Sourcing</a></li>
	<li class=""><a aria-expanded="false" href="#attachment">Attachment</a></li>
</ul>
<div id="myTabContent" class="tab-content">

	<!-- activity details -->
	<div class="tab-pane fade active in" id="activty">
		<br>
		<div class="well">
			<div class="row">
				<div class="col-lg-6">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('status', 'Status', array('class' => 'control-label')) }}
								{{ Form::text('status',$activity->status->status, array('class' => 'form-control','readonly' => '')) }}
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
								{{ Form::label('scope', 'Scope', array('class' => 'control-label')) }}
								{{ Form::text('scope',$activity->scope->scope_name, array('class' => 'form-control','readonly' => '')) }}
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
								{{ Form::text('planner','for update', array('class' => 'form-control','readonly' => '')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								
								{{ Form::label('approver', 'Activity Approver', array('class' => 'control-label')) }}
								{{ Form::select('approver[]', $approvers, $sel_approver , array('id' => 'approver', 'class' => 'form-control', 'multiple' => 'multiple')) }}
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
							<div class="col-lg-12">
								{{ Form::label('activity_type', 'Activity Type', array('class' => 'control-label')) }}
								{{ Form::text('activity_type',$activity->activitytype->activity_type, array('class' => 'form-control','readonly' => '')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('lead_time', 'Activity Leadtime (days)', array('class' => 'control-label')) }}
								{{ Form::text('lead_time', $activity->duration,array('class' => 'form-control', 'readonly' => '')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('download_date', 'Target Download Date ', array('class' => 'control-label')) }}
								{{ Form::text('download_date',date_format(date_create($activity->edownload_date),'m/d/Y'),array('class' => 'form-control', 'readonly' => '')) }}								
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('implementation_date', 'Target Implementation Date', array('class' => 'control-label')) }}
								{{ Form::text('implementation_date',date_format(date_create($activity->eimplementation_date),'m/d/Y'),array('class' => 'form-control', 'readonly' => '')) }}
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
							<div class="col-lg-6">
								{{ Form::label('cycle', 'TOP Cycle', array('class' => 'control-label')) }}
								{{ Form::text('cycle',$activity->cycle->cycle_name, array('class' => 'form-control','readonly' => '')) }}
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
								{{ Form::label('activity_title', 'Activity Title', array('class' => 'control-label')) }}
								{{ Form::text('activity_title',$activity->circular_name	,array('class' => 'form-control', 'readonly' => '')) }}
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
								{{ Form::text('division',$division->division_desc, array('class' => 'form-control','readonly' => '')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('category', 'Activity Approver', array('class' => 'control-label')) }}
								{{ Form::select('category[]', $categories, $sel_categories , array('id' => 'category', 'class' => 'form-control', 'multiple' => 'multiple')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('brand', 'Brand', array('class' => 'control-label')) }}
								{{ Form::select('brand[]', $brands, $sel_brands , array('id' => 'brand', 'class' => 'form-control', 'multiple' => 'multiple')) }}
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
								{{ Form::label('involve', 'SKU/s Involved', array('class' => 'control-label' )) }}
								{{ Form::select('involve[]', $involves, $sel_skus, array('id' => 'involve', 'data-placeholder' => 'Select SKUS Involve', 'class' => 'form-control','multiple' => 'multiple')) }}
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
									{{ Form::select('objective[]', $objectives, $sel_objectives, array('id' => 'objective', 'class' => 'form-control', 'multiple' => 'multiple')) }}
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
								{{ Form::textarea('background',$activity->background,array('class' => 'form-control', 'placeholder' => 'Background and Rationale')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-primary btn-style" type="submit">Next</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- customer details -->
	<div class="tab-pane fade" id="customer">
		<br>
		<div class="well">
			<div class="row">
				<div class="col-lg-12">
					<div id="tree3"></div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('channel', 'Select DT Channels Involved', array('class' => 'control-label' )) }}
								{{ Form::select('channel[]', $channels, $sel_channels, array('id' => 'channel', 'class' => 'form-control', 'multiple' => 'multiple')) }}
							</div>
						</div>
					</div>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-default btn-style" type="submit">Back</button>
						<button class="btn btn-primary btn-style" type="submit">Next</button>
					</div>
				</div>
			</div>

		</div>
	</div>

	<!-- scheme details -->
	<div class="tab-pane fade" id="schemes">
		<br>
		<div class="well">
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive tableScroll">
						<table class="table table-striped table-hover ">
							<thead>
								<tr>
									<th>Date Created</th>
									<th>Scheme Name</th>
									<th>Item Code</th>
									<th>Purchase Requirement</th>
									<th>SRP of Premium</th>
									<th>Other Cost Per Deal</th>
									<th>Total Unilever Cost</th>
									<th>Cost to Sales % </th>
									<th>Total Allocation</th>
									<th>UOM</th>
									<th>No. of Deals Per Case</th>
									<th>TTS Requirement</th>
									<th>PE Requirement</th>
									<th>Total Cost</th>
									<th>Action</th>
								</tr>
							</thead>
						  	<tbody>
						  		@if(count($schemes) == 0)
						  		<tr>
						  			<td colspan="15">No record found!</td>
						  		</tr>
						  		@endif
								@foreach($schemes as $scheme)
								<tr>
									<td>{{ date_format($scheme->created_at,'m/d/Y') }}</td>
								  	<td>{{ $scheme->name }}</td>
								  	<td>{{ $scheme->item_code }}</td>
								  	<td class="text-right">{{ number_format($scheme->pr,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->srp_p,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->other_cost,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->ulp,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->cost_sale,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->quantity) }}</td>
								  	<td>{{ $activity->activitytype->uom }}</td>
								  	<td class="text-right">{{ number_format($scheme->deals) }}</td>
								  	<td class="text-right">{{ number_format($scheme->tts_r,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->pe_r,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->total_cost,2) }}</td>
								  	<td>
								  		{{ HTML::linkAction('SchemeController@edit','View', $scheme->id, array('class' => 'btn btn-primary btn-xs')) }}	
								  	</td>
								</tr>
								@endforeach
						  	</tbody>
						</table> 
					</div>

				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-default btn-style" type="submit">Back</button>
						<button class="btn btn-primary btn-style" type="submit">Next</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- budget details -->
	<div class="tab-pane fade" id="budget">

		<br>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">With Existing Budget IO</h3>
				</div>
				<div class="panel-body">
					<div id="c_with_budget">
						<div class="row">
							<div class="col-lg-12">
								<div class="form-group">
									<div class="row">
										<div class="col-lg-12">
											<table id="budget_table" class="table table-striped table-hover ">
												<thead>
													<tr>
														<th>Type</th>
														<th>IO</th>
														<th>Amount</th>
														<th>Start Date</th>
														<th>End Date</th>
														<th>Remarks</th>
													</tr>
												</thead>
												<tbody>
												@if(count($budgets) == 0)
										  		<tr>
										  			<td colspan="6">No record found!</td>
										  		</tr>
										  		@endif

												@foreach ($budgets as $budget)
												<tr id="{{ $budget->id }}">
													<td>{{ $budget->budgettype->budget_type }}</td>
													<td>{{ strtoupper($budget->io_number) }}</td>
													<td>{{ number_format($budget->amount,2) }}</td>
													<td>{{ date_format(date_create($budget->start_date),'m/d/Y') }}</td>
													<td>{{ date_format(date_create($budget->end_date),'m/d/Y') }}</td>
													<td>{{ $budget->remarks }}</td>
												</tr>
												@endforeach
												</tbody>
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
										<div class="col-lg-6">
											{{ Form::label('billing_deadline', 'Billing Deadline', array('class' => 'control-label')) }}
											{{ Form::text('billing_deadline',date_format(date_create($activity->billing_date),'m/d/Y'), array('class' => 'form-control', 'readonly' => '')) }}
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
											{{ Form::label('billing_remarks', 'Billing Requirements', array('class' => 'control-label')) }}
											{{ Form::textarea('billing_remarks',$activity->billing_remarks, array('class' => 'form-control', 'placeholder' => 'Billing Requirements' , 'size' => '50x5')) }}
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">IO for Raising</h3>
				</div>
				<div class="panel-body">
					<div id="c_without_budget">
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									<table id="no_budget_table" class="table table-striped table-hover ">
										<thead>
											<tr>
												<th>Type</th>
												<th>Budget Holder</th>
												<th>Budget Name</th>
												<th>Amount</th>
												<th>Start Date</th>
												<th>End Date</th>
												<th>Remarks</th>
											</tr>
										</thead>
										<tbody>
											@if(count($nobudgets) == 0)
									  		<tr>
									  			<td colspan="7">No record found!</td>
									  		</tr>
									  		@endif
											@foreach ($nobudgets as $nobudget)
											<tr id="{{ $nobudget->id }}">
												<td>{{ $nobudget->budgettype->budget_type }}</td>
												<td>{{ $nobudget->budget_no }}</td>
												<td>{{ $nobudget->budget_name }}</td>
												<td>{{ number_format($nobudget->amount,2) }}</td>
												<td>{{ date_format(date_create($nobudget->start_date),'m/d/Y') }}</td>
												<td>{{ date_format(date_create($nobudget->end_date),'m/d/Y') }}</td>
												<td>{{ $nobudget->remarks }}</td>
											</tr>
											@endforeach
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

			

			
			<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<button class="btn btn-default btn-style" type="submit">Back</button>
							<button class="btn btn-primary btn-style" type="submit">Next</button>
						</div>
					</div>
				</div>
	</div>

	<!-- timings details -->
	<div class="tab-pane fade" id="timings">
		<br>
		<div class="well">
			<div class="row">
				<div class="col-lg-12">
					<table id="activity_timings" class="table table-striped table-hover ">
					  	<thead>
							<tr>
						  		<th data-field="task_id">Task ID</th>
						        <th data-field="milestone">Milestone</th>
						        <th data-field="task">Task</th>
						        <th data-field="responsible">Team Responsible</th>
						        <th data-field="duration">Duration (days)</th>
						        <th data-field="depend_on">Depends On</th>
						  		<th data-field="start_date">Start Date</th>
						  		<th data-field="end_date">End Date</th>
							</tr>
					  	</thead>
					</table> 
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-default btn-style" type="submit">Back</button>
						<button class="btn btn-primary btn-style" type="submit">Next</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- material details -->
	<div class="tab-pane fade" id="materials">
		<br>
		<div class="well">
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

			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-default btn-style" type="submit">Back</button>
						<button class="btn btn-primary btn-style" type="submit">Next</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- attachment details -->
	<div class="tab-pane fade" id="attachment">
		<br>
		<div class="well">
			<div class="row">
				<div class="col-lg-12">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>Description</th>
								<th>Filename</th>
								<th>Date Uploaded</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@if(count($attachments) == 0)
							<tr>
								<td colspan="4">No file attachment found!</td>
							</tr>
							@endif

							@foreach($attachments as $attachment)
							<tr>
								<td>{{ $attachment->file_desc }}</td>
								<td>{{ $attachment->file_name }}</td>
								<td>{{ date_format(date_create($attachment->created_at),'m/d/Y H:m:s') }}</td>
								<td>
									{{ HTML::linkAction('DownloadedActivityController@downloadfile','Download', $attachment->id, array('class' => 'btn btn-info btn-xs')) }}
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-default btn-style" type="submit">Back</button>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>


@stop

@section('page-script')
$('.nav-tabs a').click(function (e) {
	// No e.preventDefault() here
	var target = e.target.attributes.href.value;
	if(target == '#customer'){
		$.ajax({
			type: "GET",
			url: "../../api/customerselected?id={{$activity->id}}",
			success: function(data){
				$.each(data, function(i, node) {
					 $("#tree3").fancytree("getTree").getNodeByKey(node).setSelected(true);
					// console.log(node);
					$("#tree3").fancytree("getTree").visit(function(node){
						///if(node.key == node.text){
							///console.log(node);
							//node.setSelected(true);
						//}        
					});
				});
			}
		});
	}

	if(target == '#timings'){
		$('#activity_timings').bootstrapTable("refresh");
	}
	$(this).tab('show');
});

$(".btn-style").click(function (e) {
	e.preventDefault();
	var target = $(".nav-tabs li.active");
	var sibbling;
	if ($(this).text() === "Next") {
		sibbling = target.next();
	} else {
		sibbling = target.prev();
	}

	if (sibbling.is("li")) {
		sibbling.children("a").tab("show");
	}
});

if(location.hash.length > 0){
	var activeTab = $('[href=' + location.hash + ']');
	activeTab && activeTab.tab('show');
}



<!-- activity details -->
$('select#approver,select#category,select#brand').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

$("#involve").chosen();

$('select#objective').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

<!-- Customer details -->

$('select#channel').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

$('select#channel').multiselect('disable');
 
// fancy tree
$("#tree3").fancytree({
	extensions: [],
	checkbox: true,
	selectMode: 3,
	source: {
		url: "../../api/customers?id={{$activity->id}}"
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
		}else{
			$('select#channel').multiselect('deselectAll', false);
			$('select#channel').multiselect('updateButtonText')
			$('select#channel').multiselect('disable');
		}

		$("#customers").val(selRootKeys.join(", "));
	},
});



$("form[id='updateCustomer']").on("submit",function(e){
	var form = $(this);
	var method = form.find('input[name="_method"]').val() || 'POST';
	var url = form.prop('action');
	$.ajax({
		url: url,
		data: form.serialize(),
		method: method,
		dataType: "json",
		success: function(data){
			if(data.success == "1"){
				bootbox.alert("Activity customers was successfully updated."); 
			}else{
				bootbox.alert("An error occured while updating."); 
			}
		}
	});
	e.preventDefault();
});


<!-- Budget details -->


<!-- activity timings -->
$('#activity_timings').bootstrapTable({
    url: 'timings'
});



@stop



