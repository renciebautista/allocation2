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
			{{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('SchemeController@index', 'Back', $id, array('class' => 'btn btn-default')) }}
		</div>
	</div>
	{{ Form::close() }}
</div>

@include('partials.notification')

@stop

@section('page-script')



@stop


	