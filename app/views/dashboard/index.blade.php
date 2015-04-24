@extends('layouts.layout')

@section('content')

@include('partials.notification')

<div class="col-lg-12">
	<div class="page-header">
	  	<h1 id="tables">Ongoing Activities</h1>
	</div>

	<div class="table-responsive">	
		<table class="table table-condensed table-hover table-bordered">
			<thead>
				<tr>
					<th>Activity Title</th>
					<th>Activity Type</th>
					<th>TOP Cycle</th>
					<th colspan="2" style="text-align:center;">Action</th>
				</tr>
			</thead>
			<tbody>
				@if(count($ongoings) == 0)
				<tr>
					<td colspan="5">No record found!</td>
				</tr>
				@else
				@foreach($ongoings as $ongoing)
				<tr>
					<td>{{ $ongoing->circular_name }}</td>
					<td>{{ $ongoing->activity_type }}</td>
					<td>{{ $ongoing->cycle_name }}</td>
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
					<th>Activity Title</th>
					<th>Activity Type</th>
					<th>TOP Cycle</th>
					<th style="text-align:center;">Action</th>
				</tr>
			</thead>
			<tbody>
				@if(count($upcomings) == 0)
				<tr>
					<td colspan="4">No record found!</td>
				</tr>
				@else
				@foreach($upcomings as $upcoming)
				<tr>
					<td>{{ $upcoming->circular_name }}</td>
					<td>{{ $upcoming->activity_type }}</td>
					<td>{{ $upcoming->cycle_name }}</td>
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
					<th>Activity Title</th>
					<th>Activity Type</th>
					<th>TOP Cycle</th>
					<th style="text-align:center;">Action</th>
				</tr>
			</thead>
			<tbody>
				@if(count($lastmonths) == 0)
				<tr>
					<td colspan="4">No record found!</td>
				</tr>
				@else
				@foreach($lastmonths as $lastmonth)
				<tr>
					<td>{{ $lastmonth->circular_name }}</td>
					<td>{{ $lastmonth->activity_type }}</td>
					<td>{{ $lastmonth->cycle_name }}</td>
					<td class="action">
						{{ HTML::linkAction('ReportController@download','Download', $lastmonth->id, array('class' => 'btn btn-success btn-xs')) }}
						<a class="btn btn-info btn-xs" target="_blank" href="{{ URL::action('ReportController@preview', $upcoming->id ) }}">Preview</a>		
					</td>
				</tr>
				@endforeach
				@endif
			</tbody>
		</table> 
	</div>
</div>
@stop


