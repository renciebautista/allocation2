@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Activity List</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="panel panel-default">
	<div class="panel-heading">Filters</div>
	<div class="panel-body">
		{{ Form::open(array('method' => 'get','class' => 'bs-component')) }}
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('st', 'Status', array('class' => 'control-label')) }}
						{{ Form::select('st[]', $statuses, null, array('id' => 'st','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('cy', 'TOP Cycle', array('class' => 'control-label')) }}
						{{ Form::select('cy[]', $cycles, null, array('id' => 'cy','class' => 'form-control', 'multiple' => 'multiple')) }}
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
						{{ Form::label('sc', 'Scope', array('class' => 'control-label')) }}
						{{ Form::select('sc[]', $scopes, null, array('id' => 'sc','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('ty', 'Activity Type', array('class' => 'control-label')) }}
						{{ Form::select('ty[]', $types, null, array('id' => 'ty','class' => 'form-control', 'multiple' => 'multiple')) }}
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
						{{ Form::label('pm', 'PMOG Partner', array('class' => 'control-label')) }}
						{{ Form::select('pm[]', $planners, null, array('id' => 'pm','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('title', 'Activity Title', array('class' => 'control-label')) }}
						{{ Form::text('title',Input::old('title'),array('class' => 'form-control', 'placeholder' => 'Activity Title')) }}
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
								<a href="{{ URL::action('ActivityController@create') }}" class="btn btn-primary">New Activity</a>
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

<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
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
						<th colspan="3" class="dash-action">Action</th>
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
						<td>{{ $activity->status }}</td>
						<td>{{ $activity->cycle_name }}</td>
						<td>{{ $activity->scope_name }}</td>
						<td>{{ $activity->activity_type }}</td>
						<td>{{ $activity->circular_name }}</td>
						<td>{{ $activity->planner }}</td>
						<td>{{ date_format(date_create($activity->eimplementation_date),'m/d/Y') }}</td>
						<td>{{ date_format(date_create($activity->end_date),'m/d/Y') }}</td>
						<td>{{ date_format(date_create($activity->billing_date),'m/d/Y') }}</td>
						<td class="action">
							{{ HTML::linkAction('ActivityController@edit','View', $activity->id, array('class' => 'btn btn-success btn-xs')) }}
						</td>
						<td class="action">
							{{ Form::open(array('method' => 'POST', 'action' => array('ActivityController@duplicate', $activity->id), 'class' => 'disable-button')) }}                       
							{{ Form::submit('Duplicate', array('class'=> 'btn btn-primary btn-xs','onclick' => "if(!confirm('Are you sure to duplicate this record?')){return false;};")) }}
							{{ Form::close() }}
						</td>
						<td class="action">
							@if($activity->status_id < 4)
							{{ Form::open(array('method' => 'DELETE', 'action' => array('ActivityController@destroy', $activity->id), 'class' => 'disable-button')) }}                       
							{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
							{{ Form::close() }}
							@else
							@if($activity->status_id == 9)
								{{ HTML::linkAction('ReportController@download','Download', $activity->id, array('class' => 'btn btn-success btn-xs')) }}
							@else
								<button class="btn btn-danger btn-xs disabled">Delete</button>
							@endif
							
							@endif
						</td>
						
					</tr>
					@endforeach
					@endif
				</tbody>
			</table> 
		</div>
	</div>
</div>

@include('javascript.activity.index')

@stop

@section('page-script')

@stop