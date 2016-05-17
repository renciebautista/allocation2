<?php

class CustomerRemapController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /customerremap
	 *
	 * @return Response
	 */
	public function index()
	{
		$customers = SplitOldCustomer::getAll();
		return View::make('customerremap.index',compact('customers'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /customerremap/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /customerremap
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /customerremap/{id}
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
	 * GET /customerremap/{id}/edit
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
	 * PUT /customerremap/{id}
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
	 * DELETE /customerremap/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}