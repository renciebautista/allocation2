<?php

class LoginController extends \BaseController {

	public function index(){
		if (Confide::user()) {
        	return Redirect::to('/dashboard');
	    } else {
	    	// if($_ENV['MAIL_TEST']){
	    		return View::make('login.login');
	    	// }else{
	    	// 	return View::make('login.index');
	    	// }

	    }
	}

  public function signup(){
    $input = Input::all();
    $rules = array(
      'first_name' => 'required',
      'middle_initial' => 'required',
      'last_name' => 'required',
      'username' => 'required|unique:users,username',
      'email' => 'required|email|unique:users,email',
      'contact_number' => 'required'
    );
    $validation = Validator::make($input, $rules);

    if($validation->passes())
    {
      $password = str_random(6);
      $user = new User;
      $user->first_name = strtoupper(Input::get('first_name'));
      $user->middle_initial = strtoupper(Input::get('middle_initial'));
      $user->last_name = strtoupper(Input::get('last_name'));
      $user->username = Input::get('username');
      $user->email = Input::get('email');
      $user->contact_no = Input::get('contact_number');
      $user->password = $password;
      $user->password_confirmation = $password;
      $user->confirmation_code = md5(uniqid(mt_rand(), true));
      $user->confirmed = 0;
      $user->active = 0;
      $user->save();

      $data['email'] = $user->email;
      $data['first_name'] = $user->first_name;


      // send email about signup
      Mail::send('emails.signup', $data, function($message) use ($data){
        $message->to($data['email'],$data['first_name'])->subject('Account Application');
      });
      $settings = Setting::find(1);

      Mail::send('emails.newuser', $data, function($message) use ($settings){
        $emails = explode(",", $settings->new_user_email);
        $message->to($emails,'Admin')->subject('New Account Application');
      });

      Session::flash('signup_message', 'Sign up successfull, please wait for your account confirmation email.');
      Session::flash('class', 'alert alert-success');
      return Redirect::back();
    }

    return Redirect::to(URL::action('LoginController@index'))
      ->withInput()
      ->withErrors($validation)
      ->with('class', 'alert-danger')
      ->with('signup_message', 'There were validation errors.');
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
    	return View::make('login.forgot');
    }

    public function doforgotpassword(){
    	if (User::forgot_password(Input::get('email'))) {
			return Redirect::action('LoginController@doforgotpassword')
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
        return View::make('login.reset')->with('token', $token);
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

    public function confirm($code)
    {
        if (Confide::confirm($code)) {
            $notice_msg = Lang::get('confide::confide.alerts.confirmation');
            return Redirect::action('LoginController@index')
            ->with('message', $notice_msg)
            ->with('class', 'alert alert-success');
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_confirmation');
            return Redirect::action('LoginController@index')
            ->with('message', $error_msg)
            ->with('class', 'alert alert-danger');
        }
    }

    public function checkSession(){
      return Response::json(['guest' => Auth::guest()]);
    }
}