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
		$planners = User::lists('first_name', 'id');
		$approvers = User::lists('first_name', 'id');

		$activity_types = ActivityType::orderBy('activity_type')->lists('activity_type', 'id');
		$cycles = Cycle::orderBy('cycle_name')->lists('cycle_name', 'id');
		
		$divisions = Sku::select('division_code', 'division_desc')
			->groupBy('division_code')
			->orderBy('division_desc')->lists('division_desc', 'division_code');

		$objectives = Objective::orderBy('objective')->lists('objective', 'id');
		
		return View::make('activity.create', compact('scope_types', 'planners', 'approvers', 'cycles',
		 'activity_types', 'divisions' , 'objectives',  'users'));
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
			$id =  DB::transaction(function()   {
				$activity = new Activity;
				$activity->activity_code = "2015";
				
				$activity->scope_type_id = Input::get('scope');
				
				$activity->activity_type_id = Input::get('activity_type');
				$activity->duration = (Input::get('duration') == '') ? 0 : Input::get('duration');
				$activity->edownload_date = date('Y-m-d',strtotime(Input::get('download_date')));
				$activity->eimplementation_date = date('Y-m-d',strtotime(Input::get('implementation_date')));
				$activity->circular_name = strtoupper(Input::get('activity_title'));
				$activity->division_code = Input::get('division');
				$activity->background = Input::get('background');
				$activity->save();
				return $activity->id;

				// $activity_category = array();
				// foreach (Input::get('category') as $category){
				// 	$activity_category[] = array('activity_id' => $activity->id, 'category_code' => $category);
				// }
				// ActivityCategory::insert($activity_category);

				// $activity_brand = array();
				// foreach (Input::get('brand') as $brand){
				// 	$activity_brand[] = array('activity_id' => $activity->id, 'brand_code' => $brand);
				// }
				// ActivityBrand::insert($activity_brand);

				// $activity_objective = array();
				// foreach (Input::get('objective') as $objective){
				// 	$activity_objective[] = array('activity_id' => $activity->id, 'objective_id' => $objective);
				// }
				// ActivityObjective::insert($activity_objective);

				// $channels = Input::get('channel');
				// if(!empty($channels)){
				// 	$activity_channels = array();
				// 	foreach ($channels as $channel){
				// 		$activity_channels[] = array('activity_id' => $activity->id, 'channel_id' => $channel);
				// 	}
				// 	if(!empty($activity_channels)){
				// 		ActivityChannel::insert($activity_channels);
				// 	}
				// }


				// $_customers = Input::get('customers');
				// if(!empty($_customers)){
				// 	$customers = explode(",", $_customers);
				// 	if(!empty($customers)){
				// 		$activity_customers = array();
				// 		foreach ($customers as $customer_node){
				// 			$activity_customers[] = array('activity_id' => $activity->id, 'customer_node' => trim($customer_node));
				// 		}
				// 		ActivityCustomer::insert($activity_customers);
				// 	}
				// }
				
			});

			return Redirect::route('activity.edit',$id)
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
		$activity = Activity::find($id);

		$scope_types = ScopeType::orderBy('scope_name')->lists('scope_name', 'id');
		$planners = User::lists('first_name', 'id');
		$approvers = User::lists('first_name', 'id');

		$activity_types = ActivityType::orderBy('activity_type')->lists('activity_type', 'id');
		$cycles = Cycle::orderBy('cycle_name')->lists('cycle_name', 'id');
		
		$divisions = Sku::select('division_code', 'division_desc')
			->groupBy('division_code')
			->orderBy('division_desc')->lists('division_desc', 'division_code');

		$objectives = Objective::orderBy('objective')->lists('objective', 'id');

		$budgets = ActivityBudget::with('budgettype')
			->where('activity_id', $id)
			->get();

		return View::make('activity.edit', compact('activity', 'scope_types', 'planners', 'approvers', 'cycles',
		 'activity_types', 'divisions' , 'objectives',  'users', 'budgets'));
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
		if(Request::ajax()){
			$validation = Validator::make(Input::all(), Activity::$rules);
			$arr['success'] = 0;
			if($validation->passes())
			{
				DB::transaction(function() use ($id)  {
					$activity = Activity::find($id);
					$activity->activity_code = "2015";
					
					$activity->scope_type_id = Input::get('scope');
					
					$activity->activity_type_id = Input::get('activity_type');
					$activity->duration = (Input::get('duration') == '') ? 0 : Input::get('duration');
					$activity->edownload_date = date('Y-m-d',strtotime(Input::get('download_date')));
					$activity->eimplementation_date = date('Y-m-d',strtotime(Input::get('implementation_date')));
					$activity->circular_name = strtoupper(Input::get('activity_title'));
					$activity->division_code = Input::get('division');
					$activity->background = Input::get('background');
					$activity->update();
				});
				$arr['success'] = 1;
				
			}

			$arr['id'] = $id;
			return json_encode($arr);
		}
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

	public function addbudget($id){
		if(Request::ajax()){
			$budget = new ActivityBudget;

			$budget->activity_id = $id;
			$budget->budget_type_id = Input::get('io_ttstype');
			$budget->io_number = strtoupper(Input::get('io_no'));
			$budget->amount = str_replace(",", '', Input::get('io_amount'));
			$budget->start_date = date('Y-m-d',strtotime(Input::get('io_startdate')));
			$budget->end_date = date('Y-m-d',strtotime(Input::get('io_enddate')));
			$budget->remarks = Input::get('io_remarks');
			$budget->save();

			$arr = Input::all();

			$budget_type = BudgetType::find($budget->budget_type_id);
			$arr['id'] = $budget->id;
			$arr['io_no'] = strtoupper(Input::get('io_no'));
			$arr['io_ttstype'] = $budget_type->budget_type;
			return json_encode($arr);
		}
	}

	public function deletebudget(){
		if(Request::ajax()){
			$id = Input::get('d_id');
			$budget = ActivityBudget::find($id);
			$budget->delete();

			$arr['success'] = 1;
			$arr['id'] = $id;
			return json_encode($arr);
		}
	}

}