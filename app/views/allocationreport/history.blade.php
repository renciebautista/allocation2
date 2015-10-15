@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Requested Reports</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="form-group">
	{{ HTML::linkAction('AllocationReportController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
</div>
<br>
<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th>Report Name</th>
						<th style="width:15%;">Date Created</th>
						<th  class="dash-action">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($files) == 0)
					<tr>
						<td colspan="3">No record found!</td>
					</tr>
					@else
					@foreach($files as $file)
					<tr>
						
						<td>{{ $file->template_name }}</td>
						<td>{{ date_format(date_create($file->created_at),'M j, Y H:i:s') }}</td>
						
						<td class="action">
							{{ HTML::linkAction('AllocationReportController@download','Download', $file->token, array('class' => 'btn btn-success btn-xs')) }}
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