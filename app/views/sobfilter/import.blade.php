@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Import SOB Filters</h1>
	  	</div>
	</div>
</div>

@include('partials.notification')

{{ Form::open(array('action' => array('SobfilterController@upload'),  'class' => 'bs-component','files'=>true)) }}
	<div class="row">
		<div class="col-lg-6">
		  	<div class="form-group">
		    	{{ Form::file('file','',array('id'=>'','class'=>'')) }}
		  	</div>
		  	{{ Form::submit('Upload', array('class' => 'btn btn-primary')) }}
		  	{{ HTML::linkAction('SobfilterController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
	  	</div>
  	</div>
{{ Form::close() }}

@stop

@section('page-script')
@stop
