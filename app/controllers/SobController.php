<?php

class SobController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /sob
	 *
	 * @return Response
	 */
	public function index()
	{
		$cycles = Activity::select('cycle_desc','cycle_id')
			->groupBy('cycle_id')
			->orderBy('cycle_desc')
			->lists('cycle_desc', 'cycle_id');
		return View::make('sob.index', compact('cycles'));
	}

	public function generate(){
		// $soballocations = 
	}

}