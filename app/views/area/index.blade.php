@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Area List</h1>
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
        <a href="{{ URL::action('AreaController@export') }}" class="btn btn-info"><i class="fa fa-download"></i> Export Areas</a>
        <a href="{{ URL::action('AreaController@import') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import Areas</a>

    {{ Form::close() }}
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Group</th>
            <th>Area</th>
            <th style="text-align:center;">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(count($areas) == 0)
          <tr>
            <td colspan="9">No record found!</td>
          </tr>
          @else
          @foreach($areas as $area)
          <tr>
            <td>{{ $area->group_name }}</td>
            <td>{{ $area->area_name }}</td>
            <td class="action">
              {{ HTML::linkAction('AreaController@edit','Edit', $area->id, array('class' => 'btn btn-info btn-xs')) }}
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