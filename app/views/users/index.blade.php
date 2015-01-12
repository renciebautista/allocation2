@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>User List</h1>
	  	</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		{{ Form::open(array('method' => 'get','class' => 'form-inline')) }}
		  	<div class="filter">
		  		<label class="radio-inline">
		  			<input type="radio" name="status" value="1" <?php echo Helper::oldRadio('status', '1', true); ?>> Active
				</label>
	  	  		<label class="radio-inline">
	  	  			<input type="radio" name="status" value="2" <?php echo Helper::oldRadio('status', '2'); ?>> In Active
				</label>
				<label class="radio-inline">
			  		<input type="radio" name="status" value="3" <?php echo Helper::oldRadio('status', '3'); ?>> All
				</label>
			</div>
		 	<div class="form-group">
		 		<label class="sr-only" for="s">Search</label>
		 		{{ Form::text('s',Input::old('s'),array('class' => 'form-control', 'placeholder' => 'Search')) }}
		  	</div>
		  	<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
		  	<a href="{{ URL::action('UsersController@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> User</a>
		{{ Form::close() }}
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Full Name</th>
						<th>Group</th>
						<th>Status</th>
						<th colspan="2" style="text-align:center;">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($users) == 0)
					<tr>
						<td colspan="4">No record found!</td>
					</tr>
					@else
					@foreach($users as $user)
					<tr>
						<td>{{ $user->first_name .' ' .$user->middle_initial .' ' .$user->last_name }}</td>
						<td>
							
						</td>
						<td>{{ (($user->active == 1) ? 'Active':'In Active') }}</td>
						<td class="action">
							{{ Form::open(array('method' => 'DELETE', 'action' => array('ActivityController@destroy', $user->id))) }}                       
							{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
							{{ Form::close() }}
						</td>
						<td class="action">
							{{ HTML::linkAction('ActivityController@show','View', $user->id, array('class' => 'btn btn-info btn-xs')) }}
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