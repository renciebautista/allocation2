@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Sub Channel List</h1>
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
        <a href="{{ URL::action('SubchannelController@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Sub Channel</a>
        <a href="{{ URL::action('SubchannelController@export') }}" class="btn btn-info"><i class="fa fa-download"></i> Export Sub Channel</a>
        <a href="{{ URL::action('SubchannelController@import') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import Sub Channel</a>
    {{ Form::close() }}
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Sub Channel Code</th>
            <th>Channel Code</th>
            <th>Channel Name</th>
            <th>Level 3 Description</th>
            <th colspan="2" style="text-align:center;">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(count($subchannels) == 0)
          <tr>
            <td colspan="9">No record found!</td>
          </tr>
          @else
          @foreach($subchannels as $subchannel)
          <tr>
            <td>{{ $subchannel->coc_03_code }}</td>
            <td>{{ $subchannel->channel_code }}</td>
            <td>{{ $subchannel->channel_name }}</td>
            <td>{{ $subchannel->l3_desc }}</td>
            <td class="action">
              {{ HTML::linkAction('SubchannelController@edit','Edit', $subchannel->id, array('class' => 'btn btn-info btn-xs')) }}
            </td>
            <td class="action">
              {{ Form::open(array('method' => 'DELETE', 'action' => array('SubchannelController@destroy', $subchannel->id))) }}                       
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