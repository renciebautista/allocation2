<h2>Allocation Breakdown Summary</h2>
				<table class="sub-table">
					<tbody>
						<tr>
							<td style="border-right: 1px solid #ffffff;"></td>
							<td style="border-right: 1px solid #ffffff;"></td>
							<td style="border-right: 1px solid #ffffff;"></td>
							<td style="border-right: 1px solid #ffffff;"></td>
							<td></td>
							@foreach($tradedeal_skus as $sku)
							<td style="width:50px;">{{ $sku->pre_desc. ' '. $sku->pre_variant }}</td>
							@endforeach
						</tr>
						<?php $cnt = 1;
							$total = [];
						 ?>
						@foreach ($trade_allocations as $area)
						<tr class="m_{{$cnt}}">
							<td colspan="5">  {{ $area->area }} TOTAL</td>
							@foreach($tradedeal_skus as $sku)
							<td style="text-align:right;">
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
								<td style="width:50px;"></td>
								<td colspan="4"> {{$dist->sold_to}} TOTAL</td>
								@foreach($tradedeal_skus as $sku)
								<td style="text-align:right;">
									@if(isset($dist->dist_total[$sku->pre_desc. ' '. $sku->pre_variant]))
									{{ number_format($dist->dist_total[$sku->pre_desc. ' '. $sku->pre_variant]) }}
									@endif
								</td>
								@endforeach
							</tr>
								<?php $sub2 = 2; ?>
								@foreach ($dist->shipto as $site)
								<tr style="background-color: #d9edf7;">
									<td style="width:50px;"></td>
									<td style="width:50px;"></td>
									<td colspan="3"> {{$site->ship_to_name}} TOTAL</td>
									@foreach($tradedeal_skus as $sku)
									<td style="text-align:right;">
										@if(isset($site->ship_to_total[$sku->pre_desc. ' '. $sku->pre_variant]))
										{{ number_format($site->ship_to_total[$sku->pre_desc. ' '. $sku->pre_variant]) }}
										@endif
									</td>
									@endforeach
								</tr>
									@foreach ($site->schemes as $scheme)
									<tr style="background-color: #fcf8e3;">
										<td style="width:50px;"></td>
										<td style="width:50px;"></td>
										<td style="width:50px;"></td>
										<td style="width:200px;">{{ $scheme->scheme_code}}</td>
										<td>{{ $scheme->scheme_description }}</td>
										@foreach($tradedeal_skus as $sku)
										<td style="text-align:right;">
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