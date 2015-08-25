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

			@if($activity->status_id < 9)
				@if($recall)
				<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myAction">
			  		Actions
				</button>
				@else
					<button type="button" class="btn btn-primary disabled" >Actions</button>
				@endif
			@else
				<button type="button" class="btn btn-primary disabled" >Actions</button>
			@endif
			
			@include('partials.downloaddoc')
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
	        	<button class="btn btn-primary" onclick="return confirm('Are you sure?')">Submit</button>	    
	     	</div>
	     	{{ Form::close() }}
	    </div>
  	</div>
</div>



<ul class="nav nav-tabs">
	<li class="active"><a id="tab-activity" aria-expanded="true" href="#activity">Activity Details</a></li>
	<li class=""><a id="tab-customer" aria-expanded="false" href="#customer">Customer Details</a></li>
	@if($activity->activitytype->with_scheme)
	<li class=""><a id="tab-schemes" aria-expanded="false" href="#schemes">Schemes</a></li>
	@else

	@endif
	<li class=""><a id="tab-budget" aria-expanded="false" href="#budget">Budget Details</a></li>
	<li class=""><a id="tab-timings" aria-expanded="false" href="#timings">Timings Details</a></li>
	<li class=""><a id="tab-attachments" aria-expanded="false" href="#attachment">Attachments</a></li>
	<li class=""><a id="tab-comments" aria-expanded="false" href="#comments">Comments</a></li>
