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
			<!-- Button trigger modal -->
			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mySubmit">
			  Action
			</button>
			<a class="btn btn-info" target="_blank" href="{{ URL::action('ReportController@preview', $activity->id ) }}">Preview</a>
		</div>
	</div>

</div>

<!-- Modal -->
<div class="modal fade" id="mySubmit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog">
	    <div class="modal-content">
	    	{{ Form::open(array('action' => array('ActivityController@submittogcm', $activity->id), 'class' => 'bs-component','id' => 'submittogcm')) }}
	      	<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        	<h4 class="modal-title" id="myModalLabel">Submit To GCOM</h4>
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
									{{ Form::select('approver[]', $approvers, $sel_approver, array('id' => 'approver', 'class' => 'form-control multiselect', 'multiple' => 'multiple')) }}
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
					<div class="col-lg-2">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('download_date', 'Target Download Date ', array('class' => 'control-label')) }}
									{{ Form::text('download_date',date_format(date_create($activity->edownload_date),'m/d/Y'),array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy', 'readonly' => '')) }}								
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-2">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('implementation_date', 'Target Start Date', array('class' => 'control-label')) }}
									{{ Form::text('implementation_date',date_format(date_create($activity->eimplementation_date),'m/d/Y'),array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy')) }}
								</div>
							</div>
						</div>
					</div>	

					<div class="col-lg-2">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('end_date', 'Target End Date', array('class' => 'control-label')) }}
									{{ Form::text('end_date',date_format(date_create($activity->end_date),'m/d/Y'),array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy')) }}
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
									{{ Form::select('division[]',  $divisions, $sel_divisions, array('id' => 'division', 'class' => 'form-control' ,'multiple' => 'multiple' ,'data-placeholder' => 'SELECT DIVISION')) }}
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
									{{ Form::label('tree3', 'Select Customers', array('class' => 'control-label' )) }}<br>
									<a href="#" id="btnCSelectAll">Select all</a> |
									<a href="#" id="btnCDeselectAll">Deselect all</a>
									<div id="tree3"></div>
									{{ Form::hidden('customers', null, array('id' => 'customers')) }}
								</div>

								<div class="col-lg-6">
									{{ Form::label('tree4', 'Select DT Channels Involved', array('class' => 'control-label' )) }}<br>
									<div id="chSel">
										<a href="#" id="btnChSelectAll">Select all</a> |
										<a href="#" id="btnChDeselectAll">Deselect all</a>
									</div>
									
									<div id="tree4"></div>
									{{ Form::hidden('channels_involved', null, array('id' => 'channels_involved')) }}
								</div>
							</div>	
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<div class="checkbox">
									        <label>
									        	{{ Form::checkbox('allow_force', 1,$activity->allow_force,['id' => 'allow_force']) }} Enable Force Allocation
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
								  				<td><input class="input-number" id="{{ $area->area_code }}"  name="force_alloc[{{ $area->area_code }}]" value="{{ $area->multi }}" type="text"></td>
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
					<button class="btn btn-default btn-style" type="submit">Back</button>
					<button class="btn btn-primary btn-style" type="submit">Next</button>
				</div>
			</div>
		</div>
		{{ Form::close() }}
		<br>
	</div>

	<!-- scheme details -->
	@if($activity->activitytype->with_scheme)
	<div class="tab-pane fade" id="schemes">
		<br>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Scheme Lists</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-12">
						<a href="{{ URL::action('SchemeController@create', $activity->id) }}" class="btn btn-primary">Add New Scheme</a>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-lg-12">
						<div class="table-responsive">
							<table class="table table-striped table-condensed table-hover">
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
										<th colspan="3" class="text-center">Action</th>
									</tr>
								</thead>
							  	<tbody>
							  		@if(count($schemes) == 0)
							  		<tr>
							  			<td colspan="13">No record found!</td>
							  		</tr>
							  		@else
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
									  	<td class="action">
									  		{{ HTML::linkAction('SchemeController@edit','View', $scheme->id, array('class' => 'btn btn-success btn-xs')) }}
									  	</td>
									  	<td class="action">
									  		{{ Form::open(array('method' => 'POST', 'action' => array('SchemeController@duplicate', $scheme->id), 'class' => 'disable-button')) }}                       
											{{ Form::submit('Duplicate', array('class'=> 'btn btn-primary btn-xs disable-button','onclick' => "if(!confirm('Are you sure to duplicate this record?')){return false;};")) }}
											{{ Form::close() }}
									  	</td>

									  	<td class="action">
									  		{{ Form::open(array('method' => 'DELETE', 'action' => array('SchemeController@destroy', $scheme->id),'class' => 'disable-button')) }}                       
												{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs disable-button','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
											{{ Form::close() }}
									  	</td>
									</tr>
									@endforeach
									@endif
							  	</tbody>
							  	<tfoot>
							  		<tr>
										<th class="text-center" colspan="3">Sub Total</th>
										<th class="text-right" style="width:80px;"></th>
										<th class="text-right" style="width:80px;"></th>
										<th class="text-right" style="width:80px;"></th>
										<th class="text-right" style="width:80px;">{{ number_format($scheme_summary->final_total_deals) }}</th>
										<th class="text-right" style="width:80px;">{{ number_format($scheme_summary->final_total_cases) }}</th>
										<th class="text-right" style="width:80px;">{{ number_format($scheme_summary->final_tts_r,2) }}</th>
										<th class="text-right" style="width:80px;">{{ number_format($scheme_summary->final_pe_r,2) }}</th>
										<th class="text-right" style="width:80px;">{{ number_format($scheme_summary->final_total_cost,2) }}</th>
										<th colspan="3"></th>
									</tr>
							  	</tfoot>
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
	@else

	@endif
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
				<h3 class="panel-title">Timings Details1</h3>
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
						      	<td>{{ date_format(date_create($permit->created_at),'m/d/Y') }}</td>
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
						      	<td>{{ date_format(date_create($fi->created_at),'m/d/Y') }}</td>
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
		  		<div id="artworkupload">
		  		{{ Form::open(array('action' => array('ActivityController@artworkupload', $activity->id),'id' => 'artworkupload_form', 'class' => 'bs-component','files'=>true)) }}
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
								      	<th class="upload_date">Uploaded Date</th>
						      			<th class="att_action">Action</th>
								    </tr>
							  	</thead>
							  	<tbody>
							  		@foreach($artworks as $artwork)
								    <tr>
								      	<td>{{ $artwork->file_name }}</td>
								      	<td class="upload_date">{{ date_format(date_create($artwork->created_at),'m/d/Y') }}</td>
								      	<td class="att_action">
											{{ HTML::linkAction('ActivityController@artworkdownload','Download', $artwork->id, array('class' => 'btn btn-success btn-xs')) }}
											{{ HTML::linkAction('ActivityController@artworkdownload','Delete', null, array('class' => 'ajax_delete btn btn-danger btn-xs', 'id' => $artwork->id)) }}
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
		  	<div class="panel-heading">Marketing Backgrounds</div>
		  	<div class="panel-body">
		  		<div id="backgroundupload">
		  		{{ Form::open(array('action' => array('ActivityController@backgroundupload', $activity->id),'id' => 'backgroundupload_form',  'class' => 'bs-component','files'=>true)) }}
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
								      	<th class="upload_date">Uploaded Date</th>
						      			<th class="att_action">Action</th>
								    </tr>
							  	</thead>
							  	<tbody>
							  		@foreach($backgrounds as $background)
								    <tr>
								      	<td>{{ $background->file_name }}</td>
								      	<td class="upload_date">{{ date_format(date_create($background->created_at),'m/d/Y') }}</td>
								      	<td class="att_action">
											{{ HTML::linkAction('ActivityController@backgrounddownload','Download', $background->id, array('class' => 'btn btn-success btn-xs')) }}
											{{ HTML::linkAction('ActivityController@backgrounddelete','Delete', null, array('class' => 'ajax_delete btn btn-danger btn-xs', 'id' => $background->id)) }}
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
		  	<div class="panel-heading">Banding Guidelines / Activation Mechanics</div>
		  	<div class="panel-body">
		  		<div id="bandingupload">
		  		{{ Form::open(array('action' => array('ActivityController@bandingupload', $activity->id),'id' => 'bandingupload_form',  'class' => 'bs-component','files'=>true)) }}
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
								      	<th class="upload_date">Uploaded Date</th>
						      			<th class="att_action">Action</th>
								    </tr>
							  	</thead>
							  	<tbody>
							  		@foreach($bandings as $banding)
								    <tr>
								      	<td>{{ $banding->file_name }}</td>
								      	<td class="upload_date">{{ date_format(date_create($banding->created_at),'m/d/Y') }}</td>
								      	<td class="att_action">
											{{ HTML::linkAction('ActivityController@bandingdownload','Download', $banding->id, array('class' => 'btn btn-success btn-xs')) }}
											{{ HTML::linkAction('ActivityController@bandingdelete','Delete', null, array('class' => 'ajax_delete btn btn-danger btn-xs', 'id' => $banding->id)) }}
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

</div>


@include('javascript.downloadedactivity.edit')

@stop

@section('page-script')

$("#artworkupload").uploadifyTable({
	'fileTypeExts' : '*.gif; *.jpg; *.png'
});
$("#backgroundupload").uploadifyTable({
	'fileTypeExts' : '*.*'
});
$("#bandingupload").uploadifyTable({
	'fileTypeExts' : '*.*'
});

@stop


