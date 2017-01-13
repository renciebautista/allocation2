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

<div id="eventContent" title="Event Details" style="display:none;">
	Title: <span id="eventTitle"></span><br>
	<hr>
    Start: <span id="startTime"></span><br>
    End: <span id="endTime"></span><br><br>
    <!-- <p id="link"><strong><a id="eventLink" href="" target="_blank">Read More</a></strong></p> -->
</div>

@stop

@section('add-script')
  {{ HTML::script('assets/js/calendar.js') }}
@stop