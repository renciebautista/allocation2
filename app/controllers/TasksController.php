<?php

class TasksController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /tasks
	 *
	 * @return Response
	 */
	public function index()
	{
		Input::flash();
		$tasks = Task::search(Input::get('s'));
		return View::make('tasks.index',compact('tasks'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /tasks/create
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('tasks.create');
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /tasks
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();

		$validation = Validator::make($input, Task::$rules);

		if($validation->passes())
		{
			$task = new Task();
			$task->task = strtoupper(Input::get('task'));
			$task->save();

			return Redirect::route('tasks.index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly created.');
		}

		return Redirect::route('tasks.create')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 * GET /tasks/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return Redirect::route('tasks.edit',$id);
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /tasks/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$task = Task::findOrFail($id);
		if (is_null($task))
		{
			return Redirect::route('tasks.index')
				->with('class', 'alert-danger')
				->with('message', 'Record does not exist.');
		}
		return View::make('tasks.edit', compact('task'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /tasks/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::all();
		$rules = array(
	        'task' => 'required|between:4,128|unique:tasks,task,'.$id
	    );
		$validation = Validator::make($input,$rules);
		if ($validation->passes())
		{
			$task = Task::findOrFail($id);
			if (is_null($task))
			{
				return Redirect::route('tasks.index')
					->with('class', 'alert-danger')
					->with('message', 'Record does not exist.');
			}
			$task->task = strtoupper(Input::get('task'));
			$task->save();
			return Redirect::route('tasks.index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly updated.');
		}

		return Redirect::route('tasks.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /tasks/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}