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
		$status = array('1' => 'ACTIVE','2' => 'IN-ACTIVE');
		$groups = Role::getLists();
		$users = User::search(Input::get('status'),Input::get('group'),Input::get('search'));
		return View::make('users.index',compact('users', 'status', 'groups'));
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
				$user->contact_no = Input::get('contact_no');
				$user->password = Input::get('password');
				$user->password_confirmation = Input::get('password_confirmation');
				$user->confirmation_code = md5(uniqid(mt_rand(), true));
				$user->confirmed = 1;
				$user->active = (Input::has('active')) ? 1 : 0;
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

	public function edit($id){
		// Session::flash('url',Request::server('HTTP_REFERER'));
		// $url = Session::get('url');
		$user = User::findOrFail($id);
		$groups = Role::orderBy('name')->lists('name', 'id');
		return View::make('users.edit',compact('groups','user'));
	}

	public function show($id){
		return Redirect::route('users.edit',$id);
	}

	public function update($id){
		// Session::flash('url',Request::server('HTTP_REFERER'));
		$input = Input::all();
		$user = User::findOrFail($id);

		$rules = array(
	    	'username' => 'required|unique:users,username,'.$id,
			'email' => 'required|email|unique:users,email,'.$id,
			'first_name' => 'required',
			'last_name' => 'required',
			'group' => 'required|integer|min:1'
		);

		$validation = Validator::make($input, $rules);

		if($validation->passes())
		{
			DB::beginTransaction();
			try {

				$user->first_name = strtoupper(Input::get('first_name'));
				$user->middle_initial = strtoupper(Input::get('middle_name'));
				$user->last_name = strtoupper(Input::get('last_name'));
				$user->username = Input::get('username');
				$user->email = Input::get('email');
				$user->contact_no = Input::get('contact_no');
				$user->active = (Input::has('active')) ? 1 : 0;
				$user->update();

				$user->detachRoles($user->roles);

				$role = Role::find(Input::get('group'));

				$user->roles()->attach($role->id); // id only

				DB::commit();

				return Redirect::action('UsersController@index')
					->with('class', 'alert-success')
					->with('message', 'Record successfuly updated.');

			} catch (\Exception $e) {
				DB::rollback();
				return Redirect::action('UsersController@edit',$user->id)
				->withInput(Input::except(array('password','password_confirmation')))
				->withErrors($validation)
				->with('class', 'alert-danger')
				->with('message', 'There were validation errors.');
			 //    $class = 'alert-danger';
				// $message = 'Cannot duplicate activity.';

				// return Redirect::to(URL::action('ActivityController@index'))
				// ->with('class', $class )
				// ->with('message', $message);
				// something went wrong
			}		
			
		}

		return Redirect::action('UsersController@edit',$user->id)
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

	public function destroy($id){
		Session::flash('url',Request::server('HTTP_REFERER'));  

		$user = User::findOrFail($id);

		$errors = array();
		
		if(Activity::withActivities($id)){
			$errors[] = "There is an existing activity created.";
		}

		if(ActivityPlanner::withActivities($id)){
			$errors[] = "There is an existing activity with approval.";
		}

		if(ActivityApprover::withActivities($id)){
			$errors[] = "There is an existing activity with approval.";
		}

		if(count($errors) > 0){
			return Redirect::to(Session::get('url'))
				->withErrors($errors)
				->with('class', 'alert-danger')
				->with('message', 'Unable to delete user.');
		}else{
			$user->delete();
			return Redirect::to(Session::get('url'))
				->with('class', 'alert-success')
				->with('message', $user->last_name.', '.$user->first_name.' user is successfuly deleted.');
		}

		
	}

	public function changepassword(){
		return View::make('users.changepassword');
	}

	public function updatepassword(){
		Validator::extend('passcheck', function($attribute, $value, $parameters) {
		    return Hash::check($value, Auth::user()->password); // Works for any form!
		});

		$messages = array(
		    'passcheck' => 'Your old password was incorrect',
		);

		$validator = Validator::make(Input::all(), [
		    'old_password'  => 'passcheck',
		   	'password' => 'required|min:6|confirmed',
			'password_confirmation' => 'same:password'
		    // more rules ...
		], $messages);

		if($validator->passes())
		{
			DB::beginTransaction();
			try {
				$user = User::find(Auth::id());
				$user->password = Input::get('password');
				$user->password_confirmation = Input::get('password_confirmation');
				$user->update();
				DB::commit();

				return Redirect::action('UsersController@changepassword')
				->with('class', 'alert-success')
				->with('message', 'Your password has beeed successfuly updated.');

			} catch (\Exception $e) {
				DB::rollback();
				return Redirect::action('UsersController@changepassword')
				->withInput(Input::except(array('password','password_confirmation')))
				->withErrors($validator)
				->with('class', 'alert-danger')
				->with('message', 'There were validation errors.');
			}		
			
		}

		return Redirect::action('UsersController@changepassword')
			->withInput(Input::except(array('password','password_confirmation')))
			->withErrors($validator)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}
}
