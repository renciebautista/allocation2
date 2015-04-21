@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Downloaded Activity List</h1>
	  	</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">Filters</div>
	<div class="panel-body">
		{{ Form::open(array('method' => 'get','class' => 'bs-component')) }}
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('status', 'Status', array('class' => 'control-label')) }}
						{{ Form::select('status', array('0' => 'ALL STATUSES') + $statuses, null, array('id' => 'status','class' => 'form-control')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('cycle', 'TOP Cycle', array('class' => 'control-label')) }}
						{{ Form::select('cycle', array('0' => 'ALL CYCLES') + $cycles, null, array('id' => 'cycle','class' => 'form-control')) }}
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
						{{ Form::label('scope', 'Scope', array('class' => 'control-label')) }}
						{{ Form::select('scope', array('0' => 'ALL SCOPES') + $scopes, null, array('id' => 'scope','class' => 'form-control')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('type', 'Activity Type', array('class' => 'control-label')) }}
						{{ Form::select('type', array('0' => 'ALL ACTIVITY TYPES') + $types, null, array('id' => 'type','class' => 'form-control')) }}
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
						{{ Form::label('proponent', 'Proponent', array('class' => 'control-label')) }}
						{{ Form::select('proponent', array('0' => 'ALL PROPONENTS') + $proponents, null, array('id' => 'proponent','class' => 'form-control')) }}
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
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Status</th>
						<th>TOP Cycle</th>
						<th>Scope</th>
						<th>Activity Type</th>
						<th>Activity Title</th>
						<th>Proponent</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Billing Deadline</th>
						<th colspan="2" style="text-align:center;">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($activities) == 0)
					<tr>
						<td colspan="11">No record found!</td>
					</tr>
					@else
					@foreach($activities as $activity)
					<tr>
						<td>{{ $activity->status }}</td>
						<td>{{ $activity->cycle_name }}</td>
						<td>{{ $activity->scope_name }}</td>
						<td>{{ $activity->activity_type }}</td>
						<td>{{ $activity->circular_name }}</td>
						<td>{{ $activity->proponent }}</td>
						<td>{{ date_format(date_create($activity->edownload_date),'m/d/Y') }}</td>
						<td>{{ date_format(date_create($activity->eimplementation_date),'m/d/Y') }}</td>
						<td>{{ date_format(date_create($activity->billing_date),'m/d/Y') }}</td>
						<td class="action">
							{{ HTML::linkAction('ActivityController@edit','View', $activity->id, array('class' => 'btn btn-success btn-xs')) }}
						</td>
						<td class="action">
							<a class="btn btn-info btn-xs" target="_blank" href="{{ URL::action('ReportController@preview', $activity->id ) }}">Preview</a>							
						</td>
					</tr>
					@endforeach
					@endif
				</tbody>
			</table> 
		</div>
	</div>
</div>

@stop