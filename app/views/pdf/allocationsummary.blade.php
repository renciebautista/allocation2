<h2>Allocation Breakdown Summary</h2>
<?php 
	$sku_cnt = count($tradedeal_skus);
	$l1 = 100 - (7 * $sku_cnt);
	$l2 = 97 - (7 * $sku_cnt);
	$l3 = 94 - (7 * $sku_cnt);
	$l4 = 79 - (7 * $sku_cnt);
 ?>
<table class="trade-table-summary">
	<tbody>
		<tr>
			<td colspan="5" style="width:{{$l1}}%;"></td>
			@foreach($tradedeal_skus as $sku)
			<td style="width:7%;">{{ $sku->pre_desc. ' '. $sku->pre_variant }}</td>
			@endforeach
		</tr>
		<?php $cnt = 1;
			$total = [];
		 ?>
		@foreach ($trade_allocations as $area)
		<tr>
			<td colspan="5" style="width:{{$l1}}%;">{{ $area->area }} TOTAL</td>
			@foreach($tradedeal_skus as $sku)
			<td style="text-align:right; width:7%;">
				@if(isset($area->area_total[$sku->pre_desc. ' '. $sku->pre_variant]))
				<?php 
					if(!isset($total[$sku->pre_desc. ' '. $sku->pre_variant])){
						$total[$sku->pre_desc. ' '. $sku->pre_variant] = 0;
					}
					$total[$sku->pre_desc. ' '. $sku->pre_variant] +=  $area->area_total[$sku->pre_desc. ' '. $sku->pre_variant];
					?>
				{{ number_format($area->area_total[$sku->pre_desc. ' '. $sku->pre_variant]) }}
				@endif
			</td>
			@endforeach
		</tr>
			<?php $sub1 = 1; ?>
			@foreach ($area->dist as $dist )
			<tr style="background-color: #c1ffbd;">
				<td style="width:3%;"></td>
				<td colspan="4" style="width:{{$l2}}%;"> {{$dist->sold_to}} TOTAL</td>
				@foreach($tradedeal_skus as $sku)
				<td style="text-align:right; width:7%;">
					@if(isset($dist->dist_total[$sku->pre_desc. ' '. $sku->pre_variant]))
					{{ number_format($dist->dist_total[$sku->pre_desc. ' '. $sku->pre_variant]) }}
					@endif
				</td>
				@endforeach
			</tr>
				<?php $sub2 = 2; ?>
				@foreach ($dist->shipto as $site)
				<tr style="background-color: #d9edf7;">
					<td style="width:3%;"></td>
					<td style="width:3%;"></td>
					<td colspan="3" style="width:{{$l3}}%;"> {{$site->ship_to_name}} TOTAL</td>
					@foreach($tradedeal_skus as $sku)
					<td style="text-align:right; width:7%;">
						@if(isset($site->ship_to_total[$sku->pre_desc. ' '. $sku->pre_variant]))
						{{ number_format($site->ship_to_total[$sku->pre_desc. ' '. $sku->pre_variant]) }}
						@endif
					</td>
					@endforeach
				</tr>
					@foreach ($site->schemes as $scheme)
					<tr style="background-color: #fcf8e3;">
						<td style="width:3%;"></td>
						<td style="width:3%;"></td>
						<td style="width:3%;"></td>
						<td style="width:12%;">{{ $scheme->scheme_code}}</td>
						<td style="width:{{$l4}}%;">{{ $scheme->scheme_description }}</td>
						@foreach($tradedeal_skus as $sku)
						<td style="text-align:right; width:7%;">
							@if(isset($scheme->premiums[$sku->pre_desc. ' '. $sku->pre_variant]))
							{{ number_format($scheme->premiums[$sku->pre_desc. ' '. $sku->pre_variant]) }}
							@endif
						</td>
						@endforeach
					</tr>
					@endforeach
				<?php $sub2++; ?>
				@endforeach
			<?php $sub1++; ?>
			@endforeach
		<?php $cnt++; ?>
		@endforeach

		<tr class="total">
										
							<td colspan="5" style="text-align:right; font-weight:bold;">Total Allocation</td>
							<td colspan="{{count($tradedeal_skus)}}" style="text-align:right; font-weight:bold;">
								<?php $t = 0; ?>
								@foreach($total as $x)
								<?php $t+=$x; ?>

								@endforeach
								{{ number_format($t) }}
							</td>
						</tr>

	
		
		
	</tbody>
</table> 