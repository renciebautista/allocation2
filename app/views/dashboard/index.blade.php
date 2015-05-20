@extends('layouts.layout')

@section('content')

@include('partials.notification')

<div class="col-lg-12">
	<div class="row">
			<div class="col-lg-12">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							<div class="pull-right">
								<a href="{{ URL::action('DashboardController@filters') }}" class="btn btn-info">Filter Settings</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<div class="page-header">
	  	<h1 id="tables">Ongoing Activities</h1>
	</div>

	<div class="table-responsive">	
		<table class="table table-condensed table-hover table-bordered">
			<thead>
				<tr>
					<th>Status</th>
					<th>TOP Cycle</th>
					<th>Scope</th>
					<th>Activity Type</th>
					<th>Activity Title</th>
					<th>PMOG Partner</th>
					<th>Start Date</th>
					<th>End Date</th>
					<th>Billing Deadline</th>
					<th colspan="2" style="text-align:center;">Action</th>
				</tr>
			</thead>
			<tbody>
				@if(count($ongoings) == 0)
				<tr>
					<td colspan="11">No record found!</td>
				</tr>
				@else
				@foreach($ongoings as $ongoing)
				<tr>
					<td>{{ $ongoing->status }}</td>
					<td>{{ $ongoing->cycle_name }}</td>
					<td>{{ $ongoing->scope_name }}</td>
					<td>{{ $ongoing->activity_type }}</td>
					<td>{{ $ongoing->circular_name }}</td>
					<td>{{ $ongoing->planner }}</td>
					<td>{{ date_format(date_create($ongoing->edownload_date),'m/d/Y') }}</td>
					<td>{{ date_format(date_create($ongoing->eimplementation_date),'m/d/Y') }}</td>
					<td>{{ date_format(date_create($ongoing->billing_date),'m/d/Y') }}</td>
					<td class="action">
						{{ HTML::linkAction('ReportController@download','Download', $ongoing->id, array('class' => 'btn btn-success btn-xs')) }}
						<a class="btn btn-info btn-xs" target="_blank" href="{{ URL::action('ReportController@preview', $ongoing->id ) }}">Preview</a>							
					</td>
				</tr>
				@endforeach
				@endif
			</tbody>
		</table> 
	</div>
</div>

<div class="col-lg-12">
	<div class="page-header">
	  	<h1 id="tables">Upcoming Activity</h1>
	</div>

	<div class="table-responsive">	
		<table class="table table-condensed table-hover table-bordered">
			<thead>
				<tr>
					<th>Status</th>
					<th>TOP Cycle</th>
					<th>Scope</th>
					<th>Activity Type</th>
					<th>Activity Title</th>
					<th>PMOG Partner</th>
					<th>Start Date</th>
					<th>End Date</th>
					<th>Billing Deadline</th>
					<th colspan="2" style="text-align:center;">Action</th>
				</tr>
			</thead>
			<tbody>
				@if(count($upcomings) == 0)
				<tr>
					<td colspan="11">No record found!</td>
				</tr>
				@else
				@foreach($upcomings as $upcoming)
				<tr>
					<td>{{ $upcoming->status }}</td>
					<td>{{ $upcoming->cycle_name }}</td>
					<td>{{ $upcoming->scope_name }}</td>
					<td>{{ $upcoming->activity_type }}</td>
					<td>{{ $upcoming->circular_name }}</td>
					<td>{{ $upcoming->planner }}</td>
					<td>{{ date_format(date_create($upcoming->edownload_date),'m/d/Y') }}</td>
					<td>{{ date_format(date_create($upcoming->eimplementation_date),'m/d/Y') }}</td>
					<td>{{ date_format(date_create($upcoming->billing_date),'m/d/Y') }}</td>
					<td class="action">
						{{ HTML::linkAction('ReportController@download','Download', $upcoming->id, array('class' => 'btn btn-success btn-xs')) }}
						<a class="btn btn-info btn-xs" target="_blank" href="{{ URL::action('ReportController@preview', $upcoming->id ) }}">Preview</a>		
					</td>
				</tr>
				@endforeach
				@endif
			</tbody>
		</table> 
	</div></div>

<div class="col-lg-12">
	<div class="page-header">
	  	<h1 id="tables">Last Month Activity</h1>
	</div>

	<div class="table-responsive">	
		<table class="table table-condensed table-hover table-bordered">
			<thead>
				<tr>
					<th>Status</th>
					<th>TOP Cycle</th>
					<th>Scope</th>
					<th>Activity Type</th>
					<th>Activity Title</th>
					<th>PMOG Partner</th>
					<th>Start Date</th>
					<th>End Date</th>
					<th>Billing Deadline</th>
					<th colspan="2" style="text-align:center;">Action</th>
				</tr>
			</thead>
			<tbody>
				@if(count($lastmonths) == 0)
				<tr>
					<td colspan="11">No record found!</td>
				</tr>
				@else
				@foreach($lastmonths as $lastmonth)
				<tr>
					<td>{{ $lastmonth->status }}</td>
					<td>{{ $lastmonth->cycle_name }}</td>
					<td>{{ $lastmonth->scope_name }}</td>
					<td>{{ $lastmonth->activity_type }}</td>
					<td>{{ $lastmonth->circular_name }}</td>
					<td>{{ $lastmonth->planner }}</td>
					<td>{{ date_format(date_create($lastmonth->edownload_date),'m/d/Y') }}</td>
					<td>{{ date_format(date_create($lastmonth->eimplementation_date),'m/d/Y') }}</td>
					<td>{{ date_format(date_create($lastmonth->billing_date),'m/d/Y') }}</td>
					<td class="action">
						{{ HTML::linkAction('ReportController@download','Download', $lastmonth->id, array('class' => 'btn btn-success btn-xs')) }}
						<a class="btn btn-info btn-xs" target="_blank" href="{{ URL::action('ReportController@preview', $lastmonth->id ) }}">Preview</a>		
					</td>
				</tr>
				@endforeach
				@endif
			</tbody>
		</table> 
	</div>
</div>
@stop


