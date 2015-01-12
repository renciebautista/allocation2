@extends('layouts.layout')

@section('content')
<?php 
	$groups = array();
	$areas = array();
	$total_gsv = 0;
 ?>
<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Activity List</h1>
	  	</div>
	</div>
</div>

<div class="row mytable">
	<div class="col-lg-12">
		<div id="allocation" class="table-responsive">
			<table id="customer-allocation" class="table table-bordered table-condensed display compact ">
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

								if (array_key_exists($customer->group_name, $groups)) {
								    $groups[$customer->group_name] += $alloc;
								}else{
									$groups[$customer->group_name] = $alloc;
								}

								if (array_key_exists($customer->area_name, $areas)) {
								    $areas[$customer->area_name] += $alloc;
								}else{
									$areas[$customer->area_name] = $alloc;
								}
								
							 ?>
							{{  number_format($alloc) }}
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
								@if(!is_null( $shipto['split']))
								{{ round(($alloc * $shipto['split']) / 100) }}
								@else
									@if($shipto['gsv'] >0)
										<?php 
											$shipto_alloc = number_format(round(round($shipto['gsv'] / $customer->ado_total,2) * $alloc));
										 ?>
										{{ $shipto_alloc }}
									@else
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
											$account_alloc = number_format(round(($shipto_alloc * $p) / 100));
											$others -= $account_alloc;
										 ?>
										{{ $account_alloc }}
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
									<td>{{ ($others > 0) ? $others: 0 }}</td>
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
						<td>Sub Total MT/DT</td>
						<td>Allocated</td>
					</tr>
					<?php $total_group = 0; ?>
					@foreach($groups as $key => $group)
					<tr>
						<td>{{ $key }}</td>
						<td>{{ number_format($group) }}</td>
						<?php $total_group += $group; ?>
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
						<td>Sub Total AREA</td>
						<td>Allocated</td>
					</tr>
					<?php $total_area = 0; ?>
					@foreach($areas as $key => $area)
					<tr>
						<td>{{ $key }}</td>
						<td>{{ number_format($area) }}</td>
						<?php $total_area += $area; ?>
					</tr>
					@endforeach
					<tr class="blue">
						<td>Total</td>
						<td>{{ number_format($total_area) }}</td>
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
						<td>Sub Total BIG 10 & GAISANO</td>
					</tr>
					<tr>
						<td>MT</td>
					</tr>
					<tr>
						<td>MT</td>
					</tr>
					<tr class="blue">
						<td>Total</td>
					</tr>
				</tbody>
			</table> 
		</div>
	</div>
</div>

<div class="form-group">
	{{ HTML::linkAction('SchemeController@index', 'Back', $id, array('class' => 'btn btn-default')) }}
</div>
@stop


@section('page-script')
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