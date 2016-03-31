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
{{ Form::open(array('action' => 'CycleController@rerun','class' => 'bs-component')) }}
<div class="margin-bottom pull-left">
	<button id="pdf" type="submit" name="submit" class="btn btn-info" value="pdf"> Create PDF</button>
	<button id="doc" type="submit" name="submit" class="btn btn-info" value="doc"> Create Word Doc</button>
	<button id="release" type="submit" name="submit" class="btn btn-info" value="release"> Release</button>
	<button id="sob" type="submit" name="submit" class="btn btn-info" value="sob"> Generate PO No</button>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th></th>
						<th class="center">Cycle Name</th>
						<th class="center">Start Date</th>
						<th class="center">End Date</th>
						<th class="center">Circular Submission Deadline</th>
						<th class="center">Approval Deadline</th>
						<th class="center">PDF and Attachment Creation Date</th>
						<th class="center">Release Date</th>
						<th class="center">Implementation Date</th>
						<th class="center">SOB Deadline</th>
						<th class="center">Emergency</th>
						<th colspan="2" class="dash-action">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($cycles) == 0)
					<tr>
						<td colspan="8">No record found!</td>
					</tr>
					@else
					@foreach($cycles as $cycle)
					<tr>
						<td>{{ Form::checkbox('cycle[]', $cycle->id) }}</td>
						<td>{{ $cycle->cycle_name }}</td>
						<td class="center">{{ date_format(date_create($cycle->start_date),'m/d/Y')  }}</td>
						<td class="center">{{ date_format(date_create($cycle->end_date),'m/d/Y')  }}</td>
						<td class="center">{{ date_format(date_create($cycle->submission_deadline),'m/d/Y')  }}</td>
						<td class="center">{{ date_format(date_create($cycle->approval_deadline),'m/d/Y')  }}</td>
						<td class="center">{{ date_format(date_create($cycle->pdf_deadline),'m/d/Y')  }}</td>
						<td class="center">{{ date_format(date_create($cycle->release_date),'m/d/Y')  }}</td>
						<td class="center">{{ date_format(date_create($cycle->implemintation_date),'m/d/Y')  }}</td>
						<td class="center">
							@if(!empty($cycle->sob_deadline))
							{{ date_format(date_create($cycle->sob_deadline),'m/d/Y')  }}
							@endif
						</td>
						<td class="center">{{ ($cycle->emergency) ? '<i class="fa fa-check"></i>' : '' }}</td>
						<td class="action">
							
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
{{ Form::close() }}
@stop


