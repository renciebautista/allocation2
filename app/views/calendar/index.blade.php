@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Released Activity Calendar</h1>
      </div>
  </div>
</div>

@include('partials.notification')

<div id='calendar'></div>


@stop

@section('add-script')
  {{ HTML::script('assets/js/calendar.js') }}
@stop