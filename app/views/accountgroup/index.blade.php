@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Account List</h1>
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
        <a href="{{ URL::action('AccountGroupController@export') }}" class="btn btn-info"><i class="fa fa-download"></i> Export Account Groups</a>
        <a href="{{ URL::action('AccountGroupController@import') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import Account Groups</a>

    {{ Form::close() }}
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Account Group Code</th>
            <th>Account Group Name</th>
            <th class="center">Show in Summary</th>
          </tr>
        </thead>
        <tbody>
          @if(count($accountgroups) == 0)
          <tr>
            <td colspan="9">No record found!</td>
          </tr>
          @else
          @foreach($accountgroups as $group)
          <tr>
            <td>{{ $group->account_group_code }}</td>
            <td>{{ $group->account_group_name }}</td>
            <td class="center">{{ ($group->show_in_summary) ? '<i class="fa fa-check"></i>' : '' }}</td>
          </tr>
          @endforeach
          @endif
        </tbody>
      </table> 
    </div>
  </div>
</div>

@stop