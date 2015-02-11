@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>TOP Circulars</h1>
	  	</div>
	</div>
</div>

<table id="tableDemo" class="table table-striped table-hover">
	<tr>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Action</th>
	</tr>
</table>  

@stop

@section('page-script')


$('#tableDemo').ajax_table({
	columns: [
		{ type: "text", id: "fname", placeholder: "Enter First Name" },
    	{ type: "text", id: "lname", placeholder: "Enter Last Name" },
	]
});

@stop


