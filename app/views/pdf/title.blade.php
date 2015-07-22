<div>
	<table id="title" width="910">
		<tr>
			<td width="80">Circular Reference No.</td>
			<td>: {{ $activity->id }}</td>
		</tr>
		<tr>
			<td>Activity Name</td>
			<td>: {{ strip_tags($activity->activity_code) }}</td>
		</tr>
		<tr>
			<td>TOP Cycle</td>
			<td>: {{ $activity->cycle->cycle_name }}</td>
		</tr>
		<tr>
			<td>Proponent Name</td>
			<td>: {{ $activity->createdby->getFullname() }} 
				@if(!empty($activity->createdby->contact_no))
				/ {{ $activity->createdby->contact_no }}
				@endif
			</td>
		</tr>
		<tr>
			<td>PMOG Partner</td>

			@if(!empty($activity->pmog[0]))
			<td>: {{  $activity->pmog[0]->getFullname() }} 
				@if(!empty($activity->pmog[0]->contact_no))
				/ {{ $activity->pmog[0]->contact_no }}
				@endif
			</td>
			@else
			<td>:</td>
			@endif
		</tr>

		<tr>
			<td>Approvers</td>
			@if(!empty($approvers))
			<td>
				<ul>
				@foreach($approvers as $approver)
				<li>: {{$approver->first_name}} {{$approver->last_name}}</li>
				@endforeach
				</ul>
			</td>
			@else
			<td>:</td>
			@endif
		</tr>
		
	</table>
</div>