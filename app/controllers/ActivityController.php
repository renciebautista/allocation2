<?php

class ActivityController extends BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /activity
	 *
	 * @return Response
	 */
	public function index()
	{

		if(Auth::user()->hasRole("FIELD SALES")){
			Input::flash();
			$cycles = Cycle::getLists();
			$types = ActivityType::getLists();
			$scopes = ScopeType::getLists();
			$activities = Activity::searchField(Input::get('cy'),Input::get('ty'),Input::get('sc'),Input::get('title'));
			return View::make('dashboard.field',compact('activities', 'cycles','types','scopes'));
		}


		if(Auth::user()->hasRole("PROPONENT")){
			Input::flash();
			$statuses = ActivityStatus::availableStatus();
			$cycles = Activity::availableCycles(Auth::id());
			$scopes = Activity::availableScopes(Auth::id());
			$types = Activity::availableTypes(Auth::id());
			$planners = Activity::availablePlanners(Auth::id());
			$activities = Activity::search(Auth::id(),Input::get('st'),Input::get('cy'),Input::get('sc'),
				Input::get('ty'),Input::get('pm'),Input::get('title'));

			return View::make('activity.index',compact('statuses', 'activities', 'cycles', 'scopes', 'types', 'planners'));
		}

		if(Auth::user()->hasRole("PMOG PLANNER")){
			Input::flash();
			$statuses = ActivityStatus::availableStatus(1);
			$cycles = Cycle::getLists();
			$scopes = ScopeType::getLists();
			$types = ActivityType::getLists();
			$proponents = User::getApprovers(['PROPONENT']);
			$activities = Activity::searchDownloaded(Auth::id(),Input::get('pr'),Input::get('st'),Input::get('cy'),Input::get('sc'),
				Input::get('ty'),Input::get('title'));
			return View::make('downloadedactivity.index',compact('statuses', 'activities', 'cycles', 'scopes', 'types', 'proponents'));
		}

	}

	/**
	 * Show the form for creating a new resource.
	 * GET /activity/create
	 *
	 * @return Response
	 */
	public function create()
	{
		if(Auth::user()->hasRole("PROPONENT")){
			$scope_types = ScopeType::getLists();
			$planners = User::getApprovers(['PMOG PLANNER']);
			$approvers = User::getApprovers(['GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']);
			$activity_types = ActivityType::getWithNetworks();
			$cycles = Cycle::getLists();
			$divisions = Sku::divisions();
			$objectives = Objective::getLists();
			return View::make('activity.create', compact('scope_types', 'planners', 'approvers', 'cycles',
			 'activity_types', 'divisions' , 'objectives',  'users'));
		}

		return Response::make(View::make('shared/404'), 404);
		
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /activity
	 *
	 * @return Response
	 */
	public function store()
	{
		if(Auth::user()->hasRole("PROPONENT")){
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

					// add timings
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

					$path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id;
					if(!File::exists($path)) {
						File::makeDirectory($path);
					}
					$path2 = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
					if(!File::exists($path2)) {
						File::makeDirectory($path2);
					}

					return $activity->id;

					
				});

				return Redirect::route('activity.edit',$id)
					->with('class', 'alert-success')
					->with('message', 'Activity "'.strtoupper(Input::get('activity_title')).'" was successfuly created.');
				
			}

			return Redirect::route('activity.create')
				->withInput()
				->withErrors($validation)
				->with('class', 'alert-danger')
				->with('message', 'There were validation errors.');
		}
		

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
		return Redirect::route('activity.edit',$id);
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
		if(Auth::user()->hasRole("PROPONENT")){
			$activity = Activity::findOrFail($id);
			if(!Activity::myActivity($activity)){
				return Response::make(View::make('shared/404'), 404);
			}

			$sel_planner = ActivityPlanner::getPlanner($activity->id);
			$sel_approver = ActivityApprover::getList($activity->id);
			$sel_objectives = ActivityObjective::getList($activity->id);
			// $sel_channels = ActivityChannel::getList($activity->id);
			// Helper::print_array($sel_channels);
			$approvers = User::getApprovers(['GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']);
			// $channels = Channel::getList();
			$objectives = Objective::getLists();
			$budgets = ActivityBudget::getBudgets($activity->id);
			$nobudgets = ActivityNobudget::getBudgets($activity->id);
			$schemes = Scheme::getList($activity->id);
			// $scheme_customers = SchemeAllocation::getCustomers($activity->id);
			$force_allocs = ForceAllocation::getlist($activity->id);
			// $scheme_allcations = SchemeAllocation::getAllocation($activity->id);
			$materials = ActivityMaterial::getList($activity->id);
			// attachments
			$fdapermits = ActivityFdapermit::getList($activity->id);
			$fis = ActivityFis::getList($activity->id);
			$artworks = ActivityArtwork::getList($activity->id);
			$backgrounds = ActivityBackground::getList($activity->id);
			$bandings = ActivityBanding::getList($activity->id);
			// comments
			$comments = ActivityComment::getList($activity->id);

			if($activity->status_id < 4){
				$submitstatus = array('1' => 'SUBMIT ACTIVITY');
				$scope_types = ScopeType::getLists($activity->id);
				$planners = User::getApprovers(['PMOG PLANNER']);
				$activity_types = ActivityType::getWithNetworks();
				$cycles = Cycle::getLists();
				$divisions = Sku::getDivisionLists();

				return View::make('activity.edit', compact('activity', 'scope_types', 'planners', 'approvers', 'cycles',
				 'activity_types', 'divisions' , 'objectives',  'users', 'budgets', 'nobudgets', 'sel_planner','sel_approver',
				 'sel_objectives',  'schemes', 'networks',
				 'scheme_customers', 'scheme_allcations', 'materials', 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings',
				 'force_allocs', 'comments' ,'submitstatus'));
			}

			if($activity->status_id > 3){
				$submitstatus = array('2' => 'RECALL ACTIVITY');
				$division = Sku::division($activity->division_code);
				$route = 'activity.index';
				$recall = $activity->pro_recall;
				$submit_action = 'ActivityController@updateactivity';
				return View::make('shared.activity_readonly', compact('activity', 'sel_planner', 'approvers', 'division',
				 'objectives',  'users', 'budgets', 'nobudgets','sel_approver',
				 'sel_objectives',  'schemes', 'networks',
				 'scheme_customers', 'scheme_allcations', 'materials', 'force_allocs',
				 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings', 'comments' ,'submitstatus', 'route', 'recall', 'submit_action'));
			}
		}

		if(Auth::user()->hasRole("PMOG PLANNER")){
			$activity = Activity::findOrFail($id);
			if(!ActivityPlanner::myActivity($activity->id)){
				return Response::make(View::make('shared/404'), 404);
			}

			$sel_planner = ActivityPlanner::getPlanner($activity->id);
			$sel_approver = ActivityApprover::getList($activity->id);
			$sel_objectives = ActivityObjective::getList($activity->id);
			// $sel_channels = ActivityChannel::getList($activity->id);
			$approvers = User::getApprovers(['GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']);
			// $channels = Channel::getList();
			$objectives = Objective::getLists();
			$budgets = ActivityBudget::getBudgets($activity->id);
			$nobudgets = ActivityNobudget::getBudgets($activity->id);
			$schemes = Scheme::getList($activity->id);
			// $scheme_customers = SchemeAllocation::getCustomers($activity->id);
			$force_allocs = ForceAllocation::getlist($activity->id);
			// $scheme_customers = SchemeAllocation::getCustomers($activity->id);
			// $scheme_allcations = SchemeAllocation::getAllocation($activity->id);
			$materials = ActivityMaterial::getList($activity->id);
			// attachments
			$fdapermits = ActivityFdapermit::getList($activity->id);
			$fis = ActivityFis::getList($activity->id);
			$artworks = ActivityArtwork::getList($activity->id);
			$backgrounds = ActivityBackground::getList($activity->id);
			$bandings = ActivityBanding::getList($activity->id);
			// comments
			$comments = ActivityComment::getList($activity->id);

			if($activity->status_id == 4){
				$submitstatus = array('1' => 'SUBMIT ACTIVITY','2' => 'DENY ACTIVITY');
				$scope_types = ScopeType::getLists($activity->id);
				$planners = User::getApprovers(['PMOG PLANNER']);
				$activity_types = ActivityType::getWithNetworks();
				$cycles = Cycle::getLists();
				$divisions = Sku::getDivisionLists();

				return View::make('downloadedactivity.edit', compact('activity', 'scope_types', 'planners', 'approvers', 'cycles',
				 'activity_types', 'divisions' , 'objectives',  'users', 'budgets', 'nobudgets', 'sel_planner','sel_approver',
				 'sel_objectives',  'schemes', 'networks',
				 'scheme_customers', 'scheme_allcations', 'materials', 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings',
				 'force_allocs','submitstatus', 'comments'));
			}else{
				$submitstatus = array('3' => 'RECALL ACTIVITY');
				$division = Sku::division($activity->division_code);
				$route = 'activity.index';
				$recall = $activity->pmog_recall;
				$submit_action = 'ActivityController@submittogcm';
				return View::make('shared.activity_readonly', compact('activity', 'sel_planner', 'approvers', 'division',
				 'objectives',  'users', 'budgets', 'nobudgets','sel_approver',
				 'sel_objectives',  'schemes', 'networks',
				 'scheme_customers', 'scheme_allcations', 'materials', 'force_allocs',
				 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings', 'comments' ,'submitstatus', 'route', 'recall', 'submit_action'));
			}
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
		if(Auth::user()->hasRole("PROPONENT")){
			if(Request::ajax()){
				$activity = Activity::find($id);
				if((empty($activity)) || (!Activity::myActivity($activity))){
					$arr['success'] = 0;
				}else{
					$validation = Validator::make(Input::all(), Activity::$rules);
					$arr['success'] = 0;
					if($validation->passes())
					{
						$old_cycle = $activity->cycle_id;
						$old_type = $activity->activity_type_id;
						DB::transaction(function() use ($activity,$old_cycle,$old_type)  {
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
										'start_date' => date('Y-m-d',strtotime($network->start_date)), 
										'show' => $network->show,
										'end_date' => date('Y-m-d',strtotime($network->end_date)));
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
						
						$path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id;
						if(!File::exists($path)) {
							File::makeDirectory($path);
						}
						$path2 = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
						if(!File::exists($path2)) {
							File::makeDirectory($path2);

							$old_path = storage_path().'/uploads/'.$old_cycle.'/'.$old_type.'/'.$activity->id;
							File::copyDirectory($old_path, $path2);

							File::deleteDirectory($old_path);

							$list = File::directories(storage_path().'/uploads/'.$old_cycle.'/'.$old_type);
							if(count($list) == 0){
								File::deleteDirectory(storage_path().'/uploads/'.$old_cycle.'/'.$old_type);
							}
						}

						$arr['success'] = 1;
					}
				}
				
				$arr['id'] = $id;
				return json_encode($arr);
			}
		}

		if(Auth::user()->hasRole("PMOG PLANNER")){
			if(Request::ajax()){
				$activity = Activity::find($id);
				if((empty($activity)) || (!ActivityPlanner::myActivity($activity->id))){
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
		$activity = Activity::findOrFail($id);
		if (is_null($activity))
		{
			$class = 'alert-danger';
			$message = 'Activity does not exist.';
		}else{

			DB::beginTransaction();

			try {
			    ActivityTiming::where('activity_id',$activity->id)->delete();
				ActivityPlanner::where('activity_id',$activity->id)->delete();
				ActivityApprover::where('activity_id',$activity->id)->delete();
				ActivityCategory::where('activity_id',$activity->id)->delete();
				ActivityBrand::where('activity_id',$activity->id)->delete();
				ActivityObjective::where('activity_id',$activity->id)->delete();

				ActivityMaterial::where('activity_id',$activity->id)->delete();
				ActivityCustomer::where('activity_id',$activity->id)->delete();
				ActivityChannel::where('activity_id',$activity->id)->delete();
				ForceAllocation::where('activity_id',$activity->id)->delete();
				// loop schemes
				$schemes = Scheme::getList($activity->id);
				if(!empty($schemes)){
					foreach ($schemes as $scheme) {
						SchemeSku::where('scheme_id',$scheme->id)->delete();
						SchemeHostSku::where('scheme_id',$scheme->id)->delete();
						SchemePremuimSku::where('scheme_id',$scheme->id)->delete();
						SchemeAllocation::where('scheme_id',$scheme->id)->delete();
						$scheme->delete();
					}
				}
				

				ActivityBudget::where('activity_id',$activity->id)->delete();
				ActivityNobudget::where('activity_id',$activity->id)->delete();
				ActivityFdapermit::where('activity_id',$activity->id)->delete();
				ActivityFis::where('activity_id',$activity->id)->delete();
				ActivityArtwork::where('activity_id',$activity->id)->delete();
				ActivityBackground::where('activity_id',$activity->id)->delete();
				ActivityBanding::where('activity_id',$activity->id)->delete();
				ActivityComment::where('activity_id',$activity->id)->delete();
				$activity->delete();

				DB::commit();
				$path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
				File::deleteDirectory($path);

				$class = 'alert-success';
				$message = 'Activity successfully deleted.';

				return Redirect::to(URL::action('ActivityController@index'))
				->with('class', $class )
				->with('message', $message);
			    
			    // all good
			} catch (\Exception $e) {
			    DB::rollback();
			    $class = 'alert-danger';
				$message = 'Cannot delete activity.';

				return Redirect::to(URL::action('ActivityController@index'))
				->with('class', $class )
				->with('message', $message);
			    // something went wrong
			}			
			
		}

		return Redirect::to(URL::action('ActivityController@index'))
				->with('class', $class )
				->with('message', $message);
	}


	public function updateactivity($id){
		if(Request::ajax()){
			if(Auth::user()->hasRole("PROPONENT")){
				$arr = DB::transaction(function() use ($id)  {
					$activity = Activity::findOrFail($id);

					
					if(empty($activity)){
						$arr['success'] = 0;
						$arr['error'] = $validation['message'];
					}else{
						$planner = ActivityPlanner::getPlanner($activity->id);
						if(count($planner) > 0){
							$required_rules = array('budget','approver','cycle','activity','category','brand','objective','background','customer','scheme');
						}else{
							$required_rules = array('budget','approver','cycle','activity','category','brand','objective','background','customer','scheme');
						}
						
						$validation = Activity::validForDownload($activity,$required_rules);
						

						if($validation['status'] == 0){
							$arr['success'] = 0;
							$arr['error'] = $validation['message'];
						}else{
							$status_id = (int) Input::get('submitstatus');
							$activity_status = 3;
							$pro_recall = 0;
							$pmog_recall = 0;
							if($status_id == 1){
								$pro_recall = 1;
								if(count($planner) > 0){
									$comment_status = "SUBMITTED TO PMOG PLANNER";
									$activity_status = 4;
								}else{
									$gcom_approvers = ActivityApprover::getApproverByRole($activity->id,'GCOM APPROVER');
									if(count($gcom_approvers) > 0){
										$comment_status = "SUBMITTED TO GCOM";
										$activity_status = 5;

										foreach ($gcom_approvers as $gcom_approver) {
											$approver = ActivityApprover::find($gcom_approver->id);
											$approver->show = 1;
											$approver->update();
										}
									}else{
										$cdops_approvers = ActivityApprover::getApproverByRole($activity->id,'CD OPS APPROVER');
										if(count($cdops_approvers) > 0){
											$comment_status = "SUBMITTED TO CD OPS";
											$activity_status = 6;

											foreach ($cdops_approvers as $cdops_approver) {
												$approver = ActivityApprover::find($cdops_approver->id);
												$approver->show = 1;
												$approver->update();
											}
										}else{
											$cmd_approvers = ActivityApprover::getApproverByRole($activity->id,'CMD DIRECTOR');
											if(count($cmd_approvers) > 0){
												$comment_status = "SUBMITTED TO CMD";
												$activity_status = 7;

												foreach ($cmd_approvers as $cmd_approver) {
													$approver = ActivityApprover::find($cmd_approver->id);
													$approver->show = 1;
													$approver->update();
												}
											}
										}
									}
								}
								$class = "text-success";
							}

							if($status_id == 2){
								$comment_status = "RECALLED ACTIVITY";
								$class = "text-warning";
								ActivityApprover::resetAll($activity->id);
							}

							$comment = new ActivityComment;
							$comment->created_by = Auth::id();
							$comment->activity_id = $id;
							$comment->comment = Input::get('submitremarks');
							$comment->comment_status = $comment_status;
							$comment->class = $class;
							$comment->save();

							$activity->status_id = $activity_status;
							$activity->pro_recall = $pro_recall;
							$activity->pmog_recall = $pmog_recall;
							$activity->update();

							$arr['success'] = 1;
							Session::flash('class', 'alert-success');
							Session::flash('message', 'Activity successfully updated.'); 
						}
						
					}
					return $arr;
				});
			}


			
			return json_encode($arr);
		}

	}


	// ajax function
	// Activity Materials

	public function submittogcm($id){
		if(Request::ajax()){
			if(Auth::user()->hasRole("PMOG PLANNER")){
				$arr = DB::transaction(function() use ($id)  {
					$activity = Activity::find($id);
					
					if((empty($activity)) || (!ActivityPlanner::myActivity($activity->id)) ){
						$arr['success'] = 0;
						$arr['error'] = $validation['message'];
					}else{
						$status = (int) Input::get('submitstatus');
						$activity_status = 2;
						$pro_recall = 0;
						$pmog_recall = 0;
						if($status == 1){
							// check valdiation
							$required_rules = array('budget','approver','cycle','activity','category','brand','objective','background','customer','scheme','fdapermit','artwork');
							$validation = Activity::validForDownload($activity,$required_rules);
							if($validation['status'] == 0){
								$arr['success'] = 0;
								$arr['error'] = $validation['message'];
								return $arr;
							}else{
								//check next approver
								$pmog_recall = 1;
								$gcom_approvers = ActivityApprover::getApproverByRole($activity->id,'GCOM APPROVER');
								if(count($gcom_approvers) > 0){
									$comment_status = "SUBMITTED TO GCOM";
									$activity_status = 5;
									foreach ($gcom_approvers as $gcom_approver) {
										$approver = ActivityApprover::find($gcom_approver->id);
										$approver->show = 1;
										$approver->update();
									}
								}else{
									$cdops_approvers = ActivityApprover::getApproverByRole($activity->id,'CD OPS APPROVER');
									if(count($cdops_approvers) > 0){
										$comment_status = "SUBMITTED TO CD OPS";
										$activity_status = 6;
										foreach ($cdops_approvers as $cdops_approver) {
											$approver = ActivityApprover::find($cdops_approver->id);
											$approver->show = 1;
											$approver->update();
										}
									}else{
										$cmd_approvers = ActivityApprover::getApproverByRole($activity->id,'CMD DIRECTOR');
										if(count($cmd_approvers) > 0){
											$comment_status = "SUBMITTED TO CMD";
											$activity_status = 7;
											foreach ($cmd_approvers as $cmd_approver) {
												$approver = ActivityApprover::find($cmd_approver->id);
												$approver->show = 1;
												$approver->update();
											}
										}else{
											$comment_status = "APPROVED FOR FIELD";
											$activity_status = 8;
										}
									}
								}
								$class = "text-success";
							}
							
						}elseif($status == 3){
							$comment_status = "RECALLED ACTIVITY";
							$class = "text-warning";
							$pro_recall = 1;
							$pmog_recall = 0;
							$activity_status = 4;
							ActivityApprover::resetAll($activity->id);
						}else{
							$comment_status = "DENIED ACTIVITY";
							$class = "text-danger";
							ActivityApprover::resetAll($activity->id);
						}

						$activity->status_id = $activity_status;
						$activity->pro_recall = $pro_recall;
						$activity->pmog_recall = $pmog_recall;
						$activity->update();

						$comment = new ActivityComment;
						$comment->created_by = Auth::id();
						$comment->activity_id = $id;
						$comment->comment = Input::get('submitremarks');
						$comment->comment_status = $comment_status;
						$comment->class = $class;
						$comment->save();

						$arr['success'] = 1;
						Session::flash('class', 'alert-success');
						Session::flash('message', 'Activity successfully updated.'); 
					}
					return $arr;
				});
			}
			
			return json_encode($arr);
		}
	}

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
				$arr['io_remarks']  = "";
				if(Input::has('io_remarks')){
					$arr['io_remarks'] =  Input::get('io_remarks');
				}
				 
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
					}else{
						ForceAllocation::where('activity_id',$id)->delete();
						$activity->allow_force = false;
						$activity->update();
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

					//update schemes
					// time limit expires
					// $schemes = Scheme::where('activity_id',$id)->get();
					// if(count($schemes) > 0){
					// 	foreach ($schemes as $scheme) {
					// 		SchemeAllocRepository::updateAllocation($scheme);
					// 		$scheme2 = Scheme::find($scheme->id);
					// 		$final_alloc = SchemeAllocation::finalallocation($scheme->id);
					// 		$total_cases = 0;
					// 		$total_deals = 0;
					// 		if($scheme->activity->activitytype->uom == 'CASES'){
					// 			$total_deals = $final_alloc * $scheme->deals;
					// 			$total_cases = $final_alloc;
					// 			$final_tts = $final_alloc * $scheme->deals * $scheme->srp_p; 
					// 		}else{
								
					// 			if($final_alloc > 0){
					// 				$total_cases = round($final_alloc/$scheme->deals);
					// 				$total_deals = $final_alloc;
					// 			}
					// 			$final_tts = $final_alloc * $scheme->srp_p; 
					// 		}
							
					// 		$final_pe = $final_alloc *  $scheme->other_cost;
							
					// 		$scheme2->final_alloc = $final_alloc;
					// 		$scheme2->final_total_deals = $total_deals;
					// 		$scheme2->final_total_cases = $total_cases;
					// 		$scheme2->final_tts_r = $final_tts;
					// 		$scheme2->final_pe_r = $final_pe;
					// 		$scheme2->final_total_cost = $final_tts+$final_pe;
					// 		$scheme2->update();
					// 	}
					// }
					// end update

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
				$networks = ActivityTiming::getTimings($activity->id);
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
		
		if (Input::hasFile('Filedata'))
		{
			$file = Input::file('Filedata');
			$original_file_name = $file->getClientOriginalName();

			$file_name = pathinfo($original_file_name, PATHINFO_FILENAME);

			$extension = File::extension($original_file_name);
			$actual_name = $file_name.'.'.$extension;
			$file_path = $distination.$actual_name;
			// //Alter the file name until it's unique to prevent overwriting
			$count = 1;
			while(File::exists($file_path)) {
				$actual_name = $file_name.'_'.$count.'.'.$extension;
				$file_path = $distination.$actual_name;
				$count++;
			}
			$file->move($distination,$actual_name);

			return (object) array('file_name' => $actual_name,
			 'original_file_name' => $actual_name,
			 'status' => 1);
		}else{

		}
		
	}

	private function doupload_2($path){
		$distination = storage_path().'/uploads/'.$path.'/';
		
		if (Input::hasFile('file'))
		{
			$file = Input::file('file');
			$original_file_name = $file->getClientOriginalName();

			$file_name = pathinfo($original_file_name, PATHINFO_FILENAME);

			$extension = File::extension($original_file_name);
			$actual_name = $file_name.'.'.$extension;
			$file_path = $distination.$actual_name;
			// //Alter the file name until it's unique to prevent overwriting
			$count = 1;
			while(File::exists($file_path)) {
				$actual_name = $file_name.'_'.$count.'.'.$extension;
				$file_path = $distination.$actual_name;
				$count++;
			}
			$file->move($distination,$actual_name);

			return (object) array('file_name' => $actual_name,
			 'original_file_name' => $actual_name,
			 'status' => 1);
		}else{

		}
		
	}

	public function fdaupload($id){

		$activity = Activity::findOrFail($id);

		$input = array('file' => Input::file('file'));
		$rules = array(
			'file' => 'image'
		);
		// Now pass the input and rules into the validator
		$validator = Validator::make($input, $rules);

		if ($validator->fails())
		{
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#attachment")
				->with('class', 'alert-danger')
				->with('message', 'Error uploading file.');
		} else{
			$path = $activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;

			$upload = self::doupload_2($path);

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
		}
	}

	public function fdadelete($id){
		$fda = ActivityFdapermit::find($id);
		$activity_id = Input::get('activity_id');
		$activity = Activity::find($fda->activity_id);
		if(empty($fda)){
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity_id)) . "#attachment")
				->with('class', 'alert-danger')
				->with('message', 'Error deleting file.');
		}else{
			$fda->delete();
			$path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/';
			File::delete($path.$fda->hash_name);
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity_id)) . "#attachment")
				->with('class', 'alert-success')
				->with('message', 'FDA Permits is successfuly deleted!');
		}
	}

	public function fdadownload($id){
		$fda = ActivityFdapermit::find($id);
		$activity = Activity::find($fda->activity_id);
		$path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/';
		return Response::download($path.$fda->hash_name, $fda->hash_name);
	}

	public function fisupload($id){
		$activity = Activity::findOrFail($id);

		$input = array('file' => Input::file('file'));
		$rules = array(
			'file' => 'mimes:xls,xlsx'
		);

		// Now pass the input and rules into the validator
		$validator = Validator::make($input, $rules);

		if ($validator->fails())
		{
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#attachment")
					->with('class', 'alert-danger')
					->with('message', 'Error uploading file.');
		} else{
			$path = $activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
			$upload = self::doupload_2($path);

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
		}

		
	}

	public function fisdelete($id){
		$fis = ActivityFis::find($id);
		$activity_id = Input::get('activity_id');
		$activity = Activity::find($fis->activity_id);
		if(empty($fis)){
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity_id)) . "#attachment")
				->with('class', 'alert-danger')
				->with('message', 'Error deleting file.');
		}else{
			$fis->delete();
			$path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/';
			File::delete($path.$fis->hash_name);
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity_id)) . "#attachment")
				->with('class', 'alert-success')
				->with('message', 'Product Information Sheet is successfuly deleted!');
		}
	}

	public function fisdownload($id){
		$fis = ActivityFis::find($id);
		$activity = Activity::find($fis->activity_id);
		$path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/';
		return Response::download($path.$fis->hash_name, $fis->hash_name);
	}

	public function artworkupload($id){
		
		$activity = Activity::findOrFail($id);

		$input = array('file' => Input::file('Filedata'));
		$rules = array(
			'file' => 'image'
		);
		// Now pass the input and rules into the validator
		$validator = Validator::make($input, $rules);

		if ($validator->fails())
		{
			$arr['success'] = 0;
		} else{
			$path = $activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
			$upload = self::doupload($path);

			$docu = new ActivityArtwork;
			$docu->created_by = Auth::id();
			$docu->activity_id = $id;
			$docu->hash_name = $upload->file_name;
			$docu->file_name = $upload->original_file_name;
			$docu->file_desc = (Input::get('file_desc') =='') ? $upload->original_file_name : Input::get('file_desc');
			$docu->save();

			$arr['id'] = $docu->id; 
			$arr['div_sel'] = Input::get('div_sel');
			$arr['download'] = action('ActivityController@artworkdownload', $docu->id);
			$arr['remove'] = action('ActivityController@artworkdelete');
			$arr['file_name'] = $docu->file_name; 
			$arr['date'] = date('m/d/Y');
			$arr['success'] = 1;
		}
		return json_encode($arr);
	}

	public function artworkdelete(){
		if(Request::ajax()){
			$id = Input::get('id');
			$artwork = ActivityArtwork::find($id);
			if(empty($artwork)){
				$arr['success'] = 0;
				return json_encode($arr);
			}else{
				$activity_id = Input::get('activity_id');
				$activity = Activity::find($artwork->activity_id);
				$artwork->delete();
				$path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/';
				File::delete($path.$artwork->hash_name);
				$arr['success'] = 1;
			}
			return json_encode($arr);
		}
		
	}

	public function artworkdownload($id){
		$artwork = ActivityArtwork::find($id);
		$activity = Activity::find($artwork->activity_id);
		$path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/';
		return Response::download($path.$artwork->hash_name, $artwork->hash_name);
	}

	public function backgroundupload($id){
		$activity = Activity::findOrFail($id);
		if(Input::hasFile('Filedata')){
			$path = $activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
			$upload = self::doupload($path);

			$docu = new ActivityBackground;
			$docu->created_by = Auth::id();
			$docu->activity_id = $id;
			$docu->hash_name = $upload->file_name;
			$docu->file_name = $upload->original_file_name;
			$docu->file_desc = (Input::get('file_desc') =='') ? $upload->original_file_name : Input::get('file_desc');
			$docu->save();

			$arr['id'] = $docu->id; 
			$arr['div_sel'] = Input::get('div_sel');
			$arr['download'] = action('ActivityController@backgrounddownload', $docu->id);
			$arr['remove'] = action('ActivityController@backgrounddelete');
			$arr['file_name'] = $docu->file_name; 
			$arr['date'] = date('m/d/Y');
			$arr['success'] = 1;
		}else{
			$arr['success'] = 0;
		}
		return json_encode($arr);
	}

	public function backgrounddelete(){

		if(Request::ajax()){
			$id = Input::get('id');
			$background = ActivityBackground::find($id);
			if(empty($background)){
				$arr['success'] = 0;
				return json_encode($arr);
			}else{
				$activity_id = Input::get('activity_id');
				$activity = Activity::find($background->activity_id);
				$background->delete();
				$path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/';
				File::delete($path.$background->hash_name);
				$arr['success'] = 1;
			}
			return json_encode($arr);
		}
	}

	public function backgrounddownload($id){
		$background = ActivityBackground::find($id);
		$activity = Activity::find($background->activity_id);
		$path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/';
		return Response::download($path.$background->hash_name, $background->hash_name);
	}

	public function bandingupload($id){
		$activity = Activity::findOrFail($id);
		if(Input::hasFile('Filedata')){
			$path = $activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
			$upload = self::doupload($path);

			$docu = new ActivityBanding;
			$docu->created_by = Auth::id();
			$docu->activity_id = $id;
			$docu->hash_name = $upload->file_name;
			$docu->file_name = $upload->original_file_name;
			$docu->file_desc = (Input::get('file_desc') =='') ? $upload->original_file_name : Input::get('file_desc');
			$docu->save();

			$arr['id'] = $docu->id; 
			$arr['div_sel'] = Input::get('div_sel');
			$arr['download'] = action('ActivityController@bandingdownload', $docu->id);
			$arr['remove'] = action('ActivityController@bandingdelete');
			$arr['file_name'] = $docu->file_name; 
			$arr['date'] = date('m/d/Y');
			$arr['success'] = 1;
		}else{
			$arr['success'] = 0;
		}
		return json_encode($arr);
	}

	public function bandingdelete(){

		if(Request::ajax()){
			$id = Input::get('id');
			$banding = ActivityBanding::find($id);
			if(empty($banding)){
				$arr['success'] = 0;
				return json_encode($arr);
			}else{
				$activity_id = Input::get('activity_id');
				$activity = Activity::find($banding->activity_id);
				$banding->delete();
				$path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/';
				File::delete($path.$banding->hash_name);
				$arr['success'] = 1;
			}
			return json_encode($arr);
		}

	}

	public function bandingdownload($id){
		$banding = ActivityBanding::find($id);
		$activity = Activity::find($banding->activity_id);
		$path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/';
		return Response::download($path.$banding->hash_name, $banding->hash_name);
	}

	public function channels($id){

		$sel = ActivityChannel::getList($id);
		$selected = array();
		foreach ($sel as $value) {
			$selected[] = (string)$value;
		}
		$data['selection'] = Channel::getList();
		$data['selected'] = $selected;
		return Response::json($data,200);
	}

	public function allocsummary($id){
		$filepath = storage_path().'/uploads/tempfiles/Allocation Summary.xls';
		$scheme_allcations = SchemeAllocation::getExportAllocations($id);
		// // Helper::print_r($scheme_allcations);

		Excel::load($filepath, function($excel) use($scheme_allcations)
		{
			$excel->sheet('data', function($sheet) use($scheme_allcations) {
				$sheet->fromModel($scheme_allcations);
			});
		}) -> download('xls');
	}

	public function pistemplate(){
		$filepath = storage_path().'/uploads/tempfiles/PIS Template.xls';		
		return Response::download($filepath);
	}

}