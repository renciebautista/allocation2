<?php

use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use Rencie\Cpm\CpmActivity;
use Rencie\Cpm\Cpm;

class ActivityController extends BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /activity
	 *
	 * @return Response
	 */
	public function index()
	{

		// if(Auth::user()->hasRole("FIELD SALES")){
		// 	Input::flash();
		// 	$cycles = Cycle::getLists();
		// 	$types = ActivityType::getLists();
		// 	$scopes = ScopeType::getLists();
		// 	$activities = Activity::searchField(Input::get('cy'),Input::get('ty'),Input::get('sc'),Input::get('title'));
		// 	return View::make('dashboard.field',compact('activities', 'cycles','types','scopes'));
		// }

		if(Auth::user()->inRoles(['PROPONENT','FIELD SALES'])){
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


		if(Auth::user()->hasRole("ADMINISTRATOR")){
			Input::flash();
			$cycles = Cycle::getLists();
			$scopes = ScopeType::getLists();
			$types = ActivityType::getLists();
			$planners = User::getApprovers(['PMOG PLANNER']);
			$proponents = User::getApprovers(['PROPONENT']);
			$activities = Activity::search(Input::get('pr'),array(9),Input::get('cy'),Input::get('sc'),Input::get('ty'),Input::get('pm'),Input::get('title'));
			return View::make('activity.all',compact('statuses', 'activities', 'cycles', 'scopes', 'types', 'planners', 'proponents'));
		}

	}

	/**
	 * Show the form for creating a new resource.
	 * GET /activity/create
	 *
	 * @return Response
	 */
	public function create($type = 1)
	{
		if($type == 1){
			if(Auth::user()->hasRole("PROPONENT")){
				$scope_types = ScopeType::getLists();
				$planners = User::getApprovers(['PMOG PLANNER']);
				$approvers = User::getApprovers(['GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']);
				$activity_types = ActivityType::getWithNetworks();
				$cycles = Cycle::getLists();
				$divisions = Pricelist::divisions();
				$objectives = Objective::getLists();
				return View::make('activity.create', compact('scope_types', 'planners', 'approvers', 'cycles',
				 'activity_types', 'divisions' , 'objectives',  'users', 'type'));
			}

			return Response::make(View::make('shared/404'), 404);
		}else{
			$activity_types = ActivityType::getWithNetworks();
			$cycles = Cycle::getLists();
			$objectives = Objective::getLists();
			$divisions = Pricelist::divisions();
			return View::make('activity.createcusomized', compact('cycles', 'activity_types', 'objectives', 'divisions'));
			
		}
		
		
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /activity
	 *
	 * @return Response
	 */
	public function store($type = 1)
	{
		if($type == 1){
			if(Auth::user()->hasRole("PROPONENT")){
				$validation = Validator::make(Input::all(), Activity::$rules);
				if($validation->passes())
				{
					$id =  DB::transaction(function()   {
						$scope_id = 1;
						$cycle_id = Input::get('cycle');
						$activity_type_id = Input::get('activity_type');
						$division_code = Input::get('division');
						$category_code = Input::get('category');
						$brand_code = Input::get('brand');

						$activity = new Activity;
						$activity->created_by = Auth::id();
						$activity->proponent_name = Auth::user()->getFullname();
						$activity->contact_no = Auth::user()->contact_no;
						
						$scope = ScopeType::find($scope_id);
						$activity->scope_type_id = $scope_id;
						$activity->scope_desc = $scope->scope_name;

						$cycle = Cycle::find($cycle_id);
						$activity->cycle_id = $cycle_id;
						$activity->cycle_desc = $cycle->cycle_name;

						$activitytype = ActivityType::find($activity_type_id);
						$activity->activity_type_id = $activity_type_id;
						$activity->activitytype_desc = $activitytype->activity_type;
						$activity->uom_desc = $activitytype->uom;

						$activity->duration = (Input::get('lead_time') == '') ? 0 : Input::get('lead_time');
						$activity->edownload_date = date('Y-m-d',strtotime(Input::get('download_date')));
						$activity->eimplementation_date = date('Y-m-d',strtotime(Input::get('implementation_date')));
						$activity->end_date = date('Y-m-d',strtotime(Input::get('end_date')));
						$activity->circular_name = strtoupper(Input::get('activity_title'));
						$activity->background = Input::get('background');
						$activity->instruction = Input::get('instruction');
						$activity->status_id = 1;
						$activity->save();

						// add timings
						$networks = ActivityTypeNetwork::timings($activity->activity_type_id,$activity->edownload_date);
						if(count($networks)> 0){
							$activity_timing = array();

							foreach ($networks as $network) {
								$activity_timing[] = array('activity_id' => $activity->id,
								 	'task_id' => $network->task_id,
									'milestone' => $network->milestone, 
									'task' => $network->task, 
									'responsible' => $network->responsible,
									'duration' => $network->duration, 
									'depend_on' => $network->depend_on,
									'show' => $network->show,
									'start_date' => date('Y-m-d',strtotime($network->start_date)),
									'end_date' => date('Y-m-d',strtotime($network->end_date)),
									'final_start_date' => date('Y-m-d',strtotime($network->start_date)),
									'final_end_date' => date('Y-m-d',strtotime($network->end_date)));
							}
							ActivityTiming::insert($activity_timing);
						}
						
						$activity->activity_code =  ActivityRepository::generateActivityCode($activity,$scope,$cycle,$activitytype,$division_code,$category_code,$brand_code);
						$activity->update();

						// add planner
						ActivityRepository::addPlanner($activity);
						
						// add approver
						ActivityRepository::addApprovers($activity,$cycle);

						// add division
						ActivityRepository::addDivisions($activity);

						// add category
						ActivityRepository::addCategories($activity);

						// add brand
						ActivityRepository::addBrands($activity);

						// add skus
						ActivityRepository::addSkus($activity);
						
						// add objective
						ActivityRepository::addObjectives($activity);

						ActivityCutomerList::addCustomer($activity->id,array());

						$path_1 = storage_path().'/uploads/'.$activity->cycle_id;

						if(!File::exists($path_1)) {
							File::makeDirectory($path_1);
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

				return Redirect::route('activity.create', 1)
					->withInput()
					->withErrors($validation)
					->with('class', 'alert-danger')
					->with('message', 'There were validation errors.');
			}

			
		}else{
			// customized
			$validation = Validator::make(Input::all(), Activity::$rules);
				if($validation->passes())
				{
					$id =  DB::transaction(function()   {
						$scope_id = 2;
						$cycle_id = Input::get('cycle');
						$activity_type_id = Input::get('activity_type');
						$division_code = Input::get('division');
						$category_code = Input::get('category');
						$brand_code = Input::get('brand');

						$activity = new Activity;
						$activity->created_by = Auth::id();
						$activity->proponent_name = Auth::user()->getFullname();
						$activity->contact_no = Auth::user()->contact_no;
						
						$scope = ScopeType::find($scope_id);
						$activity->scope_type_id = $scope_id;
						$activity->scope_desc = $scope->scope_name;

						$cycle = Cycle::find($cycle_id);
						$activity->cycle_id = $cycle_id;
						$activity->cycle_desc = $cycle->cycle_name;

						$activitytype = ActivityType::find($activity_type_id);
						$activity->activity_type_id = $activity_type_id;
						$activity->activitytype_desc = $activitytype->activity_type;
						$activity->uom_desc = $activitytype->uom;

						$activity->duration = (Input::get('lead_time') == '') ? 0 : Input::get('lead_time');
						$activity->edownload_date = date('Y-m-d',strtotime(Input::get('download_date')));
						$activity->eimplementation_date = date('Y-m-d',strtotime(Input::get('implementation_date')));
						$activity->end_date = date('Y-m-d',strtotime(Input::get('end_date')));
						$activity->circular_name = strtoupper(Input::get('activity_title'));
						$activity->background = Input::get('background');
						$activity->instruction = Input::get('instruction');
						$activity->status_id = 1;
						$activity->save();

						// add timings
						$networks = ActivityTypeNetwork::timings($activity->activity_type_id,$activity->edownload_date);
						if(count($networks)> 0){
							$activity_timing = array();

							foreach ($networks as $network) {
								$activity_timing[] = array('activity_id' => $activity->id,
								 	'task_id' => $network->task_id,
									'milestone' => $network->milestone, 
									'task' => $network->task, 
									'responsible' => $network->responsible,
									'duration' => $network->duration, 
									'depend_on' => $network->depend_on,
									'show' => $network->show,
									'start_date' => date('Y-m-d',strtotime($network->start_date)),
									'end_date' => date('Y-m-d',strtotime($network->end_date)),
									'final_start_date' => date('Y-m-d',strtotime($network->start_date)),
									'final_end_date' => date('Y-m-d',strtotime($network->end_date)));
							}
							ActivityTiming::insert($activity_timing);
						}
						
						$activity->activity_code =  ActivityRepository::generateActivityCode($activity,$scope,$cycle,$activitytype,$division_code,$category_code,$brand_code);
						$activity->update();

						// // add planner
						// ActivityRepository::addPlanner($activity);
						
						// // add approver
						// ActivityRepository::addApprovers($activity,$cycle);

						// // add division
						ActivityRepository::addDivisions($activity);
						// // add category
						ActivityRepository::addCategories($activity);
						// // add brand
						ActivityRepository::addBrands($activity);

						// // add skus
						// ActivityRepository::addSkus($activity);
						
						// add objective
						ActivityRepository::addObjectives($activity);

						// ActivityCutomerList::addCustomer($activity->id,array());

						$path_1 = storage_path().'/uploads/'.$activity->cycle_id;

						if(!File::exists($path_1)) {
							File::makeDirectory($path_1);
						}

						$path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id;
						if(!File::exists($path)) {
							File::makeDirectory($path);
						}
						$path2 = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
						if(!File::exists($path2)) {
							File::makeDirectory($path2);
						}

						ActivityTimeline::addTimeline($activity, Auth::user(), "created the activity", '');

						return $activity->id;

						
					});

					

					return Redirect::route('activity.edit',$id)
						->with('class', 'alert-success')
						->with('message', 'Activity "'.strtoupper(Input::get('activity_title')).'" was successfuly created.');
					
				}

				return Redirect::route('activity.create', 2)
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
		$activity = Activity::findOrFail($id);
		$comments = ActivityComment::getList($activity->id);
		$timelines = ActivityTimeline::getTop($activity);

		if($activity->scope_type_id == 1){
			if(Auth::user()->hasRole("PROPONENT")){
				if(!Activity::myActivity($activity)){
					return Response::make(View::make('shared/404'), 404);
				}

				$sel_planner = ActivityPlanner::getPlanner($activity->id);
				$sel_approver = ActivityApprover::getList($activity->id);
				$sel_objectives = ActivityObjective::getList($activity->id);
				$sel_divisions = ActivityDivision::getList($activity->id);
				$sel_involves = ActivitySku::getSkus($activity->id);
				// $involves = Pricelist::items();
				$approvers = User::getApprovers(['GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']);
				
				$objectives = Objective::getLists();
				$budgets = ActivityBudget::getBudgets($activity->id);
				$nobudgets = ActivityNobudget::getBudgets($activity->id);
				$schemes = Scheme::getList($activity->id);
				$scheme_summary = Scheme::getSummary($schemes);
				$materials = ActivityMaterial::getList($activity->id);
				// attachments
				$fdapermits = ActivityFdapermit::getList($activity->id);
				$fis = ActivityFis::getList($activity->id);
				$artworks = ActivityArtwork::getList($activity->id);
				$backgrounds = ActivityBackground::getList($activity->id);
				$bandings = ActivityBanding::getList($activity->id);

				$timings = ActivityTiming::getList($activity->id);

				// $activity_roles = ActivityRole::getList($activity->id);

				$force_allocs = ForceAllocation::getlist($activity->id);
				$areas = Area::getAreaWithGroup();

				foreach ($areas as $key => $area) {
					$area->multi = "1.00";
					foreach ($force_allocs as $force_alloc) {
						if($area->area_code == $force_alloc->area_code){
							$area->multi = $force_alloc->multi;
						}
					}
				}


				if($activity->status_id < 4){
					$submitstatus = array('1' => 'SUBMIT ACTIVITY','4' => 'SAVE AS DRAFT');
					$planners = User::getApprovers(['PMOG PLANNER']);
					$activity_types = ActivityType::getWithNetworks();
					$cycles = Cycle::getLists();
					// $divisions = Sku::getDivisionLists();
					$divisions = Pricelist::divisions();
					

					return View::make('activity.edit', compact('activity', 'planners', 'approvers', 'cycles',
					 'activity_types', 'divisions' , 'sel_divisions','objectives',  'users', 'budgets', 'nobudgets', 'sel_planner','sel_approver',
					 'sel_objectives',  'schemes', 'scheme_summary', 'networks', 'areas', 'timings' ,'sel_involves',
					 'scheme_customers', 'scheme_allcations', 'materials', 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings',
					 'force_allocs', 'comments' ,'submitstatus'));
				}

				if($activity->status_id > 3){
					$submitstatus = array('2' => 'RECALL ACTIVITY');
					$divisions = Sku::getDivisionLists();
					$route = 'activity.index';
					$recall = $activity->pro_recall;
					$submit_action = 'ActivityController@updateactivity';
					return View::make('shared.activity_readonly', compact('activity', 'sel_planner', 'approvers', 
					 'sel_divisions','divisions', 'timings',
					 'objectives',  'users', 'budgets', 'nobudgets','sel_approver',
					 'sel_objectives',  'schemes', 'scheme_summary', 'networks','areas',
					 'scheme_customers', 'scheme_allcations', 'materials', 'force_allocs','sel_involves',
					 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings', 'comments' ,'submitstatus', 'route', 'recall', 'submit_action'));
				}
			}

			if(Auth::user()->hasRole("PMOG PLANNER")){
				if(!ActivityPlanner::myActivity($activity->id)){
					return Response::make(View::make('shared/404'), 404);
				}
				$sel_planner = ActivityPlanner::getPlanner($activity->id);
				$sel_approver = ActivityApprover::getList($activity->id);
				$sel_objectives = ActivityObjective::getList($activity->id);
				$sel_divisions = ActivityDivision::getList($activity->id);
				$sel_involves = ActivitySku::getSkus($activity->id);
				// $involves = Pricelist::items();
				$approvers = User::getApprovers(['GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']);
				// $channels = Channel::getList();
				$objectives = Objective::getLists();
				$budgets = ActivityBudget::getBudgets($activity->id);
				$nobudgets = ActivityNobudget::getBudgets($activity->id);
				$schemes = Scheme::getList($activity->id);
				$scheme_summary = Scheme::getSummary($schemes);

				$materials = ActivityMaterial::getList($activity->id);
				// attachments
				$fdapermits = ActivityFdapermit::getList($activity->id);
				$fis = ActivityFis::getList($activity->id);
				$artworks = ActivityArtwork::getList($activity->id);
				$backgrounds = ActivityBackground::getList($activity->id);
				$bandings = ActivityBanding::getList($activity->id);

				$timings = ActivityTiming::getList($activity->id);

				// $activity_roles = ActivityRole::getList($activity->id);

				$force_allocs = ForceAllocation::getlist($activity->id);
				$areas = Area::getAreaWithGroup();
				foreach ($areas as $key => $area) {
					$area->multi = "1.00";
					foreach ($force_allocs as $force_alloc) {
						if($area->area_code == $force_alloc->area_code){
							$area->multi = $force_alloc->multi;
						}
					}
				}

				if($activity->status_id == 4){
					$submitstatus = array('1' => 'SUBMIT ACTIVITY','2' => 'DENY ACTIVITY');
					$scope_types = ScopeType::getLists($activity->id);
					$planners = User::getApprovers(['PMOG PLANNER']);
					$activity_types = ActivityType::getWithNetworks();
					$cycles = Cycle::getLists();
					// $divisions = Sku::getDivisionLists();
					$divisions = Pricelist::divisions();

					return View::make('downloadedactivity.edit', compact('activity', 'scope_types', 'planners', 'approvers', 'cycles',
					 'activity_types', 'divisions' , 'sel_divisions','objectives',  'users', 'budgets', 'nobudgets', 'sel_planner','sel_approver',
					 'sel_objectives',  'schemes', 'scheme_summary', 'networks', 'areas', 'timings' ,'sel_involves',
					 'scheme_customers', 'scheme_allcations', 'materials', 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings',
					 'force_allocs', 'comments' ,'submitstatus'));
				}else{
					$submitstatus = array('3' => 'RECALL ACTIVITY');
					$divisions = Sku::getDivisionLists();
					$route = 'activity.index';
					$recall = $activity->pmog_recall;
					$submit_action = 'ActivityController@submittogcm';
					return View::make('shared.activity_readonly', compact('activity', 'sel_planner', 'approvers', 'sel_divisions','divisions' ,
					 'objectives',  'users', 'budgets', 'nobudgets','sel_approver',
					 'sel_objectives',  'schemes', 'scheme_summary', 'networks', 'areas',
					 'scheme_customers', 'scheme_allcations', 'materials', 'force_allocs', 'timings' ,'sel_involves',
					 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings', 'comments' ,'submitstatus', 'route', 'recall', 'submit_action'));
				}
			}
			
		}else{
			
			$approvers = User::getApprovers(['GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']);
			$sel_approver = ActivityApprover::getList($activity->id);
			$sel_objectives = ActivityObjective::getList($activity->id);
			$sel_divisions = ActivityDivision::getList($activity->id);
			
			$objectives = Objective::getLists();
			$budgets = ActivityBudget::getBudgets($activity->id);
			$nobudgets = ActivityNobudget::getBudgets($activity->id);
			$schemes = Scheme::getList($activity->id);
			$scheme_summary = Scheme::getSummary($schemes);
			$materials = ActivityMaterial::getList($activity->id);
			// attachments
			$fdapermits = ActivityFdapermit::getList($activity->id);
			$fis = ActivityFis::getList($activity->id);
			$artworks = ActivityArtwork::getList($activity->id);
			$backgrounds = ActivityBackground::getList($activity->id);
			$bandings = ActivityBanding::getList($activity->id);
			// comments
			$comments = ActivityComment::getList($activity->id);

			$timings = ActivityTiming::getList($activity->id);

			// $activity_roles = ActivityRole::getList($activity->id);


			$activity_types = ActivityType::getWithNetworks();
			$cycles = Cycle::getLists();
			$divisions = Pricelist::divisions();

			$allowAdd = false;
			$show_action = true;
			$allowJo = false;
			$joborders = Joborder::getActivityJo($activity);
			$view = 'activity.customizedreadonly';
			$settings = Setting::find(1);
			$ch_approvers = explode(",", $settings->customized_preapprover);

			if(Activity::myActivity($activity)){ // cmd / field
				if($activity->status_id > 3){
					$view = 'activity.customizedreadonly';
					$submitstatus = array('2' => 'RECALL ACTIVITY');
				}else{
					$allowAdd = true;
					$view = 'activity.customizededit';

					if(ActivityMember::allowToSubmit($activity)){
						$submitstatus = array('1' => 'SUBMIT ACTIVITY','4' => 'SAVE AS DRAFT');
						$allowJo = true;
					}else{
						$submitstatus = array('4' => 'SAVE AS DRAFT');
					}
				}
			}else{ //others
				$activity_member = ActivityMember::myActivity($activity->id);
				if($activity_member){ // members
					if(in_array(Auth::user()->department_id, $ch_approvers)){
						if($activity->status_id > 3){
							$view = 'activity.customizedreadonly';
							$show_action = false;
						}else{
							$allowAdd = true;
							$show_action = false;
							$view = 'activity.customizedreadonly';
						}
					}else{
						$show_action = false;
						$view = 'activity.customizedreadonly';
					}
				}else{ 
					return Response::make(View::make('shared/404'), 404);
				}
				
			}
			
			return View::make($view, compact('activity', 'planners', 'approvers', 'sel_approver', 'cycles',
					 'activity_types', 'divisions' ,'objectives',  'users', 'budgets', 'nobudgets', 
					 'sel_objectives', 'sel_divisions',  'schemes', 'scheme_summary', 'networks', 'timings' ,
					 'scheme_customers', 'scheme_allcations', 'materials', 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings',
					 'comments' ,'submitstatus', 'allowAdd', 'joborders', 'show_action', 'allowJo', 'timelines'));
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
		$activity = Activity::findOrFail($id);

		#initiator

		#planner for national

		#member 

		if(Activity::myActivity($activity)){
			if(Request::ajax()){
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
							
							$activity->activity_code =  ActivityRepository::generateActivityCode($activity,$scope,$cycle,$activity_type,$division_code,$category_code,$brand_code);

							$activity->proponent_name = Auth::user()->getFullname();
							$activity->contact_no = Auth::user()->contact_no;
							
							// $activity->scope_type_id = $scope_id;
							// $activity->scope_desc = $scope->scope_name;

							$activity->cycle_id = $cycle_id;
							$activity->cycle_desc = $cycle->cycle_name;

							$activity->activity_type_id = $activity_type_id;
							$activity->activitytype_desc = $activity_type->activity_type;
							$activity->uom_desc = $activity_type->uom;

							$activity->duration = (Input::get('lead_time') == '') ? 0 : Input::get('lead_time');
							$activity->edownload_date = date('Y-m-d',strtotime(Input::get('download_date')));
							$activity->eimplementation_date = date('Y-m-d',strtotime(Input::get('implementation_date')));
							$activity->end_date = date('Y-m-d',strtotime(Input::get('end_date')));
							$activity->circular_name = strtoupper(Input::get('activity_title'));
							$activity->background = Input::get('background');
							$activity->instruction = Input::get('instruction');
							$activity->update();

							// update timings
							if($old_type != $activity->activity_type_id){
								ActivityTiming::where('activity_id',$activity->id)->delete();
								$networks = ActivityTypeNetwork::timings($activity->activity_type_id,$activity->edownload_date);
								if(count($networks)> 0){
									$activity_timing = array();

									foreach ($networks as $network) {
										$activity_timing[] = array('activity_id' => $activity->id, 
											'task_id' => $network->task_id,
											'milestone' => $network->milestone, 
											'task' => $network->task, 
											'responsible' => $network->responsible,
											'duration' => $network->duration, 
											'depend_on' => $network->depend_on,
											'start_date' => date('Y-m-d',strtotime($network->start_date)), 
											'show' => $network->show,
											'end_date' => date('Y-m-d',strtotime($network->end_date)),
											'final_start_date' => date('Y-m-d',strtotime($network->start_date)),
											'final_end_date' => date('Y-m-d',strtotime($network->end_date)));
									}
									ActivityTiming::insert($activity_timing);
								}
							}
							
							// update planner
							ActivityRepository::addPlanner($activity);
							
							// update approver
							ActivityRepository::addApprovers($activity,$cycle);

							// update division
							ActivityRepository::addDivisions($activity);

							// update category
							ActivityRepository::addCategories($activity);

							// update brand
							ActivityRepository::addBrands($activity);

							// update skus
							ActivityRepository::addSkus($activity);
							
							// update objective
							ActivityRepository::addObjectives($activity);
							

							$path_1 = storage_path().'/uploads/'.$activity->cycle_id;
							if(!File::exists($path_1)) {
								File::makeDirectory($path_1);
							}
							
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
						});
						$arr['success'] = 1;
					}
				}
				
				$arr['id'] = $id;
				return json_encode($arr);
			}
		}

		// if(Auth::user()->hasRole("PMOG PLANNER")){
		if(ActivityPlanner::myActivity($activity->id)){
			if(Request::ajax()){
				if((empty($activity)) || (!ActivityPlanner::myActivity($activity->id))){
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

							$activity->activity_code =  ActivityRepository::generateActivityCode($activity,$scope,$cycle,$activity_type,$division_code,$category_code,$brand_code);
							
							// $activity->scope_type_id = $scope_id;
							// $activity->scope_desc = $scope->scope_name;

							$activity->cycle_id = $cycle_id;
							$activity->cycle_desc = $cycle->cycle_name;

							$activity->activity_type_id = $activity_type_id;
							$activity->activitytype_desc = $activity_type->activity_type;
							$activity->uom_desc = $activity_type->uom;

							$activity->duration = (Input::get('lead_time') == '') ? 0 : Input::get('lead_time');
							$activity->edownload_date = date('Y-m-d',strtotime(Input::get('download_date')));
							$activity->eimplementation_date = date('Y-m-d',strtotime(Input::get('implementation_date')));
							$activity->end_date = date('Y-m-d',strtotime(Input::get('end_date')));
							$activity->circular_name = strtoupper(Input::get('activity_title'));
							$activity->background = Input::get('background');
							$activity->instruction = Input::get('instruction');
							$activity->update();

							// update timings
							if($old_type != $activity->activity_type_id){
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
											'end_date' => date('Y-m-d',strtotime($network->end_date)),
											'final_start_date' => date('Y-m-d',strtotime($network->start_date)),
											'final_end_date' => date('Y-m-d',strtotime($network->end_date)));
									}
									ActivityTiming::insert($activity_timing);
								}
							}
							
							// update approver
							ActivityRepository::addApprovers($activity,$cycle);

							// update division
							ActivityRepository::addDivisions($activity);

							// update category
							ActivityRepository::addCategories($activity);

							// update brand
							ActivityRepository::addBrands($activity);

							// update skus
							ActivityRepository::addSkus($activity);
							
							// update objective
							ActivityRepository::addObjectives($activity);

							$path_1 = storage_path().'/uploads/'.$activity->cycle_id;
							if(!File::exists($path_1)) {
								File::makeDirectory($path_1);
							}
							
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
		if ((is_null($activity)) && ($activity->created_by != Auth::id()))
		{
			$class = 'alert-danger';
			$message = 'Activity does not exist.';
		}else{
			DB::beginTransaction();
			try {
				ActivityTiming::where('activity_id',$activity->id)->delete();
				ActivityRole::where('activity_id',$activity->id)->delete();
				ActivityPlanner::where('activity_id',$activity->id)->delete();
				ActivityApprover::where('activity_id',$activity->id)->delete();
				ActivityDivision::where('activity_id',$activity->id)->delete();
				ActivityCategory::where('activity_id',$activity->id)->delete();
				ActivityBrand::where('activity_id',$activity->id)->delete();
				ActivitySku::where('activity_id',$activity->id)->delete();
				ActivityObjective::where('activity_id',$activity->id)->delete();

				ActivityMaterial::where('activity_id',$activity->id)->delete();
				ActivityCustomer::where('activity_id',$activity->id)->delete();
				ActivityCutomerList::where('activity_id',$activity->id)->delete();
				ActivityChannel::where('activity_id',$activity->id)->delete();
				ActivityChannel2::where('activity_id',$activity->id)->delete();
				ActivityChannelList::where('activity_id',$activity->id)->delete();
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

	private function updateActivityStatus($activity){
		$arr = DB::transaction(function() use ($activity)  {		
			if(empty($activity)){
				$arr['success'] = 0;
				$arr['error'] = 'Activity not found';
			}else{
				$status_id = (int) Input::get('submitstatus');
				$planner_count = ActivityPlanner::getPlannerCount($activity->id);
				$activity_status = 3;
				$pro_recall = 0;
				$pmog_recall = 0;
				$allow_comment = false;
				$allow_update = false;

				// save as draft
				if($status_id == 4){
					$comment_status = "SAVED AS DRAFT";
					$class = "text-success";
					$allow_comment = true;
				}

				// recall activity
				if($status_id == 2){
					$comment_status = "RECALLED ACTIVITY";
					$class = "text-warning";
					ActivityApprover::resetAll($activity->id);
					$allow_update = true;
					$allow_comment = true;
				}

				// submit activity
				if($status_id == 1){
					if(count($planner_count) > 0){
						if($activity->activitytype->with_scheme){
							if($activity->activitytype->with_sob){
								$required_rules = array('budget','approver','cycle','division','category','brand','objective','background','customer','scheme','sob','submission_deadline','sob_start_week');
							}else{
								$required_rules = array('budget','approver','cycle','division','category','brand','objective','background','customer','scheme','submission_deadline');
							}
						}else{
							if($activity->activitytype->with_sob){
								$required_rules = array('budget','approver','cycle','division','category','brand','objective','background','customer','sob', 'submission_deadline','sob_start_week');
							}else{
								$required_rules = array('budget','approver','cycle','division','category','brand','objective','background','customer','submission_deadline');
							}

						}
						
					}else{
						if($activity->activitytype->with_scheme){
							if($activity->activitytype->with_sob){
								$required_rules = array('budget','approver','cycle','division','category','brand','objective','background','customer','scheme','sob','submission_deadline','sob_start_week');
							}else{
								$required_rules = array('budget','approver','cycle','division','category','brand','objective','background','customer','scheme','submission_deadline',);
							}
						}else{
							if($activity->activitytype->with_sob){
								$required_rules = array('budget','approver','cycle','division','category','brand','objective','background','customer','sob','submission_deadline', 'sob_start_week');
							}else{
								$required_rules = array('budget','approver','cycle','division','category','brand','objective','background','customer','submission_deadline');
							}
						}
					}

					// dd($required_rules);

					$validation = Activity::validForDownload($activity,$required_rules);

					if($validation['status'] == 0){
						$arr['success'] = 0;
						$arr['error'] = $validation['message'];
						$arr['success'] = 0;
					}else{
						$allow_update = true;
						$allow_comment = true;
						$pro_recall = 1;
						$class = "text-success";
						// check if there is a planner
						if(count($planner_count) > 0){
							$comment_status = "SUBMITTED TO PMOG PLANNER";
							$activity_status = 4;
						}else{
							// check if there is GCOM Approver
							$gcom_approvers = ActivityApprover::getApproverByRole($activity->id,'GCOM APPROVER');
							if(count($gcom_approvers) > 0){
								$comment_status = "SUBMITTED TO GCOM";
								$activity_status = 5;

								foreach ($gcom_approvers as $gcom_approver) {
									$approver = ActivityApprover::find($gcom_approver->id);
									$approver->show = 1;
									$approver->for_approval = 1;
									$approver->update();
								}
							}else{
								// check if there is CD OPS Approver
								$cdops_approvers = ActivityApprover::getApproverByRole($activity->id,'CD OPS APPROVER');
								if(count($cdops_approvers) > 0){
									$comment_status = "SUBMITTED TO CD OPS";
									$activity_status = 6;

									foreach ($cdops_approvers as $cdops_approver) {
										$approver = ActivityApprover::find($cdops_approver->id);
										$approver->show = 1;
										$approver->for_approval = 1;
										$approver->update();
									}
								}else{
									// check if there is CMD DIRECTOR Approver
									$cmd_approvers = ActivityApprover::getApproverByRole($activity->id,'CMD DIRECTOR');
									if(count($cmd_approvers) > 0){
										$comment_status = "SUBMITTED TO CMD";
										$activity_status = 7;

										foreach ($cmd_approvers as $cmd_approver) {
											$approver = ActivityApprover::find($cmd_approver->id);
											$approver->show = 1;
											$approver->for_approval = 1;
											$approver->update();
										}
									}
								}
							}
						}
					}
				}

				

				if($allow_update){
					$activity->status_id = $activity_status;
					$activity->pro_recall = $pro_recall;
					$activity->pmog_recall = $pmog_recall;
					$activity->update();
				}

				if($allow_comment){
					$comment = new ActivityComment;
					$comment->created_by = Auth::id();
					$comment->activity_id =$activity->id;
					$comment->comment = Input::get('submitremarks');
					$comment->comment_status = $comment_status;
					$comment->class = $class;
					$comment->save();

					$arr['success'] = 1;
					Session::flash('class', 'alert-success');
					Session::flash('message', 'Activity successfully updated.');
				}
				
				
			}
			return $arr;
		});
		return $arr;
	}

	public function updateactivity($id){

		if(Request::ajax()){

			$activity = Activity::findOrFail($id);

			if($activity->scope_type_id == 1){
				if(Auth::user()->hasRole("PROPONENT")){
					return json_encode($this->updateActivityStatus($activity));
				}
				
			}else{
				if(Activity::myActivity($activity)){
					return json_encode($this->updateActivityStatus($activity));
				}else{
					// $member = ActivityMember::where('user_id',Auth::id())
					// 	->where('activity_id', $activity->id)
					// 	->first();
					// if(empty($member)){
					// 	$arr['success'] = 0;
					// 	$arr['err_msg'] = 'Member not found';
					// }else{
					// 	// dd(Input::get('submitstatus'));
					// 	$member->activity_member_status_id = Input::get('submitstatus');
					// 	$member->save();

					// 	if(Input::get('submitstatus') == 3){
					// 		ActivityTimeline::addTimeline($activity, Auth::user(), "approved the activity", Input::get('submitremarks'));
					// 	}

					// 	if(Input::get('submitstatus') == 2){
					// 		ActivityTimeline::addTimeline($activity, Auth::user(), "denied the activity", Input::get('submitremarks'));
					// 	}




					// 	// if(!$activity->channel_approved){
					// 	// 	$ch_members = ActivityMember::getByDepartmentId(['2']);
					// 	// 	$approve = 1;
					// 	// 	foreach ($ch_members as $member) {
					// 	// 		if($member->activity_member_status_id < 3){
					// 	// 			$approve = 0;
					// 	// 		}
					// 	// 	}
					// 	// 	$activity->channel_approved = $approve;
					// 	// 	$activity->save();
					// 	// }

					// 	$arr['success'] = 1;
					// }
					// return json_encode($arr, 200);
				}
				
			}
			
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
							$required_rules = array('budget','approver','cycle','activity','category','brand','objective','background','customer', 'submission_deadline');

							if($activity->activitytype->with_scheme){
								array_push($required_rules, 'scheme');
							}

							if($activity->activitytype->with_sob){
								array_push($required_rules, 'sob_start_week');
							}


							if($activity->activitytype->with_msource){
								array_push($required_rules, 'material_source');
							}
							
							$validation = Activity::validForDownload($activity,$required_rules);
							if($validation['status'] == 0){
								$arr['success'] = 0;
								$arr['error'] = $validation['message'];
								return $arr;
							}else{
								// check if there is a gcom
								$pmog_recall = 1;
								$gcom_approvers = ActivityApprover::getApproverByRole($activity->id,'GCOM APPROVER');
								if(count($gcom_approvers) > 0){
									$comment_status = "SUBMITTED TO GCOM";
									$activity_status = 5;
									foreach ($gcom_approvers as $gcom_approver) {
										$approver = ActivityApprover::find($gcom_approver->id);
										$approver->show = 1;
										$approver->for_approval = 1;
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
											$approver->for_approval = 1;
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
												$approver->for_approval = 1;
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
				$budget->budget_desc = $budget_type->budget_type;
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
				$budget->budget_desc = $budget_type->budget_type;
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
				$budget->budget_desc = $budget_type->budget_type;
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
				$budget->budget_desc = $budget_type->budget_type;
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
			$activity = Activity::findOrFail($id);
			if(empty($activity)){
				$arr['success'] = 0;
			}else{
				DB::beginTransaction();

				try {
					$_customers = Input::get('customers');
					ActivityCustomer::where('activity_id',$id)->delete();

					$enable_force = (Input::has('allow_force')) ? 1 : 0;
					$activity->allow_force = $enable_force;
					$activity->update();
					if(!empty($_customers)){

						$customers = explode(",", $_customers);
						if(!empty($customers)){
							$activity_customers = array();
						
							foreach ($customers as $customer_node){
								$activity_customers[] = array('activity_id' => $id, 'customer_node' => trim($customer_node));
							}
							ActivityCustomer::insert($activity_customers);


							// dd($activity_customers);
							ActivityCutomerList::addCustomer($activity->id,$activity_customers);

							ForceAllocation::where('activity_id',$id)->delete();
							if($enable_force){
								$area_list = array();
								foreach (Input::get('force_alloc') as $key => $value) {
									$area = Area::getArea($key);
									$area_list[] = array('activity_id' => $id, 
										'group_code' => $area->group_code,
										'group_desc' => $area->group_name,
										'area_code' => $key, 
										'area_desc' => $area->area_name,
										'multi' => $value);
								}
								ForceAllocation::insert($area_list);
							}
							
						}
					}else{
						ForceAllocation::where('activity_id',$id)->delete();
						$activity->allow_force = false;
						$activity->update();
					}

					$_channels = Input::get('channels_involved');
					ActivityChannel2::where('activity_id',$id)->delete();
					$activity_channels = array();
					if(!empty($_channels)){
						$channels = explode(",", $_channels);
						if(!empty($channels)){
							$channel_group = array();
							foreach ($channels as $channel_node){
								$activity_channels[] = array('activity_id' => $id, 'channel_node' => trim($channel_node));
							}
							ActivityChannel2::insert($activity_channels);

						}
					}

					ActivityChannelList::addChannel($activity->id,$activity_channels);

					DB::commit();

					// update all schemes
					$schemes = Scheme::getList($activity->id);

					foreach ($schemes as $scheme) {
						if($scheme->compute == 1){
							$scheme->updating = 1;
							$scheme->update();
							if($_ENV['MAIL_TEST']){
								Queue::push('SchemeScheduler', array('id' => $scheme->id),'scheme');
							}else{
								Queue::push('SchemeScheduler', array('id' => $scheme->id),'p_scheme');
							}
						}else{
							$scheme->updating = 0;
							$scheme->update();
						}
						
					}
					// end update
					$arr['success'] = 1;
					Session::flash('class', 'alert-success');
					Session::flash('message', 'Customer details, allocation per scheme and sob details were updated.');
				} catch (Exception $e) {
					DB::rollback();
					$arr['success'] = 0;
					Session::flash('class', 'alert-danger');
					Session::flash('message', 'An error occcured while updating activity customers.');
				}
				
				
			}
			
			$arr['id'] = $id;
			// dd($arr);
			return json_encode($arr);
		}
	}

	// public function updateforcealloc(){
	// 	$id = Input::get("f_id");
	// 	if(Request::ajax()){
	// 		$arr['f_percent'] = Input::get("f_percent");
	// 		$force_alloc = ForceAllocation::find($id);
	// 		if(!empty($force_alloc)){
	// 			$force_alloc->multi = $arr['f_percent'];
	// 			$force_alloc->update();
	// 			$arr['success'] = 1;
	// 		}else{
	// 			$arr['success'] = 0;
	// 		}
	// 	}
		
	// 	$arr['id'] = $id;
	// 	return json_encode($arr);
	// }

	public function updatebilling($id){
		if(Request::ajax()){
			$activity = Activity::find($id);
			if(empty($activity)){
				$arr['success'] = 0;
			}else{
				DB::transaction(function() use ($activity)  {
					$billing_date = date('Y-m-d',strtotime(Input::get('billing_deadline')));
					if($billing_date == '1970-01-01'){
						$billing_date = null;
					}
					$activity->billing_date = $billing_date;
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
				$activitymaterial = MaterialSource::find($source_id);
				$material = new ActivityMaterial;

				$material->activity_id = $id;
				$material->source_id = $source_id;
				$material->source_desc = $activitymaterial->source;
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

				$material->source_id = $source_id;
				$material->material = Input::get('material');
				$material->source_desc = $source->source;
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
		
		// Validator::extend('hashmatch', function($attribute, $value, $parameters)
		// {
		// 	Helper::print_r($parameters);
		// 	Helper::print_r($attribute);
		// 	Helper::print_r($value);
		// 	echo $value->guessExtension();
		//     // return Hash::check($value, Auth::user()->$parameters[0]);
		// });
		// $messages = array(
		//     'hashmatch' => 'Your current password must match your account password.'
		// );
		// $rules = array(
		//     'current_password' => 'required|hashmatch:password',
		//     'password'         => 'required|confirmed|min:4|different:current_password'
		// );

		// $validation = Validator::make( Input::all(), $rules, $messages );

		$activity = Activity::findOrFail($id);

		$input = array('file' => Input::file('file'));
		// 'file' => 'required|mimes:jpg,jpeg,png,gif,pdf,xps'
		$rules = array(
			'file' => 'required'
		);
		// Now pass the input and rules into the validator
		$validator = Validator::make($input, $rules);
		
		
		if ($validator->fails())
		{
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#attachment")
				->with('class', 'alert-danger')
				->withErrors($validator)
				->with('message', 'Error uploading file.');

			// print_r($validator->errors()); //Messages with failed rules
			// print_r($validator->failed()); //Always Empty

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

		$input = array('file' => Input::file('Filedata'));
		$rules = array(
			'file' => 'mimes:xls,xlsx|required'
		);

		// Now pass the input and rules into the validator
		$validator = Validator::make($input, $rules);

		if ($validator->fails())
		{
			// remove uploaded file

			$arr['mesage'] = 'Invalid file type.';
			return Response::json($arr, 400);
		} else{
			$path = $activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
			$upload = self::doupload($path);

			try {
				$pis = Excel::selectSheets('Output')->load(storage_path().'/uploads/'.$path."/".$upload->file_name)->get();
				if($pis[1][0] != "031988"){

					$path_delete = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id.'/';
					File::delete($path_delete.$upload->file_name);

					$arr['mesage'] = 'Invalid file type.';
					return Response::json($arr, 400);
				}
			} catch (Exception $e) {	
				$arr['mesage'] = 'Invalid file type.';
				return Response::json($arr, 400);
			}


			$docu = new ActivityFis;
			$docu->created_by = Auth::id();
			$docu->activity_id = $id;
			$docu->hash_name = $upload->file_name;
			$docu->file_name =  $upload->original_file_name;
			$docu->file_desc = (Input::get('file_desc') =='') ? $upload->original_file_name : Input::get('file_desc');
			$docu->save();

			$arr['id'] = $docu->id; 
			$arr['div_sel'] = Input::get('div_sel');
			$arr['download'] = action('ActivityController@fdadownload', $docu->id);
			$arr['remove'] = action('ActivityController@fisdelete');
			$arr['file_name'] = $docu->file_name; 
			$arr['date'] = date('m/d/Y');
			$arr['mesage'] = 'File successfuly uploaded';
			return Response::json($arr, 200);
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
			$arr['mesage'] = 'Error uploading file';
			return Response::json($arr, 400);
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

			$arr['mesage'] = 'File successfuly uploaded';
			return Response::json($arr, 200);
		}
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
			$arr['mesage'] = 'File successfuly uploaded';
			return Response::json($arr, 200);
		}else{
			$arr['mesage'] = 'Error uploading file';
			return Response::json($arr, 400);
		}
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
			$arr['mesage'] = 'File successfuly uploaded';
			return Response::json($arr, 200);
		}else{
			$arr['mesage'] = 'Error uploading file';
			return Response::json($arr, 400);
		}
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
		$activity = Activity::findOrFail($id);
		$scheme_allcations = SchemeAllocation::getExportAllocations($id)->toArray();

		$header = ['SCHEME ID', 
			'SCHEME','GROUP', 'AREA', 'SOLD TO CODE', 'SOLD TO', 'SHIP TO CODE', 'SHIP TO',
			'CHANNEL', 'OUTLET', 
			'SOLD TO GSV', 'FORCED SOLD TO GSV', 'SOLD TO GSV PERCENTAGE', 'FORCED SOLD TO GSV PERCENTAGE', 'SOLD TO ALLOCATION', 'FORCED SOLD TO ALLOCATION',
			'SHIP TO GSV', 'FORCED SHIP TO GSV', 'SHIP TO GSV PERCENTAGE', 'FORCED SHIP TO GSV PERCENTAGE', 'SHIP TO ALLOCATION', 'FORCED SHIP TO ALLOCATION',
			'OUTLET GSV', 'FORCED OUTLET GSV', 'OUTLET GSV PERCENTAGE', 'FORCED OUTLET GSV PERCENTAGE', 'OUTLET ALLOCATION', 'FORCED OUTLET ALLOCATION',
			'FINAM MULTIPLIER', 'COMPUTED ALLOCATION', 'FORCED ALLOCATION', 'FINAL ALLOCATION', 'DEAL', 'CASES', 'TTS BUDGET', 'PE BUDGET', 'SHOW IN REPORT'];

		$writer = WriterFactory::create(Type::XLSX);
		$writer->setShouldCreateNewSheetsAutomatically(true); // default value
		$writer->openToBrowser($activity->circular_name.' Allocation Summary.xlsx'); // write data to a file or to a PHP stream
		$writer->addRow($header); // add multiple rows at a time

		foreach($scheme_allcations as $key => $value)
		{
			// dd($value['sold_to_gsv']);
			if(isset($value['sold_to_gsv'])){
				$value['sold_to_gsv'] = (double) $value['sold_to_gsv'];
			}
			
			if(isset($value['forced_sold_to_gsv'])){
				$value['forced_sold_to_gsv'] = (double) $value['forced_sold_to_gsv'];
			}

			if(isset($value['sold_to_gsv_p'])){
				$value['sold_to_gsv_p'] = (double) $value['sold_to_gsv_p'];
			}

			if(isset($value['forced_sold_to_gsv_p'])){
				$value['forced_sold_to_gsv_p'] = (double) $value['forced_sold_to_gsv_p'];
			}

			if(isset($value['ship_to_gsv'])){
				$value['ship_to_gsv'] = (double) $value['ship_to_gsv'];
			}

			if(isset($value['forced_ship_to_gsv'])){
				$value['forced_ship_to_gsv'] = (double) $value['forced_ship_to_gsv'];
			}

			if(isset($value['ship_to_gsv_p'])){
				$value['ship_to_gsv_p'] = (double) $value['ship_to_gsv_p'];
			}

			if(isset($value['forced_ship_to_gsv_p'])){
				$value['forced_ship_to_gsv_p'] = (double) $value['forced_ship_to_gsv_p'];
			}

			if(isset($value['outlet_to_gsv'])){
				$value['outlet_to_gsv'] = (double) $value['outlet_to_gsv'];
			}

			if(isset($value['outlet_to_gsv_p'])){
				$value['outlet_to_gsv_p'] = (double) $value['outlet_to_gsv_p'];
			}

			if(isset($value['forced_outlet_to_gsv_p'])){
				$value['forced_outlet_to_gsv_p'] = (double) $value['forced_outlet_to_gsv_p'];
			}

			if(isset($value['multi'])){
				$value['multi']= (double) $value['multi'];
			}

			if(isset($value['tts_budget'])){
				$value['tts_budget'] = (double) $value['tts_budget'];
			}

			if(isset($value['pe_budget'])){
				$value['pe_budget'] = (double) $value['pe_budget'];
			}
			$writer->addRow($value); // add multiple rows at a time
		} 
		$writer->close();


	}

	public function pistemplate(){
		$filepath = storage_path().'/uploads/tempfiles/PIS Template_July 29, 2015.xls';		
		return Response::download($filepath);
	}

	public function duplicate($id){
		$activity = Activity::findOrFail($id);

		if ((is_null($activity)) && ($activity->created_by != Auth::id()))
		{
			$class = 'alert-danger';
			$message = 'Activity does not exist.';
		}else{

			DB::beginTransaction();

			try {

				$activity_type = ActivityType::find($activity->activity_type_id);

				$data = array();
				$activities = ActivityTypeNetwork::activities($activity->activity_type_id);
				$holidays = Holiday::allHoliday();
				$data['days'] = 1;
				$data['start_date'] = ActivityTypeNetwork::getImplemetationDate( date('m/d/Y'),$holidays,0);
				if(count($activities)>0){
					$cpm = new Cpm($activities);
					$data['days'] = $cpm->TotalDuration();
					$data['cpm'] = $cpm->CriticalPath();
				}
				
				$data['min_date'] = ActivityTypeNetwork::getImplemetationDate($data['start_date'],$holidays,$data['days'] - 1);
				$data['end_date'] = ActivityTypeNetwork::getImplemetationDate($data['start_date'],$holidays,$data['days'] - 1);

				// dd($data);
				
				$new_activity = new Activity;
				$new_activity->created_by = Auth::id();
				$new_activity->proponent_name = $activity->proponent_name;
				$new_activity->contact_no = $activity->contact_no;
				$new_activity->activity_code =  $activity->activity_code;
				$new_activity->circular_name = $activity->circular_name;
				$new_activity->scope_type_id = $activity->scope_type_id;
				$new_activity->scope_desc = $activity->scope_desc;
				$new_activity->duration = $data['days'];
				$new_activity->edownload_date = date_format(date_create_from_format('m/d/Y', $data['start_date']), 'Y-m-d');
				$new_activity->eimplementation_date = date_format(date_create_from_format('m/d/Y', $data['end_date']), 'Y-m-d');
				$new_activity->cycle_id = $activity->cycle_id;
				$new_activity->cycle_desc = $activity->cycle_desc;
				$new_activity->activity_type_id = $activity->activity_type_id;
				$new_activity->activitytype_desc = $activity->activitytype_desc;
				$new_activity->uom_desc = $activity->uom_desc;
				$new_activity->division_code = $activity->division_code;
				$new_activity->background = $activity->background;
				$new_activity->status_id = 1;
				$new_activity->billing_date =  $activity->billing_date;
				$new_activity->billing_remarks =  $activity->billing_remarks;
				$new_activity->instruction = $activity->instruction;
				$new_activity->allow_force =  $activity->allow_force;
				$new_activity->end_date = date_format(date_create_from_format('m/d/Y', $data['end_date']), 'Y-m-d');;

				$new_activity->with_sob = $activity_type->with_sob;

				$new_activity->save();

				// add timings
				// $timings = ActivityTiming::where('activity_id',$activity->id)->get();
				// if(!empty($timings)){
				// 	$activity_timing = array();
				// 	foreach ($timings as $timing) {
				// 		$activity_timing[] = array('activity_id' => $new_activity->id, 
				// 				'task_id' => $timing->task_id,
				// 				'milestone' => $timing->milestone, 
				// 				'task' => $timing->task, 
				// 				'responsible' => $timing->responsible,
				// 				'duration' => $timing->duration, 
				// 				'depend_on' => $timing->depend_on,
				// 				'show' => $timing->show,
				// 				'start_date' => date('Y-m-d',strtotime($timing->start_date)),
				// 				'end_date' => date('Y-m-d',strtotime($timing->end_date)),
				// 				'final_start_date' => date('Y-m-d',strtotime($timing->final_start_date)),
				// 				'final_end_date' => date('Y-m-d',strtotime($timing->final_end_date)));
				// 	}
				// 	if(!empty($activity_timing)){
				// 		ActivityTiming::insert($activity_timing);
				// 	}
				// }
				$networks = ActivityTypeNetwork::timings($new_activity->activity_type_id,$new_activity->edownload_date);
				if(count($networks)> 0){
					$activity_timing = array();
					foreach ($networks as $network) {
						$activity_timing[] = array('activity_id' => $new_activity->id,
						 	'task_id' => $network->task_id,
							'milestone' => $network->milestone, 
							'task' => $network->task, 
							'responsible' => $network->responsible,
							'duration' => $network->duration, 
							'depend_on' => $network->depend_on,
							'show' => $network->show,
							'start_date' => date('Y-m-d',strtotime($network->start_date)),
							'end_date' => date('Y-m-d',strtotime($network->end_date)),
							'final_start_date' => date('Y-m-d',strtotime($network->start_date)),
							'final_end_date' => date('Y-m-d',strtotime($network->end_date)));
					}
					ActivityTiming::insert($activity_timing);
				}

				// add roles
				$roles = ActivityRole::where('activity_id',$activity->id)->get();
				if(!empty($roles)){
					$activity_role = array();
					foreach ($roles as $role) {
						$activity_role[] = array('activity_id' => $new_activity->id,
							'owner' => $role->owner,
							'point' => $role->point,
							'timing' => $role->timing,);
					}
					if(!empty($activity_role)){
						ActivityRole::insert($activity_role);
					}
					
				}

				// add planner
				$planners = ActivityPlanner::where('activity_id',$activity->id)->get();
				if(!empty($planners)){
					foreach ($planners as $planner) {
						ActivityPlanner::insert(array('activity_id' => $new_activity->id, 
							'user_id' => $planner->user_id,
							'planner_desc' => $planner->planner_desc,
							'contact_no' => $planner->contact_no));
					}
				}
				
				// add approver
				$approvers = ActivityApprover::where('activity_id',$activity->id)->get();
				if(!empty($approvers)){
					$activity_approver = array();
					foreach ($approvers as $approver) {
						$activity_approver[] = array('activity_id' => $new_activity->id, 
							'user_id' => $approver->user_id,
							'approver_desc' => $approver->approver_desc,
							'contact_no' => $approver->contact_no,
							'status_id' => 0,
							'for_approval' => 0,
							'group_id' => $approver->group_id);
					}
					if(!empty($activity_approver)){
						ActivityApprover::insert($activity_approver);
					}
					
				}

				// add division
				$divisions = ActivityDivision::where('activity_id',$activity->id)->get();
				if(!empty($divisions)){
					$activity_division = array();
					foreach ($divisions as $division){
						$activity_division[] = array('activity_id' => $new_activity->id, 
							'division_code' => $division->division_code,
							'division_desc' => $division->division_desc);
					}
					if(!empty($activity_division)){
						ActivityDivision::insert($activity_division);
					}
					
				}

				// add category
				$categories = ActivityCategory::where('activity_id',$activity->id)->get();
				if(!empty($categories)){
					$activity_category = array();
					foreach ($categories as $category){
						$activity_category[] = array('activity_id' => $new_activity->id, 
							'category_code' => $category->category_code,
							'category_desc' => $category->category_desc);
					}
					if(!empty($activity_category)){
						ActivityCategory::insert($activity_category);
					}
					
				}

				// add brand
				$brands = ActivityBrand::where('activity_id',$activity->id)->get();
				if(!empty($brands)){
					$activity_brand = array();
					foreach ($brands as $brand){
						$activity_brand[] = array('activity_id' => $new_activity->id, 
							'brand_code' => $brand->brand_code,
							'brand_desc' => $brand->brand_desc,
							'b_desc' => $brand->b_desc,
							);
					}
					if(!empty($activity_brand)){
						ActivityBrand::insert($activity_brand);
					}
					
				}

				// add skus
				$activity_skus = ActivitySku::where('activity_id',$activity->id)->get();
				if(!empty($activity_skus)){
					$activity_sku = array();
					foreach ($activity_skus as $sku){
						$activity_sku[] = array('activity_id' => $new_activity->id, 
							'sap_code' => $sku->sap_code,
							'sap_desc' => $sku->sap_desc);
					}
					if(!empty($activity_sku)){
						ActivitySku::insert($activity_sku);
					}
					
				}

				// add objective
				$objectives = ActivityObjective::where('activity_id',$activity->id)->get();
				if(!empty($objectives)){
					$activity_objective = array();
					foreach ($objectives as $objective){
						$activity_objective[] = array('activity_id' => $new_activity->id, 
							'objective_id' => $objective->objective_id,
							'objective_desc' => $objective->objective_desc);
					}
					if(!empty($activity_objective)){
						ActivityObjective::insert($activity_objective);
					}
					
				}

				// add materials
				$materials = ActivityMaterial::where('activity_id',$activity->id)->get();
				if(!empty($materials)){
					$activity_materials = array();
					foreach ($materials as $material){
						$activity_materials[] = array('activity_id' => $new_activity->id, 
							'source_id' => $material->source_id, 
							'source_desc' => $material->source_desc,
							'material' => $material->material);
					}
					if(!empty($activity_materials)){
						ActivityMaterial::insert($activity_materials);
					}
					
				}

				// add customer
				$customers = ActivityCustomer::where('activity_id',$activity->id)->get();
				if(!empty($customers)){
					$activity_customers = array();
					foreach ($customers as $customer){
						$activity_customers[] = array('activity_id' => $new_activity->id, 
							'customer_node' => $customer->customer_node);
					}
					if(!empty($activity_customers)){
						ActivityCustomer::insert($activity_customers);
					}
					
				}

				$customer_lists = ActivityCutomerList::where('activity_id',$activity->id)->get();
				$new_customerlist = array();
				foreach ($customer_lists as $key => $customer_list) {
					$new_customerlist[] = array('parent_id' => $customer_list->parent_id,
						'activity_id' =>$new_activity->id,
						'title' => $customer_list->title,
						'isfolder' => $customer_list->isfolder,
						'key' => $customer_list->key,
						'unselectable' => $customer_list->unselectable,
						'selected' => $customer_list->selected);
				}
				if(count($new_customerlist) > 0){
					ActivityCutomerList::insert($new_customerlist);
				}

				// add force allocation
				$force_allocations = ForceAllocation::where('activity_id',$activity->id)->get();
				if(!empty($force_allocations)){
					$activity_force_allocations = array();
					foreach ($force_allocations as $force_allocation){
						$activity_force_allocations[] = array('activity_id' => $new_activity->id, 
							'group_code' => $force_allocation->group_code,
							'group_desc' => $force_allocation->group_desc,
							'area_code' => $force_allocation->area_code, 
							'area_desc' => $force_allocation->area_desc, 
							'multi' => $force_allocation->multi);
					}
					if(!empty($activity_force_allocations)){
						ForceAllocation::insert($activity_force_allocations);
					}
					
				}

				// add channels
				$channels = ActivityChannel2::where('activity_id',$activity->id)->get();
				if(!empty($channels)){
					$activity_channels = array();
					foreach ($channels as $channel){
						$activity_channels[] = array('activity_id' => $new_activity->id, 
							'channel_node' => $channel->channel_node);
					}
					if(!empty($activity_channels)){
						ActivityChannel2::insert($activity_channels);
					}
				}

				$channellists = ActivityChannelList::where('activity_id',$activity->id)->get();
				$new_channells = array();
				foreach ($channellists as $key => $channellist) {
					$new_channells[] = array('parent_id' => $channellist->parent_id,
						'activity_id' =>$new_activity->id,
						'title' => $channellist->title,
						'isfolder' => $channellist->isfolder,
						'key' => $channellist->key,
						'unselectable' => $channellist->unselectable,
						'selected' => $channellist->selected);
				}
				if(count($new_channells) > 0){
					ActivityChannelList::insert($new_channells);
				}

				// add budget
				$budgets = ActivityBudget::where('activity_id',$activity->id)->get();
				if(!empty($budgets)){
					$activity_budgets = array();
					foreach ($budgets as $budget){
						$activity_budgets[] = array('activity_id' => $new_activity->id,
						 'budget_type_id' => $budget->budget_type_id,
						 'budget_desc' => $budget->budget_desc,
						 'io_number' => $budget->io_number,
						 'amount' => $budget->amount,
						 'start_date' => $budget->start_date,
						 'end_date' => $budget->end_date,
						 'remarks' => $budget->remarks,
						 'created_at' => date('Y-m-d H:m:s'),
						 'updated_at' => date('Y-m-d H:m:s'));
					}
					if(!empty($activity_budgets)){
						ActivityBudget::insert($activity_budgets);
					}
				}

				// add no budget
				$nobudgets = ActivityNobudget::where('activity_id',$activity->id)->get();
				if(!empty($nobudgets)){
					$activity_nobudgets = array();
					foreach ($nobudgets as $budget){
						$activity_nobudgets[] = array('activity_id' => $new_activity->id,
						 'budget_type_id' => $budget->budget_type_id,
						 'budget_desc' => $budget->budget_desc,
						 'budget_no' => $budget->budget_no,
						 'budget_name' => $budget->budget_name,
						 'amount' => $budget->amount,
						 'start_date' => $budget->start_date,
						 'end_date' => $budget->end_date,
						 'remarks' => $budget->remarks,
						 'created_at' => date('Y-m-d H:m:s'),
						 'updated_at' => date('Y-m-d H:m:s'));
					}
					if(!empty($activity_nobudgets)){
						ActivityNobudget::insert($activity_nobudgets);
					}
				}

				// add fda permit
				$fdapermits = ActivityFdapermit::where('activity_id',$activity->id)->get();
				if(!empty($fdapermits)){
					$activity_fdapermits = array();
					foreach ($fdapermits as $fdapermit){
						$activity_fdapermits[] = array('activity_id' => $new_activity->id,
						 'created_by' => $fdapermit->created_by,
						 'permit_no' => $fdapermit->permit_no,
						 'hash_name' => $fdapermit->hash_name,
						 'file_name' => $fdapermit->file_name,
						 'file_desc' => $fdapermit->file_desc,
						 'created_at' => date('Y-m-d H:m:s'),
						 'updated_at' => date('Y-m-d H:m:s'));
					}
					if(!empty($activity_fdapermits)){
						ActivityFdapermit::insert($activity_fdapermits);
					}
				}

				// add pis
				$pis = ActivityFis::where('activity_id',$activity->id)->get();
				if(!empty($pis)){
					$activity_pis = array();
					foreach ($pis as $pi){
						$activity_pis[] = array('activity_id' => $new_activity->id,
						 'created_by' => $pi->created_by,
						 'hash_name' => $pi->hash_name,
						 'file_name' => $pi->file_name,
						 'file_desc' => $pi->file_desc,
						 'created_at' => date('Y-m-d H:m:s'),
						 'updated_at' => date('Y-m-d H:m:s'));
					}
					if(!empty($activity_pis)){
						ActivityFis::insert($activity_pis);
					}
				}

				// add artworks
				$artworks = ActivityArtwork::where('activity_id',$activity->id)->get();
				if(!empty($artworks)){
					$activity_artworks = array();
					foreach ($artworks as $artwork){
						$activity_artworks[] = array('activity_id' => $new_activity->id,
						 'created_by' => $artwork->created_by,
						 'hash_name' => $artwork->hash_name,
						 'file_name' => $artwork->file_name,
						 'file_desc' => $artwork->file_desc,
						 'created_at' => date('Y-m-d H:m:s'),
						 'updated_at' => date('Y-m-d H:m:s'));
					}
					if(!empty($activity_artworks)){
						ActivityArtwork::insert($activity_artworks);
					}
				}

				// add backgrounds
				$backgrounds = ActivityBackground::where('activity_id',$activity->id)->get();
				if(!empty($backgrounds)){
					$activity_backgrounds = array();
					foreach ($backgrounds as $background){
						$activity_backgrounds[] = array('activity_id' => $new_activity->id,
						 'created_by' => $background->created_by,
						 'hash_name' => $background->hash_name,
						 'file_name' => $background->file_name,
						 'file_desc' => $background->file_desc,
						 'created_at' => date('Y-m-d H:m:s'),
						 'updated_at' => date('Y-m-d H:m:s'));
					}
					if(!empty($activity_backgrounds)){
						ActivityBackground::insert($activity_backgrounds);
					}
				}

				// add guidelines / banding
				$guidelines = ActivityBanding::where('activity_id',$activity->id)->get();
				if(!empty($guidelines)){
					$activity_guidelines = array();
					foreach ($guidelines as $guideline){
						$activity_guidelines[] = array('activity_id' => $new_activity->id,
						 'created_by' => $guideline->created_by,
						 'hash_name' => $guideline->hash_name,
						 'file_name' => $guideline->file_name,
						 'file_desc' => $guideline->file_desc,
						 'created_at' => date('Y-m-d H:m:s'),
						 'updated_at' => date('Y-m-d H:m:s'));
					}
					if(!empty($activity_guidelines)){
						ActivityBanding::insert($activity_guidelines);
					}
				}

				// add schemes
				$schemes = Scheme::where('activity_id',$activity->id)
					->orderBy('id')
					->get();
				if(!empty($schemes)){
					$activity_schemes = array();
					foreach ($schemes as $scheme){
						$new_scheme = new Scheme;
						$new_scheme->activity_id = $new_activity->id;
						$new_scheme->name = $scheme->name;
						$new_scheme->item_code = $scheme->item_code;
						$new_scheme->item_desc = $scheme->item_desc;
						$new_scheme->item_barcode = $scheme->item_barcode;
						$new_scheme->item_casecode = $scheme->item_casecode;
						$new_scheme->pr = $scheme->pr;
						$new_scheme->srp_p = $scheme->srp_p;
						$new_scheme->other_cost = $scheme->other_cost;
						$new_scheme->ulp = $scheme->ulp;
						$new_scheme->cost_sale = $scheme->cost_sale;
						$new_scheme->quantity = $scheme->quantity;
						$new_scheme->deals = $scheme->deals;
						$new_scheme->total_deals = $scheme->total_deals;
						$new_scheme->total_cases = $scheme->total_cases;
						$new_scheme->tts_r = $scheme->tts_r;
						$new_scheme->pe_r = $scheme->pe_r;
						$new_scheme->lpat = $scheme->lpat;
						$new_scheme->total_cost = $scheme->total_cost;
						$new_scheme->user_id = Auth::id();
						$new_scheme->final_alloc = $scheme->final_alloc;
						$new_scheme->final_total_deals = $scheme->final_total_deals;
						$new_scheme->final_total_cases = $scheme->final_total_cases;
						$new_scheme->final_tts_r = $scheme->final_tts_r;
						$new_scheme->final_pe_r = $scheme->final_pe_r;
						$new_scheme->final_total_cost = $scheme->final_total_cost;
						$new_scheme->ulp_premium = $scheme->ulp_premium;
						$new_scheme->compute = $scheme->compute;
						$new_scheme->with_upload = $scheme->with_upload;
						$new_scheme->m_remarks = $scheme->m_remarks;
						$new_scheme->save();

						// add skus
						$scheme_skus = SchemeSku::where('scheme_id',$scheme->id)->get();
						if(!empty($scheme_skus)){
							foreach ($scheme_skus as $sku) {
								SchemeSku::insert(array('scheme_id' => $new_scheme->id, 
									'sku' => $sku->sku,
									'sku_desc' => $sku->sku_desc,
									'division_code' => $sku->division_code,
									'division_desc' => $sku->division_desc,
									'category_code' => $sku->category_code,
									'category_desc' => $sku->category_desc,
									'brand_code' => $sku->brand_code,
									'brand_desc' => $sku->brand_desc,
									'cpg_code' => $sku->cpg_code,
									'cpg_desc' => $sku->cpg_desc,
									'packsize_code' => $sku->packsize_code,
									'packsize_desc' => $sku->packsize_desc));
							}
						}

						// add host sku
						$host_skus = SchemeHostSku::where('scheme_id',$scheme->id)->get();
						if(!empty($host_skus)){
							foreach ($host_skus as $sku) {
								SchemeHostSku::insert(array('scheme_id' => $new_scheme->id, 
									'sap_code' => $sku->sap_code,
									'sap_desc' => $sku->sap_desc,
									'pack_size' => $sku->pack_size,
									'barcode' => $sku->barcode,
									'case_code' => $sku->case_code,
									'price_case' => $sku->price_case,
									'price_case_tax' => $sku->price_case_tax,
									'price' => $sku->price,
									'srp' => $sku->srp));
							}
						}

						// add premuim sku
						$premuim_skus = SchemePremuimSku::where('scheme_id',$scheme->id)->get();
						if(!empty($premuim_skus)){
							foreach ($premuim_skus as $sku) {
								SchemePremuimSku::insert(array('scheme_id' => $new_scheme->id, 
									'sap_code' => $sku->sap_code,
									'sap_desc' => $sku->sap_desc,
									'pack_size' => $sku->pack_size,
									'barcode' => $sku->barcode,
									'case_code' => $sku->case_code,
									'price_case' => $sku->price_case,
									'price_case_tax' => $sku->price_case_tax,
									'price' => $sku->price,
									'srp' => $sku->srp));
							}
						}

						$allocations = Allocation::schemeAllocations($scheme->id);
						$last_area_id = 0;
						$last_shipto_id = 0;
						foreach ($allocations as $allocation) {
							$scheme_alloc = new SchemeAllocation;
							$scheme_alloc->scheme_id = $new_scheme->id;
							if((!empty($allocation->customer_id)) && (empty($allocation->shipto_id))){
								$scheme_alloc->customer_id = $last_area_id;
							}

							if((!empty($allocation->customer_id)) && (!empty($allocation->shipto_id))){
								$scheme_alloc->customer_id = $last_area_id;
								$scheme_alloc->shipto_id = $last_shipto_id;
							}
							
							$scheme_alloc->group_code = $allocation->group_code;
							$scheme_alloc->group = $allocation->group;
							$scheme_alloc->area_code = $allocation->area_code;
							$scheme_alloc->area = $allocation->area;
							$scheme_alloc->sold_to_code = $allocation->sold_to_code;
							$scheme_alloc->sold_to = $allocation->sold_to;
							$scheme_alloc->sob_customer_code = $allocation->sob_customer_code;
							$scheme_alloc->ship_to_code = $allocation->ship_to_code;
							$scheme_alloc->ship_to = $allocation->ship_to;
							$scheme_alloc->channel_code = $allocation->channel_code;
							$scheme_alloc->channel = $allocation->channel;
							$scheme_alloc->account_group_code = $allocation->account_group_code;
							$scheme_alloc->account_group_name = $allocation->account_group_name;
							$scheme_alloc->outlet = $allocation->outlet;
							$scheme_alloc->sold_to_gsv = $allocation->sold_to_gsv;
							$scheme_alloc->forced_sold_to_gsv = $allocation->forced_sold_to_gsv;
							$scheme_alloc->sold_to_gsv_p = $allocation->sold_to_gsv_p;
							$scheme_alloc->forced_sold_to_gsv_p = $allocation->forced_sold_to_gsv_p;
							$scheme_alloc->sold_to_alloc = $allocation->sold_to_alloc;
							$scheme_alloc->forced_sold_to_alloc = $allocation->forced_sold_to_alloc;
							$scheme_alloc->ship_to_gsv = $allocation->ship_to_gsv;
							$scheme_alloc->forced_ship_to_gsv = $allocation->forced_ship_to_gsv;
							$scheme_alloc->ship_to_gsv_p = $allocation->ship_to_gsv_p;
							$scheme_alloc->forced_ship_to_gsv_p = $allocation->forced_ship_to_gsv_p;
							$scheme_alloc->ship_to_alloc = $allocation->ship_to_alloc;
							$scheme_alloc->forced_ship_to_alloc = $allocation->forced_ship_to_alloc;
							$scheme_alloc->outlet_to_gsv = $allocation->outlet_to_gsv;
							$scheme_alloc->forced_outlet_to_gsv = $allocation->forced_outlet_to_gsv;
							$scheme_alloc->outlet_to_gsv_p = $allocation->outlet_to_gsv_p;
							$scheme_alloc->forced_outlet_to_gsv_p = $allocation->forced_outlet_to_gsv_p;
							$scheme_alloc->outlet_to_alloc = $allocation->outlet_to_alloc;
							$scheme_alloc->forced_outlet_to_alloc = $allocation->forced_outlet_to_alloc;
							$scheme_alloc->multi = $allocation->multi;
							$scheme_alloc->computed_alloc = $allocation->computed_alloc;
							$scheme_alloc->force_alloc = $allocation->force_alloc;
							$scheme_alloc->final_alloc = $allocation->final_alloc;
							$scheme_alloc->in_deals = $allocation->in_deals;
							$scheme_alloc->in_cases = $allocation->in_cases;
							$scheme_alloc->tts_budget = $allocation->tts_budget;
							$scheme_alloc->pe_budget = $allocation->pe_budget;
							$scheme_alloc->show = $allocation->show;
							$scheme_alloc->save();

							if((empty($allocation->customer_id)) && (empty($allocation->shipto_id))){
								$last_area_id = $scheme_alloc->id;
							}

							if((!empty($allocation->customer_id)) && (!empty($allocation->shipto_id))){
								$last_shipto_id = $scheme_alloc->id;
							}
						}

						if($new_scheme->compute == 1){
							$new_scheme->updating = 1;
							$new_scheme->update();
							if($_ENV['MAIL_TEST']){
								Queue::push('SchemeScheduler', array('id' => $new_scheme->id),'scheme');
							}else{
								Queue::push('SchemeScheduler', array('id' => $new_scheme->id),'p_scheme');
							}
						}else{
							$new_scheme->updating = 0;
							$new_scheme->update();
						}
					}
				}
				// copy all file
				$path = storage_path().'/uploads/'.$new_activity->cycle_id.'/'.$new_activity->activity_type_id;
				if(!File::exists($path)) {
					File::makeDirectory($path);
				}
				$path2 = storage_path().'/uploads/'.$new_activity->cycle_id.'/'.$new_activity->activity_type_id.'/'.$new_activity->id;
				if(!File::exists($path2)) {
					File::makeDirectory($path2);

					$old_path = storage_path().'/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
					File::copyDirectory($old_path, $path2);


					// delete old pdf
					$pdf_name = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', $activity->circular_name);
					
					$myfile = storage_path().'/uploads/'.$new_activity->cycle_id.'/'.$new_activity->activity_type_id.'/'.$new_activity->id.'/'.str_replace(":","_", $pdf_name).'.pdf';
					if (File::exists($myfile))
					{
					    File::delete($myfile);
					}

					// delete old word
					$word_name = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', $activity->circular_name);
					
					$docfile = storage_path().'/uploads/'.$new_activity->cycle_id.'/'.$new_activity->activity_type_id.'/'.$new_activity->id.'/'.str_replace(":","_", $word_name).'.docx';
					if (File::exists($docfile))
					{
					    File::delete($docfile);
					}

					// delete unnessary file
					$files = [];
					foreach ($fdapermits as $file) {
						$files[] = $file->hash_name;
					}

					foreach ($pis as $file) {
						$files[] = $file->hash_name;
					}

					foreach ($artworks as $file) {
						$files[] = $file->hash_name;
					}

					foreach ($backgrounds as $file) {
						$files[] = $file->hash_name;
					}
					
					foreach ($guidelines as $file) {
						$files[] = $file->hash_name;
					}

					$dir_files = File::files($path2);

					foreach ($dir_files as $file) {
						$x = explode("/", $file);
						$cnt = count($x) - 1;
						$f_file = $x[$cnt];
						if(!in_array($f_file, $files)){
							if (File::exists($file))
							{
							    File::delete($file);
							}
						}
					}

				}

				$comment = new ActivityComment;
				$comment->created_by = Auth::id();
				$comment->activity_id = $new_activity->id;
				$comment->comment = 'Activity duplicated from ' .$activity->circular_name;
				$comment->comment_status = 'Activity Duplicated';
				$comment->class = "text-warning";
				$comment->save();

				DB::commit();
				$class = 'alert-success';
				$message = 'Activity successfully duplicated.';

				// return Redirect::to(URL::action('ActivityController@index'))
				// 	->with('class', $class )
				// 	->with('message', $message);
				return Redirect::route('activity.edit',$new_activity->id)
					->with('class', 'alert-success')
					->with('message', 'Activity "'.strtoupper($activity->circular_name).'" was successfuly created.');
				
			} catch (\Exception $e) {
				DB::rollback();
				echo $e;
			 //    $class = 'alert-danger';
				// $message = 'Cannot duplicate activity.';

				// return Redirect::to(URL::action('ActivityController@index'))
				// ->with('class', $class )
				// ->with('message', $message);
				// something went wrong
			}			
			
		}

		// return Redirect::to(URL::action('ActivityController@index'))
		// 		->with('class', $class )
		// 		->with('message', $message);
	}

	public function updatetimings($id){
		if(Request::ajax()){
			// Helper::print_r(Input::all());
			$activity = Activity::findOrFail($id);
			$data = array();
			if (Input::has('timing_start')){
				foreach (Input::get('timing_start') as $key => $value) {
					$data[$key]['start_date'] = $value;
				}
			}

			if (Input::has('timing_end')){
				foreach (Input::get('timing_end') as $key => $value) {
					$data[$key]['end_date'] = $value;
				}
			}

			$new_timings = array();
			foreach ($data as $key => $value) {
				$timing = ActivityTiming::find($key);
				$start_date = date('Y-m-d',strtotime($value['start_date']));
				$end_date = date('Y-m-d',strtotime($value['end_date']));
				if($start_date == '1970-01-01'){
					$start_date = null;
				}

				if($end_date == '1970-01-01'){
					$end_date = null;
				}
				$timing->final_start_date = $start_date;
				$timing->final_end_date = $end_date;
				$timing->update();
			}

			ActivityRole::where('activity_id',$activity->id)->delete();
			$_data = array();
			$roles = json_decode(Input::get('roles'));
			if(count($roles) > 0){
				foreach ($roles as $role) {
					

					if(count( (array)$role) > 0){
						$owner = '';
						$point = '';
						$timing = '';
						if(isset($role->owner)){
							$owner = $role->owner;
						}
						if(isset($role->point)){
							$point = $role->point;
						}
						if(isset($role->timing)){
							$timing = $role->timing;
						}
						if(($owner != "") || ($point != "")  || ($timing != "") ){
							$_data[] = array('activity_id' => $activity->id,
								'owner' => $owner,
								'point' => $point,
								'timing' => $timing);
						}
						
						
					}
				}

				if(!empty($_data)){
					ActivityRole::insert($_data);
				}
				
			}
			

			$arr['success'] = 1;
			Session::flash('class', 'alert-success');
			Session::flash('message', 'Activity successfully updated.');
			$arr['id'] = $id;
			return json_encode($arr);
		}

	}

	public function activityroles($id){
		$data['d'] = ActivityRole::getListData($id);
		$data['msg'] = 'success';
		return json_encode($data);
	}

	public function active($id){
		if(Auth::user()->hasRole("ADMINISTRATOR")){
			$activity = Activity::findOrFail($id);
			if($activity->status_id == 9){
				return View::make('activity.active',compact('activity'));
			}
				return View::make('shared.404');
		}
		else{
			return Redirect::to('/dashboard');
		}
	}

	public function setactive($id){
		if(Auth::user()->hasRole("ADMINISTRATOR")){
			$activity = Activity::findOrFail($id);
			if($activity->status_id == 9){
				$activity->disable = (Input::has('deactivated')) ? 1 : 0;
				$activity->update();

				return Redirect::action('ActivityController@index')
					->with('class', 'alert-success')
					->with('message', 'Activity successfuly updated.');
			}
				return View::make('shared.404');
		}
		else{
			return Redirect::to('/dashboard');
		}
	}

	public function addmember($id){
		if(Request::ajax()){
			$settings = Setting::find(1);
			$approvers = explode(",", $settings->customized_preapprover);
			$activity = Activity::find($id);
			$err = [];
			$data['success'] = 1;
			if(empty($activity)){
				$err[] = 'Activity not found';
			}else{
				$user = User::find(Input::get('activity-member'));
				if(!empty($user)){
					$fullname = $user->first_name . ' ' . $user->last_name;

					$new_user = new ActivityMember;
					$new_user->activity_id = $activity->id;
					$new_user->user_id = $user->id;
					$new_user->user_desc = $user->first_name . ' ' . $user->last_name;
					$new_user->department = $user->department->department;

					$remarks = $fullname .' is added as an activity pre-approver.';

					if(!in_array($user->department_id, $approvers)){
						$new_user->activity_member_status_id = 3;
						$remarks = $fullname .' is added as an expectator.';
					}
					$new_user->save();

					ActivityTimeline::addTimeline($activity, Auth::user(), "add a member", $remarks);

				}else{
					$err[] = 'User not found.';
				}
			}
			
			if(count($err) >0){
				$data['success'] = 0;
				$data['err'] = $err;
			}
			return json_encode($data,200);
		}
	}

	public function members($id){
		$skus = ActivityMember::select(array('activity_member_statuses.id','user_desc', 'department', 'activity_member_statuses.mem_status', 'activity_members.activity_member_status_id'))
			->join('activity_member_statuses', 'activity_member_statuses.id', '=', 'activity_members.activity_member_status_id')
			->where('activity_id', $id);
			
		return Datatables::of($skus)
			->remove_column('id')
			->edit_column('mem_status', function($row) {
                if($row->activity_member_status_id == 1){
                	$class = 'text-primary';
                }

                if($row->activity_member_status_id == 2){
                	$class = 'text-danger';
                }

                if($row->activity_member_status_id == 3){
                	$class = 'text-success';
                }

                if($row->activity_member_status_id == 4){
                	$class = 'ext-warning';
                }
                
                return '<p class="'.$class.'">'.$row->mem_status.'</p>';
            })
			->make();
	}

	public function joborder($id){
		$joborder = Joborder::findOrFail($id);
		$artworks = JoborderArtwork::where('joborder_id', $joborder->id)->get();
		$comments = $joborder->comments()->orderBy('created_at', 'desc')->get();
		return View::make('activity.joborder',compact('joborder', 'comments', 'artworks'));
	}

	public function createjo($id){
		$activity = Activity::findOrFail($id);
		$tasks = Task::getLists();
		$users = User::getAll();
		return View::make('activity.createjo',compact('activity', 'tasks', 'users'));
	}

	public function storejo($id){
		$activity = Activity::findOrFail($id);
		$task = Task::findOrFail(Input::get('task'));
		$subtask = SubTask::findOrFail(Input::get('sub_task'));
		$validation = Validator::make(Input::all(), Joborder::$rules);
		if($validation->passes()){
			$joborder = new Joborder;
			$joborder->activity_id = $activity->id;
			$joborder->created_by = Auth::id();
			$joborder->task_id = $task->id;
			$joborder->task = $task->task;
			$joborder->sub_task_id = $subtask->id;
			$joborder->sub_task = $subtask->sub_task;
			$joborder->department_id = $subtask->department_id;
			$joborder->start_date = date('Y-m-d',strtotime(Input::get('start_date')));
			$joborder->end_date = date('Y-m-d',strtotime(Input::get('end_date')));
			$joborder->save();

			$comment = JoborderComment::create(['joborder_id' => $joborder->id, 
				'created_by' => Auth::user()->id,
				'comment' => Input::get('details')]);

			if(Input::hasFile('files')){

				$files = Input::file('files');
				$distination = storage_path().'/joborder_files/';
				foreach ($files as $file) {
					if(!empty($file)){

						$original_file_name = $file->getClientOriginalName();
						$file_name = pathinfo($original_file_name, PATHINFO_FILENAME);
						$extension = File::extension($original_file_name);
						$actual_name = uniqid('img_').'.'.$extension;
						$file->move($distination,$actual_name);

						CommentFile::create(['comment_id' => $comment->id,
							'random_name' => $actual_name, 
							'file_name' => $file_name.'.'.$extension]);
					}
					
				}
				
			}

			$url = route('joborders.edit', $joborder->id); 
			$message = '<a href="'.$url.'" class="linked-object-link">Job Order #'.$joborder->id.'</a>';
			ActivityTimeline::addTimeline($activity, Auth::user(), "created a job order",$message);

			return Redirect::to(URL::action('ActivityController@joborder', array('id' => $joborder->id)))
				->with('class', 'alert-success')
				->with('message', 'Joborder was successfuly created.');
						

		}

		return Redirect::route('activity.createjo', $id)
					->withInput()
					->withErrors($validation)
					->with('class', 'alert-danger')
					->with('message', 'There were validation errors.');
	}

	public function joborderuploadphoto($id){
		$joborder = Joborder::findOrFail($id);
		if(Input::hasFile('files')){
			$files = Input::file('files');
			$distination = storage_path().'/joborder_files/';
			foreach ($files as $file) {
				if(!empty($file)){
					$original_file_name = $file->getClientOriginalName();
					$file_name = pathinfo($original_file_name, PATHINFO_FILENAME);
					$extension = File::extension($original_file_name);
					$actual_name = uniqid('img_').'.'.$extension;
					$file->move($distination,$actual_name);

					JoborderArtwork::create(['joborder_id' => $id,
						'random_name' => $actual_name, 
						'file_name' => $file_name.'.'.$extension]);
				}
				
			}
		}

		return Redirect::to(URL::action('ActivityController@joborder', array('id' => $id)))
			->with('class', 'alert-success')
			->with('message', 'Artwork was successfuly updated.');
		
	}

	public function joborderartworkdelete($random_name = null){
		$file = JoborderArtwork::where('random_name', $random_name)->first();
		if(!empty($file)){
			$path = storage_path().'/joborder_files/'.$file->random_name;
			if (file_exists($path)) { 
				File::delete($path);
				$file->delete();
			}
		}
		return Redirect::back()
			->with('class', 'alert-success')
			->with('message', 'Artwork was successfuly updated.');
	}


	public function storecomment($id){
		$activity = Activity::findOrFail($id);

		$comment = new ActivityComment;
		$comment->created_by = Auth::id();
		$comment->activity_id =$activity->id;
		$comment->comment = Input::get('comment');
		$comment->save();

		ActivityTimeline::addTimeline($activity, Auth::user(), "posted a comment", Input::get('comment'));

		return Redirect::to(URL::action('ActivityController@edit', array('id' => $id)) . "#comments")
			->with('class', 'alert-success')
			->with('message', 'Comments successfuly posted.');
	}


	// customized pre approval
	public function preapprove(){
		if(!Auth::user()->isChannelApprover()){
			return View::make('shared.404');
		}else{
			Input::flash();
			$cycles = Cycle::getLists();
			$types = ActivityType::getLists();
			$proponents = Activity::getCustomProponent(Auth::user()->id);
			$activities = Activity::searchCustomForApproval(Auth::user()->id, Input::get('pr'), Input::get('title'),  Input::get('cy'),  Input::get('ty'));
			return View::make('activity.preapprove',compact('activities', 'cycles', 'types', 'proponents','s'));

		}
	}

	public function preapproveedit($id){
		$activity = Activity::findOrFail($id);
		if(!ActivityMember::myApproval($activity->id)){
			return View::make('shared.404');
		}else{

			$activityIdList = Activity::getCustomIdList();		

			$id_index = array_search($id, $activityIdList);

			// activity details
			$approver = ActivityApprover::getApprover($id,Auth::id());

			$planner = ActivityPlanner::where('activity_id', $activity->id)->first();
			$approvers = ActivityApprover::getNames($activity->id);

			$objectives = ActivityObjective::where('activity_id', $activity->id)->get();

			$budgets = ActivityBudget::with('budgettype')->where('activity_id', $id)->get();
			$nobudgets = ActivityNobudget::with('budgettype')->where('activity_id', $id)->get();

			$schemes = Scheme::getList($id);

			$skuinvolves = array();
			foreach ($schemes as $scheme) {
				$involves = SchemeHostSku::where('scheme_id',$scheme->id)
					->join('pricelists', 'scheme_host_skus.sap_code', '=', 'pricelists.sap_code')
					->get();

				$premiums = SchemePremuimSku::where('scheme_id',$scheme->id)
					->join('pricelists', 'scheme_premuim_skus.sap_code', '=', 'pricelists.sap_code')
					->get();
				
				$_involves = array();
				foreach ($involves as $value) {
					$_involves[] = $value;
				}
				$_premiums = array();
				foreach ($premiums as $premium) {
					$_premiums[] = $premium;
				}

				$scheme->allocations = SchemeAllocation::getAllocations($scheme->id);
				$non_ulp = explode(",", $scheme->ulp_premium);
				

				$skuinvolves[$scheme->id]['premiums'] = $_premiums;
				$skuinvolves[$scheme->id]['involves'] = $_involves;
				$skuinvolves[$scheme->id]['non_ulp'] = $non_ulp;
			}

			//Involved Area
			$areas = ActivityCutomerList::getSelectedAreas($activity->id);
			$channels = ActivityChannelList::getSelectecdChannels($activity->id);

			$materials = ActivityMaterial::where('activity_id', $activity->id)->get();

			$fdapermits = ActivityFdapermit::getList($activity->id);

			$networks = ActivityTiming::getTimings($activity->id,true);

			$activity_roles = ActivityRole::getListData($activity->id);

			$artworks = ActivityArtwork::getList($activity->id);

			$pispermit = ActivityFis::where('activity_id', $activity->id)->first();

			$sku_involves = ActivitySku::getInvolves($activity->id);

			// // Product Information Sheet
			$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
			if($pispermit){
				try {
					$pis = Excel::selectSheets('Output')->load(storage_path().$path."/".$pispermit->hash_name)->get();
				} catch (Exception $e) {
					return View::make('shared.invalidpis');
				}

			}else{
				$pis = array();
			}

			// attachments
			
			$fis = ActivityFis::getList($activity->id);
			$artworks = ActivityArtwork::getList($activity->id);
			$backgrounds = ActivityBackground::getList($activity->id);
			$bandings = ActivityBanding::getList($activity->id);

			// sob

			$soballocation = AllocationSob::getByActivity($activity->id);
			// Helper::print_r($schemes);
			// dd($schemes);
			// comments
			$comments = ActivityComment::getList($activity->id);

			return View::make('activity.preapproveedit',compact('activity','comments','approver', 'objectives', 'valid',
			'activity' ,'approvers', 'planner','budgets','nobudgets','schemes','skuinvolves', 'sku_involves',
			'materials','non_ulp','networks','artworks', 'pis' , 'areas','channels', 
			'fdapermits','fis', 'backgrounds', 'bandings' ,'activity_roles',
			'activityIdList','id_index','status', 'soballocation'));

		}
	}

	public function updatecustom($id){
		$activity = Activity::findOrFail($id);
		if((!Auth::user()->isChannelApprover()) || (!ActivityMember::myActivity($activity->id))){
			return View::make('shared.404');
		}else{
			$member = ActivityMember::myActivity($activity->id);
			if(!empty($member)){
				if(Input::get('update_status') == '1'){
					$member->activity_member_status_id = 3;
					$member->update();
					ActivityTimeline::addTimeline($activity, Auth::user(), "approved the activity",Input::get('submitremarks'));
				}else{
					$member->activity_member_status_id = 2;
					$member->update();
					ActivityTimeline::addTimeline($activity, Auth::user(), "denied the activity",Input::get('submitremarks'));
				}
			}

			$activityIdList = Activity::getCustomIdList();	

			if(!empty($activityIdList)){
				return Redirect::route('activity.preapproveedit',$activityIdList[0])
					->with('class', 'alert-success')
					->with('message', 'Activity was successfuly updated.');	
			}else{
				return Redirect::route('activity.preapprove')
					->with('class', 'alert-success')
					->with('message', 'Activity was successfuly updated.');	
			}	
		}
	}

}