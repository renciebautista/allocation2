<?php

class SettingsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /settings
	 *
	 * @return Response
	 */
	public function index()
	{
		$settings = Setting::find(1);
		return View::make('settings.index',compact('settings'));
	}

	public function update()
	{
		$settings = Setting::find(1);
		$settings->new_user_email = Input::get('new_user_email');
		$settings->change_password = Input::get('change_password');
		$settings->pasword_expiry = str_replace(",", "", Input::get('pasword_expiry'));
		$settings->customized_preapprover = Input::get('customized_preapprover');
		$settings->update();

		return Redirect::action('SettingsController@index')
				->with('class', 'alert-success')
				->with('message', 'Settings successfuly updated.');
	}

}