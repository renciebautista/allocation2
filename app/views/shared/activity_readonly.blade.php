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
			{{ HTML::linkRoute($route, 'Back To Activity List', array(), array('class' => 'btn btn-default')) }}

			@if(($recall) && ($activity->cycle->submission_deadline > Carbon::now()))
			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myAction">
			  	Actions
			</button>
			@else
			<button type="button" class="btn btn-primary disabled" >Actions</button>
			@endif
			<a class="btn btn-info" target="_blank" href="{{ URL::action('ReportController@preview', $activity->id ) }}">Preview</a>
		</div>
	</div>

</div>

<!-- Modal -->
<div class="modal fade" id="myAction" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog">
  		<div class="modal-content">
	    	{{ Form::open(array('action' => array($submit_action, $activity->id), 'class' => 'bs-component','id' => 'updateactivity')) }}
	      	<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        	<h4 class="modal-title" id="myModalLabel">Activity Actions</h4>
	      	</div>
	      	<div class="modal-body">
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
	<li class="active"><a aria-expanded="true" href="#activty">Activity Details</a></li>
	<li class=""><a aria-expanded="false" href="#customer">Customer Details</a></li>
	<li class=""><a aria-expanded="false" href="#schemes">Schemes</a></li>
	<li class=""><a aria-expanded="false" href="#budget">Budget Details</a></li>
	<li class=""><a aria-expanded="false" href="#timings">Timings Details</a></li>
	<li class=""><a aria-expanded="false" href="#attachment">Attachments</a></li>
	<li class=""><a aria-expanded="false" href="#comments">Comments</a></li>
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
								{{ Form::text('planner',(count($sel_planner) > 0) ? $sel_planner->planner->getFullname() : '', array('class' => 'form-control','readonly' => '')) }}
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
														<button class="btn btn-primary btn-xs disabled">Edit</button>
													</td>
													<td>
														<button class="btn btn-danger btn-xs disabled">Delete</button>
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
					        	{{ Form::checkbox('allow_force', 1,$activity->allow_force, array('disabled' => '')) }} Enable Force Allocation
					        </label>
					    </div>
					</div>
				</div>
			</div>
			@if($activity->allow_force)
			<hr>

			<div class="row">
				<div  class="col-lg-12">
					<caption>Force Allocation</caption>
					<table id="force_alloc" class="table table-striped table-hover ">
					  	<thead>
						    <tr>
						      	<th>Area</th>
						      	<th class="multiplier">Force Percentage</th>
						    </tr>
					  	</thead>
					  	<tbody>
					  		@foreach($force_allocs as $force)
					  		<tr data-link="{{ $force->id }}">
					  			<td>{{ $force->area_name }}</td>
				  				<td class="multiplier">{{ $force->multi }}</td>
					  		</tr>
					  		@endforeach
					  	</tbody>
					</table> 
				</div>
		  	</div>
		  	@endif
			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-primary btn-style" type="submit">Next</button>
						<button class="btn btn-default btn-style" type="submit">Back</button>
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
								  	<td class="text-right">{{ number_format($scheme->ulp,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->cost_sale,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->quantity) }}</td>
								  	<td class="text-right">{{ number_format($scheme->quantity) }}</td>
								  	<td class="text-right">{{ number_format($scheme->tts_r,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->pe_r,2) }}</td>
								  	<td class="text-right">{{ number_format($scheme->total_cost,2) }}</td>
								  	<td>
								  		{{ HTML::linkAction('SchemeController@edit','View', $scheme->id, array('class' => 'btn btn-primary btn-xs')) }}
								  		<button class="btn btn-danger btn-xs disabled">Delete</button>
								  	</td>
								</tr>
								@endforeach
						  	</tbody>
						</table> 
					</div>

				</div>
			</div>

			<hr>
			<div class="row">
				<div class="col-lg-12">
					<h2>Alocation Summary</h2>
					<a class="btn btn-success" target="_blank" href="{{ URL::action('ActivityController@allocsummary', $activity->id ) }}">Download Summary</a>		

				</div>
			</div>
			<br>


			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-primary btn-style" type="submit">Next</button>
						<button class="btn btn-default btn-style" type="submit">Back</button>
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
														<td class="action">
															<button class="btn btn-primary btn-xs disabled">Edit</button>
														</td >
														<td class="action">
															<button class="btn btn-danger btn-xs disabled">Delete</button>
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
													<td class="action">
														<button class="btn btn-primary btn-xs disabled">Edit</button>
													</td >
													<td class="action">
														<button class="btn btn-danger btn-xs disabled">Delete</button>
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
			</div>
			
			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<button class="btn btn-primary btn-style" type="submit">Next</button>
						<button class="btn btn-default btn-style" type="submit">Back</button>
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
						<button class="btn btn-primary btn-style" type="submit">Next</button>
						<button class="btn btn-default btn-style" type="submit">Back</button>
					</div>
				</div>
			</div>
		</div>
	</div>	

	<!-- attachment details -->
	<div class="tab-pane fade" id="attachment">
		<br>
		<div class="panel panel-default">
		  	<div class="panel-heading">FDA Permit</div>
		  	<div class="panel-body">
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
								{{ HTML::linkAction('ActivityController@fdadownload','Download', $permit->id, array('class' => 'btn btn-info btn-xs')) }}
							</td>
					      	<td class="action">
								<button class="btn btn-danger btn-xs disabled">Delete</button>
							</td>
					    </tr>
					    @endforeach
				  	</tbody>
				</table> 
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
								{{ HTML::linkAction('ActivityController@fisdownload','Download', $fi->id, array('class' => 'btn btn-info btn-xs')) }}
							</td>
					      	<td class="action">
								<button class="btn btn-danger btn-xs disabled">Delete</button>
							</td>
					    </tr>
					    @endforeach
				  	</tbody>
				</table>
		  	</div>
		</div>

		<div class="panel panel-default">
		  	<div class="panel-heading">Artwork Packshots</div>
		  	<div class="panel-body">
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
										{{ HTML::linkAction('ActivityController@artworkdownload','Download', $artwork->id, array('class' => 'btn btn-info btn-xs')) }}
									</td>
							      	<td class="action">
										<button class="btn btn-danger btn-xs disabled">Delete</button>
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
										{{ HTML::linkAction('ActivityController@backgrounddownload','Download', $background->id, array('class' => 'btn btn-info btn-xs')) }}
									</td>
							      	<td class="action">
										<button class="btn btn-danger btn-xs disabled">Delete</button>
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
										{{ HTML::linkAction('ActivityController@bandingdownload','Download', $banding->id, array('class' => 'btn btn-info btn-xs')) }}
									</td>
							      	<td class="action">
										<button class="btn btn-danger btn-xs disabled">Delete</button>
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
	</div>

