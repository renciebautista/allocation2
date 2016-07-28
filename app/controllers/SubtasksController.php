<?php

class SubtasksController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /subtasks
	 *
	 * @return Response
	 */
	public function index()
	{
		Input::flash();
		$tasks = Task::getLists();
		$subtasks = SubTask::search(Input::get('task'),Input::get('search'));
		return View::make('subtasks.index',compact('subtasks', 'tasks'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /subtasks/create
	 *
	 * @return Response
	 */
	public function create()
	{
		$tasks = Task::getLists();
		return View::make('subtasks.create', compact('tasks'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /subtasks
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Subtask::$rules);

		if($validation->passes())
		{
			DB::transaction(function()
			{
				$subtask = new Subtask;
				$subtask->task_id = Input::get('task');
				$subtask->sub_task = strtoupper(Input::get('subtask'));
				$subtask->save();
			});
			return Redirect::action('SubtasksController@index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly created.');
		}

		return Redirect::action('SubtasksController@create')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 * GET /subtasks/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return Redirect::route('subtasks.edit',$id);
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /subtasks/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$tasks = Task::getLists();
		$subtask = SubTask::findOrFail($id);
		return View::make('subtasks.edit', compact('tasks', 'subtask'));
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /subtasks/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::all();
		$subtask = SubTask::findOrFail($id);

		$rules = array(
			'task' => 'required|integer|min:1,'.$id,
			'subtask' => 'required'
			
		);

		$validation = Validator::make($input, $rules);

		if($validation->passes())
		{
			$subtask->task_id = Input::get('task');
			$subtask->sub_task = strtoupper(Input::get('subtask'));
			$subtask->update();

			return Redirect::action('SubtasksController@index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly updated.');
			
		}

		return Redirect::action('SubtasksController@edit',$user->id)
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /subtasks/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}