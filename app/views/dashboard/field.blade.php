@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Activity List</h1>
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
			<table class="table table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th>Activity Title</th>
						<th>Activity Type</th>
						<th>TOP Cycle</th>
						<th colspan="2" class="dash-action">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($activities) == 0)
					<tr>
						<td colspan="4">No record found!</td>
					</tr>
					@else
					@foreach($activities as $activity)
					<tr>
						<td>{{ $activity->circular_name }}</td>
						<td>{{ $activity->activity_type }}</td>
						<td>{{ $activity->cycle_name }}</td>
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

@include('javascript.dashboard.field')

@stop

@section('page-script')

@stop