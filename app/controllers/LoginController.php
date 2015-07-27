<?php

class LoginController extends \BaseController {

	public function index(){
		if (Confide::user()) {
        	return Redirect::to('/dashboard');
	    } else {
	        return View::make('login.index');
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

		

		// $repo = App::make('UserRepository');
		// $input = Input::all();

		// if ($repo->login($input)) {
		// 	return Redirect::action('DashboardController@index');
		// } else {
		// 	if ($repo->isThrottled($input)) {
		// 		$err_msg = Lang::get('confide::confide.alerts.too_many_attempts');
		// 	} elseif ($repo->existsButNotConfirmed($input)) {
		// 		$err_msg = Lang::get('confide::confide.alerts.not_confirmed');
		// 	} else {
		// 		$err_msg = Lang::get('confide::confide.alerts.wrong_credentials');
		// 	}

		// 	return Redirect::back()
		// 		->withInput(Input::except('password'))
		// 		->with('error', $err_msg);
		// }
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
    	if (Confide::forgotPassword(Input::get('email'))) {
			$notice_msg = Lang::get('confide::confide.alerts.password_forgot');
			return Redirect::action('LoginController@index')
				->with('class', 'alert-success')
				->with('message', $notice_msg);
		} else {
			$error_msg = Lang::get('confide::confide.alerts.wrong_password_forgot');
			return Redirect::action('LoginController@doforgotpassword')
				->withInput()
				->with('class', 'alert-danger')
				->with('message', $error_msg);
		}
    }
}