@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-12 col-md-7 col-sm-6">
			<h1>Edit {{ $activity->circular_name }}</h1>
			{{ Form::hidden('activity_id', $activity->id, ['id' => 'activity_id']) }}

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

			<div class="btn-group">
                  <a href="#" class="btn btn-info">Options</a>
                  <a href="#" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a target="_blank" href="{{ URL::action('ReportController@preview', $activity->id ) }}">HTML Preview</a></li>
                    <li><a href="{{ URL::action('ReportController@document', $activity->id ) }}">Download as Document</a></li>
                  </ul>
            </div>
			
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
	        	<button class="btn btn-primary" onclick="return confirm('Are you sure?')">Submit</button>	    
	     	</div>
	     	{{ Form::close() }}
	    </div>
  	</div>
</div>


<ul class="nav nav-tabs">
	<li class="active"><a id="tab-activity" aria-expanded="true" href="#activity">Activity Details</a></li>
	<li class=""><a id="tab-customer" aria-expanded="false" href="#customer">Customer Details</a></li>
	@if(!$activity->activitytype->with_tradedeal)
		<li class=""><a id="tab-schemes" aria-expanded="false" href="#schemes">Schemes</a></li>
	@else
		<li class=""><a id="tab-tradedeal" aria-expanded="false" href="#tradedeal">Bonus Buy Free</a></li>
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
		</div>
		<br>
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
								  				<td><input class="input-number disabled-readonly" id="{{ $area->area_code }}"  name="force_alloc[{{ $area->area_code }}]" value="{{ $area->multi }}" type="text"></td>
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
					<button class="btn btn-primary disable-button">Update</button>
					<button class="btn btn-default btn-style" type="submit">Back</button>
					<button class="btn btn-primary btn-style" type="submit">Next</button>
				</div>
			</div>
		</div>
		{{ Form::close() }}
		<br>
	</div>

	

	@if(!$activity->activitytype->with_tradedeal)
	
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
										<th>Scheme ID</th>
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
										<td>{{ $scheme->id }}</td>
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
									  	@if($scheme->updating == 1)
									  	<td class="action" colspan="3">
									  		<button class="btn btn-info btn-xs disabled">Updating Scheme</button>
									  	</td>
									  	@else
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
									  	@endif
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
	<!-- trade details -->
	<div class="tab-pane fade" id="tradedeal">
		<br>
		{{ Form::open(array('action' => array('ActivityController@updatetradedeal', $activity->id), 'method' => 'PUT', 'files'=>true, 'class' => 'bs-component','id' => 'updateTradedeal')) }}
		<div class="panel panel-default">
		  	<div class="panel-heading">Bonus Buy Free Details</div>

		  	<div class="panel-body">
				<div class="row">
					<div class="col-lg-3">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('alloc_in_weeks', 'Allocation Worth (in weeks)', array('class' => 'control-label')) }}
									{{ Form::text('alloc_in_weeks',($tradedeal) ? $tradedeal->alloc_in_weeks : '', array('class' => 'form-control')) }}
								</div>
							</div>
						</div>
					</div>	

					<div class="col-lg-3">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('total_allocation_pcs', 'Total Allocation (Pcs)', array('class' => 'control-label')) }}
									{{ Form::text('total_allocation_pcs',number_format($total_deals), array('class' => 'form-control', 'readonly' => '')) }}
								</div>
							</div>
						</div>
					</div>	

					<div class="col-lg-3">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('total_premium_php', 'Total Premium (Php)', array('class' => 'control-label')) }}
									{{ Form::text('total_premium_php',number_format($total_premium_cost,2), array('class' => 'form-control', 'readonly' => '')) }}								
								</div>
							</div>
						</div>
					</div>					
				</div>


		  	</div>
		</div>

		<div class="panel panel-default">
		  	<div class="panel-heading">Premium Type</div>

		  	<div class="panel-body">
				
				<div class="row">
					<div class="col-lg-3">
						<div class="form-group">
							<div class="radio">
							  	<label>
							  		{{ Form::radio('non_ulp_premium', '1', (isset($tradedeal->non_ulp_premium)) ? $tradedeal->non_ulp_premium : 0,['id' => 'non_ulp_premium']) }} Non-ULP Premium
							  	</label>
							</div>
						</div>
					</div>
				</div>

				<div class="row">

					<div class="col-lg-3">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('non_ulp_premium_desc', 'Non-ULP Premium Description', array('class' => 'control-label')) }}
									{{ Form::text('non_ulp_premium_desc',($tradedeal) ?  $tradedeal->non_ulp_premium_desc : '', array('class' => 'form-control', 'id' => 'non_ulp_premium_desc')) }}
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-3">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('non_ulp_premium_code', 'PIMS Code', array('class' => 'control-label')) }}
									{{ Form::text('non_ulp_premium_code',($tradedeal) ?  $tradedeal->non_ulp_premium_code : '', array('class' => 'form-control', 'id' => 'non_ulp_premium_code')) }}
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-3">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('non_ulp_premium_cost', 'Unit Cost / Piece (LPBT)', array('class' => 'control-label')) }}
									{{ Form::text('non_ulp_premium_cost',($tradedeal) ?  $tradedeal->non_ulp_premium_cost : '', array('class' => 'form-control', 'id' => 'non_ulp_premium_cost')) }}
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-3">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-12">
									{{ Form::label('non_ulp_pcs_case', 'Piece / Case', array('class' => 'control-label')) }}
									{{ Form::text('non_ulp_pcs_case',($tradedeal) ?  $tradedeal->non_ulp_pcs_case : '', array('class' => 'form-control', 'id' => 'non_ulp_pcs_case')) }}
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-3">
						<div class="form-group">
							<div class="radio">
							  	<label>
							    	{{ Form::radio('non_ulp_premium', '0', (isset($tradedeal->non_ulp_premium)) ? !$tradedeal->non_ulp_premium : 0,['id' => 'non_ulp_premium']) }} ULP Premium
							  	</label>
							</div>
						</div>
					</div>
				</div>
				<br>
				<button class="btn btn-primary disable-button">Update</button>

		  	</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Select Participating SKU</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-12">
						<button type="button" class="btn btn-primary btn-sm " id="add_sku">Add Participating SKU's</button>
					</div>
				</div>
				<br>
				<div >
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										<table id="participating_sku" class="table table-striped table-hover ">
										<thead>
											<tr>
												<th>Host SKU</th>
												<th>Unit Cost / Piece (LPBT)</th>
												<th>Piece / Case</th>
												<th>Variant Shortcut</th>
												<th>Reference SKU</th>
												<th>Premium SKU</th>
												<th>Unit Cost / Piece (LPBT)</th>
												<th>Piece / Case</th>
												<th>Variant Shortcut</th>
												<th></th>
												<th></th>
											</tr>
										</thead>
										<tbody>

											
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

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Schemes</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-12">
						<a id="add_scheme" class="btn btn-primary btn-sm" href="{{action('ActivityController@createtradealscheme', $activity->id);}}">Add Scheme</a>
					</div>
				</div>
				<br>
				<div >
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">

								

								<div class="row">
									<div class="col-lg-12">
										<table id="scheme-table" class="table table-bordered">
											<thead>
												<tr>
													<th colspan="2">Action</th>
													<th>Scheme</th>
													<th>UOM</th>
													<th>Host SKU(s)</th>
													<th>Premium SKU</th>
													<th>Puchase Requirement</th>
													<th>Cost To Sales</th>
													<th style="width:20%">Channels Involved</th>
												</tr>
											</thead>
											<tbody>
										@if(count($tradedealschemes) == 0)
										<tr>
											<td colspan="9">No record found.</td>
										</tr>
										@endif
										
										@foreach($tradedealschemes as $scheme)
												<?php $x = false; ?>
												<?php $host_cnt = 1; ?>
												@if(!empty($scheme->host_skus))
												<?php $host_cnt = count($scheme->host_skus); ?>
													@foreach($scheme->host_skus as $host_sku)
													<?php $y = false; ?>
													@if(!$x)
													<tr id="{{$scheme->id}}" class="cl_{{$scheme->id}}">
														<td rowspan="{{$host_cnt}}">
															{{ HTML::linkAction('TradealSchemeController@edit','Edit', $scheme->id, array('class' => 'btn btn-success btn-xs')) }}
															
														</td>
														<td rowspan="{{$host_cnt}}">
															<a href="javascript:void(0)" id="{{$scheme->id}}" class="deletescheme btn btn-danger btn-xs">Delete</a>
														</td>
														<td rowspan="{{$host_cnt}}">{{ $scheme->name }}</td>
														<td rowspan="{{$host_cnt}}">{{ $scheme->dealUom->tradedeal_uom }}</td>
													@endif
														@if(!$y)
														<td>{{ $host_sku->desc_variant }} </td>
														<td>{{ $host_sku->pre_variant }} </td>
														<td>{{ number_format($host_sku->pur_req,2) }}</td>
														<td>{{ number_format($host_sku->cost_to_sale,2) }} %</td>
														<?php $y = true; ?>
														@else
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														@endif
														@if(!$x)
														<?php $x = true; ?>
														<td rowspan="{{$host_cnt}}" >
															@if(!empty($scheme->rtms))
																@foreach($scheme->rtms as $rtm)
																{{ $rtm->sold_to }} </br>
																@endforeach
															@endif
															
															@if(!empty($scheme->channels))
																@foreach($scheme->channels as $channel)
																{{ $channel->sub_type_desc }} </br>
																@endforeach
															@endif
														</td>
														@endif
													</tr>
													@endforeach
												@endif
											
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
			<div class="col-lg-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Allocations</h3>
					</div>
					<div class="panel-body">
						<div class="row">
								<div class="col-lg-6">
								  	<div class="form-group">
								  		<a class="btn btn-success btn-sm" href="{{action('ActivityController@exporttddetails', $activity->id);}}">Download Allocations</a>
								  		@if($_ENV['MAIL_TEST'])
										<a class="btn btn-success btn-sm" href="{{action('ActivityController@exporttradedeal', $activity->id);}}">Field Allocations</a>
										<a class="btn btn-success btn-sm" href="{{action('TradealSchemeController@exportle', $activity->id);}}">Export LE Templates</a>
										@endif
								  	</div>
							  	</div>
						  	</div>
						
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Upload Force Allocation</h3>
					</div>
					<div class="panel-body">
						@if(isset($tradedeal))
						@if($tradedeal->forced_upload)
						<div class="row">
							<div class="col-lg-12">
							  	<div class="form-group">
							  		<div class="alert alert-danger" role="alert">Manual Force Upload is applied in this activity.</div>
							  	</div>
						  	</div>
					  	</div>
					  	@endif
					  	@endif

						<div class="row">
								<div class="col-lg-6">
								  	<div class="form-group">
								  		{{ Form::label('remarks', 'Attach Excel File', array('class' => 'control-label')) }}
								    	{{ Form::file('tdupload',array('id'=>'tdupload')) }}
								    	<br>
								    	<button class="btn btn-primary disable-button">Upload</button>
								  	</div>
							  	</div>
						  	</div>
						
					</div>
				</div>
			</div>
		</div>

		@if(count($trade_allocations) > 0)

		@include('shared.tradealloc')
		
		@endif

		<div class="row">
			<div class="col-lg-12">
				<div class="form-group">
					<button class="btn btn-primary disable-button">Update</button>
					<button class="btn btn-default btn-style" type="submit">Back</button>
					<button class="btn btn-primary btn-style" type="submit">Next</button>
				</div>
			</div>
		</div>
		{{ Form::close() }}
	</div>
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
									@if($activity->billing_date != "")
									{{ Form::text('billing_deadline',date_format(date_create($activity->billing_date),'m/d/Y'), array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy')) }}
									@else
									{{ Form::text('billing_deadline',null, array('class' => 'form-control', 'placeholder' => 'mm/dd/yyyy')) }}
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
		{{ Form::open(array('action' => array('ActivityController@updatetimings', $activity->id), 'class' => 'bs-component','id' => 'updatetimings')) }}
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
									<td><input class="timing_date" type="text" id="timing_start[{{ $timing->id }}]" name="timing_start[{{ $timing->id }}]" value="<?php echo ($timing->final_start_date != null) ?  date_format(date_create($timing->final_start_date),'m/d/Y') : '';?>"  placeholder="mm/dd/yyyy" value=""></td>
									<td><input class="timing_date" type="text" id="timing_end[{{ $timing->id }}]" name="timing_end[{{ $timing->id }}]"  value="<?php echo ($timing->final_end_date != null) ?  date_format(date_create($timing->final_end_date),'m/d/Y') : '';?>" placeholder="mm/dd/yyyy" value=""></td>
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
					<button class="btn btn-primary">Update</button>
					<button class="btn btn-default btn-style" type="submit">Back</button>
					<button class="btn btn-primary btn-style" type="submit">Next</button>
				</div>
			</div>
		</div>
		{{ Form::close() }}
		<br>
	</div>

	<!-- attachment details -->
	<div class="tab-pane fade" id="attachment">
		<br>
		<div class="panel panel-default">
		  	<div class="panel-heading">FDA Permit</div>
		  	<div class="panel-body">
		  		

		  		{{ Form::open(array('action' => array('ActivityController@fdaupload', $activity->id),  'class' => 'bs-component','id' => 'fdaupload', 'files'=>true)) }}
		  			<div class="row">
						<div class="col-lg-6">
						  	<div class="form-group">
						  		{{ Form::label('permitno', 'FDA Permit No.', array('class' => 'control-label')) }}
								{{ Form::text('permitno','',array('class' => 'form-control', 'placeholder' => 'FDA Permit No.')) }}
						 	</div>
						  	<div class="form-group">
						    	{{ Form::file('file',array('id'=>'fdafile')) }}
						  	</div>
						  	<p class="text-success">Uploadable file version/s: .jpg,.jpeg,.png,.gif,.pdf,.xps</p>
						  	{{ Form::submit('Upload', array('class' => 'btn btn-primary')) }}
					  	</div>
				  	</div>
				{{ Form::close() }}
				
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
				
		  	</div>
		</div>

		<div class="panel panel-default">
		  	<div class="panel-heading">Product Information Sheet</div>
		  	<div class="panel-body">
		  		<div class="row">
					<div class="col-lg-12">
					  	<div class="form-group">
					    	<a  target="_blank" href="{{ URL::action('ActivityController@pistemplate') }}">Download Product Information Sheet Template</a>		
					  	</div>
			  		</div>
			  	</div>
			  	<p class="text-success">Uploadable file version/s: .xls,.xlxs.</p>
		  		<div id="fisupload">
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
		</div>

		<div class="panel panel-default">
		  	<div class="panel-heading">Artwork Packshots</div>
		  	<div class="panel-body">
		  		 	<p class="text-success">Uploadable file version/s: .jpg,.jpeg,.png,.gif</p>
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
											{{ HTML::linkAction('ActivityController@artworkdelete','Delete', null, array('class' => 'ajax_delete btn btn-danger btn-xs', 'id' => $artwork->id)) }}
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
		  		 	<p class="text-success">Uploadable file version/s: any</p>
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
		  	<div class="panel-heading">Banding Guidelines / Activation Mechanics / Others</div>
		  	<div class="panel-body">
		  		<p class="text-success">Uploadable file version/s: any</p>
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
	                                <i class="fa fa-clock-o fa-fw"></i>{{ date_format(date_create($comment->created_at),'m/d/Y H:m:s') }} 
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

@if($tradedeal != null)
@if($activity->activitytype->with_tradedeal) 

<!-- Modal -->
<div class="modal fade" id="addsku" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		{{ Form::open(array('action' => array('ActivityController@addpartskus', $activity->id), 'method' => 'POST', 'class' => 'bs-component','id' => 'addpartskus')) }}
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Add Participating SKU's</h4>
			</div>
			<div class="modal-body">
				<div class="error-msg"></div>
				<table class="table table-bordered">
					<tbody>
						<tr>
							<td>Host SKU</td>
							<td colspan="3">
								{{ Form::select('host_sku', array('0' => '') + $host_skus, [], array('data-placeholder' => 'Select Host SKU','id' => 'host_sku', 'class' => 'form-control')) }}
							</td>
						</tr>
						
						<tr>
							<td>Unit Cost / Piece (LPBT)</td>
							<td>
								<div class="row">
								  <div class="col-lg-12">
								    <div class="input-group">
								      	<input class="form-control" name="host_cost_pcs" type="text" value="0" id="host_cost_pcs" readonly =''>

								      <span class="input-group-btn">
								      	<button type="button" data-toggle="tooltip" data-placement="top" title="Only for Price Increase" class="btn btn-success cost-edit">Edit</button>
								      </span>
								    </div><!-- /input-group -->
								  </div><!-- /.col-lg-6 -->
								</div><!-- /.row -->
								
							</td>
							<td>Piece / Case</td>
							<td>
								<input class="form-control" name="host_pcs_case" type="text" value="0" id="host_pcs_case" readonly =''>
							</td>
						</tr>
						<tr>
							<td>Variant Shortcut</td>
							<td colspan="3">
								<input class="form-control" name="variant" type="text" value="" id="variant">
							</td>
						</tr>
						<tr>
							<td>Reference SKU</td>
							<td colspan="3">
								{{ Form::select('ref_sku', array('0' => '') + $ref_skus, [], array('data-placeholder' => 'Select Reference SKU','id' => 'ref_sku', 'class' => 'form-control')) }}
							</td>
						</tr>
					</tbody>
				</table>

				<table class="table table-bordered">
					<tbody>
						
						
						@if(!$tradedeal->non_ulp_premium)
						<tr class="ulppremium">
							<td colspan="4">
								<button id="copy_host" type="button" class="btn btn-success btn-sm">Same as Host SKU</button>
							</td>
						</tr>
						<tr class="pre-sku ulppremium">
							<td>Premium SKU</td>
							<td colspan="3">
								{{ Form::select('pre_sku', array('0' => '') + $pre_skus, [], array('data-placeholder' => 'Select Premium SKU','id' => 'pre_sku', 'class' => 'form-control')) }}
							</td>
						</tr>
						<tr class="pre-sku ulppremium">
							<td>Unit Cost / Piece (LPBT)</td>
							<td>
								<input class="form-control" name="pre_cost_pcs" type="text" value="0" id="pre_cost_pcs" readonly =''>
							</td>
							<td>Piece / Case</td>
							<td>
								<input class="form-control" name="pre_pcs_case" type="text" value="0" id="pre_pcs_case" readonly =''>
							</td>
						</tr>
						<tr>
							<td>Variant Shortcut</td>
							<td colspan="3">
								<input class="form-control" name="pre_variant" type="text" value="" id="pre_variant">
							</td>
						</tr>
						@else
						<tr class="pre-sku">
							<td>Premium SKU</td>
							<td colspan="3">
								<input class="form-control" name="pre_sku" type="text" value="{{ $tradedeal->non_ulp_premium_desc}} - {{ $tradedeal->non_ulp_premium_code }}" id="pre_sku" readonly =''>
							</td>
						</tr>
						<tr class="pre-sku">
							<td>Unit Cost / Piece (LPBT)</td>
							<td>
								<input class="form-control" name="pre_cost_pcs" type="text" value="{{ $tradedeal->non_ulp_premium_cost}}" id="pre_cost_pcs" readonly =''>
							</td>
							<td>Piece / Case</td>
							<td>
								<input class="form-control" name="pre_pcs_case" type="text" value="{{ $tradedeal->non_ulp_pcs_case}}" id="pre_pcs_case" readonly =''>
							</td>
						</tr>

						@endif
						
						
						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button id="submitsku" class="btn btn-primary disable-button">Submit</button>
			</div>
		</div>
		{{ Form::close() }}
	</div>
</div>


<div class="modal fade" id="editsku" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		{{ Form::open(array('action' => array('ActivityController@updatepartskus'), 'method' => 'POST', 'class' => 'bs-component','id' => 'updatepartskus')) }}
		{{ Form::hidden('sku_id', 0, ['id' => 'sku_id']) }}
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Edit Participating SKU</h4>
			</div>
			<div class="modal-body">
				<div class="error-msg"></div>
				<table class="table table-bordered">
					<tbody>
						<tr>
							<td>Host SKU</td>
							<td colspan="3">
								{{ Form::select('ehost_sku', array('0' => '') + $host_skus, [], array('data-placeholder' => 'Select Host SKU','id' => 'ehost_sku', 'class' => 'form-control')) }}
							</td>
						</tr>
						
						<tr>
							<td>Unit Cost / Piece (LPBT)</td>
							<td>
								<div class="row">
								  <div class="col-lg-12">
								    <div class="input-group">
								    	<input class="form-control" name="ehost_cost_pcs" type="text" value="0" id="ehost_cost_pcs" readonly =''>

								      	<span class="input-group-btn">
								      	<button type="button" data-toggle="tooltip" data-placement="top" title="Only for Price Increase" class="btn btn-success ecost-edit">Edit</button>
								      </span>
								    </div><!-- /input-group -->
								  </div><!-- /.col-lg-6 -->
								</div><!-- /.row -->
								
							</td>

							
							<td>Piece / Case</td>
							<td>
								<input class="form-control" name="ehost_pcs_case" type="text" value="0" id="ehost_pcs_case" readonly =''>
							</td>
						</tr>
						<tr>
							<td>Variant Shortcut</td>
							<td colspan="3">
								<div class="row">
								  <div class="col-lg-12">
								    <div class="input-group">
								      	<input class="form-control" name="evariant" type="text" value="" id="evariant" readonly ='' >

								      <span class="input-group-btn">
								      	<button type="button" data-toggle="tooltip" data-placement="top" title="Will update Host SKU varaint" class="btn btn-success var-edit">Edit</button>
								      </span>
								    </div><!-- /input-group -->
								  </div><!-- /.col-lg-6 -->
								</div><!-- /.row -->
								
							</td>
						</tr>
						<tr>
							<td>Reference SKU</td>
							<td colspan="3">
								{{ Form::select('eref_sku', array('0' => '') + $ref_skus, [], array('data-placeholder' => 'Select Reference SKU','id' => 'eref_sku', 'class' => 'form-control')) }}
							</td>
						</tr>
					</tbody>
				</table>

				<table class="table table-bordered">
					<tbody>
						@if(!$tradedeal->non_ulp_premium)
						<tr class="ulppremium">
							<td colspan="4">
								<button id="ecopy_host" type="button" class="btn btn-success btn-sm">Same as Host SKU</button>
							</td>
						</tr>
						<tr class="pre-sku ulppremium">
							<td>Premium SKU</td>
							<td colspan="3">
								{{ Form::select('epre_sku', array('0' => '') + $pre_skus, [], array('data-placeholder' => 'Select Premium SKU','id' => 'epre_sku', 'class' => 'form-control')) }}
							</td>
						</tr>
						<tr class="pre-sku ulppremium">
							<td>Unit Cost / Piece (LPBT)</td>
							<td>
								<input class="form-control" name="epre_cost_pcs" type="text" value="0" id="epre_cost_pcs" readonly =''>
							</td>
							<td>Piece / Case</td>
							<td>
								<input class="form-control" name="epre_pcs_case" type="text" value="0" id="epre_pcs_case" readonly =''>
							</td>
						</tr>
						<tr>
							<td>Variant Shortcut</td>
							<td colspan="3">
								<div class="row">
								  <div class="col-lg-12">
								    <div class="input-group">
										<input class="form-control" name="epre_variant" type="text" value="" id="epre_variant" readonly =''>

								      <span class="input-group-btn">
								      	<button type="button" data-toggle="tooltip" data-placement="top" title="Will update Premium SKU varaint" class="btn btn-success prevar-edit">Edit</button>
								      </span>
								    </div><!-- /input-group -->
								  </div><!-- /.col-lg-6 -->
								</div><!-- /.row -->

							</td>
						</tr>
						@else
						<tr class="pre-sku">
							<td>Premium SKU</td>
							<td colspan="3">
								<input class="form-control" name="epre_sku" type="text" value="{{ $tradedeal->non_ulp_premium_desc}} - {{ $tradedeal->non_ulp_premium_code }}" id="epre_sku" readonly =''>
							</td>
						</tr>
						<tr class="pre-sku">
							<td>Unit Cost / Piece (LPBT)</td>
							<td>
								<input class="form-control" name="epre_cost_pcs" type="text" value="{{ $tradedeal->non_ulp_premium_cost}}" id="epre_cost_pcs" readonly =''>
							</td>
							<td>Piece / Case</td>
							<td>
								<input class="form-control" name="epre_pcs_case" type="text" value="{{ $tradedeal->non_ulp_pcs_case}}" id="epre_pcs_case" readonly =''>
							</td>
						</tr>

						@endif
						
						
						
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button id="submitsku" class="btn btn-primary">Update</button>
			</div>
		</div>
		{{ Form::close() }}
	</div>
</div>

@endif
@endif

@include('javascript.activity.edit')

@stop


@section('add-script')
	{{ HTML::script('assets/js/tradedeal.js') }}
	{{ HTML::script('assets/js/tradetable.js') }}
@stop

@section('page-script')

$("#updateTradedeal").on("submit", function (e) {
	var form = $(this);
	var url = form.prop('action');
	if(form.valid()){
		@if(isset($tradedeal))
		@if($tradedeal->forced_upload)
		if(!confirm("Are you sure to overwrite the manual upload allocation.")){
	        e.preventDefault();
	    }else{
	    	$(this).find(":submit").prop("disabled", true);
    		$("#page").hide();
			$("#pageloading").show();
		}
		@else
			$(this).find(":submit").prop("disabled", true);
    		$("#page").hide();
			$("#pageloading").show();
		@endif
		@else
		$(this).find(":submit").prop("disabled", true);
    		$("#page").hide();
			$("#pageloading").show();
			@endif
		
	}
});

$("#variant, #pre_variant").attr('maxlength','6');
$("#non_ulp_premium_desc").attr('maxlength','13');

$("#fisupload").uploadifyTable({
	'multi': false,
	'fileTypeExts' : '*.xls; *.xlsx',
	'reload' : true
});

$("#artworkupload").uploadifyTable({
	'fileTypeExts' : '*.gif; *.jpg; *.png'
});
$("#backgroundupload").uploadifyTable({
	'fileTypeExts' : '*.*'
});
$("#bandingupload").uploadifyTable({
	'fileTypeExts' : '*.*'
});

//$('INPUT[type="file"]').change(function () {
$('#fdafile').change(function () {
    var ext = this.value.match(/\.(.+)$/)[1];
    switch (ext.toLowerCase()) {
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
        case 'pdf':
        case 'xps':
            //$('#uploadButton').attr('disabled', false);
            break;
        default:
            alert('This is not an allowed file type.');
            this.value = '';
    }
});
@stop



