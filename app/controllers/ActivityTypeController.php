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
		$budget_types = BudgetType::all();
		return View::make('activitytype.create', compact('budget_types'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /activitytype
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();

		$validation = Validator::make($input, ActivityType::$rules);

		if($validation->passes())
		{
			DB::transaction(function(){
				$activitytype = new ActivityType();
				$activitytype->activity_type = strtoupper(Input::get('activity_type'));
				$activitytype->uom = strtoupper(Input::get('uom'));
				$activitytype->prefix = strtoupper(Input::get('prefix'));
				$activitytype->with_scheme = (Input::has('with_scheme')) ? 1 : 0;
				$activitytype->with_msource = (Input::has('with_msource')) ? 1 : 0;
				$activitytype->with_sob = (Input::has('with_sob')) ? 1 : 0;
				$activitytype->save();

				// add required
				if (Input::has('budget_types'))
				{
				   	$required_budget = array();
					foreach (Input::get('budget_types') as $budget_type) {
						$required_budget[] = array('activity_type_id' => $activitytype->id, 'budget_type_id' => $budget_type);
					}
					ActivityTypeBudgetRequired::insert($required_budget);
				}
			});



			return Redirect::route('activitytype.index')
				->with('class', 'alert-success')
				->with('message', 'Record successfuly created.');
		}

		return Redirect::route('activitytype.create')
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
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
		$activitytype = ActivityType::findOrFail($id);
		$required = ActivityTypeBudgetRequired::required($id);
		$budget_types = BudgetType::all();
		return View::make('activitytype.edit', compact('activitytype', 'budget_types', 'required'));
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
		$input = Input::all();

		$rules = array(
	        'activity_type' => 'required|between:1,128|unique:activity_types,activity_type,'.$id,
	        'uom' => 'required'
	    );

		$validation = Validator::make($input, $rules);

		if($validation->passes())
		{
			DB::transaction(function() use ($id){
				$activitytype = ActivityType::findOrFail($id);
				$activitytype->activity_type = strtoupper(Input::get('activity_type'));
				$activitytype->uom = strtoupper(Input::get('uom'));
				$activitytype->prefix = strtoupper(Input::get('prefix'));
				$activitytype->with_scheme = (Input::has('with_scheme')) ? 1 : 0;
				$activitytype->with_msource = (Input::has('with_msource')) ? 1 : 0;
				$activitytype->with_sob = (Input::has('with_sob')) ? 1 : 0;
				$activitytype->update();

				// add required
				ActivityTypeBudgetRequired::where('activity_type_id',$activitytype->id)->delete();
				if (Input::has('budget_types'))
				{
				   	$required_budget = array();
					foreach (Input::get('budget_types') as $budget_type) {
						$required_budget[] = array('activity_type_id' => $activitytype->id, 'budget_type_id' => $budget_type);
					}
					ActivityTypeBudgetRequired::insert($required_budget);
				}
			});



			return Redirect::route('activitytype.index')
				->with('class', 'alert-success')
				->with('message', 'Activity Type successfuly updated.');
		}

		return Redirect::route('activitytype.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('class', 'alert-danger')
			->with('message', 'There were validation errors.');
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