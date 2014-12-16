<?php

class ActivityController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /activity
	 *
	 * @return Response
	 */
	public function index()
	{
		$activities = Activity::all();
		return View::make('activity.index',compact('activities'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /activity/create
	 *
	 * @return Response
	 */
	public function create()
	{
		$scope_types = ScopeType::orderBy('scope_name')->lists('scope_name', 'id');
		$cycles = Cycle::orderBy('cycle_name')->lists('cycle_name', 'id');
		$activity_types = ActivityType::orderBy('activity_type')->lists('activity_type', 'id');
		$divisions = Sku::select('division_code', 'division_desc')
			->groupBy('division_code')
			->orderBy('division_desc')->lists('division_desc', 'division_code');

		$objectives = Objective::orderBy('objective')->lists('objective', 'id');
		
		return View::make('activity.create', compact('scope_types', 'cycles',
		 'activity_types', 'divisions' , 'objectives'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /activity
	 *
	 * @return Response
	 */
	public function store()
	{
		$validation = Validator::make(Input::all(), Activity::$rules);

		if($validation->passes())
		{
			DB::transaction(function() {
				$activity = new Activity;
				$activity->circular_name = Input::get('circular_name');
				$activity->scope_type_id = Input::get('scope');
				$activity->cycle_id = Input::get('cycle');
				$activity->activity_type_id = Input::get('activity_type');
				$activity->division_code = Input::get('division');
				$activity->budget_tts = Input::get('budget_tts');
				$activity->budget_pe = Input::get('budget_pe');
				$activity->background = Input::get('background');
				$activity->save();

				$activity_category = array();
				foreach (Input::get('category') as $category){
					$activity_category[] = array('activity_id' => $activity->id, 'category_code' => $category);
				}

				ActivityCategory::insert($activity_category);
			});

			return Redirect::route('activity.index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly created.');
			
		}

		return Redirect::route('activity.create')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 * GET /activity/{id}
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
	 * GET /activity/{id}/edit
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
	 * PUT /activity/{id}
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
	 * DELETE /activity/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}