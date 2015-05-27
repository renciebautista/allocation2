<div class="panel panel-primary">
	<div class="panel-heading">Allocation Summary</div>
	<div class="panel-body">
		

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