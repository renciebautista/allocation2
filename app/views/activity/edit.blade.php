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

			{{ HTML::linkAction('ActivityController@download','Download to PMOG', $activity->id, array('class' => 'btn btn-primary', 'onclick' => "if(!confirm('Are you sure to download this activity?')){return false;};")) }}

			<button class="btn btn-warning disabled">Recall</button>
		</div>
	</div>

</div>




<ul class="nav nav-tabs">
	<li class="active"><a aria-expanded="true" href="#activty">Activity Details</a></li>
	<li class=""><a aria-expanded="false" href="#customer">Customer Details</a></li>
	<li class=""><a aria-expanded="false" href="#schemes">Schemes</a></li>
	<li class=""><a aria-expanded="false" href="#budget">Budget Details</a></li>
	<li class=""><a aria-expanded="false" href="#timings">Timings Details</a></li>
	<li class=""><a aria-expanded="false" href="#attachment">Attachment</a></li>
</ul>
<div id="myTabContent" class="tab-content">

	<!-- activity details -->
	<div class="tab-pane fade active in" id="activty">
		<br>
		{{ Form::open(array('route' => array('activity.update', $activity->id), 'method' => 'PUT', 'class' => 'bs-component','id' => 'updateActivity')) }}
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
								{{ Form::select('scope', array('0' => 'PLEASE SELECT') + $scope_types, $activity->scope_type_id, array('class' => 'form-control')) }}
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
								{{ Form::select('planner', array('0' => 'PLEASE SELECT') + $planners, (!is_null($sel_planner)) ? $sel_planner->user_id : 0, array('class' => 'form-control')) }}
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
							<div id="multiselect" class="col-lg-12">
								{{ Form::label('activity_type', 'Activity Type', array('class' => 'control-label')) }}
								{{ Form::select('activity_type',array('0' => 'PLEASE SELECT') + $activity_types, $activity->activity_type_id, array('class' => 'form-control')) }}
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
								{{ Form::text('download_date',date_format(date_create($activity->edownload_date),'m/d/Y'),array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy', 'readonly' => '')) }}								
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('implementation_date', 'Target Implementation Date', array('class' => 'control-label')) }}
								{{ Form::text('implementation_date',date_format(date_create($activity->eimplementation_date),'m/d/Y'),array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy')) }}
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
								{{ Form::select('cycle', array('0' => 'PLEASE SELECT') + $cycles, $activity->cycle_id, array('class' => 'form-control')) }}
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
								{{ Form::text('activity_title',$activity->circular_name	,array('class' => 'form-control', 'placeholder' => 'Activity Title')) }}
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
								{{ Form::select('division',  array('0' => 'PLEASE SELECT') + $divisions, $activity->division_code, array('class' => 'form-control')) }}
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
			</div>

			<div class="row">
  				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('instruction', 'Special Instruction', array('class' => 'control-label')) }}
									{{ Form::textarea('instruction',$activity->instruction ,array('class' => 'form-control', 'placeholder' => 'Special Instruction')) }}
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
								<div class="panel panel-default">
									<div class="panel-heading">
										{{ Form::label('materials', 'Material Sourcing', array('class' => 'control-label')) }}
									</div>
									<div class="panel-body">
										<table id="materials" class="table table-striped table-hover ">
										  	<thead>
												<tr>
											  		<th style="width:20%;">Source</th>
											  		<th style="width:65%;">Material</th>
											  		<th colspan="2">Action</th>
												</tr>
										 	 </thead>
										 	 <tbody>
												@foreach ($materials as $material)
												<tr id="{{ $material->id }}">
													<td class="source">{{ $material->source->source }}</td>
													<td class="material">{{ $material->material }}</td>
													<td>
														<a href="javascript:;" id="{{ $material->id }}" class="ajaxEdit btn btn-primary btn-xs">Edit</a>
													</td>
													<td>
														<a href="javascript:;" id="{{ $material->id }}" class="ajaxDelete btn btn-danger btn-xs">Delete</a>
													</td>
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



			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-primary">Update</button>
						<button class="btn btn-primary btn-style" type="submit">Next</button>
					</div>
				</div>
			</div>


		</div>
		{{ Form::close() }}
	</div>

	<!-- customer details -->
	
	<div class="tab-pane fade" id="customer">
		<br>
		{{ Form::open(array('action' => array('ActivityController@updatecustomer', $activity->id), 'method' => 'PUT', 'class' => 'bs-component','id' => 'updateCustomer')) }}
		<div class="well">
			<div class="row">
				<div class="col-lg-6">
					{{ Form::label('tree3', 'Select Customers', array('class' => 'control-label' )) }}
					<div id="tree3"></div>
					{{ Form::hidden('customers', null, array('id' => 'customers')) }}
				</div>
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
						<button class="btn btn-primary">Update</button>
						<button class="btn btn-primary btn-style" type="submit">Next</button>
					</div>
				</div>
			</div>

		</div>
		{{ Form::close() }}
	</div>

	<!-- scheme details -->
	<div class="tab-pane fade" id="schemes">

		<br>
		<div class="well">
			<a href="{{ URL::action('SchemeController@create', $activity->id) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Scheme</a>
			<div class="row">
				<div class="col-lg-12">

					<div class="table-responsive tableScroll">
						<table class="table table-striped table-hover ">
							<thead>
								<tr>
									<th>Scheme Name</th>
									<th>Item Code</th>
									<th>Purchase Req't</th>
									<th>Total Unilever Cost</th>
									<th>Cost to Sales % </th>
									<th>Total Deals</th>
									<th>Total Cases</th>
									<th>Total TTS</th>
									<th>Total PE</th>
									<th>Total Cost</th>
									<th class="text-center">Action</th>
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
								  	<td>{{ $scheme->name }}</td>
								  	<td>{{ $scheme->item_code }}</td>
								  	<td class="text-right">{{ number_format($scheme->pr,2) }}</td>
								  	<!-- <td class="text-right">{{ number_format($scheme->srp_p,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->other_cost,2) }}</td> -->
								  	<td class="text-right">{{ number_format($scheme->ulp,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->cost_sale,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->quantity) }}</td>
								  	<td class="text-right">{{ number_format($scheme->quantity) }}</td>
								  	<!-- <td>{{ $activity->activitytype->uom }}</td> -->
								  	<!-- <td class="text-right">{{ number_format($scheme->deals) }}</td> -->
								  	<td class="text-right">{{ number_format($scheme->tts_r,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->pe_r,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->total_cost,2) }}</td>
								  	<td>
								  		{{ HTML::linkAction('SchemeController@edit','View', $scheme->id, array('class' => 'btn btn-primary btn-xs')) }}
								  		<a class="btn btn-danger btn-xs" href="#">Delete</a>
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
					<h2>Alocation Summary</h2>
					<div id="allocation" class="table-responsive">
						<table id="scheme_summary" class="table table-condensed table-bordered display compact ">
							<thead>
								<tr>
									<th>Customers</th>
									@foreach($schemes as $scheme)
										<th class="text-center" colspan="4">{{ $scheme->name }}</th>
									@endforeach
								</tr>
								
								<tr>
									<th>Group</th>
									<th>Area</th>
									<th>Sold To</th>
									<th>Ship To</th>
									<th>Channel</th>
									<th>Outlet</th>
									@foreach($schemes as $scheme)
										<th class="rotate-45"><div><span>Deals</span></th>
										<th class="rotate-45"><div><span>Cases</span></th>
										<th class="rotate-45"><div><span>TTS Budget</span></th>
										<th class="rotate-45"><div><span>PE Budget</span></th>
									@endforeach
								</tr>
							</thead>
						  	<tbody>
						  		@if(count($scheme_customers) == 0)
						  		<tr>
						  			<td colspan="15">No record found!</td>
						  		</tr>
						  		@endif

								@foreach($scheme_customers as $scheme_customer)
								<tr>
									<td>{{ $scheme_customer->group }}</td>
									<td>{{ $scheme_customer->area }}</td>
									<td>{{ $scheme_customer->sold_to }}</td>
									<td>{{ $scheme_customer->ship_to }}</td>
									<td>{{ $scheme_customer->channel }}</td>
									<td>{{ $scheme_customer->outlet }}</td>
									@foreach($schemes as $scheme)
										@if($activity->activitytype->uom == "CASES")
										<td class="text-right">0</td>
										<td class="text-right">
											@if(isset($scheme_allcations[$scheme->id][md5($scheme_customer->group.'.'.$scheme_customer->area.'.'.$scheme_customer->sold_to.'.'.$scheme_customer->ship_to.'.'.$scheme_customer->channel.'.'.$scheme_customer->outlet)]))
											{{ number_format($scheme_allcations[$scheme->id][md5($scheme_customer->group.'.'.$scheme_customer->area.'.'.$scheme_customer->sold_to.'.'.$scheme_customer->ship_to.'.'.$scheme_customer->channel.'.'.$scheme_customer->outlet)]) }}
											@endif
										</td>
										@else
										<td class="text-right">
											@if(isset($scheme_allcations[$scheme->id][md5($scheme_customer->group.'.'.$scheme_customer->area.'.'.$scheme_customer->sold_to.'.'.$scheme_customer->ship_to.'.'.$scheme_customer->channel.'.'.$scheme_customer->outlet)]))
											{{ number_format($scheme_allcations[$scheme->id][md5($scheme_customer->group.'.'.$scheme_customer->area.'.'.$scheme_customer->sold_to.'.'.$scheme_customer->ship_to.'.'.$scheme_customer->channel.'.'.$scheme_customer->outlet)]) }}
											@endif
										</td>
										<td class="text-right">0</td>
										@endif
										
										<td class="text-right">1</td>
										<td class="text-right">1</td>
									@endforeach
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
												<th style="width:9%;">Type</th>
												<th>IO</th>
												<th>Amount</th>
												<th>Start Date</th>
												<th>End Date</th>
												<th>Remarks</th>
												<th colspan="2">Action</th>
											</tr>
										</thead>
										<tbody>

											@foreach ($budgets as $budget)
											<tr id="{{ $budget->id }}">
												<td class="io_ttstype">{{ $budget->budgettype->budget_type }}</td>
												<td class="io_no">{{ strtoupper($budget->io_number) }}</td>
												<td class="io_amount">{{ number_format($budget->amount,2) }}</td>
												<td class="io_startdate">{{ date_format(date_create($budget->start_date),'m/d/Y') }}</td>
												<td class="io_enddate">{{ date_format(date_create($budget->end_date),'m/d/Y') }}</td>
												<td class="io_remarks">{{ $budget->remarks }}</td>
												<td>
													<a href="javascript:;" id="{{ $budget->id }}" class="ajaxEdit btn btn-primary btn-xs">Edit</a>
												</td>
												<td><a href="javascript:;" id="{{ $budget->id }}" class="ajaxDelete btn btn-danger btn-xs">Delete</a></td>
											</tr>
											@endforeach
										</tbody>
									</table> 
								</div>
							</div>
						</div>
					</div>
				</div>
				{{ Form::open(array('action' => array('ActivityController@updatebilling', $activity->id), 'method' => 'PUT', 'class' => 'bs-component','id' => 'updateBilling')) }}
				<div class="row">
					<div class="col-lg-6">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-6">
									{{ Form::label('billing_deadline', 'Billing Deadline', array('class' => 'control-label')) }}
									{{ Form::text('billing_deadline',date_format(date_create($activity->billing_date),'m/d/Y'), array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy')) }}
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

				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<button class="btn btn-primary">Update</button>
						</div>
					</div>
				</div>

				{{ Form::close() }}

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
												<th style="width:9%;">Type</th>
												<th>Budget Holder</th>
												<th>Budget Name</th>
												<th>Amount</th>
												<th>Start Date</th>
												<th>End Date</th>
												<th>Remarks</th>
												<th colspan="2">Action</th>
											</tr>
										</thead>
										<tbody>
										@foreach ($nobudgets as $nobudget)
										<tr id="{{ $nobudget->id }}">
											<td class="budget_ttstype">{{ $nobudget->budgettype->budget_type }}</td>
											<td class="budget_no">{{ $nobudget->budget_no }}</td>
											<td class="budget_name">{{ $nobudget->budget_name }}</td>
											<td class="budget_amount">{{ number_format($nobudget->amount,2) }}</td>
											<td class="budget_startdate">{{ date_format(date_create($nobudget->start_date),'m/d/Y') }}</td>
											<td class="budget_enddate">{{ date_format(date_create($nobudget->end_date),'m/d/Y') }}</td>
											<td class="budget_remarks">{{ $nobudget->remarks }}</td>
											<td>
												<a href="javascript:;" id="{{ $nobudget->id }}" class="ajaxEdit btn btn-primary btn-xs">Edit</a>
											</td>
											<td><a href="javascript:;" id="{{ $nobudget->id }}" class="ajaxDelete btn btn-danger btn-xs">Delete</a></td>
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

	<!-- attachment details -->
	<div class="tab-pane fade" id="attachment">
		<br>
		<div class="well">
			<div class="row">
				<div class="col-lg-12">
					<caption>FDA Permit</caption>
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

					<caption>Product Information Sheet</caption>
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

					<caption>Artwork Packshot</caption>
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
		getCustomer();
	}

	if(target == '#timings'){
		$('#activity_timings').bootstrapTable("refresh");
	}
	$(this).tab('show');
});

function getCustomer(){
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

$("a[href='#customer']").on('shown.bs.tab', function(e) {
    getCustomer();
});

$("a[href='#schemes']").on('shown.bs.tab', function(e) {
    $( $.fn.dataTable.tables( true ) ).DataTable().columns.adjust();
});




<!-- activity details -->
function duration(value){
	$.ajax({
		type: "GET",
		url: "../../activitytype/"+value+"/network/totalduration",
		success: function(msg){
			$('#lead_time').val(msg);

			$('#implementation_date').val(moment().add(msg,'days').format('MM/DD/YYYY'));
			$('#download_date').val(moment().format('MM/DD/YYYY'))

			$('#implementation_date').data("DateTimePicker").setMinDate(moment().add(msg,'days').format('MM/DD/YYYY'));
		},
		error: function(){
			alert("failure");
		}
	});
}


$('select#approver').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

$('select#activity_type').on("change",function(){
	duration($(this).val());
});

$('#implementation_date').datetimepicker({
	pickTime: false,
	calendarWeeks: true,
	minDate: moment()
});

$("#implementation_date").on("dp.change",function (e) {
	// console.log(moment(e.date).subtract($('#lead_time').val(),'days').format('MM/DD/YYYY'));
	$('#download_date').val(moment(e.date).subtract($('#lead_time').val(),'days').format('MM/DD/YYYY'));
});

$('#implementation_date').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});

function updatecategory(id){
	$.ajax({
		type: "POST",
		data: {q: id, id: {{ $activity->id }}},
		url: "../../api/category/getselected",
		success: function(data){
			$('select#category').empty();
			$.each(data.selection, function(i, text) {
				var sel_class = '';
				if($.inArray( i,data.selected ) > -1){
					sel_class = 'selected="selected"';
				}
				$('<option '+sel_class+' value="'+i+'">'+text+'</option>').appendTo($('select#category')); 
			});
		$('select#category').multiselect('rebuild');
		updatebrand();
	   }
	});
}

function updatebrand(){
	$.ajax({
			type: "POST",
			data: {categories: GetSelectValues($('select#category :selected')),id: {{ $activity->id }}},
			url: "../../api/brand/getselected",
			success: function(data){
				$('select#brand').empty();
				$.each(data.selection, function(i, text) {
					var sel_class = '';
					if($.inArray( i,data.selected ) > -1){
						sel_class = 'selected="selected"';
					}
					$('<option '+sel_class+' value="'+i+'">'+text+'</option>').appendTo($('select#brand'));
				});
			$('select#brand').multiselect('rebuild');
		   }
		});
}


var div = $("select#division").val();
if(parseInt(div) > 0) {
   updatecategory(div);
}


$('select#division').on("change",function(){
	updatecategory($(this).val());
});


$('select#category').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true,
	onDropdownHide: function(event) {
		updatebrand();
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


$('select#objective').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});


