@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
	<div class="row">
		<div class="col-lg-8 col-md-7 col-sm-6">
			<h1>Sales Order Booking</h1>
		</div>
	</div>
</div>

@include('partials.notification')

<div class="panel panel-default">
	<div class="panel-heading">Filters</div>
	<div class="panel-body">
		 {{ Form::open(array('route' => 'sob.filterbooking','class' => 'bs-component', 'id' => 'myform')) }}
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('ty', 'Activity Type', array('class' => 'control-label')) }}
						{{ Form::select('ty[]', $types, null, array('id' => 'ty','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('br', 'SOB Allocation Brand', array('class' => 'control-label')) }}
						{{ Form::select('br[]', $brands, null, array('id' => 'br','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-6">
						{{ Form::label('yr', 'Year', array('class' => 'control-label')) }}
						{{ Form::select('yr[]', $years, null, array('id' => 'yr','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
						<div class="col-lg-6">
						{{ Form::label('wk', 'Week #', array('class' => 'control-label')) }}
						{{ Form::select('wk[]', $weeks, null, array('id' => 'wk','class' => 'form-control', 'multiple' => 'multiple')) }}
						</div>
						
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<div class="row">
						<div class="col-lg-12">
						{{ Form::label('st', 'Status', array('class' => 'control-label')) }}
						{{ Form::select('st[]', $status, null, array('id' => 'st','class' => 'form-control', 'multiple' => 'multiple')) }}
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
								<button type="submit" value="process" name="submit" class="btn btn-success"><i class="fa fa-search"></i> Process</button>
								@if($allow_download)
								<button type="submit" value="skulist" name="submit" class="btn btn-primary">Download SKU List</button>
								@else
								<button type="submit" value="skulist" name="submit" class="btn btn-primary disabled">Download SKU List</button>

								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		{{ Form::close() }}
	</div>
</div>


<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
    <label class="pull-right bold">{{ count($bookings) }} records found.</label>

      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Activity Type</th>
            <th>SOB Allocation Brand</th>
            <th class="text-right">Week #</th>
            <th class="text-right">Year</th>
            <th class="text-right">Total Allocation</th>
            <th>Status</th>
            <th style="text-align:center;">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(count($bookings) == 0)
          <tr>
            <td colspan="7">No record found!</td>
          </tr>
          @else
          @foreach($bookings as $booking)
          <tr>
            <td>{{ $booking->activitytype_desc }}</td>
            <td>{{ $booking->brand_desc }}</td>
            <td class="text-right">{{ $booking->weekno }}</td>
            <td class="text-right">{{ $booking->year }}</td>
            <td class="text-right">{{ number_format($booking->total_allocation) }}</td>
            <td>{{ $booking->status }}</td>
            <td class="action">
				{{ HTML::linkAction('SobController@showbooking','View Booking', ['week' => $booking->weekno, 'year' => $booking->year, 'brand_code' => $booking->brand_code, 'type' => $booking->activity_type_id], array('class' => 'btn btn-primary btn-xs')) }}
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

@section('page-script')

$('#ty,#br,#wk,#yr,#st').multiselect({
	maxHeight: 200,
	includeSelectAllOption: true,
	enableCaseInsensitiveFiltering: true,
	enableFiltering: true
});


@stop