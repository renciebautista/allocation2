@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>TOP Calendar</h1>
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
						<th class="center">Start Date</th>
						<th class="center">End Date</th>
						<th class="center">Circular Submission Deadline</th>
						<th class="center">Approval Deadline</th>
						<th class="center">PDF Creation Date</th>
						<th class="center">Release Date</th>
						<th class="center">Implementation Date</th>
						<th class="center">Emergency</th>
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
						<td class="center">{{ date_format(date_create($cycle->start_date),'m/d/Y')  }}</td>
						<td class="center">{{ date_format(date_create($cycle->end_date),'m/d/Y')  }}</td>
						<td class="center">{{ date_format(date_create($cycle->submission_deadline),'m/d/Y')  }}</td>
						<td class="center">{{ date_format(date_create($cycle->approval_deadline),'m/d/Y')  }}</td>
						<td class="center">{{ date_format(date_create($cycle->pdf_deadline),'m/d/Y')  }}</td>
						<td class="center">{{ date_format(date_create($cycle->release_date),'m/d/Y')  }}</td>
						<td class="center">{{ date_format(date_create($cycle->implemintation_date),'m/d/Y')  }}</td>
						<td class="center">
							@if($cycle->emergency) 
							TRUE
							@else
							FALSE
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

@stop