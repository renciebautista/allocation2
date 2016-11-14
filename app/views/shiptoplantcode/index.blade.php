@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Ship To / Plant Code Mapping</h1>
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
        <a href="{{ URL::action('ShiptoPlantCodeController@export') }}" class="btn btn-info"><i class="fa fa-download"></i> Export Ship To / Plant Code Mapping</a>
        <a href="{{ URL::action('ShiptoPlantCodeController@import') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import Ship To / Plant Code Mapping</a>
    {{ Form::close() }}
  </div>
</div>
<hr>
<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover datatable">
        <thead>
          <tr>
            <th>Group</th>
            <th>Area</th>
            <th>Customer</th>
            <th>Distributor</th>
            <th>Plant Code</th>
            <th>Ship To Name</th>
          </tr>
        </thead>
        <tbody>
          @if(count($mappings) == 0)
          <tr>
            <td colspan="9">No record found!</td>
          </tr>
          @else
          @foreach($mappings as $mapping)
          <tr>
            <td>{{ $mapping->group }}</td>
            <td>{{ $mapping->area }}</td>
            <td>{{ $mapping->customer }}</td>
            <td>{{ $mapping->distributor_name }}</td>
            <td>{{ $mapping->plant_code }}</td>
            <td>{{ $mapping->ship_to_name }}</td>

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
