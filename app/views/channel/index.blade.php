@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Channel / Sub Channel List</h1>
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
        <a href="{{ URL::action('ChannelController@create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Channel</a>
        <a href="{{ URL::action('ChannelController@export') }}" class="btn btn-info"><i class="fa fa-download"></i> Export Channel</a>
        <a href="{{ URL::action('ChannelController@import') }}" class="btn btn-info"><i class="fa fa-upload"></i> Import Channel</a>
    {{ Form::close() }}
  </div>
</div>

<p class="pull-right"><b>{{ count($channels)}} record/s found.</b></p>
<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Channel Code</th>
            <th>Channel Name</th>
            <th>Sub Channel Code</th>
            <th>Level 3 Description</th>
            <th>Level 4 Code</th>
            <th>Level 4 Description</th>
            <th>Level 5 Code</th>
            <th>Level 5 Description</th>
            <th>RTM Tag</th>
            <th>Trade Deal</th>
            <th colspan="2" style="text-align:center;">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(count($channels) == 0)
          <tr>
            <td colspan="9">No record found!</td>
          </tr>
          @else
          @foreach($channels as $channel)
          <tr>
            <td>{{ $channel->channel_code }}</td>
            <td>{{ $channel->channel_name }}</td>
            <td>{{ $channel->coc_03_code }}</td>
            <td>{{ $channel->l3_desc }}</td>
            <td>{{ $channel->l4_code }}</td>
            <td>{{ $channel->l4_desc }}</td>
            <td>{{ $channel->l5_code }}</td>
            <td>{{ $channel->l5_desc }}</td>
            <td>{{ $channel->rtm_tag }}</td>
            <td class="center">{{ ($channel->trade_deal) ? '<i class="fa fa-check"></i>' : '' }}</td>
            <td class="action">
              {{ HTML::linkAction('CustomerController@edit','Edit', $channel->id, array('class' => 'btn btn-info btn-xs')) }}
            </td>
            <td class="action">
              {{ Form::open(array('method' => 'DELETE', 'action' => array('CustomerController@destroy', $channel->id))) }}                       
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