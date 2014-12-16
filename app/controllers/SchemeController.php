<?php

class SchemeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /scheme
	 *
	 * @return Response
	 */
	public function index($id)
	{
		return View::make('scheme.index', compact('id'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /scheme/create
	 *
	 * @return Response
	 */
	public function create($id)
	{
		return View::make('scheme.create', compact('id'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /scheme
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /scheme/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$allocations = Allocation::customers();
		return View::make('scheme.show', compact('allocations'));
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /scheme/{id}/edit
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
	 * PUT /scheme/{id}
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
	 * DELETE /scheme/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}