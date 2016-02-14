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
        <a href="{{ URL::action('ShiptoController@export') }}" class="btn btn-success"><i class="fa fa-download"></i> Book This Sales Order</a>

        <a href="{{ URL::action('SobController@downloadbooking') }}" class="btn btn-info"><i class="fa fa-download"></i> Download Booking</a>


<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">

      <table id="sobform" class="table table-striped table-hover">
        <thead>

          <tr>
            <th>Purchase Order #</th>
            <th>Loading Date</th>
            <th>Receipt Date</th>
            <th>Customer Name</th>
           	<th>Ship To</th>
            <th class="text-right">Total Allocation</th>
            <th>Action</th>
          </tr>
          <tr></tr>
        </thead>
        <tbody>
          @if(count($soldtos) == 0)
          <tr>
            <td colspan="7">No record found!</td>
          </tr>
          @else
           <?php $cnt = 1;  ?>
           <?php $total = 0; ?>
          @foreach($soldtos as $soldto)

          <tr>
            <td>{{ $po."_".$cnt }}</td>
            <td></td>
            <td></td>
            <td>{{ $soldto->ship_to }}</td>
            <td>{{ $soldto->ship_to_code }}</td>
            <td class="text-right">{{ number_format($soldto->total_allocation) }}</td>
          <td>
                  <a href="{{ URL::action('SobController@downloadbooking') }}" class="btn btn-primary btn-xs ">View Schemes</a>
</td>
          </tr>
          <?php $total += $soldto->total_allocation; ?>
          <?php $cnt++;  ?>
          @endforeach
          <tr>
            <td colspan="5" class="text-right">Total</td>
            <td class="text-right">{{ number_format($total) }}</td>
          </tr>
          @endif
        </tbody>
      </table> 
    </div>
  </div>
</div>

@stop

@section('page-script')
@stop