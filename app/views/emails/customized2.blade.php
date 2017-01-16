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
	{{ $line3 }}
	<hr>
	<table width="100%" border="0" cellspacing="0" cellpadding="5">
		<thead>
			<tr class="blue">
				<th class="center header1" colspan="12">Timings Details</th>
			</tr>
			<tr class="blue">
				<th class="center">Task ID</th>
				<th class="center">Milestone</th>
				<th class="center">Task</th>
				<th class="center">Team Responsible</th>
				<th class="center">Duration (days)</th>
				<th class="center">Depends On</th>
				<th class="center">Start Date</th>
				<th class="center">End Date</th>
				<th class="center">Final Start Date</th>
				<th class="center">Final End Date</th>
			</tr>
		</thead>
		<tbody>
			@if(count($timings) == 0)
			<tr>
				<td colspan="10">No record found!</td>
			</tr>
			@else
			@foreach($timings as $timing)
			<tr>
				<td>{{ $timing->task_id }}</td>
				<td>{{ $timing->milestone }}</td>
				<td>{{ $timing->task }}</td>
				<td>{{ $timing->responsible }}</td>
				<td>{{ $timing->duration }}</td>
				<td>{{ $timing->depend_on }}</td>
				<td>{{ date_format(date_create($timing->start_date),'m/d/Y') }}</td>
				<td>{{ date_format(date_create($timing->end_date),'m/d/Y') }}</td>
				<td>{{ date_format(date_create($timing->final_start_date),'m/d/Y') }}</td>
				<td>{{ date_format(date_create($timing->finanl_end_date),'m/d/Y') }}</td>
			</tr>
			@endforeach
			@endif
		</tbody>
		</table> 
	<hr>
	<br>
	<p><b>Disclaimer : </b>This is an auto-generated email. Please do not reply.</p>
</body>


</html>