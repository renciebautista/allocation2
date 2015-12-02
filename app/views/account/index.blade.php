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
        <a href="{{ URL::action('AccountController@export') }}" class="btn btn-info"><i class="fa fa-download"></i> Export Accounts</a>
        <a href="{{ URL::action('AccountController@import') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import Accounts</a>

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
            <th>Ship To Code</th>
            <th>Account Name</th>
            <th>Active</th>
            <th style="text-align:center;">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(count($accounts) == 0)
          <tr>
            <td colspan="9">No record found!</td>
          </tr>
          @else
          @foreach($accounts as $account)
          <tr>
            <td>{{ $account->area_name }}</td>
            <td>{{ $account->ship_to_code }}</td>
            <td>{{ $account->account_name }}</td>
            <td>{{ (($account->active == 1) ? 'Active':'Inactive') }}</td>
            <td class="action">
              {{ HTML::linkAction('AccountController@edit','Edit', $account->id, array('class' => 'btn btn-info btn-xs')) }}
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