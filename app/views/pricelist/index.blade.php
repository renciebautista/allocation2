@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Price List</h1>
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
        <a href="{{ URL::action('PricelistController@export') }}" class="btn btn-info"><i class="fa fa-download"></i> Export Price List</a>
        <a href="{{ URL::action('PricelistController@import') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import Price List</a>
    {{ Form::close() }}
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>Category</th>
            <th>Brand</th>
            <th>Item Code</th>
            <th>Item Description</th>
            <th>Pack Size</th>
            <th>LPBT</th>
            <th>LPAT</th>
            <th>SRP</th>
            <th>SKU Format</th>
            <th>Active</th>
            <th colspan="2" style="text-align:center;">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(count($pricelists) == 0)
          <tr>
            <td colspan="9">No record found!</td>
          </tr>
          @else
        @foreach($pricelists as $item)
          <tr>
            <td>{{ $item->category_desc }}</td>
            <td>{{ $item->brand_desc }}</td>
            <td>{{ $item->sap_code }}</td>
            <td>{{ $item->sap_desc }}</td>
            <td>{{ $item->pack_size }}</td>
            <td>{{ $item->price_case }}</td>
            <td>{{ $item->price_case_tax }}</td>
            <td>{{ $item->srp }}</td>
            <td>{{ $item->sku_format }}</td>
            <td>{{ (($item->active == 1) ? 'Active':'Inactive') }}</td>
            <td class="action">
              {{ HTML::linkAction('PricelistController@edit','Edit', $item->id, array('class' => 'btn btn-info btn-xs')) }}
            </td>
            <td class="action">
              {{ Form::open(array('method' => 'DELETE', 'action' => array('PricelistController@destroy', $item->id))) }}                       
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