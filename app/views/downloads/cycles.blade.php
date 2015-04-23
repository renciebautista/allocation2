@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Cycles List</h1>
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
						{{ Form::label('search', 'Search', array('class' => 'control-label')) }}
						{{ Form::text('search',Input::old('search'),array('class' => 'form-control', 'placeholder' => 'Cycle Name')) }}
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
						<th>TOP Cycle</th>
						<th style="text-align:center;">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($cycles) == 0)
					<tr>
						<td colspan="2">No record found!</td>
					</tr>
					@else
					@foreach($cycles as $cycle)
					<tr>
						<td>{{ $cycle->cycle_name }}</td>
						<td class="action">
							{{ HTML::linkAction('DownloadsController@download','Download Zip', $cycle->id, array('class' => 'btn btn-success btn-xs')) }}						
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