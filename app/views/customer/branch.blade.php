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
        <a href="{{ URL::action('CustomerController@exportbranch') }}" class="btn btn-info"><i class="fa fa-download"></i> Export Customer Branch</a>
        <a href="{{ URL::action('CustomerController@importbranch') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import Customer Branch</a>
    {{ Form::close() }}
  </div>
</div>

<hr>
<p class="pull-right"><b>{{ count($branches)}} record/s found.</b></p>
<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Area</th>
            <th>Customer Name</th>
            <th>Branch Name</th>
            <th>Distributor Code</th>
            <th>Plant Code</th>
            
          </tr>
        </thead>
        <tbody>
          @if(count($branches) == 0)
          <tr>
            <td colspan="9">No record found!</td>
          </tr>
          @else
          @foreach($branches as $branch)
          <tr>
            <td>{{ $branch->area_name }}</td>
            <td>{{ $branch->customer_name }}</td>
            <td>{{ $branch->branch_name }}</td>
            <td>{{ $branch->distributor_code }}</td>
            <td>{{ $branch->plant_code }}</td>
            
          </tr>
          @endforeach
          @endif
        </tbody>
      </table> 
    </div>
  </div>
</div>

@stop