
@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
    <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>Approve User</h1>
    </div>
  </div>
</div>

  

@include('partials.notification')

<div class="row">

  <div class="col-lg-6">
  {{ Form::open(array('action' => array('UsersController@setapprove', $user->id), 'method' => 'POST', 'class' => 'bs-component')) }}
    <div class="form-group">
      {{ Form::label('username', 'Username', array('class' => 'control-label')) }}
      {{ Form::text('username',$user->username ,array('class' => 'form-control', 'placeholder' => 'Username', 'readonly' => '')) }}
    </div>

    <div class="form-group">
      {{ Form::label('email', 'Email Address', array('class' => 'control-label')) }}
      {{ Form::text('email', $user->email,array('class' => 'form-control', 'placeholder' => 'Email Address', 'readonly' => '')) }}
    </div>

    <div class="form-group">
      {{ Form::label('contact_no', 'Contact No.', array('class' => 'control-label')) }}
      {{ Form::text('contact_no', $user->contact_no,array('class' => 'form-control', 'placeholder' => 'Contact No.', 'readonly' => '')) }}
    </div>

    <div class="form-group">
      {{ Form::label('first_name', 'First Name', array('class' => 'control-label')) }}
      {{ Form::text('first_name', $user->first_name,array('class' => 'form-control', 'placeholder' => 'First Name', 'readonly' => '')) }}
    </div>

    <div class="form-group">
      {{ Form::label('middle_name', 'Middle Initial', array('class' => 'control-label')) }}
      {{ Form::text('middle_name', $user->middle_initial,array('class' => 'form-control', 'placeholder' => 'Middle Initial', 'readonly' => '')) }}
    </div>

    <div class="form-group">
      {{ Form::label('last_name', 'Last Name', array('class' => 'control-label')) }}
      {{ Form::text('last_name', $user->last_name,array('class' => 'form-control', 'placeholder' => 'Last Name', 'readonly' => '')) }}
    </div>


    <div class="form-group">
      {{ Form::label('group_id', 'Group', array('class' => 'control-label')) }}
      {{ Form::select('group_id', array('0' => 'PLEASE SELECT') + $groups, null, array('class' => 'form-control')) }}
    </div>

    <div class="form-group">
      <div class="checkbox">
        <label>
          {{ Form::checkbox('is_active', true, (($user->active == 1) ? true : false)) }} Active
        </label>
      </div>
    </div>


    <div class="form-group">
      {{ Form::submit('Submit', array('class' => 'btn btn-primary')) }}
      {{ HTML::linkAction('UsersController@forapproval', 'Back', array(), array('class' => 'btn btn-default')) }}
    </div>
  {{ Form::close() }}


  </div>
</div>

@include('javascript.user.edit')

@stop

@section('page-script')

@stop


