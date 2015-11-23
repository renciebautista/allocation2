@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Brand List</h1>
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
        <a href="{{ URL::action('BrandController@export') }}" class="btn btn-info"><i class="fa fa-download"></i> Export Brands</a>
        <a href="{{ URL::action('BrandController@import') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import Brands</a>

    {{ Form::close() }}
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Division</th>
            <th>Category</th>
            <th>Brand</th>
            <th>Brand Shortcut</th>
            <th style="text-align:center;">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(count($brands) == 0)
          <tr>
            <td colspan="9">No record found!</td>
          </tr>
          @else
          @foreach($brands as $brand)
          <tr>
            <td>{{ $brand->division_desc }}</td>
            <td>{{ $brand->category_desc }}</td>
            <td>{{ $brand->brand_desc }}</td>
            <td>{{ $brand->brand_shortcut }}</td>
            <td class="action">
              {{ HTML::linkAction('BrandController@edit','Edit', $brand->id, array('class' => 'btn btn-info btn-xs')) }}
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