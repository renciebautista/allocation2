<?php

class ActivityTypeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /activitytype
	 *
	 * @return Response
	 */
	public function index()
	{
		Input::flash();
		$activitytypes = ActivityType::search(Input::get('s'));
		return View::make('activitytype.index', compact('activitytypes'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /activitytype/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /activitytype
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /activitytype/{id}
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
	 * GET /activitytype/{id}/edit
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
	 * PUT /activitytype/{id}
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
	 * DELETE /activitytype/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}