@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Customer Master</h1>
      </div>
  </div>
</div>

@include('partials.notification')

<div class="row">
  <div class="col-lg-12">
    {{ Form::open(array('route' => 'customermaster.export','class' => 'form-inline', 'id' => 'myform')) }}
         <button type="submit" class="btn btn-primary disable-button"> Export Customer Masterfiles</button>
    {{ Form::close() }}
  </div>
</div>

<br>
<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-condensed table-hover table-bordered">
        <thead>
          <tr>
            <th>Template Name</th>
            <th style="width:10%;">Date Created</th>
            <th style="width:10%;" class="dash-action">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(count($exports) == 0)
          <tr>
            <td colspan="7">No record found!</td>
          </tr>
          @else
          @foreach($exports as $export)
          <tr>
            
            <td>{{ $export->filename }}</td>
            <td>{{ date_format(date_create($export->created_at),'M j, Y') }}</td>
            <td class="action">
              {{ HTML::linkAction('CustomerMasterController@download','Download', $export->id, array('class' => 'btn btn-info btn-xs')) }}
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
@section('scripts')

$("#myform").disableButton();

@stop