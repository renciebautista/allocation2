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
		Input::flash();
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
		$channels = Channel::orderBy('channel_name')->lists('channel_name', 'id');
		
		return View::make('activity.create', compact('scope_types', 'cycles',
		 'activity_types', 'divisions' , 'objectives', 'channels'));
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /activity
	 *
	 * @return Response
	 */
	public function store()
	{

		// dd(Input::all());
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

				$activity_brand = array();
				foreach (Input::get('brand') as $brand){
					$activity_brand[] = array('activity_id' => $activity->id, 'brand_code' => $brand);
				}
				ActivityBrand::insert($activity_brand);

				$activity_objective = array();
				foreach (Input::get('objective') as $objective){
					$activity_objective[] = array('activity_id' => $activity->id, 'objective_id' => $objective);
				}
				ActivityObjective::insert($activity_objective);

				$channels = Input::get('channel');
				if(!empty($channels)){
					$activity_channels = array();
					foreach ($channels as $channel){
						$activity_channels[] = array('activity_id' => $activity->id, 'channel_id' => $channel);
					}
					if(!empty($activity_channels)){
						ActivityChannel::insert($activity_channels);
					}
				}


				$_customers = Input::get('customers');
				if(!empty($_customers)){
					$customers = explode(",", $_customers);
					if(!empty($customers)){
						$activity_customers = array();
						foreach ($customers as $customer_node){
							$activity_customers[] = array('activity_id' => $activity->id, 'customer_node' => trim($customer_node));
						}
						ActivityCustomer::insert($activity_customers);
					}
				}
				
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