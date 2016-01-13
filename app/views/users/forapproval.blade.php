@extends('layouts.layout')

@section('content')

<div class="page-header" id="banner">
  <div class="row">
      <div class="col-lg-8 col-md-7 col-sm-6">
      <h1>User For Approval List</h1>
      </div>
  </div>
</div>

@include('partials.notification')


<p><b>{{ count($users)}} record/s found.</b></p>

<div class="row">
  <div class="col-lg-12">
    <div class="table-responsive">
      <table class="table table-striped table-condensed table-hover table-bordered">
        <thead>
          <tr>
            <th class="center">Full Name</th>
            <th class="center">Email</th>
            <th class="center">Contact Number</th>
            <th colspan="2" style="text-align:center; width:10%;">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(count($users) == 0)
          <tr>
            <td colspan="5">No record found!</td>
          </tr>
          @else
          @foreach($users as $user)
          <tr>
            <td>{{ $user->getFullname() }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->contact_no }}</td>
            <td class="action">
              {{ Form::open(array('method' => 'POST', 'action' => array('UsersController@deny', $user->id))) }}                       
              {{ Form::submit('Deny', array('class'=> 'btn btn-danger btn-xs','onclick' => "if(!confirm('Are you sure to deny this user application?')){return false;};")) }}
              {{ Form::close() }}
            </td>
            <td class="action">
              {{ HTML::linkAction('UsersController@edit','Approve', $user->id, array('class' => 'btn btn-info btn-xs')) }}
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