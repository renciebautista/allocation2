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

<div class="row">
  <div class="col-lg-12">
    {{ Form::open(array('method' => 'get','class' => 'form-inline')) }}
      <div class="form-group">
        <label class="sr-only" for="s">Search</label>
        {{ Form::text('s',Input::old('s'),array('class' => 'form-control', 'placeholder' => 'Search')) }}
        </div>
        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
        <a href="{{ URL::action('SobfilterController@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> SOB Filter</a>
        <a href="{{ URL::action('SobfilterController@export') }}" class="btn btn-info"><i class="fa fa-download"></i> Export SOB Filter</a>
        <a href="{{ URL::action('SobfilterController@import') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import SOB Filter</a>
    {{ Form::close() }}
  </div>
</div>


<p class="pull-right"><b>{{ count($filters)}} record/s found.</b></p>

<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th >Group</th>
						<th >Area</th>
						<th >Sold To</th>
						<th >SOB Group</th>
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
						<td>{{ $filter->getGroup() }}</td>
						<td>{{ $filter->getArea() }}</td>
						<td>{{ $filter->getCustomer() }}</td>
						<td>{{ $filter->sobGroup->sobgroup }}</td>
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