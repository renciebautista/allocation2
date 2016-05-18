@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Customer Inactive / Active Mapping</h1>
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
        <a href="{{ URL::action('CustomerRemapController@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Mapping</a>
        <a href="{{ URL::action('CustomerRemapController@export') }}" class="btn btn-info"><i class="fa fa-download"></i> Export Mapping</a>
        <a href="{{ URL::action('CustomerRemapController@import') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import Mapping</a>
    {{ Form::close() }}
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>From Customer </th>
            <th>To Customer</th>
            <th>Status</th>
            <th>Percentage Share</th>
            <th colspan="2" style="text-align:center;">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(count($customers) == 0)
          <tr>
            <td colspan="5">No record found!</td>
          </tr>
          @else
          @foreach($customers as $customer)
          <tr>
            <td>{{ $customer->from_customer_code }} - {{ $customer->from_customer_name }}</td>
            <td>{{ $customer->to_customer_code }} - {{ $customer->to_customer_name }}</td>
            <td>{{ (($customer->active == 1) ? 'Active':'Inactive') }}</td>
            <td>{{ $customer->split }}</td>
            <td class="action">
            </td>
            <td class="action">
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