@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Users List</h1>
	  	</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">Filters</div>
	<div class="panel-body">
		{{ Form::open(array('method' => 'get','class' => 'bs-component')) }}
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('status', 'Status', array('class' => 'control-label')) }}
						{{ Form::select('status', array('0' => 'ALL STATUSES') + $status , null, array('id' => 'status','class' => 'form-control')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('group', 'TOP Cycle', array('class' => 'control-label')) }}
						{{ Form::select('group', array('0' => 'ALL GROUP') + $groups, null, array('id' => 'cycle','class' => 'form-control')) }}
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('search', 'Full Name', array('class' => 'control-label')) }}
						{{ Form::text('search',Input::old('search'),array('class' => 'form-control', 'placeholder' => 'Full Name')) }}
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
							<div class="search">
								<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
								<a href="{{ URL::action('UsersController@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> User</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		{{ Form::close() }}
	</div>
</div>

<hr>


<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-condensed table-hover table-bordered">
				<thead>
					<tr>
						<th class="center">Full Name</th>
						<th class="center">Group</th>
						<th class="center">Email</th>
						<th class="center">All Status</th>
						<th colspan="2" style="text-align:center;">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($users) == 0)
					<tr>
						<td colspan="5">No record found!</td>
					</tr>
					@else
					@foreach($users as $user)
					<tr>
						<td>{{ $user->getFullname() }}</td>
						<td>
							{{ $user->roles[0]->name }}
						</td>
						<td>{{ $user->email }}</td>
						<td class="center">{{ (($user->active == 1) ? 'Active':'Inactive') }}</td>
						<td class="action">
							{{ Form::open(array('method' => 'DELETE', 'action' => array('ActivityController@destroy', $user->id))) }}                       
							{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
							{{ Form::close() }}
						</td>
						<td class="action">
							{{ HTML::linkAction('UsersController@edit','Edit', $user->id, array('class' => 'btn btn-info btn-xs')) }}
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