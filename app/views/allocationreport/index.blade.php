@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Allocation Report</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">
	<div class="col-lg-6">
		<div class="form-group">
			<div class="row">
				<div class="col-lg-12">
					<a href="{{ URL::action('AllocationReportController@create') }}" class="btn btn-primary">Add New Report Template</a>
				</div>
			</div>
		</div>
	</div>
</div>
<br>
<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th class="center">Template Name</th>
						<th colspan="4" style="width:25%;" class="dash-action">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($templates) == 0)
					<tr>
						<td colspan="4">No record found!</td>
					</tr>
					@else
					@foreach($templates as $template)
					<tr>
						<td>{{ $template->name }}</td>
						<td class="action">
							{{ Form::open(array('method' => 'DELETE', 'action' => array('AllocationReportController@destroy', $template->id))) }}                       
							{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
							{{ Form::close() }}
						</td>
						<td class="action">
							{{ HTML::linkAction('AllocationReportController@edit','Edit', $template->id, array('class' => 'btn btn-info btn-xs')) }}
						</td>
						<td class="action">
							{{ Form::open(array('action' => array('AllocationReportController@duplicate', $template->id))) }}                       
							{{ Form::submit('Duplicate', array('class'=> 'btn btn-info btn-xs','onclick' => "if(!confirm('Are you sure to duplicate this record?')){return false;};")) }}
							{{ Form::close() }}
						</td>
						<td class="action">
							{{ HTML::linkAction('AllocationReportController@show','Generate Report', $template->id, array('class' => 'btn btn-success btn-xs')) }}
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

@section('page-script')

@stop