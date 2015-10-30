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
    {{ Form::close() }}
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Customer Code</th>
            <th>Ship To Code</th>
            <th>Ship To Name</th>
            <th>Split %</th>
            <th>Loading Day</th>
            <th>Lead Time (days)</th>
            <th>Active</th>
            <th colspan="2" style="text-align:center;">Action</th>
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
            <td>{{ $shipto->split }}</td>
            <td>{{ $shipto->dayofweek }}</td>
            <td>{{ $shipto->leadtime }}</td>
            <td>{{ (($shipto->active == 1) ? 'Active':'Inactive') }}</td>
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