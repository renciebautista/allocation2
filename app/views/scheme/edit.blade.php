@extends('layouts.layout')

@section('content')
<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-12 col-md-7 col-sm-6">
			<h1>Activity : {{ $scheme->activity->circular_name }}</h1>
			<h2>Edit {{ $scheme->name }}</h2>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="form-group">
			{{ Form::open(array('method' => 'POST', 'action' => array('SchemeController@duplicatescheme', $scheme->id), 'class' => 'disable-button')) }}                       
			<a id="scheme_back" class="btn btn-default" href="{{action('ActivityController@edit', $activity->id);}}#schemes">Back to Activity Details</a>
			<a href="{{ URL::action('SchemeController@create', $activity->id) }}" class="btn btn-primary">Add New Scheme</a>
			
			{{ Form::submit('Duplicate Scheme', array('class'=> 'btn btn-primary disable-button','onclick' => "if(!confirm('Are you sure to duplicate this record?')){return false;};")) }}
			{{ Form::close() }}
		</div>
	</div>

</div>

@include('partials.scheme_pagination')



@include('partials.notification')

<div class="panel panel-primary">
	<div class="panel-heading">Scheme Details</div>
	<div class="panel-body">

			{{ Form::open(array('action' => array('SchemeController@update', $scheme->id), 'files'=>true, 'method' => 'PUT', 'id' => 'updatescheme', 'class' => 'bs-component')) }}
			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('scheme_name', 'Scheme Name', array('class' => 'control-label')) }}
								{{ Form::text('scheme_name', $scheme->name, array('id' => 'scheme_name', 'class' => 'form-control', 'placeholder' => 'Scheme Name')) }}
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
								{{ Form::label('skus', 'Reference Sales SKU', array('class' => 'control-label')) }}
								{{ Form::select('skus[]', array('0' => '') + $skus, $sel_skus, array('data-placeholder' => 'Select Reference Sales SKU','id' => 'skus', 'class' => 'form-control')) }}
							</div>
							<div class="col-lg-6">
								{{ Form::label('involve', 'Host SKU', array('class' => 'control-label')) }}
								{{ Form::select('involve[]', $host_sku, $sel_hosts, array('id' => 'involve', 'class' => 'form-control multiselect', 'multiple' => 'multiple')) }}
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
								{{ Form::label('premuim', 'Premium SKU', array('class' => 'control-label')) }}
								{{ Form::select('premuim[]', $premuim_sku, $sel_premuim, array('id' => 'premuim', 'class' => 'form-control multiselect', 'multiple' => 'multiple')) }}
							</div>
							<div class="col-lg-6">
								{{ Form::label('ulp_premium', 'Non ULP Premium SKU', array('class' => 'control-label')) }}
								{{ Form::text('ulp_premium',$scheme->ulp_premium,array('id' => 'ulp_premium', 'class' => 'form-control', 'placeholder' => 'Non ULP Premium SKU','maxlength' => 100)) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			

			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-4">
								{{ Form::label('item_code', 'Item Code', array('class' => 'control-label')) }}
								{{ Form::text('item_code',$scheme->item_code, array('class' => 'form-control', 'placeholder' => 'Item Code','maxlength' => 8)) }}
							</div>
							<div class="col-lg-4">
								{{ Form::label('item_barcode', 'Item Barcode', array('class' => 'control-label')) }}
								{{ Form::text('item_barcode',$scheme->item_barcode, array('class' => 'form-control', 'placeholder' => 'Item Barcode','maxlength' => 13)) }}
							</div>
							<div class="col-lg-4">
								{{ Form::label('item_casecode', 'Item Casecode', array('class' => 'control-label')) }}
								{{ Form::text('item_casecode',$scheme->item_casecode,array('class' => 'form-control', 'placeholder' => 'Item Casecode','maxlength' => 14)) }}
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
								{{ Form::label('srp_p', 'Cost of Premium (Php)', array('class' => 'control-label')) }}
								{{ Form::text('srp_p',number_format($scheme->srp_p,2), array('id' => 'srp_p', 'class' => 'form-control', 'placeholder' => 'Cost of Premium (Php)s')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('other_cost', 'Other Cost Per Deal (Php)', array('class' => 'control-label')) }}
								{{ Form::text('other_cost',number_format($scheme->other_cost,2), array('class' => 'form-control', 'placeholder' => 'Other Cost Per Deal (Php)')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('pr', 'Purchase Requirement (Php)', array('class' => 'control-label')) }}
								{{ Form::text('pr', number_format($scheme->pr,2), array('id' => 'pr', 'class' => 'form-control', 'placeholder' => 'Purchase Requirement (Php)')) }}
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
								{{ Form::label('ulp', 'Total Unilever Cost (Php)', array('class' => 'control-label')) }}
								{{ Form::text('ulp',number_format($scheme->ulp,2), array('class' => 'form-control', 'placeholder' => 'Total Unilever Cost (Php)', 'id' => 'ulp', 'readonly' => '')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('lpat', 'List Price After Tax Per Deal (Php)', array('class' => 'control-label')) }}
								{{ Form::text('lpat',number_format($scheme->lpat,2) , array('id' => 'lpat', 'class' => 'form-control', 'placeholder' => 'List Price After Tax Per Deal (Php)')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('cost_sale', 'Cost to Sales %', array('class' => 'control-label')) }}
								{{ Form::text('cost_sale',number_format($scheme->cost_sale,2), array('class' => 'form-control', 'placeholder' => 'Cost to Sales %', 'id' => 'cost_sale', 'readonly' => '')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-4">
					<div class="form-group">
						<div class="row">
							
							<div class="col-lg-6">
								{{ Form::label('uom', 'UOM', array('class' => 'control-label')) }}
								{{ Form::text('uom',$activity->activitytype->uom,array('class' => 'form-control', 'placeholder' => 'UOM', 'readonly' => '')) }}
							</div>
							<div class="col-lg-6">
								{{ Form::label('total_alloc', 'Total Allocation', array('class' => 'control-label')) }}
								{{ Form::text('total_alloc',number_format($scheme->quantity), array('class' => 'form-control', 'placeholder' => 'Total Allocation')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-8">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-3">
								{{ Form::label('deals', 'Deals Per Case', array('class' => 'control-label')) }}
								{{ Form::text('deals', number_format($scheme->deals), array('class' => 'form-control', 'placeholder' => 'Deals Per Case')) }}
							</div>
							<div class="col-lg-3">
								{{ Form::label('total_deals', 'Total No. of Deals', array('class' => 'control-label')) }}
								{{ Form::text('total_deals',number_format($scheme->total_deals), array('id' => 'total_deals', 'class' => 'form-control', 'placeholder' => 'Total No. of Deals','readonly' => '')) }}
							</div>
							<div class="col-lg-3">
								{{ Form::label('total_cases', 'Total No. of Cases', array('class' => 'control-label')) }}
								{{ Form::text('total_cases',number_format($scheme->total_cases), array('id' => 'total_cases', 'class' => 'form-control', 'placeholder' => 'Total No. of Cases','readonly' => '')) }}
							</div>
							<div class="col-lg-3">
								{{ Form::label('weeks_alloc', 'Total Allocation in Weeks', array('class' => 'control-label')) }}
								{{ Form::text('weeks_alloc','', array('id' => 'weeks_alloc', 'class' => 'form-control', 'placeholder' => 'Total Allocation in Weeks','readonly' => '')) }}
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
								{{ Form::label('tts_r', 'TTS Requirement (Php)', array('class' => 'control-label')) }}
								{{ Form::text('tts_r',number_format($scheme->tts_r,2), array('class' => 'form-control', 'placeholder' => 'TTS Requirement (Php)', 'readonly' => '')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('pe_r', 'PE Requirement (Php)', array('class' => 'control-label')) }}
								{{ Form::text('pe_r',number_format($scheme->pe_r,2), array('class' => 'form-control', 'placeholder' => 'PE Requirement (Php)', 'readonly' => '')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('total_cost', 'Total Cost (Php)', array('class' => 'control-label')) }}
								{{ Form::text('total_cost',number_format($scheme->total_cost,2), array('class' => 'form-control', 'placeholder' => 'Total Cost (Php)', 'readonly' => '')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<div class="checkbox">
							<label>
								{{ Form::checkbox('compute', true,$scheme->compute) }} Use System Generated Allocation
							</label>
						</div>
					</div>	
				</div>
			</div>	

			<div class="row">
				<div class="col-lg-4">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::label('alloc_ref', 'Allocation Reference', array('class' => 'control-label')) }}
								{{ Form::select('alloc_ref', $alloc_refs, 1, array('id' => 'alloc_ref', 'class' => 'form-control')) }}
							</div>
						</div>
					</div>
				</div>
			</div>

			@if(!$scheme->compute)
			<div class="row">
				<div class="col-lg-12">
				  	<div class="form-group">
				  		{{ HTML::linkAction('SchemeController@gettemplate','Download Manual Allocation Template', $scheme->id, array('target' => '_blank')) }}
				  	</div>
		  		</div>
		  		
			</div>

			<div class="row">
				<div class="col-lg-6">
				  	<div class="form-group">
				    	{{ Form::file('file','',array('id'=>'','class'=>'')) }}
				  	</div>
			  	</div>
		  	</div>
			@endif
			<br>
			<div class="row">
				<div class="col-lg-12">
					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								{{ Form::submit('Update', array('class' => 'btn btn-primary', 'id'=>'update')) }}
								<button type="button" class="btn btn-info" data-toggle="modal" data-target="#myCalculator">Deal Calculator</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			{{ Form::close() }}

	</div>
</div>



<div class="panel panel-warning">
	<div class="panel-heading">
		<h3 class="panel-title">Final Allocation</h3>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						
						<div class="col-lg-6">
							{{ Form::label('uom', 'UOM', array('class' => 'control-label')) }}
							{{ Form::text('uom',$activity->activitytype->uom,array('class' => 'form-control', 'placeholder' => 'UOM', 'readonly' => '')) }}
						</div>
						<div class="col-lg-6">
							{{ Form::label('final_total_alloc', 'Total Allocation', array('class' => 'control-label')) }}
							{{ Form::text('final_total_alloc',number_format($scheme->final_alloc), array('id' => 'final_total_alloc', 'class' => 'form-control', 'readonly' => '')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-8">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-3">
							{{ Form::label('deals', 'No. of Deals Per Case', array('class' => 'control-label')) }}
							{{ Form::text('deals',number_format($scheme->deals), array('class' => 'form-control', 'placeholder' => 'No. of Deals Per Case', 'readonly' => '')) }}
						</div>
						<div class="col-lg-3">
							{{ Form::label('final_total_deals', 'Total No. of Deals', array('class' => 'control-label')) }}
							{{ Form::text('final_total_deals',number_format($scheme->final_total_deals), array('id' => 'final_total_deals','class' => 'form-control', 'placeholder' => 'No. of Deals Per Case','readonly' => '')) }}
						</div>
						<div class="col-lg-3">
							{{ Form::label('final_total_cases', 'Total No. of Cases', array('class' => 'control-label')) }}
							{{ Form::text('final_total_cases',number_format($scheme->final_total_cases), array('id' => 'final_total_cases', 'class' => 'form-control', 'placeholder' => 'No. of Deals Per Case','readonly' => '')) }}
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
							{{ Form::label('final_tts_r', 'TTS Requirement (Php)', array('class' => 'control-label')) }}
							{{ Form::text('final_tts_r',number_format($scheme->final_tts_r,2), array('id' => 'final_tts_r', 'class' => 'form-control', 'placeholder' => 'TTS Requirement (Php)', 'readonly' => '')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							{{ Form::label('final_pe_r', 'PE Requirement (Php)', array('class' => 'control-label')) }}
							{{ Form::text('final_pe_r',number_format($scheme->final_pe_r,2), array('id' => 'final_pe_r', 'class' => 'form-control', 'placeholder' => 'PE Requirement (Php)', 'readonly' => '')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							{{ Form::label('final_total_cost', 'Total Cost (Php)', array('class' => 'control-label')) }}
							{{ Form::text('final_total_cost',number_format($scheme->final_total_cost,2), array('id' => 'final_total_cost', 'class' => 'form-control', 'placeholder' => 'Total Cost (Php)', 'readonly' => '')) }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@if($count > 0)
<div class="row">
	<div class="col-lg-12">
		<h2>Allocation Summary</h2>
		<a class="btn btn-success" target="_blank" href="{{ URL::action('SchemeController@export', $scheme->id ) }}">Export To Excel</a>
		<div id="allocation">
				<table id="customer-allocation" class="table table-condensed table-bordered display compact ">
					<tr>
						<th>Group</th>
						<th>Area</th>
						<th>Sold To</th>
						<th>Ship To</th>
						<th>Channel</th>
						<th>Outlet</th>
						<th>COMPUTED ALLOC</th>
						<th>FORCED ALLOC</th>
						<th>FINAL ALLOC</th>
					</tr>
				</thead>
				<tbody>
				</tbody>   
			</table> 
		</div>

	</div>
</div>



@include('shared.alloc')

<div class="row">
	<div class="col-lg-12">
		<div class="form-group">
			<a id="scheme_back" class="btn btn-default" href="{{action('ActivityController@edit', $activity->id);}}#schemes">Back to Activity Details</a>
		</div>
	</div>

</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		{{ Form::open(array('action' => array('SchemeController@updateallocation'), 'method' => 'PUT', 'class' => 'bs-component','id' => 'updateAlloc')) }}
		{{ Form::hidden('scheme_id', '', array('id' => 'scheme_id')) }}
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Update Allocation</h4>
			</div>
			<div class="modal-body">
				<table id="alloc_table" class="table table-bordered">
					<tbody>
						<tr>
							<td>Group</td>
							<td field="group"></td>
						</tr>
						<tr>
							<td>Area</td>
							<td field="area"></td>
						</tr>
						<tr>
							<td>Sold To</td>
							<td field="soldto"></td>
						</tr>
						<tr>
							<td>Ship To</td>
							<td field="shipto"></td>
						</tr>
						<tr>
							<td>Channel</td>
							<td field="channel"></td>
						</tr>
						<tr>
							<td>Outlet</td>
							<td field="outlet"></td>
						</tr>
						<tr>
							<td>Allocation</td>
							<td>
								<input class="form-control" placeholder="Allocation" name="new_alloc" type="text" value="" id="new_alloc">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button class="btn btn-primary">Update</button>
			</div>
		</div>
		{{ Form::close() }}
	</div>
</div>

@endif
<!-- Modal -->
<div class="modal fade" id="myCalculator" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Deal Calculator</h4>
			</div>
			<div class="modal-body">
				<table id="calculator" class="table table-bordered">
					<tbody>
						<tr>
							<td>Average Weekly Sales (in cases)</td>
							<td>
								<input class="form-control" name="weekly_sales" type="text" value="{{ number_format($total_gsv/52,2) }}" id="weekly_sales" readonly =''>
								
							</td>
						</tr>
						<tr>
							<td>Desired Number of Weeks</td>
							<td>
								<input class="form-control" placeholder="Desired Number of Weeks" name="no_weeks" type="text" value="" id="no_weeks">
							</td>
						</tr>
						<tr>
							<td>Ideal Number of Allocation (in cases)</td>
							<td>
								<input class="form-control" placeholder="Ideal Number of Allocation in Cases" name="no_alloc_cases" type="text" value="" id="no_alloc_cases" readonly =''>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button id="calculate" class="btn btn-primary">Calculate</button>
			</div>
		</div>
	</div>
</div>

@include('javascript.scheme.edit')

@stop

@section('page-script')

@stop