</div>


@stop

@section('page-script')

function sumOfColumns(table, columnIndex) {
    var tot = 0;
    table.find("tr").children("td:nth-child(" + columnIndex + ")")
    .each(function() {
        $this = $(this);
        if (!$this.hasClass("sum") && $this.html() != "") {
            tot += parseInt($this.html());
        }
    });
    return tot;
}

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
$('select#approver').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

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
			updatechannel();
		}else{
			$('select#channel').multiselect('deselectAll', false);
			$('select#channel').multiselect('updateButtonText')
			$('select#channel').multiselect('disable');
		}

		$("#customers").val(selRootKeys.join(", "));
	},
});

function updatechannel(){
	$.ajax({
		type: "GET",
		url: "{{ URL::action('ActivityController@channels', $activity->id ) }}",
		success: function(data){
			$('select#channel').empty();
			$.each(data.selection, function(i, text) {
				var sel_class = '';
				if(data.selected.length > 0){
					if($.inArray( i,data.selected ) > -1){
						sel_class = 'selected="selected"';
					}
				}else{
					sel_class = 'selected="selected"';
				}
				
				$('<option '+sel_class+' value="'+i+'">'+text+'</option>').appendTo($('select#channel')); 
			});
		$('select#channel').multiselect('rebuild');
	   }
	});
}


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
				 location.reload();
				// bootbox.alert("Activity customers was successfully updated."); 
			}else{
				bootbox.alert("An error occured while updating."); 
			}
		}
	});
	e.preventDefault();
});

<!-- schemes -->
var table = $('#scheme_summary').DataTable( {
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
} );

<!-- Budget details -->

<!-- activity timings -->
$('#activity_timings').bootstrapTable({
    url: 'timings'
});

<!-- update activty -->

$("form[id='updateactivity']").on("submit",function(e){
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
				location.reload();
			}else{
				bootbox.alert("An error occured while updating."); 
			}
		}
	});
	e.preventDefault();
});

$("#updateactivity").validate({
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		submitstatus: "is_natural_no_zero",
		submitremarks: "required"

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

$("#myAction" ).on('show.bs.modal', function(){
    $("#submitstatus").val(0);
    $("#submitremarks").val('');
    $('.form-group').removeClass('has-error');
});


@stop



