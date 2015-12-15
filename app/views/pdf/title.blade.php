<div>
	<table id="title" width="910">
		<tr>
			<td width="80"><b>Circular Reference No.</b></td>
			<td>: {{ $activity->id }}</td>
		</tr>
		<tr>
			<td><b>Activity Name</b></td>
			<td>: {{ strip_tags($activity->activity_code) }}</td>
		</tr>
		<tr>
			<td><b>TOP Cycle</b></td>
			<td>: {{ $activity->cycle->cycle_name }}</td>
		</tr>
		<tr>
			<td><b>Proponent Name</b></td>
			<td>: {{ $activity->proponent_name }} 
				@if(!empty($activity->contact_no))
				/ {{ $activity->contact_no }}
				@endif
			</td>
		</tr>
		<tr>
			<td><b>PMOG Partner</b></td>

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
			<td><b>Approvers</b></td>
			@if(!empty($approvers))
			<td>
				<ul>
				@foreach($approvers as $approver)
				<li>: {{$approver->approver_desc}} 
					@if(!empty($approver->contact_no))
						/ {{ $approver->contact_no }}
						@endif
					</li>
				@endforeach
				</ul>
			</td>
			@else
			<td>:</td>
			@endif
		</tr>
		
	</table>
</div>