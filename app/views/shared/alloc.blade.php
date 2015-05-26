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
								<td colspan="2">{{ $group->account_group_name}} Breakdown</td>
								<td>Computed Allocated</td>
								<td>Forced Allocated</td>
								<td>Final Allocated</td>
							</tr>
							
						</tbody>
					</table> 
				</div>
			</div>
		</div>
		@endforeach
	</div>
</div>