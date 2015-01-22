<?php

class GroupController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /group
	 *
	 * @return Response
	 */
	public function index()
	{
		Input::flash();
		$groups = Role::search(Input::get('s'));
		return View::make('group.index',compact('groups'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /group/create
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('group.create');
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /group
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();

		$validation = Validator::make($input, Role::$rules);

		if($validation->passes())
		{
			$role = new Role();
			$role->name = strtoupper(Input::get('name'));
			$role->save();

			return Redirect::route('group.index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly created.');
		}

		return Redirect::route('group.create')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 * GET /group/{id}
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
	 * GET /group/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$group = Role::find($id);
		if (is_null($group))
		{
			return Redirect::route('group.index')
				->with('class', 'alert-danger')
				->with('message', 'Record does not exist.');
		}
		return View::make('group.edit', compact('group'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /group/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::all();
		$rules = array(
	        'name' => 'required|between:4,128|unique:roles,name,'.$id
	    );
		$validation = Validator::make($input,$rules);
		if ($validation->passes())
		{
			$group = Role::find($id);
			if (is_null($group))
			{
				return Redirect::route('group.index')
					->with('class', 'alert-danger')
					->with('message', 'Record does not exist.');
			}
			$group->name = strtoupper(Input::get('name'));
			$group->save();
			return Redirect::route('group.index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly updated.');
		}

		return Redirect::route('group.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /group/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$role = Role::find($id)->delete();
		if (is_null($role))
		{
			$class = 'alert-danger';
			$message = 'Record does not exist.';
		}else{
			$class = 'alert-success';
			$message = 'Record successfully deleted.';
		}
		return Redirect::route('group.index')
				->with('class', $class )
				->with('message', $message);
	}

}