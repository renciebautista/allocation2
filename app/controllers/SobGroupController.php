<?php

class SobGroupController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /sobgroup
	 *
	 * @return Response
	 */
	public function index()
	{
		Input::flash();
		$groups = SobGroup::search(Input::all());
		return View::make('sobgroup.index',compact('groups'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /sobgroup/create
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('sobgroup.create');
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /sobgroup
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();

		$validation = Validator::make($input, SobGroup::$rules);

		if($validation->passes())
		{
			$group = new SobGroup();
			$group->sobgroup = strtoupper(Input::get('sobgroup'));
			$group->save();

			return Redirect::route('sobgroup.index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly created.');
		}

		return Redirect::route('sobgroup.create')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 * GET /sobgroup/{id}
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
	 * GET /sobgroup/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$sobgroup = SobGroup::findOrFail($id);
		return View::make('sobgroup.edit',compact('sobgroup'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /sobgroup/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$sobgroup = SobGroup::findOrFail($id);
		$input = Input::all();
		$validation = Validator::make($input, SobGroup::$rules);
		if ($validation->passes())
		{
			$sobgroup->sobgroup = strtoupper(Input::get('sobgroup'));
			$sobgroup->save();


			return Redirect::route('sobgroup.index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly created.');
		}

		return Redirect::route('sobgroup.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /sobgroup/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$sobgroup = SobGroup::findOrFail($id);
		
		$sobgroup->delete();

		return Redirect::route('sobgroup.index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly deleted.');
	}

}