<?php

use Rencie\Cpm\CpmActivity;
use Rencie\Cpm\Cpm;
class DashboardController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /dashboard
	 *
	 * @return Response
	 */
	public function index()
	{
		// if(Auth::user()->hasRole("FIELD SALES")){
		// 	Input::flash();
		// 	$cycles = Cycle::getLists();
		// 	$types = ActivityType::getLists();
		// 	$scopes = ScopeType::getLists();
		// 	$activities = Activity::searchField(Input::get('cy'),Input::get('ty'),Input::get('sc'),Input::get('title'));
		// 	return View::make('dashboard.field',compact('activities', 'cycles','types','scopes'));
		// }

		// if(Auth::user()->hasRole("ADMINISTRATOR")){
		// 	return View::make('dashboard.admin');
		// }


		$ongoings = Activity::summary(8,'ongoing');
		// Helper::print_array($ongoings);
		$upcomings = Activity::summary(8,'nextmonth');
		// Helper::print_array($upcomings);
		$lastmonths = Activity::summary(8,'lastmonth');
		// Helper::print_array($lastmonths);
		return View::make('dashboard.index',compact('ongoings', 'upcomings', 'lastmonths'));
	}

	
	public function filters(){
		$divisions = Sku::divisions();
		return View::make('dashboard.filters', compact('divisions'));
	}

}