<?php



/**
 * UsersController Class
 *
 * Implements actions regarding user management
 */
class UsersController extends Controller
{
	/**
	 * Display a listing of the resource.
	 * GET /users
	 *
	 * @return Response
	 */
	public function index()
	{
		Input::flash();
		$users = User::search(Input::get('status'),Input::get('s'));
		return View::make('users.index',compact('users'));
	}

	/**
	 * Displays the form for account creation
	 *
	 * @return  Illuminate\Http\Response
	 */
	public function create()
	{
		$groups = Role::orderBy('name')->lists('name', 'id');
		return View::make('users.create',compact('groups'));
	}

	/**
	 * Stores new account
	 *
	 * @return  Illuminate\Http\Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, User::$rules);

		if($validation->passes())
		{
			DB::transaction(function()
			{
				$user = new User;
				$user->first_name = strtoupper(Input::get('first_name'));
				$user->middle_initial = strtoupper(Input::get('middle_name'));
				$user->last_name = strtoupper(Input::get('last_name'));
				$user->username = Input::get('username');
				$user->email = Input::get('email');
				$user->password = Input::get('password');
				$user->password_confirmation = Input::get('password_confirmation');
				$user->confirmation_code = md5(uniqid(mt_rand(), true));
				$user->confirmed = 1;
				$user->active = 1;
				$user->save();

				$role = Role::find(Input::get('group'));

				$user->roles()->attach($role->id); // id only
			});
			return Redirect::action('UsersController@index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly created.');
		}

		return Redirect::action('UsersController@create')
			->withInput(Input::except(array('password','password_confirmation')))
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Displays the login form
	 *
	 * @return  Illuminate\Http\Response
	 */
	public function login()
	{
		if (Confide::user()) {
			return Redirect::to('/');
		} else {
			return View::make(Config::get('confide::login_form'));
		}
	}

	/**
	 * Attempt to do login
	 *
	 * @return  Illuminate\Http\Response
	 */
	public function doLogin()
	{
		$repo = App::make('UserRepository');
		$input = Input::all();

		if ($repo->login($input)) {
			return Redirect::intended('/');
		} else {
			if ($repo->isThrottled($input)) {
				$err_msg = Lang::get('confide::confide.alerts.too_many_attempts');
			} elseif ($repo->existsButNotConfirmed($input)) {
				$err_msg = Lang::get('confide::confide.alerts.not_confirmed');
			} else {
				$err_msg = Lang::get('confide::confide.alerts.wrong_credentials');
			}

			return Redirect::action('UsersController@login')
				->withInput(Input::except('password'))
				->with('error', $err_msg);
		}
	}

	/**
	 * Attempt to confirm account with code
	 *
	 * @param  string $code
	 *
	 * @return  Illuminate\Http\Response
	 */
	public function confirm($code)
	{
		if (Confide::confirm($code)) {
			$notice_msg = Lang::get('confide::confide.alerts.confirmation');
			return Redirect::action('UsersController@login')
				->with('notice', $notice_msg);
		} else {
			$error_msg = Lang::get('confide::confide.alerts.wrong_confirmation');
			return Redirect::action('UsersController@login')
				->with('error', $error_msg);
		}
	}

	/**
	 * Displays the forgot password form
	 *
	 * @return  Illuminate\Http\Response
	 */
	public function forgotPassword()
	{
		return View::make(Config::get('confide::forgot_password_form'));
	}

	/**
	 * Attempt to send change password link to the given email
	 *
	 * @return  Illuminate\Http\Response
	 */
	public function doForgotPassword()
	{
		if (Confide::forgotPassword(Input::get('email'))) {
			$notice_msg = Lang::get('confide::confide.alerts.password_forgot');
			return Redirect::action('UsersController@login')
				->with('notice', $notice_msg);
		} else {
			$error_msg = Lang::get('confide::confide.alerts.wrong_password_forgot');
			return Redirect::action('UsersController@doForgotPassword')
				->withInput()
				->with('error', $error_msg);
		}
	}

	/**
	 * Shows the change password form with the given token
	 *
	 * @param  string $token
	 *
	 * @return  Illuminate\Http\Response
	 */
	public function resetPassword($token)
	{
		return View::make(Config::get('confide::reset_password_form'))
				->with('token', $token);
	}

	/**
	 * Attempt change password of the user
	 *
	 * @return  Illuminate\Http\Response
	 */
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
			$notice_msg = Lang::get('confide::confide.alerts.password_reset');
			return Redirect::action('UsersController@login')
				->with('notice', $notice_msg);
		} else {
			$error_msg = Lang::get('confide::confide.alerts.wrong_password_reset');
			return Redirect::action('UsersController@resetPassword', array('token'=>$input['token']))
				->withInput()
				->with('error', $error_msg);
		}
	}

	/**
	 * Log the user out of the application.
	 *
	 * @return  Illuminate\Http\Response
	 */
	public function logout()
	{
		Confide::logout();

		return Redirect::to('/');
	}
}