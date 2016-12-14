<?php
namespace Api;
class ActivityController extends \BaseController {


	public function index()
	{
		$activities = \Activity::getReleased(\Input::all());
		$data['events'] = $activities;
		return \Response::json($activities,200);
	}



}