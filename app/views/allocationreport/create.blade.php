@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>New Allocation Report Template</h1>
		</div>
	</div>
</div>

@include('partials.notification')

{{ Form::open(array('action' => 'AllocationReportController@store','class' => 'bs-component')) }}
<div class="panel panel-default">
	<div class="panel-heading">General Filters</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-lg-12">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							{{ Form::label('name', 'Report Template Name', array('class' => 'control-label')) }}
							{{ Form::text('name','',array('id' => 'name', 'class' => 'form-control', 'placeholder' => 'Report Template Name' ,'maxlength' => 80)) }}
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
						{{ Form::label('scope', 'Scope', array('class' => 'control-label')) }}
						{{ Form::select('scope[]', $scopes, null, array('id' => 'scope','class' => 'form-control', 'multiple' => 'multiple')) }}
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
						{{ Form::label('pro', 'Proponent', array('class' => 'control-label')) }}
						{{ Form::select('pro[]', $proponents, null, array('id' => 'pro','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('planner', 'PMOG Planner', array('class' => 'control-label')) }}
						{{ Form::select('planner[]', $planners, null, array('id' => 'planner','class' => 'form-control', 'multiple' => 'multiple')) }}
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
						{{ Form::label('app', 'Activity Approver', array('class' => 'control-label')) }}
						{{ Form::select('app[]', $approvers, null, array('id' => 'app','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('type', 'Activity Type', array('class' => 'control-label')) }}
						{{ Form::select('type[]', $activitytypes, null, array('id' => 'type','class' => 'form-control', 'multiple' => 'multiple')) }}
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
							{{ Form::label('division', 'Division', array('class' => 'control-label')) }}
							{{ Form::select('division[]',  $divisions, null, array('id' => 'division', 'class' => 'form-control multiselect' ,'multiple' => 'multiple' ,'data-placeholder' => 'SELECT DIVISION')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div id="multiselect" class="col-lg-12">
							{{ Form::label('category', 'Category', array('class' => 'control-label')) }}
							{{ Form::select('category[]',  $categories, null, array('id' => 'category', 'class' => 'form-control multiselect' ,'multiple' => 'multiple' ,'data-placeholder' => 'SELECT CATEGORY')) }}
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
							{{ Form::label('brand', 'Brand', array('class' => 'control-label')) }}
							{{ Form::select('brand[]',  $brands, null, array('id' => 'brand', 'class' => 'form-control multiselect' ,'multiple' => 'multiple' ,'data-placeholder' => 'SELECT BRAND')) }}
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>


<div class="filters panel panel-default">
	<div class="panel-heading">Show Fields</div>
	<div class="panel-body min-height">
		@foreach($schemefields as $field)
		<div class="form-group">
			<div class="checkbox">
				<label>
					{{ Form::checkbox('field[]', $field->id,true) }} {{ $field->desc_name}}
				</label>
			</div>
		</div>
		@endforeach
	</div>
</div>

<div class="form-group">
	{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
	{{ HTML::linkAction('AllocationReportController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
</div>
{{ Form::close() }}
@include('javascript.allocationreport.create')

@stop

@section('page-script')


@stop