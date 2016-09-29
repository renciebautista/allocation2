@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Department Joborder List</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="panel panel-default">
	<div class="panel-heading">Filters</div>
	<div class="panel-body">
		{{ Form::open(array('method' => 'get','class' => 'bs-component')) }}
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('st', 'Status', array('class' => 'control-label')) }}
						{{ Form::select('st[]', $statuses, null, array('id' => 'st','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('tsk', 'Task', array('class' => 'control-label')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('tsk', 'Sub Task', array('class' => 'control-label')) }}
						</div>
					</div>
				</div>
			</div>
			
		</div>
		
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							<div class="search">
								<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		{{ Form::close() }}
	</div>
</div>

<hr>

<p class="pull-right"><b>{{ count($joborders)}} record/s found.</b></p>
<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th>Joborder #</th>
						<th>Activity Title</th>
						<th>Task</th>
						<th>Sub Task</th>
						<th>Department</th>
						<th>Assign To</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Date Created</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
			  	<tbody>
			  		@foreach($joborders as $jo)
			  		<tr>
			  			<td>{{ $jo->id }}</td>
			  			<td>{{ $jo->activity->id . ' - '. $jo->activity->circular_name  }}</td>
			  			<td>{{ $jo->task }}</td>
			  			<td>{{ $jo->sub_task }}</td>
			  			<td>{{ $jo->department->department }}</td>
			  			<td></td>
			  			
			  			<td>{{ date_format(date_create($jo->start_date),'m/d/Y') }}</td>
			  			<td>{{ date_format(date_create($jo->end_date),'m/d/Y') }}</td>
			  			<td>{{ date_format(date_create($jo->created_at),'m/d/Y H:m:s') }}</td>
			  			<td>{{ $jo->status->joborder_status }}</td>
			  			<td>
			  				{{ HTML::linkAction('JoborderController@edit','View', $jo->id, array('class' => 'btn btn-success btn-xs')) }}
			  			</td>
			  		</tr>
			  		@endforeach
			  	</tbody>
			  	
			</table>
		</div>
	</div>
</div>

@stop
