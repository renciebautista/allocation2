<div class="panel panel-primary">
	<div class="panel-heading">Allocation Summary</div>
	<div class="panel-body">
		<?php 
			$groups_alloc = array();
			$areas_alloc = array();
			$big_10 = array();
			$gia = array();
			$nc = array();
			$total_gsv = 0;
		 ?>

							<?php $total_alloc = 0; ?>
							@foreach($allocations as $customer)
							<?php $alloc = 0; ?>
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
								@if(!empty($customer->shiptos))
								@foreach($customer->shiptos as $shipto)
								<?php $shipto_alloc = 0; ?>
										@if(!is_null($shipto['split']))
											@if($alloc > 0)
											<?php $shipto_alloc = round(($alloc * $shipto['split']) / 100) ?>
											@endif
											
										@else
											@if($shipto['gsv'] >0)
												<?php 
													if(empty($customer->area_code_two)){
														$shipto_alloc = round(round($shipto['gsv'] / $customer->ado_total,2) * $alloc);
													}else{
														$shipto_alloc = $alloc;
													}
													
												 ?>
											@endif
										@endif
									@if(!empty($shipto['accounts'] ))
										<?php $others = $shipto_alloc; ?>
										
										@foreach($shipto['accounts'] as $account)
										<?php $account_alloc = 0; ?>
										
												<?php $p = 0; ?>
												@if($customer->gsv > 0)
												<?php 
													$p = round($account['gsv']/$customer->gsv * 100,2)
												 ?>
												 @endif
												<?php 
													$account_alloc = round(($p * $shipto_alloc)/100);
													if($account_alloc > 0){
														$others -= $account_alloc;
													}
													
													if($account['account_group_code'] == 'AG1'){
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
			
										@endforeach	
										
									@endif
								@endforeach	
								@endif
							@endforeach

		<div class="row mytable">
			<div class="col-lg-12">
				<div class="allocation_total table-responsive">
					<table class="table table-condensed display compact ">
						<tbody>
							<tr class="blue">
								<td>MT/DT Breakdown </td>
								<td>Computed Allocated</td>
								<td>Forced Allocated</td>
								<td>Final Allocated</td>
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
								<td>Computed Allocated</td>
								<td>Forced Allocated</td>
								<td>Final Allocated</td>
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
								<td>Computed Allocated</td>
								<td>Forced Allocated</td>
								<td>Final Allocated</td>
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
								<td>Computed Allocated</td>
								<td>Forced Allocated</td>
								<td>Final Allocated</td>
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