<div>
	<table id="title" width="910">
		<tr>
			<td width="100">Circular Reference No.</td>
			<td>: {{ $activity->id }}</td>
		</tr>
		<tr>
			<td>Activity Name</td>
			<td>: {{ strip_tags($activity->activity_code) }}</td>
		</tr>
		<tr>
			<td>Proponent Name</td>
			<td>: {{ $activity->createdby->getFullname() }} / {{ $activity->createdby->contact_no }}</td>
		</tr>
		<tr>
			<td>PMOG Partner</td>
			@if(!empty($activity->pmog[0]))
			<td>: {{ $activity->pmog[0]->getFullname() }} / {{ $activity->pmog[0]->contact_no }}</td>
			@else
			<td>:</td>
			@endif
		</tr>
		<tr>
			<td>TOP Cycle</td>
			<td>: {{ $activity->cycle->cycle_name }}</td>
		</tr>
	</table>
</div>