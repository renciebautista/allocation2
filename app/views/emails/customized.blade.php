<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>For Approval</title>
	<meta http-equiv="Content-Type" content="text/html; charset=us-ascii"><!-- CREATE TEXT STYLES USED IN THIS HTML FILE, START -->

	<style type="text/css">
	body{
		font-family: "Open Sans","Helvetica Neue",Helvetica,Arial,sans-serif; 
		font-size: 13px;
	}
	p{

	}
	table, th, td {
	   border: 1px solid #aaa;
	   font-size: 12px;
	}
	.header1{
		font-size: 20px;
		color: #fff;
	}
	.center{
		text-align: center;
	}
	.right{
		text-align: right;
	}
	.blue{
		color: #ffffff;
 	 	background-color: #008cba;
	}
	.alert{
		color: #ffffff;
 	 	background-color:  #A90000;
	}
	.success{
		color: #ffffff;
 	 	background-color:  #018E33;
	}
	h1{
		font-size: 18px;
	}
	table>tbody>tr:nth-of-type(odd) {
	    background-color: #f9f9f9;
	    color: #000;
	}
	</style><!-- CREATE TEXT STYLES USED IN THIS HTML FILE, END -->
</head>

<body>

	<h1>Hi {{ ucwords(strtolower($to_user)) }}!</h1>
	{{ $line1 }}
	{{ $line2 }}
	<hr>
	<table width="100%" border="0" cellspacing="0" cellpadding="5">
		<thead>
			<tr class="blue">
				<th class="center header1" colspan="12">Summary of Activities</th>
			</tr>
			<tr class="blue">
				<th class="center"></th>
				<th class="center">ID</th>
				<th class="center">Status</th>
				<th class="center">TOP Cycle</th>
				<th class="center">Scope</th>
				<th class="center">Activity Type</th>
				<th class="center">Activity Title</th>
				<th class="center">Proponent</th>
				<th class="center">PMOG Partner</th>
				<th class="center">Start Date</th>
				<th class="center">End Date</th>
				<th class="center">Billing Deadline</th>
			</tr>
		</thead>
		<tbody>
			
				<td class="right">1</td>
				<td class="right">{{ $activity->id }}</td>
				<td class="center">{{ $activity->status }}</td>
				<td class="center">{{ $activity->cycle_name }}</td>
				<td class="center">{{ $activity->scope_name }}</td>
				<td class="center">{{ $activity->activity_type }}</td>
				<td class="center">{{ $activity->circular_name }}</td>
				<td class="center">{{ $activity->proponent }}</td>
				<td class="center">{{ $activity->planner }}</td>
				<td class="center">{{ date_format(date_create($activity->eimplementation_date),'m/d/Y') }}</td>
				<td class="center">{{ date_format(date_create($activity->end_date),'m/d/Y') }}</td>
				@if($activity->billing_date != "")
				<td class="center">{{ date_format(date_create($activity->billing_date),'m/d/Y') }}</td>
				@else
				<td class="center"></td>
				@endif
			
		</tbody>
		</table> 
	<hr>
	<br>
	<p><b>Disclaimer : </b>This is an auto-generated email. Please do not reply.</p>
</body>


</html>