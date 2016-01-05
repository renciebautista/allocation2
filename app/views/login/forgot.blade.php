@extends('login.form1')

@section('content')
<div id="register" class="bg-form"> 
		 @if (Session::has('message'))
                                <div class="alert alert-dismissable {{ Session::get('class') }}">
                                    <button class="close" data-dismiss="alert" type="button">×</button>
                                    {{ Session::get('message') }}
                                </div>
                            @endif
	  <h2><strong>Find your E-TOP account</strong></h2>
	  {{ Form::open(array('action' => 'LoginController@forgotpassword','class' => 'form-horizontal')) }}
	      <div class="form-group"> 
	        <div class="col-sm-12"> 
	          <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email or username"> 
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