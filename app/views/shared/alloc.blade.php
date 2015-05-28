<div class="panel panel-primary">
	<div class="panel-heading">Allocation Breakdown Summary</div>
	<div class="panel-body">
		
		<div class="row mytable">
			<div class="col-lg-12">
				<div class="allocation_total table-responsive">
					<table class="table table-condensed display compact ">
						<tbody>
							<tr class="blue">
								<td>MT/DT Breakdown </td>
								<td class="summary-alloc">Computed Allocated</td>
								<td class="summary-alloc">Forced Allocated</td>
								<td class="summary-alloc">Final Allocated</td>
							</tr>
							<?php 
								$total_computed_alloc = 0; 
								$total_force_alloc = 0; 
								$total_final_alloc = 0; 
							?>
							@foreach($groups as $group)
							<?php 
								$total_computed_alloc += $group->computed_alloc; 
								$total_force_alloc += $group->force_alloc; 
								$total_final_alloc += $group->final_alloc; 
							?>
							<tr>
								<td>{{ $group->group_name }}</td>
								<td class="right">{{ number_format($group->computed_alloc) }}</td>
								<td class="right">{{ number_format($group->force_alloc) }}</td>
								<td class="right">{{ number_format($group->final_alloc) }}</td>
							</tr>
							@endforeach
							<tr class="blue">
								<td>MT/DT Total</td>
								<td class="right">{{ number_format($total_computed_alloc) }}</td>
								<td class="right">{{ number_format($total_force_alloc) }}</td>
								<td class="right">{{ number_format($total_final_alloc) }}</td>
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
								<td colspan="2">Area Breakdown</td>
								<td class="summary-alloc">Computed Allocated</td>
								<td class="summary-alloc">Forced Allocated</td>
								<td class="summary-alloc">Final Allocated</td>
							</tr>
							<?php 
								$total_computed_alloc = 0; 
								$total_force_alloc = 0; 
								$total_final_alloc = 0; 

								
							?>
							@foreach($groups as $group)
								<?php 
									$per_computed_alloc = 0; 
									$per_force_alloc = 0; 
									$per_final_alloc = 0; 
								?>
								@foreach($group->area as $area)
									<?php 
										$per_computed_alloc += $area->computed_alloc; 
										$per_force_alloc += $area->force_alloc; 
										$per_final_alloc += $area->final_alloc; 

										$total_computed_alloc += $area->computed_alloc; 
										$total_force_alloc += $area->force_alloc; 
										$total_final_alloc += $area->final_alloc; 
									?>
									<tr>
										<td style="width:5%;">{{ $area->group }}</td>
										<td>{{ $area->area_name }}</td>
										<td class="right">{{ number_format($area->computed_alloc) }}</td>
										<td class="right">{{ number_format($area->force_alloc) }}</td>
										<td class="right">{{ number_format($area->final_alloc) }}</td>
									</tr>
								@endforeach
								<tr class="blue">
									<td colspan="2">{{ $area->group }} Total</td>
									<td class="right">{{ number_format($per_computed_alloc) }}</td>
									<td class="right">{{ number_format($per_force_alloc) }}</td>
									<td class="right">{{ number_format($per_final_alloc) }}</td>
								</tr>
							@endforeach
							<tr class="blue">
								<td colspan="2">Area Total</td>
								<td class="right">{{ number_format($total_computed_alloc) }}</td>
								<td class="right">{{ number_format($total_force_alloc) }}</td>
								<td class="right">{{ number_format($total_final_alloc) }}</td>
							</tr>
							
							
						</tbody>
					</table> 
				</div>
			</div>
		</div>

		@foreach($ac_groups as $group)
		<div class="row mytable">
			<div class="col-lg-12">
				<div class="allocation_total table-responsive">
					<table class="table table-condensed display compact ">
						<tbody>
							<tr class="blue">
								<td>{{ $group->account_group_name}} Breakdown</td>
								<td class="summary-alloc">Computed Allocated</td>
								<td class="summary-alloc">Forced Allocated</td>
								<td class="summary-alloc">Final Allocated</td>
							</tr>
							<?php 
								$total_computed_alloc = 0; 
								$total_force_alloc = 0; 
								$total_final_alloc = 0; 
							?>
							@foreach($group->customers as $customer)
							<?php 
								$total_computed_alloc += $customer->computed_alloc; 
								$total_force_alloc += $customer->force_alloc; 
								$total_final_alloc += $customer->final_alloc; 
							?>
							<tr>
								<td>{{ $customer->account_name }}</td>
								<td class="right">{{ number_format($customer->computed_alloc) }}</td>
								<td class="right">{{ number_format($customer->force_alloc) }}</td>
								<td class="right">{{ number_format($customer->final_alloc) }}</td>
							</tr>
							@endforeach
							<tr class="blue">
								<td>{{ $group->account_group_name}} Total</td>
								<td class="right">{{ number_format($total_computed_alloc) }}</td>
								<td class="right">{{ number_format($total_force_alloc) }}</td>
								<td class="right">{{ number_format($total_final_alloc) }}</td>
							</tr>
						</tbody>
					</table> 
				</div>
			</div>
		</div>
		@endforeach
	</div>
</div>