@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Customer List</h1>
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
        <a href="{{ URL::action('CustomerController@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Customer</a>
        <a href="{{ URL::action('CustomerController@export') }}" class="btn btn-info"><i class="fa fa-download"></i> Export Customer</a>
        <a href="{{ URL::action('CustomerController@import') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import Customer</a>
    {{ Form::close() }}
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Area</th>
            <th>Customer Code</th>
            <th>SOB Customer Code</th>
            <th>Customer Name</th>
            <th>Sales Multiplier</th>
            <th>Active</th>
            <th colspan="2" style="text-align:center;">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(count($customers) == 0)
          <tr>
            <td colspan="9">No record found!</td>
          </tr>
          @else
          @foreach($customers as $customer)
          <tr>
            <td>{{ $customer->area_name }}</td>
            <td>{{ $customer->customer_code }}</td>
            <td>{{ $customer->sob_customer_code }}</td>
            <td>{{ $customer->customer_name }}</td>
            <td>{{ $customer->multiplier }}</td>
            <td>{{ (($customer->active == 1) ? 'Active':'Inactive') }}</td>
            <td class="action">
              {{ HTML::linkAction('CustomerController@edit','Edit', $customer->id, array('class' => 'btn btn-info btn-xs')) }}
            </td>
            <td class="action">
              {{ Form::open(array('method' => 'DELETE', 'action' => array('CustomerController@destroy', $customer->id))) }}                       
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