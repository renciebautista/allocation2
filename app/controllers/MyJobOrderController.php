<?php

class MyJobOrderController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /myjoborder
	 *
	 * @return Response
	 */
	public function index()
	{
		$joborders = Joborder::myJoborder(Auth::user());
		$statuses = JoborderStatus::getLists();
		return View::make('myjoborders.index',compact('joborders', 'statuses'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /myjoborder/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /myjoborder
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /myjoborder/{id}
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
	 * GET /myjoborder/{id}/edit
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
	 * PUT /myjoborder/{id}
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
	 * DELETE /myjoborder/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}