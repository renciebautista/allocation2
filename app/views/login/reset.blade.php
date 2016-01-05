@extends('login.form1')

@section('content')
<div id="register" class="bg-form"> 
     @if (Session::has('message'))
                                <div class="alert alert-dismissable {{ Session::get('class') }}">
                                    <button class="close" data-dismiss="alert" type="button">×</button>
                                    {{ Session::get('message') }}
                                </div>
                            @endif
    <h2><strong>Reset Password</strong></h2>
    {{ Form::open(array('action' => 'LoginController@doResetPassword','class' => 'form-horizontal')) }}
  {{ Form::hidden('token', $token) }}
        <div class="form-group"> 
          <div class="col-sm-12"> 
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password"> 
          </div> 
        </div>

         <div class="form-group"> 
          <div class="col-sm-12"> 
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Retype new password"> 
          </div> 
        </div>
      
        <div class="form-group"> 
          <div class="col-sm-12"> 
            <button type="submit" class="btn btn-primary">Submit</button>
            {{ HTML::linkAction('LoginController@index', 'Back',null, array('class' => "btn btn-default")) }}
          </div>
        </div>
    {{ Form::close() }}
  </div>
@stop