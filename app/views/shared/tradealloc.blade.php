<div class="panel panel-primary">
			<div class="panel-heading">Allocation Breakdown Summary</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-12">
						<div class="table-responsive">
							<table id="trade_table" class="treegrid table table-bordered">
								<tbody>
									<tr>
										<td style="width:50px;"></td>
										<td style="width:50px;"></td>
										<td style="width:50px;"></td>
										<td style="width:200px;"></td>
										<td></td>
										@foreach($tradedeal_skus as $sku)
										<td class="premiums">{{ $sku->pre_desc. ' '. $sku->pre_variant }}</td>
										@endforeach
									</tr>
									<?php $cnt = 1;
										$total = [];
									 ?>
									@foreach ($trade_allocations as $area)
									<tr class="m_{{$cnt}}">
										<td colspan="5"> <span>+</span> {{ $area->area }}</td>
										@foreach($tradedeal_skus as $sku)
										<td class="alloc">
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
										<tr class="m_{{$cnt}}_ m_{{$cnt}}_{{$sub1}} sub light-green">
											<td style="width:50px;"></td>
											<td colspan="4"><span>+</span> {{$dist->sold_to}}</td>
											@foreach($tradedeal_skus as $sku)
											<td class="alloc">
												@if(isset($dist->dist_total[$sku->pre_desc. ' '. $sku->pre_variant]))
												{{ number_format($dist->dist_total[$sku->pre_desc. ' '. $sku->pre_variant]) }}
												@endif
											</td>
											@endforeach
										</tr>
											<?php $sub2 = 2; ?>
											@foreach ($dist->shipto as $site)
											<tr class="m_{{$cnt}}_{{$sub1}}_ m_{{$cnt}}_{{$sub1}}_{{$sub2}} sub light-blue">
												<td style="width:50px;"></td>
												<td style="width:50px;"></td>
												<td colspan="3"><span>+</span> {{$site->ship_to_name}}</td>
												@foreach($tradedeal_skus as $sku)
												<td class="alloc">
													@if(isset($site->ship_to_total[$sku->pre_desc. ' '. $sku->pre_variant]))
													{{ number_format($site->ship_to_total[$sku->pre_desc. ' '. $sku->pre_variant]) }}
													@endif
												</td>
												@endforeach
											</tr>
												@foreach ($site->schemes as $scheme)
												<tr class="m_{{$cnt}}_{{$sub1}}_{{$sub2}}_ sub light-orange">
													<td style="width:50px;"></td>
													<td style="width:50px;"></td>
													<td style="width:50px;"></td>
													<td style="width:200px;">{{ $scheme->scheme_code}}</td>
													<td>{{ $scheme->scheme_description }}</td>
													@foreach($tradedeal_skus as $sku)
													<td class="alloc">
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
										
										<td colspan="5" style="text-align:right">Total Allocation</td>
										<td colspan="{{count($tradedeal_skus)}}" style="text-align:right">
											<?php $t = 0; ?>
											@foreach($total as $x)
											<?php $t+=$x; ?>

											@endforeach
											{{ number_format($t) }}
										</td>
									</tr>

								</tbody>
							</table> 

						
						</div>
					</div>
				</div>
			</div>
		</div>