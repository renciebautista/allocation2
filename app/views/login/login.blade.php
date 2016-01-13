@extends('login.form1')

@section('content')

<div id="login" class="bg-form"> 

                            @if (Session::has('message'))
                                <div class="alert alert-dismissable {{ Session::get('class') }}">
                                    <button class="close" data-dismiss="alert" type="button">×</button>
                                    {{ Session::get('message') }}
                                </div>
                            @endif


              {{ Form::open(array('action' => 'LoginController@dologin','class' => 'form-horizontal')) }}
                  <div class="form-group"> 
                    <div class="col-sm-12"> 
                  {{ Form::text('name','',array('class' => 'form-control' , 'placeholder' => 'Email')) }}
                    </div> 
                  </div> 
                  <div class="form-group"> 
                    <div class="col-sm-12"> 
                      {{ Form::password('password',array('class' => 'form-control', 'placeholder' => 'Password')) }}
                    </div>
                  </div> 

                   
                  <div class="form-group"> 
                    <div class="col-sm-12"> 
                      <div class="checkbox"> 
                        <label> <input type="checkbox"> Remember me </label> 
                                        {{ HTML::linkAction('LoginController@forgotpassword', 'Forgot password?',null, array('class' => "pull-right")) }}
                      </div> 
                  </div> 
                </div> 

                <div class="form-group"> 
                    <div class="col-sm-12"> 
                      <button type="submit" class="btn btn-primary">Sign in</button>
                    </div>
                  </div>
            {{ Form::close() }}
            </div>

            <div id="register" class="bg-form"> 

              @if (Session::has('signup_message'))
                                <div class="alert alert-dismissable {{ Session::get('class') }}">
                                    <button class="close" data-dismiss="alert" type="button">×</button>
                                    {{ Session::get('signup_message') }}
                                </div>
                            @endif

                          @if ($errors->any())
    <ul>
        {{ implode('', $errors->all('<li class="error">:message</li>')) }}
    </ul>
@endif

              <h2><strong>New to E-TOP?</strong> Sign up</h2>
                {{ Form::open(array('action' => 'LoginController@signup','class' => 'form-horizontal')) }}
                  <div class="form-group"> 
                    <div class="col-sm-12"> 
                      {{ Form::text('first_name','',array('class' => 'form-control' , 'placeholder' => 'First Name' , 'required')) }}
                    </div> 
                  </div> 

                  <div class="form-group"> 
                    <div class="col-sm-12">
                      {{ Form::text('middle_initial','',array('class' => 'form-control' , 'placeholder' => 'Middle Initial' , 'required')) }}
                    </div> 
                  </div>

                  <div class="form-group"> 
                    <div class="col-sm-12"> 
                      {{ Form::text('last_name','',array('class' => 'form-control' , 'placeholder' => 'Last Name' , 'required')) }}                      
                    </div> 
                  </div>

                  <div class="form-group"> 
                    <div class="col-sm-12">
                      {{ Form::text('username','',array('class' => 'form-control' , 'placeholder' => 'Username' , 'required')) }}                      
                    </div> 
                  </div>

                  <div class="form-group"> 
                    <div class="col-sm-12"> 
                      {{ Form::email('email','',array('class' => 'form-control' , 'placeholder' => 'Email' , 'required')) }}                      
                    </div> 
                  </div>
                  
                  <div class="form-group"> 
                    <div class="col-sm-12"> 
                      {{ Form::text('contact_number','',array('class' => 'form-control' , 'placeholder' => 'Contact Number' , 'required')) }}                      
                    </div> 
                  </div>

                <div class="form-group"> 
                    <div class="col-sm-12"> 
                      <button type="submit" class="btn btn-primary">Sign up for E-TOP</button>
                    </div>
                  </div>
              </form> 
            </div>


            @stop