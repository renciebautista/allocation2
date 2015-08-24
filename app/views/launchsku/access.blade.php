@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Allow access on SKU</h1>
			<h2>{{ $sku->sku_code }} - {{ $sku->sku_desc }}</h2>
	  	</div>
	</div>
</div>

@include('partials.notification')
{{ Form::open(array('route' => array('launchskus.update', $sku->sku_code), 'method' => 'PUT', 'class' => 'bs-component')) }}
<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th></th>
						<th class="center">Proponent</th>
					</tr>
				</thead>
				<tbody>
					@if(count($proponents) == 0)
					<tr>
						<td colspan="2">No record found!</td>
					</tr>
					@else
					@foreach($proponents as $proponent)
					<tr>
						<td class="center" style="width:30px;">{{ Form::checkbox('proponent[]', $proponent->user_id,(in_array($proponent->user_id,$selecteduser) ? true : false) ) }}</td>
						<td>{{ $proponent->getFullname() }}</td>
					</tr>
					@endforeach
					@endif
				</tbody>
			</table> 
		</div>
	</div>
</div>
<div class="form-group">
	{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
	{{ HTML::linkAction('LaunchSkuController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
</div>
{{ Form::close() }}
@stop

@section('page-script')
@stop
