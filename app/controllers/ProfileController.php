<?php

class ProfileController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /profile
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = User::find(Auth::id());
		return View::make('profile.index',compact('user'));
	}

	public function update(){
		$input = Input::all();
		$user = User::findOrFail(Auth::id());

		$rules = array(
			'email' => 'required|email|unique:users,email,'.(Auth::id()),
			'first_name' => 'required',
			'last_name' => 'required'
		);

		$validation = Validator::make($input, $rules);

		if($validation->passes())
		{
			DB::beginTransaction();
			try {

				$user->first_name = strtoupper(Input::get('first_name'));
				$user->middle_initial = strtoupper(Input::get('middle_name'));
				$user->last_name = strtoupper(Input::get('last_name'));
				$user->email = Input::get('email');
				$user->contact_no = Input::get('contact_no');

				$user->update();

				DB::commit();

				return Redirect::action('ProfileController@index')
					->with('class', 'alert-success')
					->with('message', 'Record successfuly updated.');

			} catch (\Exception $e) {
				DB::rollback();
				return Redirect::action('ProfileController@index')
				->withErrors($validation)
				->with('class', 'alert-danger')
				->with('message', 'There were validation errors.');
			}		
			
		}

		return Redirect::action('ProfileController@index')
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	public function changepassword(){
		return View::make('profile.changepassword');
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
				$user->last_update = date('Y-m-d H:i:s');
				$user->update();
				DB::commit();

				return Redirect::action('ProfileController@changepassword')
				->with('class', 'alert-success')
				->with('message', 'Your password has beeed successfuly updated.');

			} catch (\Exception $e) {
				DB::rollback();
				return Redirect::action('ProfileController@changepassword')
				->withInput(Input::except(array('password','password_confirmation')))
				->withErrors($validator)
				->with('class', 'alert-danger')
				->with('message', 'There were validation errors.');
			}		
			
		}

		return Redirect::action('ProfileController@changepassword')
			->withInput(Input::except(array('password','password_confirmation')))
			->withErrors($validator)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}
}