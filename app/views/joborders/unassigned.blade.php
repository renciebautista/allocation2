@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Unassigned Joborder List</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<p class="pull-right"><b>{{ count($joborders)}} record/s found.</b></p>
<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th>Joborder #</th>
						
						<th>Task</th>
						<th>Sub Task</th>
						<th>Department</th>
						<th>Assign To</th>
						<th>Status</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Date Created</th>
						<th>Action</th>
					</tr>
				</thead>
			  	<tbody>
			  		@foreach($joborders as $jo)
			  		<tr>
			  			<td>{{ $jo->id }}</td>
			  			<td>{{ $jo->task }}</td>
			  			<td>{{ $jo->sub_task }}</td>
			  			<td>{{ $jo->department->department }}</td>
			  			<td></td>
			  			<td>{{ $jo->status->joborder_status }}</td>
			  			<td>{{ date_format(date_create($jo->start_date),'m/d/Y') }}</td>
			  			<td>{{ date_format(date_create($jo->end_date),'m/d/Y') }}</td>
			  			<td>{{ date_format(date_create($jo->created_at),'m/d/Y H:m:s') }}</td>
			  			<td>
			  				{{ HTML::linkAction('JoborderController@unassignededit','View', $jo->id, array('class' => 'btn btn-success btn-xs')) }}
			  			</td>
			  		</tr>
			  		@endforeach
			  	</tbody>
			  	
			</table>
		</div>
	</div>
</div>

@stop
