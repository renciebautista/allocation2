@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
	  	<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Downloaded Activity List</h1>
	  	</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		{{ Form::open(array('method' => 'get','class' => 'form-inline')) }}
		  	<div class="filter">
		  		<label class="radio-inline">
		  			<input type="radio" name="status" value="1" <?php echo Helper::oldRadio('status', '1', true); ?>> For Approval
				</label>
	  	  		<label class="radio-inline">
	  	  			<input type="radio" name="status" value="2" <?php echo Helper::oldRadio('status', '2'); ?>> Approved
				</label>
				<label class="radio-inline">
			  		<input type="radio" name="status" value="3" <?php echo Helper::oldRadio('status', '3'); ?>> Denied
				</label>
				<label class="radio-inline">
			  		<input type="radio" name="status" value="4" <?php echo Helper::oldRadio('status', '4'); ?>> All
				</label>
			</div>
		 	<div class="form-group">
		 		<label class="sr-only" for="s">Search</label>
		 		{{ Form::text('s',Input::old('s'),array('class' => 'form-control', 'placeholder' => 'Search')) }}
		  	</div>
		  	<button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
		{{ Form::close() }}
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="table-responsive">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Activity Code</th>
						<th>Created By</th>
						<th>Status</th>
						<th style="text-align:center;">Action</th>
					</tr>
				</thead>
				<tbody>
					@if(count($activities) == 0)
					<tr>
						<td colspan="5">No record found!</td>
					</tr>
					@else
					@foreach($activities as $activity)
					<tr>
						<td>{{ $activity->activity_code }}</td>
						<td>{{ $activity->createdby->getFullname() }}</td>
						<td>{{ $activity->status->status }}</td>
						<td>
							{{ HTML::linkAction('DownloadedActivityController@edit','Edit', $activity->id, array('class' => 'btn btn-info btn-xs')) }}
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