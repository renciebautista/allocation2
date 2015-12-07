<table>
	<tr>
		<td>Circular Reference No.</td>
		<td>: {{ $activity->id }}</td>
	</tr>
	<tr>
		<td>Activity Name</td>
		<td>: {{ $activity->activity_code }}</td>
	</tr>
	<tr>
		<td>TOP Cycle</td>
		<td>: {{ $activity->cycle->cycle_name }}</td>
	</tr>
	<tr>
		<td>Proponent Name</td>
		<td>: {{ $activity->proponent_name }} 
			@if(!empty($activity->contact_no))
			/ {{ $activity->contact_no }}
			@endif
		</td>
	</tr>
	<tr>
		<td>PMOG Partner</td>
		@if(!empty($planner))
		<td>: {{  $planner->planner_desc }} 
			@if(!empty($planner->contact_no))
			/ {{ $planner->contact_no }}
			@endif
		</td>
		@else
		<td>:</td>
		@endif
	</tr>

	<tr>
		<td>Approvers</td>
		<td>
			<ul style="list-style:none; margin-top:0px;margin-bottom:0px;margin-left:-40px;">
			@foreach($approvers as $approver)
			<li>: {{$approver->approver_desc}} 
				@if(!empty($approver->contact_no))
					/ {{ $approver->contact_no }}
					@endif
				</li>
			@endforeach
			</ul>
		</td>	
	</tr>
	
</table>