</ul>
<div id="myTabContent" class="tab-content">

	<!-- activity details -->
	<div class="tab-pane fade active in" id="activity">
		<br>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Activity Details</h3>
			</div>
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
					<div class="col-lg-2">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('download_date', 'Target Download Date ', array('class' => 'control-label')) }}
									{{ Form::text('download_date',date_format(date_create($activity->edownload_date),'m/d/Y'),array('class' => 'form-control', 'readonly' => '')) }}								
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-2">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('implementation_date', 'Target Start Date', array('class' => 'control-label')) }}
									{{ Form::text('implementation_date',date_format(date_create($activity->eimplementation_date),'m/d/Y'),array('class' => 'form-control', 'readonly' => '')) }}
								</div>
							</div>
						</div>
					</div>	
					<div class="col-lg-2">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('end_date', 'Target End Date', array('class' => 'control-label')) }}
								{{ Form::text('end_date',date_format(date_create($activity->end_date),'m/d/Y'),array('class' => 'form-control', 'readonly' => '')) }}
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
					<div class="col-lg-6">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('division', 'Division', array('class' => 'control-label')) }}
									{{ Form::select('division[]',  $divisions, $sel_divisions, array('id' => 'division', 'class' => 'form-control' ,'multiple' => 'multiple' ,'data-placeholder' => 'SELECT DIVISION')) }}
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<div class="row">
								<div id="multiselect" class="col-lg-12">
									{{ Form::label('category', 'Category', array('class' => 'control-label')) }}
									<select class="form-control" data-placeholder="SELECT CATEGORY" id="category" name="category[]" multiple="multiple" ></select>
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
									{{ Form::label('brand', 'Brand', array('class' => 'control-label')) }}
									<select class="form-control" data-placeholder="SELECT BRAND" id="brand" name="brand[]" multiple="multiple" ></select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('skus', 'SKU/s Involved', array('class' => 'control-label')) }}
									<select class="form-control" data-placeholder="SELECT SKU/s" id="skus" name="skus[]" multiple="multiple" ></select>
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
									{{ Form::textarea('background',$activity->background,array('class' => 'form-control', 'placeholder' => 'Background and Rationale' ,'readonly' => '')) }}
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
										{{ Form::textarea('instruction',$activity->instruction ,array('class' => 'form-control', 'placeholder' => 'Special Instruction','readonly' => '')) }}
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

		<div class="row">
			<div class="col-lg-12">
				<div class="form-group">
					<button class="btn btn-primary btn-style" type="submit">Next</button>
				</div>
			</div>
		</div>
		<br>
	</div>

	<!-- customer details -->
	<div class="tab-pane fade" id="customer">
		<br>
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
									{{ Form::label('tree4', 'Select DT Channels Involved', array('class' => 'control-label' )) }}
									<div id="tree4"></div>
									{{ Form::hidden('channels_involved', null, array('id' => 'channels_involved')) }}
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
							<div class="row">
								<div  class="col-lg-12">
									<caption>Force Allocation</caption>
									<table id="force_alloc" class="table table-striped table-condensed table-hover table-bordered">
									  	<thead>
										    <tr>
										    	<th style="width:10%;">Group</th>
										      	<th>Area</th>
										      	<th>Sales Multiplier</th>
										    </tr>
									  	</thead>
									  	<tbody>
									  		@foreach($areas as $area)
									  		<tr>
									  			<td>{{ $area->group_name }}</td>
									  			<td>{{ $area->area_name }}</td>
								  				<td><input class="input-number" disabled="" id="{{ $area->area_code }}"  name="force_alloc[{{ $area->area_code }}]" value="{{ $area->multi }}" type="text"></td>
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
					<button class="btn btn-default btn-style" type="submit">Back</button>
					<button class="btn btn-primary btn-style" type="submit">Next</button>
				</div>
			</div>
		</div>
		<br>
	</div>

	@if($activity->activitytype->with_scheme)
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
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									<table id="budget_table" class="table table-striped table-hover ">
										<thead>
											<tr>
												<th>#</th>
												<th class="text-center">Scheme Name</th>
												<th class="text-center">Item Code</th>
												<th class="text-center" style="width:80px;">Purchase Req't</th>
												<th class="text-center" style="width:80px;">Total Unilever Cost</th>
												<th class="text-center" style="width:80px;">Cost to Sales % </th>
												<th class="text-center" style="width:80px;">Total Deals</th>
												<th class="text-center" style="width:80px;">Total Cases</th>
												<th class="text-center" style="width:80px;">Total TTS</th>
												<th class="text-center" style="width:80px;">Total PE</th>
												<th class="text-center" style="width:80px;">Total Cost</th>
												<th class="text-center" style="width:110px;">Action</th>
											</tr>
										</thead>
									  	<tbody>
									  		@if(count($schemes) == 0)
									  		<tr>
									  			<td colspan="15">No record found!</td>
									  		</tr>
									  		@else

											<?php $i = 0; ?>
											@foreach($schemes as $scheme)
											<?php $i += 1; ?>
											
											<tr>
												<td>{{ $i }}</td>
											  	<td>{{ $scheme->name }}</td>
											  	<td>{{ $scheme->item_code }}</td>
											  	<td class="text-right"></td>
											  	<td class="text-right"></td>
											  	<td class="text-right"></td>
											  	<td class="text-right">{{ number_format($scheme->final_total_deals) }}</td>
											  	<td class="text-right">{{ number_format($scheme->final_total_cases) }}</td>
											  	<td class="text-right">{{ number_format($scheme->final_tts_r,2) }}</td>
											  	<td class="text-right">{{ number_format($scheme->final_pe_r,2) }}</td>
											  	<td class="text-right">{{ number_format($scheme->final_total_cost,2) }}</td>
											  	<td>
											  		{{ HTML::linkAction('SchemeController@edit','View', $scheme->id, array('class' => 'btn btn-primary btn-xs')) }}
											  		<button class="btn btn-danger btn-xs disabled">Delete</button>
											  	</td>
											</tr>
											@endforeach
											@endif
									  	</tbody>
									  	<tfoot>
									  		<tr>
												<th class="text-center" colspan="3">Sub Total</th>
												<th class="text-right" style="width:80px;">{{ number_format($scheme_summary->pr,2) }}</th>
												<th class="text-right" style="width:80px;">{{ number_format($scheme_summary->ulp,2) }}</th>
												<th class="text-right" style="width:80px;">{{ number_format($scheme_summary->cost_sale,2) }}</th>
												<th class="text-right" style="width:80px;">{{ number_format($scheme_summary->final_total_deals) }}</th>
												<th class="text-right" style="width:80px;">{{ number_format($scheme_summary->final_total_cases) }}</th>
												<th class="text-right" style="width:80px;">{{ number_format($scheme_summary->final_tts_r,2) }}</th>
												<th class="text-right" style="width:80px;">{{ number_format($scheme_summary->final_pe_r,2) }}</th>
												<th class="text-right" style="width:80px;">{{ number_format($scheme_summary->final_total_cost,2) }}</th>
												<th class="text-right" style="width:110px;"></th>
											</tr>
									  	</tfoot>
									</table> 
								</div>
							</div>
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

	@else

	@endif

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
														<th style="width:10%;" colspan="2">Action</th>
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
											@if($activity->billing_date != "")
											{{ Form::text('billing_deadline',date_format(date_create($activity->billing_date),'m/d/Y'), array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy', 'readonly' => '')) }}
											@else
											{{ Form::text('billing_deadline',null, array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy', 'readonly' => '')) }}
											@endif
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
											{{ Form::textarea('billing_remarks',$activity->billing_remarks, array('class' => 'form-control', 'placeholder' => 'Billing Requirements' , 'size' => '50x5', 'readonly' => '')) }}
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
														<th style="width:10%;" colspan="2">Action</th>
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
						<button class="btn btn-default btn-style" type="submit">Back</button>
						<button class="btn btn-primary btn-style" type="submit">Next</button>
					</div>
				</div>
			</div>
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
							  		<th>Task ID</th>
							        <th>Milestone</th>
							        <th>Task</th>
							        <th>Team Responsible</th>
							       	<th>Duration (days)</th>
							        <th>Depends On</th>
							  		<th>Start Date</th>
							  		<th>End Date</th>
							  		<th>Final Start Date</th>
							  		<th>Final End Date</th>
								</tr>
						  	</thead>
						  	<tbody>
								@if(count($timings) == 0)
								<tr>
									<td colspan="10">No record found!</td>
								</tr>
								@else
								@foreach($timings as $timing)
								<tr>
									<td>{{ $timing->task_id }}</td>
									<td>{{ $timing->milestone }}</td>
									<td>{{ $timing->task }}</td>
									<td>{{ $timing->responsible }}</td>
									<td>{{ $timing->duration }}</td>
									<td>{{ $timing->depend_on }}</td>
									<td>{{ date_format(date_create($timing->start_date),'m/d/Y') }}</td>
									<td>{{ date_format(date_create($timing->end_date),'m/d/Y') }}</td>
									<td>
										@if($timing->final_start_date != null)
										{{ date_format(date_create($timing->final_start_date),'m/d/Y') }}
										@else
										@endif
									</td>
									<td>
										@if($timing->final_end_date != null)
										{{ date_format(date_create($timing->final_end_date),'m/d/Y') }}
										@else
										@endif
									</td>
								</tr>
								@endforeach
								@endif
							</tbody>
						</table> 
					</div>
				</div>
		  	</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Roles and Responsibilities</h3>
			</div>
			<div class="panel-body">
				<div id="roles"></div>
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
				<table class="table table-striped table-hover ">
				  	<thead>
					    <tr>
					      	<th class="permit">FDA Permit No.</th>
					      	<th >File Name</th>
					      	<th class="update">Uploaded Date</th>
					      	<th style="width:15%;" colspan="2">Action</th>
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
		  	<div class="panel-heading">Banding Guidelines / Activation Mechanics / Others</div>
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
	                                <i class="fa fa-clock-o fa-fw"></i> {{ date_format(date_create($comment->created_at),'m/d/Y H:m:s') }} 
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

</div>


@stop

@section('page-script')




if(location.hash.length > 0){
	var activeTab = $('[href=' + location.hash + ']');
	activeTab && activeTab.tab('show');
}

// Change hash for page-reload
$('.nav-tabs a').on('shown', function (e) {
    window.location.hash = e.target.hash;
})

$('.nav-tabs a').click(function (e) {
	pre = "#activity";
	if(window.location.hash.length > 0){
		pre = window.location.hash;
	}
	var target = $(this);
	target_id = $(pre).find('form').attr('id');	
	$(target).tab('show');
});

$(".btn-style").click(function (e) {
	e.preventDefault();
	target_id = $(this.closest('form')).attr('id');
	var target = $(".nav-tabs li.active");
	var sibbling;
	if ($(this).text() === "Next") {
		sibbling = target.next();
	} else {
		sibbling = target.prev();
	}

	if (sibbling.is("li")) {
		$('#'+sibbling.children("a").attr("id")).trigger('click');
		str = sibbling.children("a").attr("href");
		location.hash = str.replace("#","");
	}
});



$("a[href='#customer']").on('shown.bs.tab', function(e) {
    getCustomer();
});

$("a[href='#schemes']").on('shown.bs.tab', function(e) {
    $( $.fn.dataTable.tables( true ) ).DataTable().columns.adjust();
});




<!-- activity details -->
$('select#approver').multiselect({
	maxHeight: 200,
	onDropdownShow: function(event) {
        $('select#approver option').each(function() {
          	var input = $('input[value="' + $(this).val() + '"]');
          	input.prop('disabled', true);
            input.parent('li').addClass('disabled');
        });
    }
});


function updatecategory(){
	$.ajax({
		type: "POST",
		data: {divisions: GetSelectValues($('select#division :selected')),id: {{ $activity->id }}},
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
			updateskus();
		   }
		});
}

function updateskus(){
	$.ajax({
			type: "POST",
			data: {brand: GetSelectValues($('select#brand :selected')),id: {{ $activity->id }}},
			url: "../../api/sku/skuselected",
			success: function(data){
				$('select#skus').empty();
				$.each(data.selection, function(i, text) {
					var sel_class = '';
					if($.inArray( i,data.selected ) > -1){
						sel_class = 'selected="selected"';
					}
					$('<option '+sel_class+' value="'+i+'">'+text+'</option>').appendTo($('select#skus'));
				});
			$('select#skus').multiselect('rebuild');
		   }
		});
}

var div = $("select#division").val();
if(parseInt(div) > 0) {
  updatecategory();
}


$('select#division').multiselect({
	maxHeight: 200,
	onDropdownShow: function(event) {
        $('select#division option').each(function() {
          	var input = $('input[value="' + $(this).val() + '"]');
          	input.prop('disabled', true);
            input.parent('li').addClass('disabled');
        });
    },
	onDropdownHide: function(event) {
		updatecategory();
	}
});


$('select#category').multiselect({
	maxHeight: 200,
	onDropdownShow: function(event) {
        $('select#category option').each(function() {
          	var input = $('input[value="' + $(this).val() + '"]');
          	input.prop('disabled', true);
            input.parent('li').addClass('disabled');
        });
    },
	onDropdownHide: function(event) {
		updatebrand();
	}
});

$('select#brand').multiselect({
	maxHeight: 200,
	numberDisplayed: 1,
	onDropdownShow: function(event) {
        $('select#brand option').each(function() {
          	var input = $('input[value="' + $(this).val() + '"]');
          	input.prop('disabled', true);
            input.parent('li').addClass('disabled');
        });
    },
	onDropdownHide: function(event) {
		updateskus();
	}
});


$('select#objective').multiselect({
	maxHeight: 200,
	numberDisplayed: 1,
	onDropdownShow: function(event) {
        $('select#objective option').each(function() {
          	var input = $('input[value="' + $(this).val() + '"]');
          	input.prop('disabled', true);
            input.parent('li').addClass('disabled');
        });
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
		url: "../../api/customers?id={{$activity->id}}&status=2"
	},
	select: function(event, data) {
		// Get a list of all selected TOP nodes
		var selRootNodes = data.tree.getSelectedNodes(true);
		// ... and convert to a key array:
		var selRootKeys = $.map(selRootNodes, function(node){
		  return node.key;
		});

		var keys = selRootKeys.join(".").split(".");
		// console.log(keys);
		if($.inArray('E1397', keys) != -1){
			$("#tree4").fancytree("enable");
			getChannel();
		}else{
			$("#tree4").fancytree("getTree").visit(function(node){
		        node.setSelected(false);
		    });
			$("#tree4").fancytree("disable");
		}
	}
});

function getCustomer(){
	$.ajax({
		type: "GET",
		url: "../../api/customerselected?id={{$activity->id}}",
		success: function(data){
			$.each(data, function(i, node) {
				$("#tree3").fancytree("getTree").getNodeByKey(node).setSelected(true);
			});
		}
	});
}


$("#tree4").fancytree({
	extensions: [],
	checkbox: true,
	selectMode: 3,
	source: {
		url: "../../api/channels?id={{$activity->id}}"
	}
});


function getChannel(){
	$.ajax({
		type: "GET",
		url: "../../api/channelselected?id={{$activity->id}}",
		success: function(data){
			$.each(data, function(i, node) {
				$("#tree4").fancytree("getTree").getNodeByKey(node).setSelected(true);
			});
		}
	});
}

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


<!-- schemes -->

<!-- Budget details -->

<!-- activity timings -->
$('select#skus').multiselect({
	maxHeight: 200,
	onDropdownShow: function(event) {
        $('select#skus option').each(function() {
          	var input = $('input[value="' + $(this).val() + '"]');
          	input.prop('disabled', true);
            input.parent('li').addClass('disabled');
        });
    }
});

var $container = $("#roles");

function getRoles() {
    var roles = "";

    $.ajax({
        async: false,
        type: "GET",
        url: "{{ URL::action('ActivityController@activityroles', $activity->id ) }}",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (msg) { roles = msg.d; },
        error: function (msg) { roles = msg; }
    });
    return roles;
}

$container.handsontable({
	data: getRoles(),
	startRows: 5,
    minSpareRows: 1,
    rowHeaders: true,
    colHeaders: true,
    contextMenu: false,
    colWidths: [300, 300, 300],
	colHeaders: ["Process Owner", "Action Points", "Timing"],
	columns: [{
      data: "owner",
      readOnly: true
    },{
      data: "point",
      readOnly: true
    },{
      data: "timing",
     	readOnly: true
    }]
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
		submitstatus: "is_natural_no_zero"
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


