@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-12 col-md-7 col-sm-6">
			<h1>Edit {{ $activity->circular_name }}</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">
	<div class="col-lg-12">
		<div class="form-group">
			{{ HTML::linkRoute('activity.index', 'Back To Activity List', array(), array('class' => 'btn btn-default')) }}
			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myAction">
			  	Actions
			</button>
			<a class="btn btn-info" target="_blank" href="{{ URL::action('ReportController@preview', $activity->id ) }}">Preview</a>
		</div>
	</div>

</div>

<!-- Modal -->
<div class="modal fade" id="myAction" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog">
  		<div class="modal-content">
  			
	    	{{ Form::open(array('action' => array('ActivityController@updateactivity', $activity->id), 'class' => 'bs-component','id' => 'submitactivity')) }}
	      	<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</button>
	        	<h4 class="modal-title" id="myModalLabel">Activity Actions</h4>
	      	</div>
	      	<div class="modal-body">
	      		<div id="error"></div>
	      		
	          	<div class="form-group">
	            	{{ Form::label('submitstatus', 'Status:', array('class' => 'control-label')) }}
	            	{{ Form::select('submitstatus', array('0' => 'PLEASE SELECT') + $submitstatus, null, array('id' => 'submitstatus', 'class' => 'form-control')) }}
	          	</div>
	          	<div class="form-group">
	            	{{ Form::label('submitremarks', 'Comments:', array('class' => 'control-label')) }}
	            	{{ Form::textarea('submitremarks','',array('class' => 'form-control', 'placeholder' => 'Comments', 'size' => '30x5')) }}
	          	</div>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        	<button class="btn btn-primary">Submit</button>	    
	     	</div>
	     	{{ Form::close() }}
	    </div>
  	</div>
</div>


<ul class="nav nav-tabs">
	<li class="active"><a id="tab-activity" aria-expanded="true" href="#activity">Activity Details</a></li>
	<li class=""><a id="tab-customer" aria-expanded="false" href="#customer">Customer Details</a></li>
	<li class=""><a id="tab-schemes" aria-expanded="false" href="#schemes">Schemes</a></li>
	<li class=""><a id="tab-budget" aria-expanded="false" href="#budget">Budget Details</a></li>
	<li class=""><a id="tab-timings" aria-expanded="false" href="#timings">Timings Details</a></li>
	<li class=""><a id="tab-attachments" aria-expanded="false" href="#attachment">Attachments</a></li>
	<li class=""><a id="tab-comments" aria-expanded="false" href="#comments">Comments</a></li>
</ul>

