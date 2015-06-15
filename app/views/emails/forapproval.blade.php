<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>For Approval</title>
	<meta http-equiv="Content-Type" content="text/html; charset=us-ascii"><!-- CREATE TEXT STYLES USED IN THIS HTML FILE, START -->

	<style type="text/css">
	table, th, td {
	   border: 1px solid black;
	}
	.center{
		text-align: center;
	}
	.right{
		text-align: right;
	}
	</style><!-- CREATE TEXT STYLES USED IN THIS HTML FILE, END -->
</head>

<body>

	<h1>Hi, {{ $user }}!</h1>
 
<p>Here are the list of activities for approval today.</p>

<table width="100%" border="0" cellspacing="0" cellpadding="5">
	<thead>
		<tr>
			<th class="center" colspan="10">Activities For Approval This Day</th>
		</tr>
		<tr>
			<th class="center" colspan="10">The information below is only valid for this day</th>
		</tr>
		<tr>
			<th class="center">ID</th>
			<th class="center">Status</th>
			<th class="center">TOP Cycle</th>
			<th class="center">Scope</th>
			<th class="center">Activity Type</th>
			<th class="center">Activity Title</th>
			<th class="center">PMOG Partner</th>
			<th class="center">Start Date</th>
			<th class="center">End Date</th>
			<th class="center">Billing Deadline</th>
		</tr>
	</thead>
	<tbody>
		@if(count($activities) == 0)
		<tr>
			<td colspan="12">No record found!</td>
		</tr>
		@else
		@foreach($activities as $activity)
		<tr>
			<td class="right">{{ $activity->id }}</td>
			<td class="center">{{ $activity->status }}</td>
			<td class="center">{{ $activity->cycle_name }}</td>
			<td class="center">{{ $activity->scope_name }}</td>
			<td class="center">{{ $activity->activity_type }}</td>
			<td class="center">{{ $activity->circular_name }}</td>
			<td class="center">{{ $activity->planner }}</td>
			<td class="center">{{ date_format(date_create($activity->eimplementation_date),'m/d/Y') }}</td>
			<td class="center">{{ date_format(date_create($activity->end_date),'m/d/Y') }}</td>
			<td class="center">{{ date_format(date_create($activity->billing_date),'m/d/Y') }}</td>
		</tr>
		@endforeach
		@endif
	</tbody>
	</table> 

</body>


</html>