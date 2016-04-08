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
        <a href="{{ URL::action('SobGroupController@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> SOB Group</a>
    {{ Form::close() }}
  </div>
</div>


<p class="pull-right"><b>{{ count($groups)}} record/s found.</b></p>

<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th >Group</th>
						<th colspan="2" style="text-align:center; width:10%;">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($groups) == 0)
					<tr>
						<td colspan="5">No record found!</td>
					</tr>
					@else
					@foreach($groups as $group)
					<tr>
						<td>{{ $group->sobgroup }}</td>
						<td class="action">
							{{ HTML::linkAction('SobGroupController@edit','Edit', $group->id, array('class' => 'btn btn-info btn-xs')) }}
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