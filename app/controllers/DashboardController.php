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
		if(Auth::user()->hasRole("FIELD SALES")){
			Input::flash();
			$cycles = Cycle::getLists();
			$types = ActivityType::getLists();
			$activities = Activity::searchField(Input::get('cy'),Input::get('ty'),Input::get('title'));
			return View::make('dashboard.field',compact('activities', 'cycles','types'));
		}

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

	/**
	 * Show the form for creating a new resource.
	 * GET /dashboard/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /dashboard
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /dashboard/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /dashboard/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /dashboard/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /dashboard/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}