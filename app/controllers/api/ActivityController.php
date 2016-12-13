<?php
namespace Api;
class ActivityController extends \BaseController {


	public function index()
	{
		$activities = \Activity::getReleased();
		$data['events'] = $activities;
		return \Response::json($activities,200);
	}



}