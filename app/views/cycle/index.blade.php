@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Cycle List</h1>
	  	</div>
	</div>
</div>

@include('partials.notification')

<div class="row">
	<div class="col-lg-12">
		{{ Form::open(array('method' => 'get','class' => 'form-inline')) }}
		 	<div class="form-group">
		 		<label class="sr-only" for="s">Search</label>
		 		{{ Form::text('s',Input::old('s'),array('class' => 'form-control', 'placeholder' => 'Search')) }}
		  	</div>
		  	<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
		  	<a href="{{ URL::action('CycleController@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Cycle</a>
		{{ Form::close() }}
	</div>
</div>
<br>
<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th class="center">Cycle Name</th>
						<th class="center">Cycle Month-Year</th>
						<th class="center">Emergency</th>
						<th class="center">Circular Submission Deadline</th>
						<th colspan="2" class="dash-action">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($cycles) == 0)
					<tr>
						<td colspan="6">No record found!</td>
					</tr>
					@else
					@foreach($cycles as $cycle)
					<tr>
						<td>{{ $cycle->cycle_name }}</td>
						<td class="center">{{ $cycle->month_year }}</td>
						<td class="center">
							@if($cycle->emergency) 
							TRUE
							@else
							FALSE
							@endif
						</td>
						<td class="center">{{ date_format(date_create($cycle->submission_deadline),'m/d/Y')  }}</td>
						<td class="action">
							{{ Form::open(array('method' => 'DELETE', 'action' => array('CycleController@destroy', $cycle->id))) }}                       
							{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
							{{ Form::close() }}
						</td>
						<td class="action">
							{{ HTML::linkAction('CycleController@edit','Edit', $cycle->id, array('class' => 'btn btn-info btn-xs')) }}
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