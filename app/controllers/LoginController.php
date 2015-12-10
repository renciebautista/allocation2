<?php

class LoginController extends \BaseController {

	public function index(){
		if (Confide::user()) {
        	return Redirect::to('/dashboard');
	    } else {
	    	if($_ENV['MAIL_TEST']){
	    		return View::make('login.form1');
	    	}else{
	    		return View::make('login.index');
	    	}

	    }
	}

	public function dologin()
	{
		$usernameinput =  Input::get('name');
		$password = Input::get('password');
		$field = filter_var($usernameinput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

		if (Auth::attempt(array($field => $usernameinput, 'password' => $password), false)) {
			// 
			if(Auth::user()->isActive()){
				Session::flash('message', '<h4>Welcome to E-TOP,</h4><p> '.ucwords(strtolower(Auth::user()->getFullname())).'</p>');
				Session::flash('class', 'alert alert-success');
				return Redirect::intended('/dashboard');
			}else{
				Auth::logout();
				Session::flash('message', 'User account is inactive, please contact the administrator');
				Session::flash('class', 'alert alert-danger');
				return Redirect::back();
			}
			
		    // return Redirect::action('DashboardController@index');
		}

		Auth::logout();
		Session::flash('message', 'Invalid credentials, please try again');
		Session::flash('class', 'alert alert-danger');
		return Redirect::back();

		
	}

	public function logout()
    {
        Confide::logout();
        return Redirect::to('/dashboard');
    }

    public function forgotpassword(){
    	return View::make('login.forgotpassword');
    }

    public function doforgotpassword(){
    	if (User::forgot_password(Input::get('email'))) {
			return Redirect::action('LoginController@index')
				->with('class', ' alert alert-success')
				->with('message', 'The information regarding password reset was sent to your email.');
		} else {
			return Redirect::action('LoginController@doforgotpassword')
				->withInput()
				->with('class', 'alert alert-danger')
				->with('message', 'User not found.');
		}
    }

    public function resetpassword($token)
    {
        return View::make('login.reset_password')->with('token', $token);
    }

    public function doResetPassword()
    {
        $repo = App::make('UserRepository');
        $input = array(
            'token'                 =>Input::get('token'),
            'password'              =>Input::get('password'),
            'password_confirmation' =>Input::get('password_confirmation'),
        );

        // By passing an array with the token, password and confirmation
        if ($repo->resetPassword($input)) {
            return Redirect::action('LoginController@index')
                ->with('message', 'Your password has been changed successfully.')
                ->with('class', 'alert alert-success');
        } else {
            return Redirect::action('LoginController@resetpassword', array('token'=>$input['token']))
                ->withInput()
                ->with('message', 'Invalid password. Try again')
                ->with('class', 'alert alert-danger');
        }
    }
}