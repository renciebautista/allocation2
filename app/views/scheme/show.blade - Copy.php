@extends('layouts.layout')

@section('content')

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
			<table id="customer-allocation" class="table table-condensed table-header-rotated">
				<thead>
					<tr>
						<th>Area Group</th>
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
						<td>{{ number_format($customer->gsv,2) }}</td>
						@if( $customer->gsv > 0)
						<td> {{ round(($customer->gsv/$total_sales) * 100,2) }} %</td>
						<td>
							<?php 
								$alloc = round(($customer->gsv/$total_sales) * $qty);
								$total_alloc += $alloc;
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
									<td></td>
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
	<label>Minimum Limit</label> : 10 <br>
	<label>Total Sales</label> : {{ number_format($total_sales,2)}}<br>
	<label>Total Allocattion</label> : {{ number_format($total_alloc)}}
</div>
@stop