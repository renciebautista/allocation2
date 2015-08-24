@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Launch SKU List</h1>
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
		  	{{ HTML::linkAction('LaunchSkuController@upload','Import SKU/s',null, array('class' => 'btn btn-info')) }}
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
						<th class="center">Sap Code</th>
						<th class="center">Description</th>
						
						<th class="center">Division</th>
						<th class="center">Category</th>
						<th class="center">Brand</th>
						<th class="center">CPG Description</th>
						<th colspan="2" class="dash-action">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($launchsku) == 0)
					<tr>
						<td colspan="8">No record found!</td>
					</tr>
					@else
					@foreach($launchsku as $launch)
					<tr>
						<td>{{ $launch->sku_code }}</td>
						<td>{{ $launch->sku_desc }}</td>
						<td>{{ $launch->division_desc }}</td>
						<td>{{ $launch->category_desc }}</td>
						<td>{{ $launch->brand_desc }}</td>
						<td>{{ $launch->cpg_desc }}</td>
						<td class="action">
							{{ Form::open(array('method' => 'DELETE', 'action' => array('LaunchSkuController@destroy', $launch->sku_code))) }}                       
							{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
							{{ Form::close() }}
						</td>
						<td class="action">
							{{ HTML::linkAction('LaunchSkuController@access','Proponent Access', $launch->sku_code, array('class' => 'btn btn-info btn-xs')) }}
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
