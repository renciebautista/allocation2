<div class="panel panel-default">
	<div class="panel-heading">Job Order Details</div>
	<div class="panel-body">
		<table id="joborder" class="table table-bordered"> 
			<tbody>
				<tr> 
					<td>Job Order #</td> 
					<td>{{ $joborder->id }}</td> 
					<td>Date Created</td> 
					<td>{{ date_format(date_create($joborder->created_at),'m/d/Y H:m:s') }}</td> 
				</tr>
				<tr> 
					<td>Revision Count</td> 
					<td colspan="3">{{ $joborder->revision }}</td> 
				</tr>
				<tr> 
					<td>Activity</td> 
					<td colspan="3">{{ $joborder->activity->id}} - {{ $joborder->activity->circular_name }}</td> 
				</tr>
				<tr> 
					<td>Status</td> 
					<td colspan="3">{{ $joborder->status->joborder_status }}</td> 
				</tr>
				<tr> 
					<td>Task</td> 
					<td colspan="3">{{ $joborder->task }}</td> 
				</tr> 
				<tr> 
					<td>Sub Task</td> 
					<td colspan="3">{{ $joborder->sub_task }}</td> 
				</tr> 
				<tr> 
					<td>Target Date</td> 
					<td colspan="3">{{ date_format(date_create($joborder->target_date),'m/d/Y') }}</td> 
				</tr>
				<tr> 
					<td>Estimated Start Date</td> 
					<td colspan="3">
						@if($joborder->start_date != '0000-00-00')
						{{ date_format(date_create($joborder->start_date),'m/d/Y') }}
						@endif
					</td> 
				</tr>
				<tr> 
					<td>Estimated End Date</td> 
					<td colspan="3">
						@if($joborder->end_date != '0000-00-00')
						{{ date_format(date_create($joborder->end_date),'m/d/Y') }}
						@endif
					</td> 
				</tr>
				<tr> 
					<td>Created By</td> 
					<td colspan="3">
						{{ $joborder->createdBy->getFullname() }}
					</td> 
				</tr>
				<tr> 
					<td>Assigned To</td> 
					<td colspan="3">
						@if($joborder->assigned_to > 0)
						{{ $joborder->assignedto->getFullname() }}
						@endif
					</td> 
				</tr>
			</tbody>
		</table>
	</div>		
</div>