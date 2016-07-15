@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Level 4 Channel List</h1>
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
        <a href="{{ URL::action('Level4Controller@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Channel</a>
        <a href="{{ URL::action('Level4Controller@export') }}" class="btn btn-info"><i class="fa fa-download"></i> Export Channel</a>
        <a href="{{ URL::action('Level4Controller@import') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import Channel</a>
    {{ Form::close() }}
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Level 3 Code</th>
            <th>Level 4 Code</th>
            <th>Level 4 Description</th>
            <th colspan="2" style="text-align:center;">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(count($l4channels) == 0)
          <tr>
            <td colspan="9">No record found!</td>
          </tr>
          @else
          @foreach($l4channels as $channel)
          <tr>
            <td>{{ $channel->coc_03_code }}</td>
            <td>{{ $channel->l4_code }}</td>
            <td>{{ $channel->l4_desc }}</td>
            <td class="action">
              {{ HTML::linkAction('Level4Controller@edit','Edit', $channel->id, array('class' => 'btn btn-info btn-xs')) }}
            </td>
            <td class="action">
              {{ Form::open(array('method' => 'DELETE', 'action' => array('Level4Controller@destroy', $channel->id))) }}                       
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