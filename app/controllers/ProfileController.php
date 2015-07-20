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
		return View::make('profile.index');
	}
}