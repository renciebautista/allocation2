@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>SOB Filter List</h1>
	  	</div>
	</div>
</div>

@include('partials.notification')


<p><b>{{ count($filters)}} record/s found.</b></p>

<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th class="center">Group</th>
						<th class="center">Area</th>
						<th class="center">Sold To</th>
						<th colspan="2" style="text-align:center; width:10%;">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($filters) == 0)
					<tr>
						<td colspan="5">No record found!</td>
					</tr>
					@else
					@foreach($filters as $filter)
					<tr>
						<td>{{ $filter->group_code }}</td>
						<td>{{ $filter->area_code }}</td>
						<td>{{ $filter->customer_code }}</td>
						<td class="action">
							{{ HTML::linkAction('SobfilterController@edit','Edit', $filter->id, array('class' => 'btn btn-info btn-xs')) }}
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