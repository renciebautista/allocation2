<div class="row mytable">
					<div class="col-lg-12">
						<div class="allocation_total table-responsive">
							
							<table id="sob-allocation" class="table table-condensed table-bordered display compact">
								
								<thead>
									<?php $first_index = 1; ?>
									@foreach($sobgroups as $group)
									<tr class="sob-percent">
										<th colspan="4" >
											<span class="pull-right">{{ $group->sobgroup }}</span>
										</th>
										<?php $total = 0; ?>
										@foreach($sob_header[$group->id] as $key => $header)
										<th class="alloc_per">
											{{ $header }}%
										</th>
										<?php $total += $header; ?>
										@endforeach
										<th>{{ number_format($total,2) }}%</th>
									</tr>
									<tr class="even">
										<th colspan="4" >
											<span class="pull-right">{{ $group->sobgroup }} Week Total</span>
										</th>
										<?php $total = 0; ?>
										@foreach($sob_header[$group->id] as $key => $header)
										<th>
										<?php 
											if(!isset($sob_group_total[$group->sobgroup][$key])){
												echo 0;
											}else{
												echo number_format($sob_group_total[$group->sobgroup][$key]);
											}
										?>
			
										</th>
										<?php 
											if(!isset($sob_group_total[$group->sobgroup][$key])){

											}else{
												$total += $sob_group_total[$group->sobgroup][$key];
											}
										?>
										@endforeach
										<th>{{ number_format($total) }}</th>
									</tr>
									@endforeach
									<tr class="sob-header">
										<th>GROUP</th>
										<th>AREA</th>
										<th>SHIP TO</th>
										<th>PERCENTAGE GROUP</th>
										@foreach($sob_header[$sobgroups[0]->id] as $key => $header)
										<th class="alloc_per">WK {{ $key }}</th>
										@endforeach
										<th class="sob_alloc_header">Total</th>
									</tr>
								</thead>
								<tbody>
									@foreach($sobs as $sob)
									<?php $sum = 0; ?>
									<tr>
										<td>{{ $sob->group }}</td>
										<td>{{ $sob->area }}</td>
										<td>{{ $sob->ship_to }}</td>
										<td>{{ $sob->sobgroup }}</td>
										@foreach($sob_header[$sobgroups[0]->id] as $key => $header)
										<?php $col = "wk_".$key; ?>
										<td class="sob_alloc"><span class="pull-right">{{ number_format($sob->$col) }}</span></td>
										<?php $sum += $sob->$col; ?>
										@endforeach
										<td class="sob_alloc_header wek_sum"><span clss="sum_alloc">{{ number_format($sum) }}</span></td>
										
									</tr>
									@endforeach
								</tbody>
								
							</table> 
						</div>
					</div>
				</div>