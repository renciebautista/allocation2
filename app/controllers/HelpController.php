<?php

class HelpController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /help
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('help.index');
	}



}