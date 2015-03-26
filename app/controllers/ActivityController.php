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
		$activities = Activity::where('created_by', '=', Auth::id())
			->orderBy('created_at','desc')
			->get();
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
				$scope_id = Input::get('scope');
				$cycle_id = Input::get('cycle');
				$activity_type_id = Input::get('activity_type');
				$division_code = Input::get('division');
				$category_code = Input::get('category');
				$brand_code = Input::get('brand');

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
				$activity->instruction = Input::get('instruction');
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
					$code .= '_'.$scope->scope_name;
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
				if(!empty($category_code)){
					if(count($category_code) > 1){
						$code .= '_MULTI';
					}else{
						$category = Sku::select('category_code', 'category_desc')
									->where('category_code',$category_code[0])
									->first();
						$code .= '_'.$category->category_desc;
					}
					
				}
				if(!empty($brand_code)){
					if(count($brand_code) > 1){
						$code .= '_MULTI';
					}else{
						$brand = Sku::select('brand_code', 'brand_desc')
									->where('brand_code',$brand_code[0])
									->first();
						$code .= '_'.$brand->brand_desc;
					}
					
				}

				$activity->activity_code =  $code;
				$activity->update();

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
				
				// add objective
				if (Input::has('objective'))
				{
					$activity_objective = array();
					foreach (Input::get('objective') as $objective){
						$activity_objective[] = array('activity_id' => $activity->id, 'objective_id' => $objective);
					}
					ActivityObjective::insert($activity_objective);
				}
				return $activity->id;
			});

			return Redirect::route('activity.edit',$id)
				->with('class', 'alert-success')
				->with('message', 'Activity was successfuly created.');
			
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
			$sel_planner = ActivityPlanner::where('activity_id',$id)->first();
			$sel_approver = ActivityApprover::getList($id);
			$sel_objectives = ActivityObjective::getList($id);
			$sel_channels = ActivityChannel::getList($id);

			$scope_types = ScopeType::orderBy('scope_name')->lists('scope_name', 'id');
			$planners = User::isRole('PMOG PLANNER')->lists('first_name', 'id');
			$approvers = User::isRole('CD OPS APPROVER')->lists('first_name', 'id');
			$channels = Channel::orderBy('channel_name')->lists('channel_name', 'id');

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

			$schemes = Scheme::where('activity_id', $activity->id)
				->orderBy('created_at', 'desc')
				->get();

			$scheme_customers = SchemeAllocation::getCustomers($activity->id);
			$force_allocs = ForceAllocation::getlist($activity->id);

			// $attachments = ActivityAttachment::where('activity_id', $activity->id)->get();

			$scheme_allcations = SchemeAllocation::getAllocation($activity->id);
			$materials = ActivityMaterial::where('activity_id', $activity->id)->get();

			$fdapermits = ActivityFdapermit::where('activity_id', $activity->id)->get();
			$fis = ActivityFis::where('activity_id', $activity->id)->get();
			$artworks = ActivityArtwork::where('activity_id', $activity->id)->get();
			$backgrounds = ActivityBackground::where('activity_id', $activity->id)->get();
			$bandings = ActivityBanding::where('activity_id', $activity->id)->get();

			return View::make('activity.edit', compact('activity', 'scope_types', 'planners', 'approvers', 'cycles',
			 'activity_types', 'divisions' , 'objectives',  'users', 'budgets', 'nobudgets', 'sel_planner','sel_approver',
			 'sel_objectives', 'channels', 'sel_channels', 'schemes', 'networks',
			 'scheme_customers', 'scheme_allcations', 'materials', 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings',
			 'force_allocs'));
		}

		// if($activity->status_id == 2){
		// 	$sel_planner = ActivityPlanner::where('activity_id',$id)
		// 		->first();
		// 	$sel_approver = ActivityApprover::getList($id);
		// 	$sel_skus = ActivitySku::getList($id);
		// 	$sel_objectives = ActivityObjective::getList($id);
		// 	$sel_channels = ActivityChannel::getList($id);

		// 	$scope_types = ScopeType::orderBy('scope_name')->lists('scope_name', 'id');
		// 	$planners = User::isRole('PMOG PLANNER')->lists('first_name', 'id');
		// 	$approvers = User::isRole('CD OPS APPROVER')->lists('first_name', 'id');
		// 	$involves = Pricelist::orderBy('sap_desc')->lists('sap_desc', 'sap_code');
		// 	$channels = Channel::orderBy('channel_name')->lists('channel_name', 'id');

		// 	$activity_types = ActivityType::orderBy('activity_type')->lists('activity_type', 'id');
		// 	$cycles = Cycle::orderBy('cycle_name')->lists('cycle_name', 'id');
			
		// 	$divisions = Sku::select('division_code', 'division_desc')
		// 		->groupBy('division_code')
		// 		->orderBy('division_desc')->lists('division_desc', 'division_code');

		// 	$objectives = Objective::orderBy('objective')->lists('objective', 'id');

		// 	$budgets = ActivityBudget::with('budgettype')
		// 		->where('activity_id', $id)
		// 		->get();

		// 	$nobudgets = ActivityNobudget::with('budgettype')
		// 		->where('activity_id', $id)
		// 		->get();

		// 	$schemes = Scheme::where('activity_id', $activity->id)
		// 		->orderBy('created_at', 'desc')
		// 		->get();

		// 	$division = Sku::division($activity->division_code);
		// 	$categories = Sku::categories($activity->division_code);
		// 	$sel_categories = ActivityCategory::selected_category($activity->id);

		// 	$brands = Sku::brands($sel_categories);
		// 	$sel_brands = ActivityBrand::selected_brand($activity->id);

		// 	$attachments = ActivityAttachment::where('activity_id', $activity->id)->get();

		// 	// return View::make('activity.downloaded', compact('activity', 'scope_types', 'planners', 'approvers', 'cycles',
		// 	//  'activity_types', 'divisions' , 'objectives',  'users', 'budgets', 'nobudgets', 'sel_planner','sel_approver',
		// 	//  'involves', 'sel_skus', 'sel_objectives', 'channels', 'sel_channels', 'schemes', 'networks', 'division', 
		// 	//  'categories', 'sel_categories', 'brands', 'sel_brands', 'attachments'));
		// }

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
			$activity = Activity::find($id);
			if(empty($activity)){
				$arr['success'] = 0;
			}else{
				$validation = Validator::make(Input::all(), Activity::$rules);
				$arr['success'] = 0;
				if($validation->passes())
				{
					DB::transaction(function() use ($activity)  {
						

						$scope_id = Input::get('scope');
						$cycle_id = Input::get('cycle');
						$activity_type_id = Input::get('activity_type');
						$division_code = Input::get('division');
						$category_code = Input::get('category');
						$brand_code = Input::get('brand');

						
						$scope = ScopeType::find($scope_id);
						$cycle = Cycle::find($cycle_id);
						$activity_type = ActivityType::find($activity_type_id);
						$division = Sku::select('division_code', 'division_desc')
											->where('division_code',$division_code)
											->first();

						$code = date('Y').$activity->id;
						if(!empty($scope)){
							$code .= '_'.$scope->scope_name;
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
						if(!empty($category_code)){
							if(count($category_code) > 1){
								$code .= '_MULTI';
							}else{
								$category = Sku::select('category_code', 'category_desc')
											->where('category_code',$category_code[0])
											->first();
								$code .= '_'.$category->category_desc;
							}
							
						}
						if(!empty($brand_code)){
							if(count($brand_code) > 1){
								$code .= '_MULTI';
							}else{
								$brand = Sku::select('brand_code', 'brand_desc')
											->where('brand_code',$brand_code[0])
											->first();
								$code .= '_'.$brand->brand_desc;
							}
							
						}

						$activity->activity_code =  $code;
						
						$activity->scope_type_id = $scope_id;
						$activity->cycle_id = $cycle_id;
						$activity->activity_type_id = $activity_type_id;
						$activity->division_code = $division_code;

						$activity->duration = (Input::get('lead_time') == '') ? 0 : Input::get('lead_time');
						$activity->edownload_date = date('Y-m-d',strtotime(Input::get('download_date')));
						$activity->eimplementation_date = date('Y-m-d',strtotime(Input::get('implementation_date')));
						$activity->circular_name = strtoupper(Input::get('activity_title'));
						$activity->background = Input::get('background');
						$activity->instruction = Input::get('instruction');
						$activity->update();

						// update timings
						ActivityTiming::where('activity_id',$activity->id)->delete();
						$networks = ActivityTypeNetwork::timings($activity->activity_type_id,$activity->edownload_date);
						if(count($networks)> 0){
							$activity_timing = array();

							foreach ($networks as $network) {
								$activity_timing[] = array('activity_id' => $activity->id, 'task_id' => $network->task_id,
									'milestone' => $network->milestone, 'task' => $network->task, 'responsible' => $network->responsible,
									'duration' => $network->duration, 'depend_on' => $network->depend_on,
									'start_date' => date('Y-m-d',strtotime($network->start_date)), 'end_date' => date('Y-m-d',strtotime($network->end_date)));
							}
							ActivityTiming::insert($activity_timing);
						}

						// update planner
						ActivityPlanner::where('activity_id',$activity->id)->delete();
						if (Input::has('planner'))
						{
							if(Input::get('planner') > 0){
								ActivityPlanner::insert(array('activity_id' => $activity->id, 'user_id' => Input::get('planner')));
							}
						}
						
						// update approver
						ActivityApprover::where('activity_id',$activity->id)->delete();
						if (Input::has('approver'))
						{
						   	$activity_approver = array();
							foreach (Input::get('approver') as $approver) {
								$activity_approver[] = array('activity_id' => $activity->id, 'user_id' => $approver);
							}
							ActivityApprover::insert($activity_approver);
						}

						// update category
						ActivityCategory::where('activity_id',$activity->id)->delete();
						if (Input::has('category'))
						{
							$activity_category = array();
							foreach (Input::get('category') as $category){
								$activity_category[] = array('activity_id' => $activity->id, 'category_code' => $category);
							}
							ActivityCategory::insert($activity_category);
						}

						// update brand
						ActivityBrand::where('activity_id',$activity->id)->delete();
						if (Input::has('brand'))
						{
							$activity_brand = array();
							foreach (Input::get('brand') as $brand){
								$activity_brand[] = array('activity_id' => $activity->id, 'brand_code' => $brand);
							}
							ActivityBrand::insert($activity_brand);
						}

						// update skus involve
						// ActivitySku::where('activity_id',$activity->id)->delete();
						// if (Input::has('involve'))
						// {
						// 	$activity_skuinvoled = array();
						// 	foreach (Input::get('involve') as $sku){
						// 		$activity_skuinvoled[] = array('activity_id' => $activity->id, 'sap_code' => $sku);
						// 	}
						// 	ActivitySku::insert($activity_skuinvoled);
						// }
						
						// update objective
						ActivityObjective::where('activity_id',$activity->id)->delete();
						if (Input::has('objective'))
						{
							$activity_objective = array();
							foreach (Input::get('objective') as $objective){
								$activity_objective[] = array('activity_id' => $activity->id, 'objective_id' => $objective);
							}
							ActivityObjective::insert($activity_objective);
						}
					});

					$arr['success'] = 1;
				}
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


	public function download($id){

		$activity = Activity::findOrFail($id);

		$validation = Activity::validForDownload($activity);
		if($validation['status'] == 0){
			return Redirect::route('activity.edit',$id)
				->with('class', 'alert-danger')
				->withErrors($validation['message'])
				->with('message', 'Other information are required.');
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

	// ajac function
	// Activity Materials
	public function addbudget($id){
		if(Request::ajax()){
			$activity = Activity::find($id);
			$type_id = Input::get('io_ttstype');
			$budget_type = BudgetType::find($type_id);
			if(empty($activity) || (empty($budget_type))){
				$arr['success'] = 0;
			}else{
				$budget = new ActivityBudget;

				$budget->activity_id = $id;
				$budget->budget_type_id = $type_id;
				$budget->io_number = strtoupper(Input::get('io_no'));
				$budget->amount = str_replace(",", '', Input::get('io_amount'));
				$budget->start_date = date('Y-m-d',strtotime(Input::get('io_startdate')));
				$budget->end_date = date('Y-m-d',strtotime(Input::get('io_enddate')));
				$budget->remarks = Input::get('io_remarks');
				$budget->save();

				$arr = Input::all();

				$arr['id'] = $budget->id;
				$arr['io_no'] = strtoupper(Input::get('io_no'));
				$arr['io_ttstype'] = $budget_type->budget_type;
				$arr['success'] = 1;
			}
			
			return json_encode($arr);
		}
	}

	public function deletebudget(){
		if(Request::ajax()){
			$id = Input::get('d_id');
			$budget = ActivityBudget::find($id);
			if(empty($budget)){
				$arr['success'] = 0;
			}else{
				$budget->delete();
				$arr['success'] = 1;
				
			}
			$arr['id'] = $id;
			return json_encode($arr);
		}
	}

	public function updatebudget(){
		if(Request::ajax()){
			$id = Input::get('id');
			$budget = ActivityBudget::find($id);
			$type_id = Input::get('io_ttstype');
			$budget_type = BudgetType::find($type_id);
			if(empty($budget) || (empty($budget_type))){
				$arr['success'] = 0;
			}else{
				$budget->budget_type_id = $type_id;
				$budget->io_number = strtoupper(Input::get('io_no'));
				$budget->amount = str_replace(",", '', Input::get('io_amount'));
				$budget->start_date = date('Y-m-d',strtotime(Input::get('io_startdate')));
				$budget->end_date = date('Y-m-d',strtotime(Input::get('io_enddate')));
				$budget->remarks = Input::get('io_remarks');
				$budget->update();

				$arr = Input::all();

				$arr['id'] = $budget->id;
				$arr['io_no'] = strtoupper(Input::get('io_no'));
				$arr['io_ttstype'] = $budget_type->budget_type;
				$arr['success'] = 1;
			}
			$arr['id'] = $id;
			return json_encode($arr);

		}
	}

	public function addnobudget($id){
		if(Request::ajax()){

			$activity = Activity::find($id);
			$type_id = Input::get('budget_ttstype');
			$budget_type = BudgetType::find($type_id);
			if(empty($activity) || (empty($budget_type))){
				$arr['success'] = 0;
			}else{
				$budget = new ActivityNobudget;

				$budget->activity_id = $id;
				$budget->budget_type_id = $type_id;
				$budget->budget_no = Input::get('budget_no');
				$budget->budget_name = Input::get('budget_name');
				$budget->amount = str_replace(",", '', Input::get('budget_amount'));
				$budget->start_date = date('Y-m-d',strtotime(Input::get('budget_startdate')));
				$budget->end_date = date('Y-m-d',strtotime(Input::get('budget_enddate')));
				$budget->remarks = Input::get('budget_remarks');
				$budget->save();

				$arr = Input::all();
				$arr['id'] = $budget->id;
				$arr['budget_ttstype'] = $budget_type->budget_type;
				$arr['success'] = 1;
			}
			
			return json_encode($arr);
		}
	}

	public function deletenobudget(){
		if(Request::ajax()){
			$id = Input::get('d_id');
			$budget = ActivityNobudget::find($id);
			if(empty($budget)){
				$arr['success'] = 0;
			}else{
				$budget->delete();
				$arr['success'] = 1;
			}
			
			$arr['id'] = $id;
			return json_encode($arr);
		}
	}

	public function updatenobudget(){
		if(Request::ajax()){
			$id = Input::get('id');
			$budget = ActivityNobudget::find($id);
			$type_id = Input::get('budget_ttstype');
			$budget_type = BudgetType::find($type_id);
			if(empty($budget) || (empty($budget_type))){
				$arr['success'] = 0;
			}else{
				$budget->budget_type_id = $type_id;
				$budget->budget_no = Input::get('budget_no');
				$budget->budget_name = Input::get('budget_name');
				$budget->amount = str_replace(",", '', Input::get('budget_amount'));
				$budget->start_date = date('Y-m-d',strtotime(Input::get('budget_startdate')));
				$budget->end_date = date('Y-m-d',strtotime(Input::get('budget_enddate')));
				$budget->remarks = Input::get('budget_remarks');
				$budget->update();

				$arr = Input::all();
				$arr['id'] = $budget->id;
				$arr['budget_ttstype'] = $budget_type->budget_type;
				$arr['success'] = 1;
			}
			$arr['id'] = $id;
			return json_encode($arr);

		}
	}

	public function updatecustomer($id){
		if(Request::ajax()){
			$activity = Activity::find($id);
			if(empty($activity)){
				$arr['success'] = 0;
			}else{
				DB::transaction(function() use ($id,$activity)  {
					$_customers = Input::get('customers');
					ActivityCustomer::where('activity_id',$id)->delete();

					$enable_force = (Input::has('allow_force')) ? 1 : 0;
					$activity->allow_force = $enable_force;
					$activity->update();
					if(!empty($_customers)){
						$customers = explode(",", $_customers);
						if(!empty($customers)){
							$activity_customers = array();
							$area_list = array();
							foreach ($customers as $customer_node){
								$activity_customers[] = array('activity_id' => $id, 'customer_node' => trim($customer_node));
								// add area
								if($enable_force){
									$_selected_customer = explode(".", trim($customer_node));

									if(count($_selected_customer) < 2){
										$areas = Area::where('group_code',$_selected_customer[0])->get();
										foreach ($areas as $area) {
											$area_list[$area->area_code] = array('activity_id' => $id, 'area_code' => $area->area_code);
										}
									}else{
										if(!empty($_selected_customer[1])){
											$area_list[$_selected_customer[1]] = array('activity_id' => $id, 'area_code' => $_selected_customer[1]);
										}
									}
								}
								
							}
							ForceAllocation::where('activity_id',$id)->delete();
							if($enable_force){
								ForceAllocation::insert($area_list);
							}
							ActivityCustomer::insert($activity_customers);
						}
					}



					$channels = Input::get('channel');
					ActivityChannel::where('activity_id',$id)->delete();
					if(!empty($channels)){
						$activity_channels = array();
						foreach ($channels as $channel){
							$activity_channels[] = array('activity_id' => $id, 'channel_id' => $channel);
						}
						if(!empty($activity_channels)){
							ActivityChannel::insert($activity_channels);
						}
					}


				});

				$arr['success'] = 1;
			}
			
			$arr['id'] = $id;
			return json_encode($arr);
		}
	}

	public function updateforcealloc(){
		$id = Input::get("f_id");
		if(Request::ajax()){
			$arr['f_percent'] = Input::get("f_percent");
			$force_alloc = ForceAllocation::find($id);
			if(!empty($force_alloc)){
				$force_alloc->multi = $arr['f_percent'];
				$force_alloc->update();
				$arr['success'] = 1;
			}else{
				$arr['success'] = 0;
			}
		}
		
		$arr['id'] = $id;
		return json_encode($arr);
	}

	public function updatebilling($id){
		if(Request::ajax()){
			$activity = Activity::find($id);
			if(empty($activity)){
				$arr['success'] = 0;
			}else{
				DB::transaction(function() use ($activity)  {
					$activity->billing_date = date('Y-m-d',strtotime(Input::get('billing_deadline')));
					$activity->billing_remarks = Input::get('billing_remarks');
					$activity->update();
				});

				$arr['success'] = 1;
			}
			
			$arr['id'] = $id;
			return json_encode($arr);
		}
	}

	public function timings($id){
		if(Request::ajax()){
			$activity = Activity::find($id);
			if(empty($activity)){
				return Response::json(array('success' => 0));
			}else{
				$networks = ActivityTiming::select(DB::raw('task_id,milestone,task,responsible,duration,depend_on,DATE_FORMAT(start_date, "%m/%d/%Y") AS start_date,DATE_FORMAT(end_date, "%m/%d/%Y") AS end_date'))
					->where('activity_id', $id)->get();
				return Response::json($networks);
			}
			
		}
	}

	// Activity Materials
	public function addmaterial($id){
		if(Request::ajax()){
			$source_id = Input::get('source');
			$source = MaterialSource::find($source_id);

			if(empty($source)){
				$arr['success'] = 0;
			}else{
				$material = new ActivityMaterial;

				$material->activity_id = $id;
				$material->source_id = $source_id;
				$material->material = Input::get('material');
				$material->save();

				$arr = Input::all();
				$arr['success'] = 1;
				$arr['id'] = $material->id;
				$arr['source'] = $source->source;
			}
			return json_encode($arr);
		}
	}

	public function deletematerial(){
		if(Request::ajax()){
			$id = Input::get('d_id');
			$material = ActivityMaterial::find($id);

			if(empty($material)){
				$arr['success'] = 0;
			}else{
				$material->delete();
				$arr['success'] = 1;
			}
			
			$arr['id'] = $id;
			return json_encode($arr);
		}
	}

	public function updatematerial(){
		if(Request::ajax()){
			$source_id = Input::get('source');
			$source = MaterialSource::find($source_id);

			$id = Input::get('id');
			$material = ActivityMaterial::find($id);

			if(empty($source) || empty($material)){
				$arr['success'] = 0;
			}else{
				$material->source_id = Input::get('source');
				$material->material = Input::get('material');
				$material->update();
	
				$arr = Input::all();
				$arr['id'] = $material->id;
				$arr['source'] = $source->source;
				$arr['success'] = 1;
			}
			return json_encode($arr);
		}
	}

	private function doupload($path){
		$distination = storage_path().'/uploads/'.$path.'/';
		$file = Input::file('file');
		$original_file_name = $file->getClientOriginalName();
		$file_name = Str::random(20).'.'.File::extension($file->getClientOriginalName());
		$file_path = $distination.$file_name;


		//Alter the file name until it's unique to prevent overwriting
		while(File::exists($file_path)) {
			$file_name = Str::random(20).'.'.File::extension($file->getClientOriginalName());
			$file_path = $distination.$file_name;
		}

		$file->move($distination,$file_name);

		return (object) array('file_name' => $file_name,
		 'original_file_name' => $original_file_name,
		 'status' => 1);
	}

	public function fdaupload($id){
		if(Input::hasFile('file')){
			
			$upload = self::doupload('fdapermits');

			$docu = new ActivityFdapermit;
			$docu->created_by = Auth::id();
			$docu->activity_id = $id;
			$docu->permit_no = Input::get('permitno');
			$docu->hash_name = $upload->file_name;
			$docu->file_name = $upload->original_file_name;
			$docu->file_desc = (Input::get('file_desc') =='') ? $upload->original_file_name : Input::get('file_desc');
			$docu->save();

			return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#attachment")
				->with('class', 'alert-success')
				->with('message', 'FDA Permits is successfuly uploaded!');
		}else{
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#attachment")
				->with('class', 'alert-danger')
				->with('message', 'Error uploading file.');
		}
	}

	public function fdadelete($id){
		$fda = ActivityFdapermit::find($id);
		$activity_id = Input::get('activity_id');
		if(empty($fda)){
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity_id)) . "#attachment")
				->with('class', 'alert-danger')
				->with('message', 'Error deleting file.');
		}else{
			$fda->delete();
			$filename = storage_path().'/uploads/fdapermits/';
			File::delete($filename.$fda->hash_name);
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity_id)) . "#attachment")
				->with('class', 'alert-success')
				->with('message', 'FDA Permits is successfuly deleted!');
		}
	}

	public function fdadownload($id){
		$fda = ActivityFdapermit::find($id);
		$filename = storage_path().'/uploads/fdapermits/';
		return Response::download($filename.$fda->hash_name, $fda->file_name);
	}

	public function fisupload($id){
		if(Input::hasFile('file')){

			$upload = self::doupload('fisupload');

			$docu = new ActivityFis;
			$docu->created_by = Auth::id();
			$docu->activity_id = $id;
			$docu->hash_name = $upload->file_name;
			$docu->file_name =  $upload->original_file_name;
			$docu->file_desc = (Input::get('file_desc') =='') ? $upload->original_file_name : Input::get('file_desc');
			$docu->save();

			return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#attachment")
				->with('class', 'alert-success')
				->with('message', 'Product information Sheet is successfuly uploaded!');
		}else{
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#attachment")
				->with('class', 'alert-danger')
				->with('message', 'Error uploading file.');
		}
	}

	public function fisdelete($id){
		$fis = ActivityFis::find($id);
		$activity_id = Input::get('activity_id');
		if(empty($fis)){
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity_id)) . "#attachment")
				->with('class', 'alert-danger')
				->with('message', 'Error deleting file.');
		}else{
			$fis->delete();
			$filename = storage_path().'/uploads/fisupload/';
			File::delete($filename.$fis->hash_name);
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity_id)) . "#attachment")
				->with('class', 'alert-success')
				->with('message', 'Product Information Sheet is successfuly deleted!');
		}
	}

	public function fisdownload($id){
		$fis = ActivityFis::find($id);
		$filename = storage_path().'/uploads/fisupload/';
		return Response::download($filename.$fis->hash_name, $fis->file_name);
	}

	public function artworkupload($id){
		if(Input::hasFile('file')){
			$upload = self::doupload('artworkupload');

			$docu = new ActivityArtwork;
			$docu->created_by = Auth::id();
			$docu->activity_id = $id;
			$docu->hash_name = $upload->file_name;
			$docu->file_name = $upload->original_file_name;
			$docu->file_desc = (Input::get('file_desc') =='') ? $upload->original_file_name : Input::get('file_desc');
			$docu->save();

			return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#attachment")
				->with('class', 'alert-success')
				->with('message', 'Product artwork is successfuly uploaded!');
		}else{
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#attachment")
				->with('class', 'alert-danger')
				->with('message', 'Error uploading file.');
		}
	}

	public function artworkdelete($id){
		$artwork = ActivityArtwork::find($id);
		$activity_id = Input::get('activity_id');
		if(empty($artwork)){
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity_id)) . "#attachment")
				->with('class', 'alert-danger')
				->with('message', 'Error deleting file.');
		}else{
			$artwork->delete();
			$filename = storage_path().'/uploads/artworkupload/';
			File::delete($filename.$artwork->hash_name);
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity_id)) . "#attachment")
				->with('class', 'alert-success')
				->with('message', 'Product artwork is successfuly deleted!');
		}
	}

	public function artworkdownload($id){
		$artwork = ActivityArtwork::find($id);
		$filename = storage_path().'/uploads/artworkupload/';
		return Response::download($filename.$artwork->hash_name, $artwork->file_name);
	}

	public function backgroundupload($id){
		if(Input::hasFile('file')){
			$upload = self::doupload('background');

			$docu = new ActivityBackground;
			$docu->created_by = Auth::id();
			$docu->activity_id = $id;
			$docu->hash_name = $upload->file_name;
			$docu->file_name = $upload->original_file_name;
			$docu->file_desc = (Input::get('file_desc') =='') ? $upload->original_file_name : Input::get('file_desc');
			$docu->save();

			return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#attachment")
				->with('class', 'alert-success')
				->with('message', 'Marketing Background is successfuly uploaded!');
		}else{
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#attachment")
				->with('class', 'alert-danger')
				->with('message', 'Error uploading file.');
		}
	}

	public function backgrounddelete($id){
		$background = ActivityBackground::find($id);
		$activity_id = Input::get('activity_id');
		if(empty($background)){
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity_id)) . "#attachment")
				->with('class', 'alert-danger')
				->with('message', 'Error deleting file.');
		}else{
			$artwork->delete();
			$filename = storage_path().'/uploads/background/';
			File::delete($filename.$artwork->hash_name);
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity_id)) . "#attachment")
				->with('class', 'alert-success')
				->with('message', 'Marketing Background is successfuly deleted!');
		}
	}

	public function backgrounddownload($id){
		$background = ActivityBackground::find($id);
		$filename = storage_path().'/uploads/background/';
		return Response::download($filename.$background->hash_name, $background->file_name);
	}

		public function bandingupload($id){
		if(Input::hasFile('file')){
			$upload = self::doupload('banding');

			$docu = new ActivityBanding;
			$docu->created_by = Auth::id();
			$docu->activity_id = $id;
			$docu->hash_name = $upload->file_name;
			$docu->file_name = $upload->original_file_name;
			$docu->file_desc = (Input::get('file_desc') =='') ? $upload->original_file_name : Input::get('file_desc');
			$docu->save();

			return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#attachment")
				->with('class', 'alert-success')
				->with('message', 'Banding Guideline / Activation Mechanic is successfuly uploaded!');
		}else{
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#attachment")
				->with('class', 'alert-danger')
				->with('message', 'Error uploading file.');
		}
	}

	public function bandingdelete($id){
		$banding = ActivityBanding::find($id);
		$activity_id = Input::get('activity_id');
		if(empty($banding)){
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity_id)) . "#attachment")
				->with('class', 'alert-danger')
				->with('message', 'Error deleting file.');
		}else{
			$artwork->delete();
			$filename = storage_path().'/uploads/banding/';
			File::delete($filename.$artwork->hash_name);
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity_id)) . "#attachment")
				->with('class', 'alert-success')
				->with('message', 'Banding Guideline / Activation Mechanic is successfuly deleted!');
		}
	}

	public function bandingdownload($id){
		$banding = ActivityBanding::find($id);
		$filename = storage_path().'/uploads/banding/';
		return Response::download($filename.$banding->hash_name, $banding->file_name);
	}
}