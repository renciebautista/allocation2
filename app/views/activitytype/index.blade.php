@extends('layouts.layout')

@section('content')



<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Activity Type List</h1>
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
		  	<a href="{{ URL::action('ActivityTypeController@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Activity Type</a>
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
						<th class="center">Activity Type Name</th>
						<th class="center">With Scheme</th>
						<th class="center">Unit of Measurement</th>
						<th colspan="3" style="text-align:center;">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($activitytypes) == 0)
					<tr>
						<td colspan="6">No record found!</td>
					</tr>
					@else
					@foreach($activitytypes as $type)
					<tr>
						<td>{{ $type->activity_type }}</td>
						<td class="center">{{ ($type->with_scheme) ? 'TRUE' : 'FALSE' }}</td>
						<td class="center">{{ $type->uom }}</td>
						<td class="action">
							{{ HTML::linkAction('NetworkController@index','Manage Networks', $type->id, array('class' => 'btn btn-primary btn-xs')) }}
						</td>
						<td class="action">
							{{ Form::open(array('method' => 'DELETE', 'action' => array('ActivityTypeController@destroy', $type->id))) }}                       
							{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
							{{ Form::close() }}
						</td>
						<td class="action">
							{{ HTML::linkAction('ActivityTypeController@edit','Edit', $type->id, array('class' => 'btn btn-info btn-xs')) }}
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