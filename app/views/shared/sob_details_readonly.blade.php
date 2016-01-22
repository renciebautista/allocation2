@if($scheme->activity->activitytype->with_sob)
	<div class="tab-pane fade" id="sob">
		<br>
		<div class="panel panel-primary">
			<div class="panel-heading">SOB Details</div>
			<div class="panel-body">
					<br>
					@if(count($sobs) > 0)
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-12">
										<a class="btn btn-success" target="_blank" href="{{ URL::action('SchemeController@exportsob', $scheme->id ) }}">Export To Excel</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row mytable">
						<div class="col-lg-12">
							<div class="allocation_total table-responsive">
								<table id="sob-allocation" class="table table-condensed table-bordered display compact">
									<thead>
										
										<tr class="sob-percent">
											<th colspan="3"></th>
											<?php $total = 0; ?>
											@foreach($sob_header as $key => $header)
											<th class="alloc_per">
												{{ Form::text('wek['.$key.']',$header,array('id' => 'wek_'.$key, 'class' => 'numweek', 'readonly' => '')) }}
											</th>
											<?php $total += $header; ?>
											@endforeach
											<th><span id="sum">{{ number_format($total,2) }}%</span></th>
										</tr>
										<tr class="sob-header">
											<th>GROUP</th>
											<th>AREA</th>
											<th>SHIP TO</th>
											@foreach($sob_header as $key => $header)
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
											@foreach($sob_header as $key => $header)
											<?php $col = "wk_".$key; ?>
											<td class="sob_alloc">{{ $sob->$col }}</td>
											<?php $sum += $sob->$col; ?>
											@endforeach
											<td class="sob_alloc_header wek_sum"><span id="sum_alloc">{{ $sum }}</span></td>
											
										</tr>
										@endforeach
										
									</tbody>
								</table> 
							</div>
						</div>
					</div>	
					@endif				
			</div>
		</div>
	</div>
	@endif