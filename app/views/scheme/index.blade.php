@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Scheme List</h1>
	  	</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		{{ Form::open(array('method' => 'get','class' => 'form-inline')) }}
		 	<div class="form-group">
		 		<label class="sr-only" for="s">Search</label>
		 		{{ Form::text('s',Input::old('s'),array('class' => 'form-control', 'placeholder' => 'Search')) }}
		  	</div>
		  	<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>

		  	<a href="{{ URL::action('SchemeController@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Scheme</a>
		  	<a href="{{ URL::action('ActivityController@index') }}" class="btn btn-default">Back</a>
		{{ Form::close() }}
	</div>
</div>


<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Scheme Name</th>
						<th colspan="3" style="text-align:center;">Action</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Sample Scheme</td>
						<td class="action">
							{{ Form::open(array('method' => 'DELETE', 'action' => array('SchemeController@destroy', 1))) }}                       
							{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
							{{ Form::close() }}
						</td>
						<td class="action">
							{{ HTML::linkAction('SchemeController@show','View', 1, array('class' => 'btn btn-info btn-xs')) }}
						</td>
					</tr>
				</tbody>
			</table> 
		</div>
	</div>
</div>

@stop