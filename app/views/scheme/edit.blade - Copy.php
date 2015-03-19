@extends('layouts.layout')

@section('content')
<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Edit {{ $scheme->name }} </h1>
			<h2>Activty : {{ $scheme->activity->circular_name }}</h2>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="form-group">
			<a class="btn btn-default" href="{{action('ActivityController@edit', $activity->id);}}#schemes">Back to Activity Details</a>
		</div>
	</div>

</div>



@include('partials.notification')

<div class="panel panel-primary">
	<div class="panel-heading">Scheme Details</div>
	<div class="panel-body">

			{{ Form::open(array('action' => array('SchemeController@update', $scheme->id), 'method' => 'PUT', 'class' => 'bs-component')) }}
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
								{{ Form::select('skus[]', $skus, $sel_skus, array('data-placeholder' => 'Select Reference SKUS','id' => 'skus', 'class' => 'form-control')) }}
							</div>
							<div class="col-lg-6">
								{{ Form::label('involve', 'Host SKU', array('class' => 'control-label')) }}
								{{ Form::select('involve[]', $skus, $sel_skus, array('data-placeholder' => 'Select Reference SKUS','id' => 'involve', 'class' => 'form-control')) }}
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
								{{ Form::text('item_code',$scheme->item_code, array('class' => 'form-control', 'placeholder' => 'Item Code')) }}
							</div>
							<div class="col-lg-4">
								{{ Form::label('item_barcode', 'Item Barcode', array('class' => 'control-label')) }}
								{{ Form::text('item_barcode',$scheme->item_barcode, array('class' => 'form-control', 'placeholder' => 'Item Barcode')) }}
							</div>
							<div class="col-lg-4">
								{{ Form::label('item_casecode', 'Item Casecode', array('class' => 'control-label')) }}
								{{ Form::text('item_casecode',$scheme->item_casecode,array('class' => 'form-control', 'placeholder' => 'Item Casecode')) }}
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
								{{ Form::label('srp_p', 'SRP of Premium (Php)', array('class' => 'control-label')) }}
								{{ Form::text('srp_p',number_format($scheme->srp_p,2), array('id' => 'srp_p', 'class' => 'form-control', 'placeholder' => 'SRP of Premium (Php)')) }}
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
								{{ Form::label('ulp', 'Total Unilever Cost (Php)', array('class' => 'control-label')) }}
								{{ Form::text('ulp',number_format($scheme->ulp,2), array('class' => 'form-control', 'placeholder' => 'Total Unilever Cost (Php)', 'id' => 'ulp', 'readonly' => '')) }}
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
								{{ Form::label('pr', 'Purchase Requirement (Php)', array('class' => 'control-label')) }}
								{{ Form::text('pr', number_format($scheme->pr,2), array('id' => 'pr', 'class' => 'form-control', 'placeholder' => 'Purchase Requirement (Php)')) }}
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
								{{ Form::label('deals', 'No. of Deals Per Case', array('class' => 'control-label')) }}
								{{ Form::text('deals',number_format($scheme->deals), array('class' => 'form-control', 'placeholder' => 'No. of Deals Per Case')) }}
							</div>
							<div class="col-lg-3">
								{{ Form::label('deals', 'Total No. of Deals', array('class' => 'control-label')) }}
								{{ Form::text('deals',number_format($scheme->deals), array('class' => 'form-control', 'placeholder' => 'No. of Deals Per Case','readonly' => '')) }}
							</div>
							<div class="col-lg-3">
								{{ Form::label('deals', 'Total No. of Cases', array('class' => 'control-label')) }}
								{{ Form::text('deals',number_format($scheme->deals), array('class' => 'form-control', 'placeholder' => 'No. of Deals Per Case','readonly' => '')) }}
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
						<div class="row">
							<div class="col-lg-12">
								{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
							</div>
						</div>
					</div>
				</div>
			</div>
			{{ Form::close() }}

	</div>
</div>


<div class="panel panel-primary">
	<div class="panel-heading">Allocation Details</div>
	<div class="panel-body">
		<?php 
			$groups_alloc = array();
			$areas_alloc = array();
			$big_10 = array();
			$gia = array();
			$nc = array();
			$total_gsv = 0;
		 ?>
		<div class="row mytable">
			<div class="col-lg-12">
				<div id="allocation" class="table-responsive">
					<table id="customer-allocation" class="table table-condensed table-bordered display compact ">
						<thead>
							<tr>
								<th>Group</th>
								<th>Area</th>
								<th>Sold To</th>
								<th>Ship To</th>
								<th>Channel</th>
								<th>Outlet</th>
								<th class="rotate-45"><div><span>SOLD TO GSV</span></div></th>
								<th class="rotate-45"><div><span>SOLD TO GSV %</span></div></th>
								<th class="rotate-45"><div><span>SOLD TO ALLOC</span></div></th>
								<th class="rotate-45"><div><span>SHIP TO GSV</span></div></th>
								<th class="rotate-45"><div><span>SHIP TO ALLOC</span></div></th>
								<th class="rotate-45"><div><span>OUTLET GSV</span></div></th>
								<th class="rotate-45"><div><span>OUTLET ALLOC %</span></div></th>
								<th class="rotate-45"><div><span>OUTLET ALLOC</span></div></th>
								<th class="rotate-45"><div><span>ALLOCATION</span></div></th>
								<th class="rotate-45"><div><span>COMPUTED ALLOCATION</span></div></th>
								<th class="rotate-45"><div><span>VETTED ALLOCATION</span></div></th>
								<th class="rotate-45"><div><span>FINAL ALLOCATION</span></div></th>
							</tr>
						</thead>
						<tbody>
							
							@if(count($allocations) == 0)
							<tr>
								<td colspan="16">No record found!</td>
							</tr>
							@else
							<?php $total_alloc = 0; ?>
							@foreach($allocations as $customer)
							<?php $alloc = 0; ?>
							<tr class="info">
								<td>{{ $customer->group_name }}</td>
								<td>{{ $customer->area_name }}</td>
								<td>{{ $customer->customer_name }}</td>
								<td>{{ $customer->customer_name }} TOTAL</td>
								<td></td>
								<td></td>
								<td>
									<?php 
									if($customer->gsv > 0){
										$total_gsv += $customer->gsv;
									}
									?>
									{{ number_format($customer->gsv,2) }}
								</td>
								@if( $customer->gsv > 0)
								<td> {{ round(($customer->gsv/$total_sales) * 100,2) }} %</td>
								<td>
									<?php 
										$alloc = round(($customer->gsv/$total_sales) * $qty);
										$total_alloc += $alloc;

										if (array_key_exists($customer->group_code, $groups_alloc)) {
										    $groups_alloc[$customer->group_code] += $alloc;
										}else{
											$groups_alloc[$customer->group_code] = $alloc;
										}

										if (array_key_exists($customer->area_code, $areas_alloc)) {
										    $areas_alloc[$customer->area_code] += $alloc;
										}else{
											$areas_alloc[$customer->area_code] = $alloc;
										}
										
									 ?>
									{{ number_format($alloc) }}
								</td>
								@else
								<td> 0.00 %</td>
								<td> 0 </td>
								@endif
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
								
								@if(!empty($customer->shiptos))
								@foreach($customer->shiptos as $shipto)
								<?php $shipto_alloc = 0; ?>
								<tr>
									<td>{{ $customer->group_name }}</td>
									<td>{{ $customer->area_name }}</td>
									<td>{{ $customer->customer_name }}</td>
									<td>{{ $shipto['ship_to_name'] }}</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td>{{ $shipto['gsv'] }}</td>
									<td>
										@if(!is_null($shipto['split']))
											@if($alloc > 0)
											<?php $shipto_alloc = round(($alloc * $shipto['split']) / 100) ?>
												{{ number_format($shipto_alloc) }}
											@endif
											
										@else
											@if($shipto['gsv'] >0)
												<?php 
													$shipto_alloc = round(round($shipto['gsv'] / $customer->ado_total,2) * $alloc);
												 ?>
												{{ number_format($shipto_alloc) }}
											@endif
										@endif
									</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
									@if(!empty($shipto['accounts'] ))
										<?php $others = $shipto_alloc; ?>
										
										@foreach($shipto['accounts'] as $account)
										<?php $account_alloc = 0; ?>
										<tr class="warning">
											<td>{{ $customer->group_name }}</td>
											<td>{{ $customer->area_name }}</td>
											<td>{{ $customer->customer_name }}</td>
											<td>{{ $shipto['ship_to_name'] }}</td>
											<td>{{ $account['channel_name'] }}</td>
											<td>{{ $account['account_name'] }}</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td>{{ number_format($account['gsv'],2) }}</td>
											<td>
												<?php $p = 0; ?>
												@if($customer->gsv > 0)
												<?php 
													$p = round($account['gsv']/$customer->gsv * 100,2)
												 ?>
												 {{ number_format($p,2) }} %
												 @else
												 0.00 %
												 @endif
											</td>
											<td>
												<?php 
													$account_alloc = round(($p * $shipto_alloc)/100);
													if($account_alloc > 0){
														$others -= $account_alloc;
													}
													
													if($account['account_group_code'] == 'AG4'){
														if (array_key_exists($account['account_name'], $big_10)) {
														    $big_10[$account['account_name']] += $account_alloc;
														}else{
															$big_10[$account['account_name']] = $account_alloc;
														}
													}

													if($account['account_group_code'] == 'AG5'){
														if (array_key_exists($account['account_name'], $gia)) {
														    $gia[$account['account_name']] += $account_alloc;
														}else{
															$gia[$account['account_name']] = $account_alloc;
														}
													}

													if($account['account_group_code'] == 'AG7'){
														if (array_key_exists($account['account_name'], $nc)) {
														    $nc[$account['account_name']] += $account_alloc;
														}else{
															$nc[$account['account_name']] = $account_alloc;
														}
													}

													
												 ?>
												{{ number_format($account_alloc) }}
											</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
										@endforeach	
										<tr class="warning">
											<td>{{ $customer->group_name }}</td>
											<td>{{ $customer->area_name }}</td>
											<td>{{ $customer->customer_name }}</td>
											<td>{{ $shipto['ship_to_name'] }}</td>
											<td></td>
											<td>OTHERS</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td>{{ number_format(($others > 0) ? $others: 0) }}</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
										</tr>
									@endif
								@endforeach	
								@endif
							@endforeach
							@endif
						</tbody>
					</table> 
				</div>
			</div>
		</div>

		<div>
			<!-- <label>Minimum Limit</label> : 10 <br> -->
			<label>Total Sales GSV</label> : {{ number_format($total_gsv,2)}}<br>
			<label>Total Sales</label> : {{ number_format($total_sales,2)}}<br>
			<label>Total Allocattion</label> : {{ number_format($total_alloc)}}
		</div>

		<div class="row mytable">
			<div class="col-lg-12">
				<div class="allocation_total table-responsive">
					<table class="table table-condensed display compact ">
						<tbody>
							<tr class="blue">
								<td>MT/DT Breakdown </td>
								<td>Allocated</td>
							</tr>
							<?php $total_group = 0; ?>
							@foreach($summary as $grp)
							<tr>
								<td>{{ $grp->group_name }}</td>
								<td>
									<?php 
										if(array_key_exists($grp->group_code, $groups_alloc)){
											$total_group += $groups_alloc[$grp->group_code];
											echo number_format($groups_alloc[$grp->group_code]);
										}else{
											echo 0;
										}
									 ?>
								</td>
							</tr>
							@endforeach
							<tr class="blue">
								<td>Total</td>
								<td>{{ number_format($total_group) }}</td>
							</tr>
						</tbody>
					</table> 
				</div>
			</div>
		</div>

		<div class="row mytable">
			<div class="col-lg-12">
				<div class="allocation_total table-responsive">
					<table class="table table-condensed display compact ">
						<tbody>
							<tr class="blue">
								<td colspan="2">Area Breakdown </td>
								<td>Allocated</td>
							</tr>
							
							@foreach($summary as $grp)
								<?php $total_areas = 0; ?>
								@foreach($grp->areas as $area)
								<tr>
									<td>{{ $grp->group_name }}</td>
									<td>{{ $area->area_name }}</td>
									<td><?php 
										if(array_key_exists($area->area_code, $areas_alloc)){
											$total_areas += $areas_alloc[$area->area_code];
											echo number_format($areas_alloc[$area->area_code]);
										}else{
											echo 0;
										}
									 ?>
									</td>
								</tr>
								@endforeach
								<tr class="blue">
									<td colspan="2">{{ $grp->group_name }} Total</td>
									<td>{{ number_format($total_areas) }}</td>
								</tr>
							@endforeach
						</tbody>
					</table> 
				</div>
			</div>
		</div>

		<div class="row mytable">
			<div class="col-lg-12">
				<div class="allocation_total table-responsive">
					<table class="table table-condensed display compact ">
						<tbody>
							<tr class="blue">
								<td colspan="2">BIG 10 Breakdown</td>
								<td>Allocated</td>
							</tr>
							<?php $total_10 = 0; ?>
							@foreach($big10 as $row)
							<tr>
								<td colspan="2">{{ $row->account_name }}</td>
								<td><?php 
										if(array_key_exists($row->account_name, $big_10)){
											$total_10 += $big_10[$row->account_name];
											echo number_format($big_10[$row->account_name]);
										}else{
											echo 0;
										}
									 ?>
									</td>
							</tr>
							@endforeach
							<tr class="blue">
									<td colspan="2">BIG 10 Total</td>
									<td>{{ number_format($total_10) }}</td>
								</tr>
						</tbody>
					</table> 
				</div>
			</div>
		</div>

		<div class="row mytable">
			<div class="col-lg-12">
				<div class="allocation_total table-responsive">
					<table class="table table-condensed display compact ">
						<tbody>
							<tr class="blue">
								<td colspan="2">GAISANO Breakdown</td>
								<td>Allocated</td>
							</tr>
							<?php $total_g = 0; ?>
							@foreach($gaisanos as $row)
							<tr>
								<td colspan="2">{{ $row->account_name }}</td>
								<td><?php 
										if(array_key_exists($row->account_name, $gia)){
											$total_g += $gia[$row->account_name];
											echo number_format($gia[$row->account_name]);
										}else{
											echo 0;
										}
									 ?>
									</td>
							</tr>
							@endforeach
							<tr class="blue">
									<td colspan="2">GAISANO Total</td>
									<td>{{ number_format($total_g) }}</td>
								</tr>
						</tbody>
					</table> 
				</div>
			</div>
		</div>

		<div class="row mytable">
			<div class="col-lg-12">
				<div class="allocation_total table-responsive">
					<table class="table table-condensed display compact ">
						<tbody>
							<tr class="blue">
								<td colspan="2">NCCC Breakdown</td>
								<td>Allocated</td>
							</tr>
							<?php $total_n = 0; ?>
							@foreach($nccc as $row)
							<tr>
								<td colspan="2">{{ $row->account_name }}</td>
								<td><?php 
										if(array_key_exists($row->account_name, $nc)){
											$total_n += $nc[$row->account_name];
											echo number_format($nc[$row->account_name]);
										}else{
											echo 0;
										}
									 ?>
									</td>
							</tr>
							@endforeach
							<tr class="blue">
									<td colspan="2">NCCC Total</td>
									<td>{{ number_format($total_n) }}</td>
								</tr>
						</tbody>
					</table> 
				</div>
			</div>
		</div>
		
	</div>
</div>

@stop


@section('page-script')
$("#skus,#involve").chosen();

$('#pr, #srp_p, #other_cost,#total_alloc').inputNumber();


$('#pr, #srp_p, #other_cost').blur(function() {
	var srp = accounting.unformat($('#srp_p').val()) || 0;
	var others = accounting.unformat($('#other_cost').val()) || 0;
	$('#ulp').val(accounting.formatNumber(srp+others, 2, ",","."));
	var ulp = accounting.unformat($('#ulp').val()) || 0;
	var pr = accounting.unformat($('#pr').val()) || 0;
	if(pr == 0){
		$('#cost_sale').val(0);
	}else{
		$('#cost_sale').val(accounting.formatNumber((ulp/pr) * 100 ,2));
	}
	compute_budget();
});



$('#total_alloc').blur(function() {
	compute_budget();
});

function compute_budget(){
	var total_alloc = accounting.unformat($('#total_alloc').val()) || 0;
	var srp = accounting.unformat($('#srp_p').val()) || 0;
	var others = accounting.unformat($('#other_cost').val()) || 0;
	$('#tts_r').val(accounting.formatNumber(total_alloc*srp, 2, ",","."));

	$('#pe_r').val(accounting.formatNumber(total_alloc*others, 2, ",","."));

	var tts_r = accounting.unformat($('#tts_r').val()) || 0;
	var pe_r = accounting.unformat($('#pe_r').val()) || 0;

	$('#total_cost').val(accounting.formatNumber(tts_r+pe_r, 2, ",","."));
}

$("form").validate({
	ignore: null,
	errorElement: "span", 
	errorClass : "has-error",
	rules: {
		scheme_name: "required",
		pr: "required",
		srp_p: "required",
		total_alloc: "required",
		deals: "required",
		'skus[]': {
                required: true
            }
	},
	errorPlacement: function(error, element) {               
		
	},
	highlight: function( element, errorClass, validClass ) {
    	$(element.closest('div')).addClass(errorClass).removeClass(validClass);
  	},
  	unhighlight: function( element, errorClass, validClass ) {
    	$(element.closest('div')).removeClass(errorClass).addClass(validClass);
  	}
});

var table = $('#customer-allocation').DataTable( {
	"dom": 'C<"clear">lfrtip',
	"bSort": false,
	"searching": false,
	"scrollY": $(window).height()/2,
	"scrollX": "100%",
	"scrollCollapse": true,
	"paging": false
} );
new $.fn.dataTable.FixedColumns( table, {
	leftColumns: 6
} );

@stop