@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>{{ $template->name }}</h1>
		</div>
	</div>
</div>

@include('partials.notification')

{{ Form::open(array('action' => array('AllocationReportController@download', $template->id) ,'class' => 'bs-component')) }}
{{ Form::hidden('temp_id', $template->id) }}
<div class="panel panel-default">
	<div class="panel-heading">Cycle Filter</div>
	<div class="panel-body">

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
			
		</div>

	</div>
</div>

<div class="form-group">
	{{ Form::submit('Generate Report', array('class' => 'btn btn-primary')) }}
	{{ HTML::linkAction('AllocationReportController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
</div>
{{ Form::close() }}

@stop

@section('page-script')
$('#cy').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});


@stop