<div id="myTabContent" class="tab-content">

	<!-- activity details -->
	<div class="tab-pane fade active in" id="activity">
		<br>
		{{ Form::open(array('route' => array('activity.update', $activity->id), 'method' => 'PUT', 'class' => 'bs-component','id' => 'updateActivity')) }}
		<div class="panel panel-default">
		  	<div class="panel-heading">Activity Details</div>
		  	<div class="panel-body">
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
									{{ Form::label('lead_time', 'Activity Leadtime (working days)', array('class' => 'control-label')) }}
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
									<select class="form-control" data-placeholder="SELECT CYCLE" id="cycle" name="cycle"></select>
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
		  	</div>
		</div>

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

		<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-primary">Update</button>
						<button class="btn btn-primary btn-style" type="submit">Next</button>
					</div>
				</div>
			</div><br>
			{{ Form::close() }}
	</div>

	<!-- customer details -->
	<div class="tab-pane fade" id="customer">

		<br>
		{{ Form::open(array('action' => array('ActivityController@updatecustomer', $activity->id), 'method' => 'PUT', 'class' => 'bs-component','id' => 'updateCustomer')) }}
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Customer Details</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
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
												<select class="form-control" data-placeholder="SELECT CHANNEL" id="channel" name="channel[]" multiple="multiple" ></select>
											</div>
										</div>
									</div>
								</div>
							</div>	
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<div class="checkbox">
									        <label>
									        	{{ Form::checkbox('allow_force', 1,$activity->allow_force) }} Enable Force Allocation
									        </label>
									    </div>
									</div>
								</div>
							</div>
							<br>
							@if($activity->allow_force)
							<hr>

							<div class="row">
								<div  class="col-lg-12">
									<caption>Force Allocation</caption>
									<table id="force_alloc" class="table table-striped table-hover ">
									  	<thead>
										    <tr>
										    	<th>Group</th>
										      	<th>Area</th>
										      	<th class="multiplier">Force Percentage</th>
								      			<th class="action">Action</th>
										    </tr>
									  	</thead>
									  	<tbody>
									  		@foreach($force_allocs as $force)
									  		<tr data-link="{{ $force->id }}">
									  			<td>{{ $force->group_name }}</td>
									  			<td>{{ $force->area_name }}</td>
								  				<td class="multiplier">{{ $force->multi }}</td>
									  			<td class="action">
									  				<button class="btn btn-primary btn-xs">Update</button>
									  			</td>
									  		</tr>
									  		@endforeach
									  	</tbody>
									</table> 
								</div>
						  	</div>
						  	@endif
							
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<div class="form-group">
					<button class="btn btn-primary">Update</button>
					<button class="btn btn-default btn-style" type="submit">Back</button>
					<button class="btn btn-primary btn-style" type="submit">Next</button>
				</div>
			</div>
		</div>
		{{ Form::close() }}
		<br>
	</div>

	<!-- scheme details -->
	<div class="tab-pane fade" id="schemes">
		<br>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Scheme Lists</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-12">
						<a href="{{ URL::action('SchemeController@create', $activity->id) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Scheme</a>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-lg-12">
						<div class="table-responsive">
							<table class="table table-condensed table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th class="text-center">Scheme Name</th>
										<th class="text-center">Item Code</th>
										<th class="text-center">Purchase Req't</th>
										<th class="text-center">Total Unilever Cost</th>
										<th class="text-center">Cost to Sales % </th>
										<th class="text-center">Total Deals</th>
										<th class="text-center">Total Cases</th>
										<th class="text-center">Total TTS</th>
										<th class="text-center">Total PE</th>
										<th class="text-center">Total Cost</th>
										<th class="text-center">Action</th>
									</tr>
								</thead>
							  	<tbody>
							  		@if(count($schemes) == 0)
							  		<tr>
							  			<td colspan="12">No record found!</td>
							  		</tr>
							  		@endif
							  		<?php $i = 0; ?>
									@foreach($schemes as $scheme)
									<?php $i += 1; ?>
									<tr>
										<td>{{ $i }}</td>
									  	<td>{{ $scheme->name }}</td>
									  	<td>{{ $scheme->item_code }}</td>
									  	<td class="text-right">{{ number_format($scheme->pr,2) }}</td>
									  	<td class="text-right">{{ number_format($scheme->ulp,2) }}</td>
									  	<td class="text-right">{{ number_format($scheme->cost_sale,2) }}</td>
									  	<td class="text-right">{{ number_format($scheme->final_total_deals) }}</td>
									  	<td class="text-right">{{ number_format($scheme->final_total_cases) }}</td>
									  	<td class="text-right">{{ number_format($scheme->final_tts_r,2) }}</td>
									  	<td class="text-right">{{ number_format($scheme->final_pe_r,2) }}</td>
									  	<td class="text-right">{{ number_format($scheme->final_total_cost,2) }}</td>
									  	<td style="width:10%;">
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
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Allocation Summary</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-12">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									<a class="btn btn-success" target="_blank" href="{{ URL::action('ActivityController@allocsummary', $activity->id ) }}">Download Summary</a>		
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
		<br>
	</div>

	<!-- budget details -->
	<div class="tab-pane fade" id="budget">
		<br>
			{{ Form::open(array('action' => array('ActivityController@updatebilling', $activity->id), 'method' => 'PUT', 'class' => 'bs-component','id' => 'updateBilling')) }}
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
							<button class="btn btn-primary">Update</button>
							<button class="btn btn-default btn-style" type="submit">Back</button>
							<button class="btn btn-primary btn-style" type="submit">Next</button>

						</div>
					</div>
				</div>
						{{ Form::close() }}
	</div>

	<!-- timings details -->
	<div class="tab-pane fade" id="timings">
		<br>
		<div class="panel panel-default">
		  	<div class="panel-heading">
				<h3 class="panel-title">Timings Details</h3>
			</div>
		  	<div class="panel-body">
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
		<br>
	</div>

	<!-- attachment details -->
	<div class="tab-pane fade" id="attachment">
		<br>
		<div class="panel panel-default">
		  	<div class="panel-heading">FDA Permit</div>
		  	<div class="panel-body">
		  		@if(count($fdapermits)==0)
		  		{{ Form::open(array('action' => array('ActivityController@fdaupload', $activity->id),  'class' => 'bs-component','id' => 'fdaupload', 'files'=>true)) }}
		  			<div class="row">
						<div class="col-lg-6">
						  	<div class="form-group">
						  		{{ Form::label('permitno', 'FDA Permit No.', array('class' => 'control-label')) }}
								{{ Form::text('permitno','',array('class' => 'form-control', 'placeholder' => 'FDA Permit No.')) }}
						 	</div>
						  	<div class="form-group">
						    	{{ Form::file('file','',array('id'=>'','class'=>'')) }}
						  	</div>
						  	{{ Form::submit('Upload', array('class' => 'btn btn-primary')) }}
					  	</div>
				  	</div>
				{{ Form::close() }}
				@else
					<table class="table table-striped table-hover ">
					  	<thead>
						    <tr>
						      	<th class="permit">FDA Permit No.</th>
						      	<th >File Name</th>
						      	<th class="update">Uploaded Date</th>
						      	<th colspan="2" class="action">Action</th>
						    </tr>
					  	</thead>
					  	<tbody>
					  		@foreach($fdapermits as $permit)
						    <tr>
						      	<td>{{ $permit->permit_no }}</td>
						      	<td>{{ $permit->file_name }}</td>
						      	<td>{{ date_format(date_create($permit->created_at),'m/d/Y H:m:s') }}</td>
						      	<td class="action">
									{{ HTML::linkAction('ActivityController@fdadownload','Download', $permit->id, array('class' => 'btn btn-success btn-xs')) }}
								</td>
						      	<td class="action">
									{{ Form::open(array('method' => 'DELETE', 'action' => array('ActivityController@fdadelete', $permit->id))) }}  
									{{ Form::hidden('activity_id', $activity->id) }}                     
									{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
									{{ Form::close() }}
								</td>
						    </tr>
						    @endforeach
					  	</tbody>
					</table> 
				@endif
		  	</div>
		</div>

		<div class="panel panel-default">
		  	<div class="panel-heading">Product Information Sheet</div>
		  	<div class="panel-body">
		  		<div class="row">
					<div class="col-lg-6">
					  	<div class="form-group">
					    	<a class="btn btn-success" target="_blank" href="{{ URL::action('ActivityController@pistemplate') }}">Download Template</a>		
					  	</div>
			  		</div>
			  	</div>

		  		<hr>
		  		@if(count($fis)==0)
		  		{{ Form::open(array('action' => array('ActivityController@fisupload', $activity->id),  'class' => 'bs-component','id' => 'fisupload', 'files'=>true)) }}
		  			<div class="row">
						<div class="col-lg-6">
						  	<div class="form-group">
						    	{{ Form::file('file','',array('id'=>'','class'=>'')) }}
						  	</div>
						  	{{ Form::submit('Upload', array('class' => 'btn btn-primary')) }}
				  		</div>
				  	</div>
				{{ Form::close() }}
				@else
					<table class="table table-striped table-hover ">
					  	<thead>
						    <tr>
						      	<th>File Name</th>
						      	<th class="update">Uploaded Date</th>
						      	<th colspan="2" class="action">Action</th>
						    </tr>
					  	</thead>
					  	<tbody>
					  		@foreach($fis as $fi)
						    <tr>
						      	<td>{{ $fi->file_name }}</td>
						      	<td>{{ date_format(date_create($fi->created_at),'m/d/Y H:m:s') }}</td>
						      	<td class="action">
									{{ HTML::linkAction('ActivityController@fisdownload','Download', $fi->id, array('class' => 'btn btn-success btn-xs')) }}
								</td>
						      	<td class="action">
									{{ Form::open(array('method' => 'DELETE', 'action' => array('ActivityController@fisdelete', $fi->id))) }}  
									{{ Form::hidden('activity_id', $activity->id) }}                     
									{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete btn btn-success btn-xsthis record?')){return false;};")) }}
									{{ Form::close() }}
								</td>
						    </tr>
						    @endforeach
					  	</tbody>
					</table> 
				@endif
		  	</div>
		</div>

		<div class="panel panel-default">
		  	<div class="panel-heading">Artwork Packshots</div>
		  	<div class="panel-body">

		  		<!-- The fileinput-button span is used to style the file input field as button -->
			    <span class="btn btn-success fileinput-button">
			        <i class="glyphicon glyphicon-plus"></i>
			        <span>Select files...</span>
			        <!-- The file input field used as target for the file upload widget -->
			        <input id="file" type="file" name="file[]" multiple>
			    </span>
			    <br>
			    <br>
			    <!-- The global progress bar -->
			    <div id="progress" class="progress">
			        <div class="progress-bar progress-bar-success"></div>
			    </div>
			    <!-- The container for the uploaded files -->
			    <div id="files" class="files"></div>

		  		{{ Form::open(array('action' => array('ActivityController@artworkupload', $activity->id),  'class' => 'bs-component','id' => 'artworkupload', 'files'=>true)) }}
		  			<div class="row">
						<div class="col-lg-6">
						  	<div class="form-group">
						    	{{ Form::file('file','',array('id'=>'','class'=>'')) }}
						  	</div>
				  		</div>
				  	</div>
				{{ Form::close() }}
				
					<div class="row">
						<div class="col-lg-12">
							<table class="table table-striped table-hover ">
							  	<thead>
								    <tr>
								      	<th>File Name</th>
								      	<th class="update">Uploaded Date</th>
						      			<th colspan="2" class="action">Action</th>
								    </tr>
							  	</thead>
							  	<tbody>
							  		@foreach($artworks as $artwork)
								    <tr>
								      	<td>{{ $artwork->file_name }}</td>
								      	<td>{{ date_format(date_create($artwork->created_at),'m/d/Y H:m:s') }}</td>
								      	<td class="action">
											{{ HTML::linkAction('ActivityController@artworkdownload','Download', $artwork->id, array('class' => 'btn btn-success btn-xs')) }}
										</td>
								      	<td class="action">
											{{ Form::open(array('method' => 'DELETE', 'action' => array('ActivityController@artworkdelete', $artwork->id))) }}  
											{{ Form::hidden('activity_id', $activity->id) }}                     
											{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
											{{ Form::close() }}
										</td>
								    </tr>
								    @endforeach
							  	</tbody>
							</table> 
						</div>
				  	</div>
		  	</div>
		</div>	

		<div class="panel panel-default">
		  	<div class="panel-heading">Marketing Backgrounds</div>
		  	<div class="panel-body">
		  		{{ Form::open(array('action' => array('ActivityController@backgroundupload', $activity->id),  'class' => 'bs-component','id' => 'backgroundupload', 'files'=>true)) }}
		  			<div class="row">
						<div class="col-lg-6">
						  	<div class="form-group">
						    	{{ Form::file('file','',array('id'=>'','class'=>'')) }}
						  	</div>
						  	{{ Form::submit('Upload', array('class' => 'btn btn-primary')) }}
				  		</div>
				  	</div>
				{{ Form::close() }}
					<div class="row">
						<div class="col-lg-12">
							<table class="table table-striped table-hover ">
							  	<thead>
								    <tr>
								      	<th>File Name</th>
								      	<th class="update">Uploaded Date</th>
						      			<th colspan="2" class="action">Action</th>
								    </tr>
							  	</thead>
							  	<tbody>
							  		@foreach($backgrounds as $background)
								    <tr>
								      	<td>{{ $background->file_name }}</td>
								      	<td>{{ date_format(date_create($background->created_at),'m/d/Y H:m:s') }}</td>
								      	<td class="action">
											{{ HTML::linkAction('ActivityController@backgrounddownload','Download', $background->id, array('class' => 'btn btn-success btn-xs')) }}
										</td>
								      	<td class="action">
											{{ Form::open(array('method' => 'DELETE', 'action' => array('ActivityController@backgrounddelete', $background->id))) }}  
											{{ Form::hidden('activity_id', $activity->id) }}                     
											{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
											{{ Form::close() }}
										</td>

								    </tr>
								    @endforeach
							  	</tbody>
							</table> 
						</div>
				  	</div>
		  	</div>
		</div>

		<div class="panel panel-default">
		  	<div class="panel-heading">Banding Guidelines / Activation Mechanics</div>
		  	<div class="panel-body">
		  		{{ Form::open(array('action' => array('ActivityController@bandingupload', $activity->id),  'class' => 'bs-component','id' => 'fisupload', 'files'=>true)) }}
		  			<div class="row">
						<div class="col-lg-6">
						  	<div class="form-group">
						    	{{ Form::file('file','',array('id'=>'','class'=>'')) }}
						  	</div>
						  	{{ Form::submit('Upload', array('class' => 'btn btn-primary')) }}
				  		</div>
				  	</div>
				{{ Form::close() }}
					<div class="row">
						<div class="col-lg-12">
							<table class="table table-striped table-hover ">
							  	<thead>
								    <tr>
								      	<th>File Name</th>
								      	<th class="update">Uploaded Date</th>
						      			<th colspan="2" class="action">Action</th>
								    </tr>
							  	</thead>
							  	<tbody>
							  		@foreach($bandings as $banding)
								    <tr>
								      	<td>{{ $banding->file_name }}</td>
								      	<td>{{ date_format(date_create($banding->created_at),'m/d/Y H:m:s') }}</td>
										<td class="action">
											{{ HTML::linkAction('ActivityController@bandingdownload','Download', $banding->id, array('class' => 'btn btn-success btn-xs')) }}
										</td>
								      	<td class="action">
											{{ Form::open(array('method' => 'DELETE', 'action' => array('ActivityController@bandingdelete', $banding->id))) }}  
											{{ Form::hidden('activity_id', $activity->id) }}                     
											{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
											{{ Form::close() }}
										</td>
								    </tr>
								    @endforeach
							  	</tbody>
							</table> 
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
		<br>
	</div>

		<!-- attachment details -->
	<div class="tab-pane fade" id="comments">
		<br>
		<div class="panel panel-default">
		  	<div class="panel-heading">Comments</div>
		  	<div class="panel-body">
				<ul class="comment">
					@foreach($comments as $comment)
	                <li class="left clearfix">
	                    <div class="comment-body clearfix">
	                        <div class="header">
	                            <strong class="primary-font">{{ $comment->createdby->getFullname()}} 
	                            	<p class="{{ $comment->class }}">({{ $comment->comment_status }})</p>
	                            </strong> 
	                            <small class="pull-right text-muted">
	                                <i class="fa fa-clock-o fa-fw"></i> {{ Carbon::parse($comment->created_at)->subMinutes(2)->diffForHumans()}}
	                            </small>
	                        </div>
	                        <p>{{ $comment->comment }}</p>
	                    </div>
	                </li>
	                @endforeach
	            </ul>
		  	</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="form-group">
					<button class="btn btn-default btn-style" type="submit">Back</button>
				</div>
			</div>
		</div>
		<br>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="myForceAlloc" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Force Allocation</h4>
				</div>
				{{ Form::open(array('action' => array('ActivityController@updateforcealloc'), 'method' => 'PUT', 'class' => 'bs-component','id' => 'updateforcealloc')) }}
				{{ Form::hidden('f_id', '', array('id' => 'f_id')) }}
				<div class="modal-body">
					<table id="forcealloc" class="table table-bordered">
						<tbody>
							<tr>
								<td>Area</td>
								<td field="area_name">
								</td>
							</tr>
							<tr>
								<td>Force Allocation (percentage)</td>
								<td>
									<input class="form-control" placeholder="Force Allocation (percentage)" name="f_percent" type="text" value="" id="f_percent">
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button class="btn btn-primary">Update</button>
				</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>

@include('javascript.activity.edit')

@stop

@section('page-script')

$('#artworkupload').fileupload({
        url: '{{ URL::action('ActivityController@artworkupload', $activity->id ) }}',
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $('<p/>').text(file.name).appendTo('#files');
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
@stop



