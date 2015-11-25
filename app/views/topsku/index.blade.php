@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Top Sku List</h1>
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
        <a href="{{ URL::action('TopskuController@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Top Sku</a>
        <a href="{{ URL::action('TopskuController@export') }}" class="btn btn-info"><i class="fa fa-download"></i> Export Top Sku</a>
        <a href="{{ URL::action('TopskuController@import') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import Top Sku</a>
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
            <th>SKU Code</th>
            <th>Sku Description</th>
            <th>Active</th>
            <th colspan="2" style="text-align:center;">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(count($skus) == 0)
          <tr>
            <td colspan="9">No record found!</td>
          </tr>
          @else
          @foreach($skus as $sku)
          <tr>
            <td>{{ $sku->division_desc }}</td>
            <td>{{ $sku->category_desc }}</td>
            <td>{{ $sku->brand_desc }}</td>
            <td>{{ $sku->sku_code }}</td>
            <td>{{ $sku->sku_desc }}</td>
            <td>{{ (($sku->active == 1) ? 'Active':'Inactive') }}</td>
            <td class="action">
              {{ HTML::linkAction('TopskuController@edit','Edit', $sku->id, array('class' => 'btn btn-info btn-xs')) }}
            </td>
            <td class="action">
              {{ Form::open(array('method' => 'DELETE', 'action' => array('TopskuController@destroy', $sku->id))) }}                       
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