@extends('layouts.layout')

@section('content')
<?php 
	$groups_alloc = array();
	$areas_alloc = array();
	$big_10 = array();
	$gia = array();
	$nc = array();
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
			<table id="customer-allocation" class="table table-condensed display compact ">
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