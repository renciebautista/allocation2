@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Sub Tasks List</h1>
	  	</div>
	</div>
</div>

@include('partials.notification')
<div class="panel panel-default">
	<div class="panel-heading">Filters</div>
	<div class="panel-body">
		{{ Form::open(array('method' => 'get','class' => 'bs-component')) }}
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('task', 'Task', array('class' => 'control-label')) }}
						{{ Form::select('task', array('0' => 'All Task') + $tasks , null, array('id' => 'status','class' => 'form-control')) }}
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
						{{ Form::label('search', 'Keyword', array('class' => 'control-label')) }}
						{{ Form::text('search',Input::old('search'),array('class' => 'form-control', 'placeholder' => 'Keyword')) }}
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
								
								<a href="{{ URL::action('SubtasksController@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Sub Task</a>
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


<p class="pull-right"><b>{{ count($subtasks)}} record/s found.</b></p>
<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Tasks</th>
						<th>Sub Task</th>
						<th colspan="2" style="text-align:center;">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($subtasks) == 0)
					<tr>
						<td colspan="3">No record found!</td>
					</tr>
					@else
					@foreach($subtasks as $task)
					<tr>
						<td>{{ $task->task }}</td>
						<td>{{ $task->sub_task }}</td>
						<td class="action">
							{{ HTML::linkAction('SubtasksController@edit','Edit', $task->id, array('class' => 'btn btn-info btn-xs')) }}
						</td>
						<td class="action">
							{{ Form::open(array('method' => 'DELETE', 'action' => array('SubtasksController@destroy', $task->id))) }}                       
							{{ Form::submit('Delete', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to delete this record?')){return false;};")) }}
							{{ Form::close() }}
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