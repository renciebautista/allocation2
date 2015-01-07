@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>New Scheme</h1>
		</div>
	</div>
</div>

<div class="row">
	{{ Form::open(array('route' => 'activity.store','class' => 'bs-component')) }}
	<div class="col-lg-12">
		<div class="form-group">
			<div class="row">
				<div class="col-lg-6">
					{{ Form::label('scheme_qty', 'Allocation Quantity', array('class' => 'control-label')) }}
					{{ Form::text('scheme_qty','',array('class' => 'form-control', 'placeholder' => 'Allocation Quantity')) }}
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-12">
		<div class="form-group">
			<div class="row">
				<div class="col-lg-6 ">
					{{ Form::label('skus', 'SKU/s Involved', array('class' => 'control-label')) }}
					{{ Form::select('skus[]', $skus, null, array('id' => 'skus', 'class' => 'form-control', 'multiple' => 'multiple')) }}
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-12">
		<div class="form-group">
			{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('SchemeController@index', 'Back', $id, array('class' => 'btn btn-default')) }}
		</div>
	</div>
	{{ Form::close() }}
</div>

@include('partials.notification')

@stop

@section('page-script')

$('select#skus').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});
@stop


	