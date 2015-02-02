@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>New Scheme</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="row">
	{{ Form::open(array('action' => array('SchemeController@store', $id) ,'class' => 'bs-component')) }}
	{{ Form::hidden('activity_id', $id) }}
	<div class="col-lg-12">
		<div class="form-group">
			<div class="row">
				<div class="col-lg-6">
					{{ Form::label('name', 'Scheme Name', array('class' => 'control-label')) }}
					{{ Form::text('name','',array('class' => 'form-control', 'placeholder' => 'Scheme Name')) }}
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-12">
		<div class="form-group">
			<div class="row">
				<div class="col-lg-6">
					{{ Form::label('quantity', 'Allocation Quantity', array('class' => 'control-label')) }}
					{{ Form::text('quantity','',array('class' => 'form-control', 'placeholder' => 'Allocation Quantity')) }}
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



@stop

@section('page-script')

$('select#skus').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});

$('#quantity').inputNumber();
@stop


	