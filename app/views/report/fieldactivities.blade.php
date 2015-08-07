@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>All Released Activities Report</h1>
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
						{{ Form::label('cy', 'TOP Cycle', array('class' => 'control-label')) }}
						{{ Form::select('cy[]', $cycles, null, array('id' => 'cy','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
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
		</div>
		<div class="row">
			
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
		</div>
		<div class="row">
			
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('pr', 'Proponent', array('class' => 'control-label')) }}
						{{ Form::select('pr[]', $proponents, null, array('id' => 'pr','class' => 'form-control', 'multiple' => 'multiple')) }}
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

<p><b>{{ count($activities)}} record/s found.</b></p>

<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th class="center">ID</th>
						<th class="center">All Status</th>
						<th class="center">TOP Cycle</th>
						<th class="center">Scope</th>
						<th class="center">Activity Type</th>
						<th class="center">Activity Title</th>
						<th class="center">Proponent</th>
						<th class="center">PMOG Partner</th>
						<th class="center">Start Date</th>
						<th class="center">End Date</th>
						<th class="center">Billing Deadline</th>
						<th class="center">With PDF Attachment</th>
						<th colspan="2" style="text-align:center;">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($activities) == 0)
					<tr>
						<td colspan="13">No record found!</td>
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
						<td>{{ $activity->proponent }}</td>
						<td>{{ $activity->planner }}</td>
						<td>{{ date_format(date_create($activity->eimplementation_date),'m/d/Y') }}</td>
						<td>{{ date_format(date_create($activity->end_date),'m/d/Y') }}</td>
						@if($activity->billing_date != "")
						<td>{{ date_format(date_create($activity->billing_date),'m/d/Y') }}</td>
						@else
						<td></td>
						@endif
						<td>{{ ($activity->pdf) ? "YES":"NO" }}</td>
						<td class="action">
							{{ HTML::linkAction('ReportController@download','Download', $activity->id, array('class' => 'btn btn-success btn-xs')) }}
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

@include('javascript.report.activities')

@stop

@section('page-script')

@stop