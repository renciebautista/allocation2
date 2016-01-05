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
              <h2><strong>New to E-TOP?</strong> Sign up</h2>
                <form class="form-horizontal"> 

                  <div class="form-group"> 
                    <div class="col-sm-12"> 
                      <input type="email" class="form-control" id="inputEmail3" placeholder="First Name"> 
                    </div> 
                  </div> 

                  <div class="form-group"> 
                    <div class="col-sm-12"> 
                      <input type="email" class="form-control" id="inputEmail3" placeholder="Middle Initial"> 
                    </div> 
                  </div>

                  <div class="form-group"> 
                    <div class="col-sm-12"> 
                      <input type="email" class="form-control" id="inputEmail3" placeholder="Last Name"> 
                    </div> 
                  </div>

                  <div class="form-group"> 
                    <div class="col-sm-12"> 
                      <input type="email" class="form-control" id="inputEmail3" placeholder="Username"> 
                    </div> 
                  </div>

                  <div class="form-group"> 
                    <div class="col-sm-12"> 
                      <input type="email" class="form-control" id="inputEmail3" placeholder="Email"> 
                    </div> 
                  </div>
                  
                  <div class="form-group"> 
                    <div class="col-sm-12"> 
                      <input type="email" class="form-control" id="inputEmail3" placeholder="Contact Number"> 
                    </div> 
                  </div>

                  <div class="form-group"> 
                    <div class="col-sm-12"> 
                      <input type="email" class="form-control" id="inputEmail3" placeholder="Department"> 
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