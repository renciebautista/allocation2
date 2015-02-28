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
		$activities = Activity::where('created_by', '=', Auth::id())->get();
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
		$planners = User::isRole('PMOG PLANNER')->lists('first_name', 'id');
		$approvers = User::isRole('CD OPS APPROVER')->lists('first_name', 'id');
		$involves = Pricelist::orderBy('sap_desc')->lists('sap_desc', 'sap_code');

		$activity_types = ActivityType::orderBy('activity_type')->lists('activity_type', 'id');
		$cycles = Cycle::orderBy('cycle_name')->lists('cycle_name', 'id');
		
		$divisions = Sku::select('division_code', 'division_desc')
			->groupBy('division_code')
			->orderBy('division_desc')->lists('division_desc', 'division_code');

		$objectives = Objective::orderBy('objective')->lists('objective', 'id');
		
		return View::make('activity.create', compact('scope_types', 'planners', 'approvers', 'cycles',
		 'activity_types', 'divisions' , 'objectives',  'users', 'involves'));
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
				$scope_id = Input::get('scope');
				$cycle_id = Input::get('cycle');
				$activity_type_id = Input::get('activity_type');
				$division_code = Input::get('division');

				$activity = new Activity;
				$activity->created_by = Auth::id();
				
				$activity->scope_type_id = $scope_id;
				$activity->cycle_id = $cycle_id;
				$activity->activity_type_id = $activity_type_id;
				$activity->duration = (Input::get('lead_time') == '') ? 0 : Input::get('lead_time');
				$activity->edownload_date = date('Y-m-d',strtotime(Input::get('download_date')));
				$activity->eimplementation_date = date('Y-m-d',strtotime(Input::get('implementation_date')));
				$activity->circular_name = strtoupper(Input::get('activity_title'));
				$activity->division_code = $division_code;
				$activity->background = Input::get('background');
				$activity->status_id = 1;
				$activity->save();

				$scope = ScopeType::find($scope_id);
				$cycle = Cycle::find($cycle_id);
				$activity_type = ActivityType::find($activity_type_id);
				$division = Sku::select('division_code', 'division_desc')
									->where('division_code',$division_code)
									->first();

				
				$code = date('Y').$activity->id;
				if(!empty($scope)){
					$code .= '-'.$scope->scope_name;
				}
				if(!empty($cycle)){
					$code .= '_'.$cycle->cycle_name;
				}
				if(!empty($activity_type)){
					$code .= '_'.$activity_type->activity_type;
				}
				if(!empty($division)){
					$code .= '_'.$division->division_desc;
				}
				// // $activity->activity_code = 

				$activity->activity_code =  $code;
				$activity->update();
				// echo '<pre>';
				// print_r($code);
				// echo '</pre>';

				// add planner
				if (Input::has('planner'))
				{
					if(Input::get('planner') > 0){
						ActivityPlanner::insert(array('activity_id' => $activity->id, 'user_id' => Input::get('planner')));
					}
				}
				
				// add approver
				if (Input::has('approver'))
				{
				   	$activity_approver = array();
					foreach (Input::get('approver') as $approver) {
						$activity_approver[] = array('activity_id' => $activity->id, 'user_id' => $approver);
					}
					ActivityApprover::insert($activity_approver);
				}

				// add category
				if (Input::has('category'))
				{
					$activity_category = array();
					foreach (Input::get('category') as $category){
						$activity_category[] = array('activity_id' => $activity->id, 'category_code' => $category);
					}
					ActivityCategory::insert($activity_category);
				}

				// add brand
				if (Input::has('brand'))
				{
					$activity_brand = array();
					foreach (Input::get('brand') as $brand){
						$activity_brand[] = array('activity_id' => $activity->id, 'brand_code' => $brand);
					}
					ActivityBrand::insert($activity_brand);
				}

				// add skus involve
				if (Input::has('involve'))
				{
					$activity_skuinvoled = array();
					foreach (Input::get('involve') as $sku){
						$activity_skuinvoled[] = array('activity_id' => $activity->id, 'sap_code' => $sku);
					}
					ActivitySku::insert($activity_skuinvoled);
				}
				
				// add objective
				if (Input::has('objective'))
				{
					$activity_objective = array();
					foreach (Input::get('objective') as $objective){
						$activity_objective[] = array('activity_id' => $activity->id, 'objective_id' => $objective);
					}
					ActivityObjective::insert($activity_objective);
				}
				

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
				return $activity->id;
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
		if(($activity->status_id == 1) || ($activity->status_id == 3)){
			$sel_planner = ActivityPlanner::where('activity_id',$id)
				->first();
			$sel_approver = ActivityApprover::getList($id);
			$sel_skus = ActivitySku::getList($id);
			$sel_objectives = ActivityObjective::getList($id);

			$scope_types = ScopeType::orderBy('scope_name')->lists('scope_name', 'id');
			$planners = User::isRole('PMOG PLANNER')->lists('first_name', 'id');
			$approvers = User::isRole('CD OPS APPROVER')->lists('first_name', 'id');
			$involves = Pricelist::orderBy('sap_desc')->lists('sap_desc', 'sap_code');

			$activity_types = ActivityType::orderBy('activity_type')->lists('activity_type', 'id');
			$cycles = Cycle::orderBy('cycle_name')->lists('cycle_name', 'id');
			
			$divisions = Sku::select('division_code', 'division_desc')
				->groupBy('division_code')
				->orderBy('division_desc')->lists('division_desc', 'division_code');

			$objectives = Objective::orderBy('objective')->lists('objective', 'id');

			$budgets = ActivityBudget::with('budgettype')
				->where('activity_id', $id)
				->get();

			$nobudgets = ActivityNobudget::with('budgettype')
				->where('activity_id', $id)
				->get();

			// echo '<pre>';
			// print_r($sel_approver);
			// echo '</pre>';

			return View::make('activity.edit', compact('activity', 'scope_types', 'planners', 'approvers', 'cycles',
			 'activity_types', 'divisions' , 'objectives',  'users', 'budgets', 'nobudgets', 'sel_planner','sel_approver',
			 'involves', 'sel_skus', 'sel_objectives'));
		}

		if($activity->status_id == 2){
			$sel_planner = ActivityPlanner::where('activity_id',$id)
				->first();
			$sel_approver = ActivityApprover::getList($id);

			$scope_types = ScopeType::orderBy('scope_name')->lists('scope_name', 'id');
			$planners = User::isRole('PMOG PLANNER')->lists('first_name', 'id');
			$approvers = User::isRole('CD OPS APPROVER')->lists('first_name', 'id');

			$activity_types = ActivityType::orderBy('activity_type')->lists('activity_type', 'id');
			$cycles = Cycle::orderBy('cycle_name')->lists('cycle_name', 'id');
			
			$divisions = Sku::select('division_code', 'division_desc')
				->groupBy('division_code')
				->orderBy('division_desc')->lists('division_desc', 'division_code');

			$objectives = Objective::orderBy('objective')->lists('objective', 'id');

			$budgets = ActivityBudget::with('budgettype')
				->where('activity_id', $id)
				->get();

			$nobudgets = ActivityNobudget::with('budgettype')
				->where('activity_id', $id)
				->get();

			// echo '<pre>';
			// print_r($sel_approver);
			// echo '</pre>';

			return View::make('activity.downloaded', compact('activity', 'scope_types', 'planners', 'approvers', 'cycles',
			 'activity_types', 'divisions' , 'objectives',  'users', 'budgets', 'nobudgets', 'sel_planner','sel_approver'));
		}

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

					$scope_id = Input::get('scope');
					$cycle_id = Input::get('cycle');
					$activity_type_id = Input::get('activity_type');
					$division_code = Input::get('division');
					
					$scope = ScopeType::find($scope_id);
					$cycle = Cycle::find($cycle_id);
					$activity_type = ActivityType::find($activity_type_id);
					$division = Sku::select('division_code', 'division_desc')
										->where('division_code',$division_code)
										->first();

					$code = date('Y').$activity->id;
					if(!empty($scope)){
						$code .= '-'.$scope->scope_name;
					}
					if(!empty($cycle)){
						$code .= '_'.$cycle->cycle_name;
					}
					if(!empty($activity_type)){
						$code .= '_'.$activity_type->activity_type;
					}
					if(!empty($division)){
						$code .= '_'.$division->division_desc;
					}
					$activity->activity_code =  $code;

					
					$activity->scope_type_id = $scope_id;
					$activity->cycle_id = $cycle_id;
					$activity->activity_type_id = $activity_type_id;
					$activity->division_code = $division_code;

					$activity->duration = (Input::get('duration') == '') ? 0 : Input::get('duration');
					$activity->edownload_date = date('Y-m-d',strtotime(Input::get('download_date')));
					$activity->eimplementation_date = date('Y-m-d',strtotime(Input::get('implementation_date')));
					$activity->circular_name = strtoupper(Input::get('activity_title'));
					$activity->background = Input::get('background');
					$activity->update();

					// update planner
					ActivityPlanner::where('activity_id',$activity->id)->delete();
					if (Input::has('planner'))
					{
						if(Input::get('planner') > 0){
							ActivityPlanner::insert(array('activity_id' => $activity->id, 'user_id' => Input::get('planner')));
						}
					}


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

	public function addnobudget($id){
		if(Request::ajax()){
			$budget = new ActivityNobudget;

			$budget->activity_id = $id;
			$budget->budget_type_id = Input::get('budget_ttstype');
			$budget->budget_no = Input::get('budget_no');
			$budget->budget_name = Input::get('budget_name');
			$budget->start_date = date('Y-m-d',strtotime(Input::get('budget_startdate')));
			$budget->end_date = date('Y-m-d',strtotime(Input::get('budget_enddate')));
			$budget->remarks = Input::get('budget_remarks');
			$budget->save();

			$arr = Input::all();

			$budget_type = BudgetType::find($budget->budget_type_id);
			$arr['id'] = $budget->id;
			$arr['budget_ttstype'] = $budget_type->budget_type;
			return json_encode($arr);
		}
	}

	public function deletenobudget(){
		if(Request::ajax()){
			$id = Input::get('d_id');
			$budget = ActivityNobudget::find($id);
			$budget->delete();

			$arr['success'] = 1;
			$arr['id'] = $id;
			return json_encode($arr);
		}
	}


	public function download($id){

		$activity = Activity::findOrFail($id);

		// print_r(Activity::validForDownload($activity));
		$validation = Activity::validForDownload($activity);
		if($validation['status'] == 0){
			return Redirect::route('activity.edit',$id)
				->with('class', 'alert-danger')
				->withErrors($validation['message'])
				->with('message', 'Budget details are required.');
		}
		

		$activity->status_id = 2;
		$activity->update();

		return Redirect::route('activity.edit',$id)
				->with('class', 'alert-success')
				->with('message', 'Activity was successfuly downloaded to PMOG.');


	}

	public function recall($id){
		$activity = Activity::findOrFail($id);
		$activity->status_id = 3;
		$activity->update();

		return Redirect::route('activity.edit',$id)
				->with('class', 'alert-success')
				->with('message', 'Activity was successfuly recalled from PMOG');
	}
}