$("form[id='updateActivity']").on("submit",function(e){
	var form = $(this);
	var method = form.find('input[name="_method"]').val() || 'POST';
	var url = form.prop('action');
	if(form.valid()){
		$.ajax({
			url: url,
			data: form.serialize(),
			method: method,
			dataType: "json",
			success: function(data){
				if(data.success == "1"){
					bootbox.alert("Activity details was successfully updated."); 
				}else{
					bootbox.alert("An error occured while updating."); 
				}
			}
		});
	}
	
	e.preventDefault();
});

$("#updateActivity").validate({
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		activity_title: "required",
		planner: "is_natural_no_zero",
		scope: "is_natural_no_zero",
		activity_type: "is_natural_no_zero",
		cycle: "is_natural_no_zero",
		implementation_date: {
			required: true,
			greaterdate : true
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

$.validator.addMethod("greaterdate", function(value, element) {
	return this.optional(element) || (moment(value).isAfter(moment().format('MM/DD/YYYY')) || moment(value).isSame(moment().format('MM/DD/YYYY')));
}, "Please select from the list.");

$('#materials').ajax_table({
	add_url: "{{ URL::action('ActivityController@addmaterial', $activity->id ) }}",
	delete_url: "{{ URL::action('ActivityController@deletematerial') }}",
	update_url: "{{ URL::action('ActivityController@updatematerial') }}",
	columns: [
		{ type: "select", id: "source", ajax_url: "{{ URL::action('api\MaterialController@getsource') }}" },
		{ type: "text", id: "material", placeholder: "Remarks" }
	],onError: function (){
		bootbox.alert("Unexpected error, Please try again"); 
	}
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

<!-- schemes -->

		

/*var table = $('#scheme_summary').DataTable( {
	"scrollY": "300px",
	"scrollX": true,
	"scrollCollapse": true,
	"paging": false,
	"bSort": false,
	"columnDefs": [ { //this prevents errors if the data is null
		"targets": "_all",
		"defaultContent": ""
	} ],
} );
new $.fn.dataTable.FixedColumns( table, {
	leftColumns: 6
} );*/
<!-- Budget details -->

$('#billing_deadline').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
$('#billing_deadline').datetimepicker({
	pickTime: false,
	calendarWeeks: true,
	minDate: moment()
});

$("form[id='updateBilling']").on("submit",function(e){
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
				bootbox.alert("Billling details was successfully updated."); 
			}else{
				bootbox.alert("An error occured while updating."); 
			}
		}
	});
	e.preventDefault();
});

$('#budget_table').ajax_table({
	add_url: "{{ URL::action('ActivityController@addbudget', $activity->id ) }}",
	delete_url: "{{ URL::action('ActivityController@deletebudget') }}",
	update_url: "{{ URL::action('ActivityController@updatebudget') }}",
	columns: [
		{ type: "select", id: "io_ttstype" , ajax_url: "{{ URL::action('api\BudgetTypeController@gettype') }}"},
		{ type: "text", id: "io_no", placeholder: "IO Number" },
		{ type: "text", id: "io_amount", placeholder: "Amount" },
		{ type: "text", id: "io_startdate", placeholder: "mm/dd/yyyy" },
		{ type: "text", id: "io_enddate", placeholder: "mm/dd/yyyy" },
		{ type: "text", id: "io_remarks", placeholder: "Remarks" },
	],
	onError: function (){
		bootbox.alert("Unexpected error, Please try again"); 
	},onInitRow: function() {
		$('#io_startdate, #io_enddate').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
		$('#io_startdate, #io_enddate').datetimepicker({
			pickTime: false,
			calendarWeeks: true,
			minDate: moment()
		});
		$('#io_amount').inputNumber();
		$("#io_no").mask("aa99999999");
	},onEditRow : function(){
		$('#io_startdate, #io_enddate').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
		$('#io_startdate, #io_enddate').datetimepicker({
			pickTime: false,
			calendarWeeks: true,
			minDate: moment()
		});
		$('#io_amount').inputNumber();
		$("#io_no").mask("aa99999999");
	},onError: function (){
		bootbox.alert("Unexpected error, Please try again"); 
	}
});




$('#no_budget_table').ajax_table({
	add_url: "{{ URL::action('ActivityController@addnobudget', $activity->id ) }}",
	delete_url: "{{ URL::action('ActivityController@deletenobudget') }}",
	update_url: "{{ URL::action('ActivityController@updatenobudget') }}",
	columns: [
		{ type: "select", id: "budget_ttstype" , ajax_url: "{{ URL::action('api\BudgetTypeController@gettype') }}"},
		{ type: "text", id: "budget_no", placeholder: "Budget Number" },
		{ type: "text", id: "budget_name", placeholder: "Budget Name" },
		{ type: "text", id: "budget_amount", placeholder: "Amount" },
		{ type: "text", id: "budget_startdate", placeholder: "mm/dd/yyyy" },
		{ type: "text", id: "budget_enddate", placeholder: "mm/dd/yyyy" },
		{ type: "text", id: "budget_remarks", placeholder: "Remarks" },
	],
	onInitRow: function() {
		$('#budget_startdate, #budget_enddate').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
		$('#budget_startdate, #budget_enddate').datetimepicker({
			pickTime: false,
			calendarWeeks: true,
			minDate: moment()
		});
		$('#budget_amount').inputNumber();
	},onEditRow : function(){
		$('#budget_startdate, #budget_enddate').mask("99/99/9999",{placeholder:"mm/dd/yyyy"});
		$('#budget_startdate, #budget_enddate').datetimepicker({
			pickTime: false,
			calendarWeeks: true,
			minDate: moment()
		});
		$('#budget_amount').inputNumber();
	},onError: function (){
		bootbox.alert("Unexpected error, Please try again"); 
	}

});


<!-- activity timings -->
$('#activity_timings').bootstrapTable({
    url: 'timings'
});


@stop



