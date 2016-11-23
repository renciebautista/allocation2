
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Edit {{ $role->name }} Permissions</h1>
		</div>
	</div>
</div>

@include('partials.notification')

{{ Form::open(array('action' => array('GroupController@updatepermissions', $role->id), 'class' => 'bs-component')) }}
<div class="form-group">
			{{ Form::submit('Update', array('class' => 'btn btn-primary')) }}
			{{ HTML::linkAction('GroupController@index', 'Back', array(), array('class' => 'btn btn-default')) }}
		</div>
<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th></th>
						<th>Permission</th>
					</tr>
				</thead>
				<tbody>
					@if(count($permissions) == 0)
					<tr>
						<td colspan="2">No record found!</td>
					</tr>
					@else
					@foreach($permissions as $permission)
					<tr>
						<td>
							{{ Form::checkbox('permission[]', $permission->id,(in_array($permission->id,$role_permissions) ? true: false)) }}
						</td>
						<td>{{ $permission->display_name }}</td>
					</tr>
					@endforeach
					@endif
				</tbody>
			</table> 
		</div>
	</div>
</div>
{{ Form::close() }}	

@stop


