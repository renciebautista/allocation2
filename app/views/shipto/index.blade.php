@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Ship To List</h1>
      </div>
  </div>
</div>

@include('partials.notification')

<div class="row">
  <div class="col-lg-12">
    {{ Form::open(array('method' => 'get','class' => 'form-inline')) }}
      <div class="form-group">
        <label class="sr-only" for="s">Search</label>
        {{ Form::text('s',Input::old('s'),array('class' => 'form-control', 'placeholder' => 'Search')) }}
        </div>
        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
        <a href="{{ URL::action('ShiptoController@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Ship To</a>
        <a href="{{ URL::action('ShiptoController@export') }}" class="btn btn-info"><i class="fa fa-download"></i> Export Ship To</a>
        <a href="{{ URL::action('ShiptoController@import') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import Ship To</a>
    {{ Form::close() }}
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover datatable">
        <thead>
          <tr>
            <th colspan="5"></th>
            <th class="center" colspan="7">Loading Day</th>
            <th></th>
            <th></th>
            <th></th>
          </tr>
          <tr>
            <th>Customer Code</th>
            <th>Ship To Code</th>
            <th>Ship To Name</th>
            <th>Split %</th>
            <th>Lead Time (days)</th>
            <th class="center">Mon</th>
            <th class="center">Tue</th>
            <th class="center">Wed</th>
            <th class="center">Thur</th>
            <th class="center">Fri</th>
            <th class="center">Sat</th>
            <th class="center">Sun</th>
            <th class="center">Active</th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @if(count($shiptos) == 0)
          <tr>
            <td colspan="9">No record found!</td>
          </tr>
          @else
          @foreach($shiptos as $shipto)
          <tr>
            <td>{{ $shipto->customer_code }}</td>
            <td>{{ $shipto->ship_to_code }}</td>
            <td>{{ $shipto->ship_to_name }}</td>
            <td class="center">{{ $shipto->split }}</td>
            <td class="center">{{ $shipto->leadtime }}</td>
            <td class="center">{{ ($shipto->mon) ? '<i class="fa fa-check"></i>' : '' }}</td>
            <td class="center">{{ ($shipto->tue) ? '<i class="fa fa-check"></i>' : '' }}</td>
            <td class="center">{{ ($shipto->wed) ? '<i class="fa fa-check"></i>' : '' }}</td>
            <td class="center">{{ ($shipto->thu) ? '<i class="fa fa-check"></i>' : '' }}</td>
            <td class="center">{{ ($shipto->fri) ? '<i class="fa fa-check"></i>' : '' }}</td>
            <td class="center">{{ ($shipto->sat) ? '<i class="fa fa-check"></i>' : '' }}</td>
            <td class="center">{{ ($shipto->sun) ? '<i class="fa fa-check"></i>' : '' }}</td>
            <td class="center">{{ (($shipto->active == 1) ? 'Active':'Inactive') }}</td>
            <td class="action">
              {{ HTML::linkAction('ShiptoController@edit','Edit', $shipto->id, array('class' => 'btn btn-info btn-xs')) }}
            </td>
            <td class="action">
              {{ Form::open(array('method' => 'DELETE', 'action' => array('ShiptoController@destroy', $shipto->id))) }}                       
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

@section('page-script')

$('.datatable').DataTable({
  "scrollY": "500px",
  "scrollCollapse": true,
  "paging": false,
  "bSort": false,
  "searching": false
});
@stop
