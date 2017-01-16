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

				// tradedeal
				$tradedeal = Tradedeal::getActivityTradeDeal($activity);
				$tradedeal_skus = TradedealPartSku::where('activity_id', $activity->id)->get();

				$acdivisions = \ActivityDivision::getList($activity->id);
				$accategories = \ActivityCategory::selected_category($activity->id);
				$acbrands = \ActivityBrand::selected_brand($activity->id);

				$host_skus = ActivitySku::tradedealSkus($activity);
				$ref_skus = Sku::items($acdivisions,$accategories,$acbrands);

				$pre_skus = \Pricelist::items();
				$dealtypes = TradedealType::getList();
				$dealuoms = TradedealUom::get()->lists('tradedeal_uom', 'id');

				$tradedealschemes = [];
				$total_deals = Tradedeal::total_deals($activity);
				$total_premium_cost = Tradedeal::total_premium_cost($activity);

				if($tradedeal != null){
					$tradedealschemes = TradedealScheme::getScheme($tradedeal->id);
				}
				// end tradedeal

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
					$scope_types = ScopeType::getLists($activity->id);
					$planners = User::getApprovers(['PMOG PLANNER']);
					$activity_types = ActivityType::getWithNetworks();
					$cycles = Cycle::getLists();
					$divisions = Pricelist::divisions();
					
					return View::make('activity.edit', compact('activity', 'scope_types', 'planners', 'approvers', 'cycles',
					 'activity_types', 'divisions' , 'sel_divisions','objectives',  'users', 'budgets', 'nobudgets', 'sel_planner','sel_approver',
					 'sel_objectives',  'schemes', 'scheme_summary', 'networks', 'areas', 'timings' ,'sel_involves',
					 'scheme_customers', 'scheme_allcations', 'materials', 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings',
					 'force_allocs', 'comments' ,'submitstatus', 'tradedeal', 'tradedeal_skus', 'dealtypes', 'dealuoms', 'host_skus', 'ref_skus',
					 'pre_skus', 'tradedealschemes', 'td_shiptos', 'td_premiums', 'total_deals', 'total_premium_cost'));
				}

				if($activity->status_id > 3){
					$submitstatus = array('2' => 'RECALL ACTIVITY');
					$divisions = Sku::getDivisionLists();
					$route = 'activity.index';
					$recall = $activity->pro_recall;
					$submit_action = 'ActivityController@updateactivity';

					$participating_skus = TradedealPartSku::getParticipatingSku($activity);

					return View::make('shared.activity_readonly', compact('activity', 'sel_planner', 'approvers', 
					 'sel_divisions','divisions', 'timings',
					 'objectives',  'users', 'budgets', 'nobudgets','sel_approver',
					 'sel_objectives',  'schemes', 'scheme_summary', 'networks','areas',
					 'scheme_customers', 'scheme_allcations', 'materials', 'force_allocs','sel_involves',
					 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings', 'comments' ,'submitstatus', 'route', 'recall', 'submit_action',
					 'tradedeal', 'total_deals', 'total_premium_cost', 'tradedealschemes', 'participating_skus'));
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

				$tradedeal = Tradedeal::where('activity_id', $activity->id)->first();
				$tradedeal_skus = TradedealPartSku::where('activity_id', $activity->id)->get();


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

				// tradedeal
				$tradedeal_skus = TradedealPartSku::where('activity_id', $activity->id)->get();

				$acdivisions = \ActivityDivision::getList($activity->id);
				$accategories = \ActivityCategory::selected_category($activity->id);
				$acbrands = \ActivityBrand::selected_brand($activity->id);

				$host_skus = ActivitySku::tradedealSkus($activity);
				$ref_skus = Sku::items($acdivisions,$accategories,$acbrands);

				$pre_skus = \Pricelist::items();
				$dealtypes = TradedealType::getList();
				$dealuoms = TradedealUom::get()->lists('tradedeal_uom', 'id');

				$tradedealschemes = [];
				$total_deals = Tradedeal::total_deals($activity);
				$total_premium_cost = Tradedeal::total_premium_cost($activity);

				if($tradedeal != null){
					$tradedealschemes = TradedealScheme::getScheme($tradedeal->id);
				}
				// end tradedeal


				if($activity->status_id == 4){
					$submitstatus = array('1' => 'SUBMIT ACTIVITY','2' => 'DENY ACTIVITY');
					$scope_types = ScopeType::getLists($activity->id);
					$planners = User::getApprovers(['PMOG PLANNER']);
					$activity_types = ActivityType::getWithNetworks();
					$cycles = Cycle::getLists();
					// $divisions = Sku::getDivisionLists();
					$divisions = Pricelist::divisions();

					$participating_skus = TradedealPartSku::getParticipatingSku($activity);
					return View::make('downloadedactivity.edit', compact('activity', 'scope_types', 'planners', 'approvers', 'cycles',
					 'activity_types', 'divisions' , 'sel_divisions','objectives',  'users', 'budgets', 'nobudgets', 'sel_planner','sel_approver',
					 'sel_objectives',  'schemes', 'scheme_summary', 'networks', 'areas', 'timings' ,'sel_involves',
					 'scheme_customers', 'scheme_allcations', 'materials', 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings',
					 'force_allocs', 'comments' ,'submitstatus', 'tradedeal_skus',
					 'tradedeal', 'total_deals', 'total_premium_cost', 'tradedealschemes', 'participating_skus'));
				}else{
					$submitstatus = array('3' => 'RECALL ACTIVITY');
					$divisions = Sku::getDivisionLists();
					$route = 'activity.index';
					$recall = $activity->pmog_recall;
					$submit_action = 'ActivityController@submittogcm';

					$participating_skus = TradedealPartSku::getParticipatingSku($activity);
					
					return View::make('shared.activity_readonly', compact('activity', 'sel_planner', 'approvers', 'sel_divisions','divisions' ,
					 'objectives',  'users', 'budgets', 'nobudgets','sel_approver',
					 'sel_objectives',  'schemes', 'scheme_summary', 'networks', 'areas',
					 'scheme_customers', 'scheme_allcations', 'materials', 'force_allocs', 'timings' ,'sel_involves',
					 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings', 'comments' ,'submitstatus', 'route', 'recall', 'submit_action', 'tradedeal_skus',
					 'tradedeal', 'total_deals', 'total_premium_cost', 'tradedealschemes', 'participating_skus'));
				}
			}
		}else{ 
			// customized
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

			// tradedeal
			$tradedeal = Tradedeal::getActivityTradeDeal($activity);

			if($tradedeal != null){
				$tradedeal_skus = TradedealPartSku::where('activity_id', $activity->id)->get();

				$acdivisions = \ActivityDivision::getList($activity->id);
				$accategories = \ActivityCategory::selected_category($activity->id);
				$acbrands = \ActivityBrand::selected_brand($activity->id);

				$host_skus = ActivitySku::tradedealSkus($activity);
				$ref_skus = Sku::items($acdivisions,$accategories,$acbrands);

				$pre_skus = \Pricelist::items();
				$dealtypes = TradedealType::getList();
				$dealuoms = TradedealUom::get()->lists('tradedeal_uom', 'id');

				$tradedealschemes = [];
				$total_deals = Tradedeal::total_deals($activity);
				$total_premium_cost = Tradedeal::total_premium_cost($activity);
				$tradedealschemes = TradedealScheme::getScheme($tradedeal->id);
			}
			// end tradedeal

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

			$activty_members = ActivityMember::memberList($activity);




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
					if($activity_member->pre_approve){
						if($activity->status_id > 3){
							$view = 'activity.customizedreadonly';
							$show_action = false;
						}else{
							$allowAdd = true;
							$show_action = false;
							$view = 'activity.customizedreadonly';
						}
					}else{
						if(ActivityMember::allowToSubmit($activity)){
							$show_action = false;
							$view = 'activity.customizedreadonly';
						}else{
							return Response::make(View::make('shared/404'), 404);
						}
						
					}
					
				}else{ 
					return Response::make(View::make('shared/404'), 404);
				}
				
			}
			
			return View::make($view, compact('activity', 'planners', 'approvers', 'sel_approver', 'cycles',
					 'activity_types', 'divisions' ,'objectives',  'users', 'budgets', 'nobudgets', 
					 'sel_objectives', 'sel_divisions',  'schemes', 'scheme_summary', 'networks', 'timings' ,
					 'scheme_customers', 'scheme_allcations', 'materials', 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings',
					 'comments' ,'submitstatus', 'allowAdd', 'joborders', 'show_action', 'allowJo', 'timelines', 'activty_members',
					 'tradedeal', 'tradedeal_skus', 'dealtypes', 'dealuoms', 'host_skus', 'ref_skus',
					 'pre_skus', 'tradedealschemes', 'td_shiptos', 'td_premiums', 'total_deals', 'total_premium_cost', 'areas'));
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

				// Tradedeal
				$tradedeal = Tradedeal::getActivityTradeDeal($activity);
				if(!empty($tradedeal)){
					$tradedealschemes = TradedealScheme::where('tradedeal_id', $tradedeal->id)->get();
					foreach ($tradedealschemes as $tradedealscheme) {
						TradedealSchemeAllocation::where('tradedeal_scheme_id', $tradedealscheme->id)->delete();
						TradedealSchemeSku::where('tradedeal_scheme_id', $tradedealscheme->id)->delete();
						TradedealSchemeChannel::where('tradedeal_scheme_id', $tradedealscheme->id)->delete();
						TradedealChannelList::where('tradedeal_scheme_id', $tradedealscheme->id)->delete();
						TradedealSchemeSubType::where('tradedeal_scheme_id', $tradedealscheme->id)->delete();
						$tradedealscheme->delete();
					}
					$tradedeal->delete();

					TradedealPartSku::where('activity_id', $activity->id);
					File::deleteDirectory(storage_path().'/le/'.$activity->id);
				}

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
					$comment_status = "RECALLED THE ACTIVITY";
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
							$comment_status = "SUBMITTED THE ACTIVITY TO PMOG PLANNER";
							$activity_status = 4;
						}else{
							// check if there is GCOM Approver
							$gcom_approvers = ActivityApprover::getApproverByRole($activity->id,'GCOM APPROVER');
							if(count($gcom_approvers) > 0){
								$comment_status = "SUBMITTED THE ACTIVITY TO GCOM";
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
									$comment_status = "SUBMITTED THE ACTIVITY  TO CD OPS";
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
										$comment_status = "SUBMITTED THE ACTIVITY TO CMD";
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

					ActivityTimeline::addTimeline($activity, Auth::user(), strtolower($comment_status), Input::get('submitremarks'));


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

						ActivityTimeline::addTimeline($activity, Auth::user(), strtolower($comment_status), Input::get('submitremarks'));


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

					// $_channels = Input::get('channels_involved');
					ActivityChannel2::where('activity_id',$id)->delete();
					$activity_channels = array();
					if(!empty($_customers)){
						$channels = explode(",", $_customers);
						if(!empty($channels)){
							$channel_group = array();
							foreach ($channels as $channel_node){
								$channels = explode(".", trim($channel_node)); 
								$activity_channels[] = array('activity_id' => $id, 'channel_node' => $channels[0]);
							}
							ActivityChannel2::insert($activity_channels);
						}
					}

					ActivityChannelList::addChannel($activity->id,$activity_channels);

					DB::commit();
					// dd(1);
					// update all schemes
					$schemes = Scheme::getList($activity->id);
					if (!App::environment('local')){
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
					}

					$tradedeal = Tradedeal::where('activity_id', $id)->first();
					if((!empty($tradedeal)) && (!$tradedeal->forced_upload)){
						$tradedeal_schemes = TradedealScheme::where('tradedeal_id', $tradedeal->id)->get();
						foreach ($tradedeal_schemes as $td_scheme) {
							TradedealAllocRepository::updateAllocation($td_scheme);
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

				// duplicate trade deals
				$tradedeal_activity = Tradedeal::getActivityTradeDeal($activity);
				if($tradedeal_activity != null){
					$_part_sku = [];
					// copy participating skus
					$partskus = TradedealPartSku::where('activity_id',$activity->id)
						->orderBy('id')
						->get();
					foreach ($partskus as $partsku) {
						$new_partsku = new TradedealPartSku;
						$new_partsku->activity_id = $new_activity->id;
						$new_partsku->host_code = $partsku->host_code;
						$new_partsku->host_desc = $partsku->host_desc;
						$new_partsku->variant = $partsku->variant;
						$new_partsku->brand_shortcut = $partsku->brand_shortcut;
						$new_partsku->host_sku_format = $partsku->host_sku_format;
						$new_partsku->host_cost = $partsku->host_cost;
						$new_partsku->host_pcs_case = $partsku->host_pcs_case;
						$new_partsku->ref_code = $partsku->ref_code;
						$new_partsku->ref_desc = $partsku->ref_desc;
						$new_partsku->ref_pcs_case = $partsku->ref_pcs_case;
						$new_partsku->pre_code = $partsku->pre_code;
						$new_partsku->pre_desc = $partsku->pre_desc;
						$new_partsku->pre_variant = $partsku->pre_variant;
						$new_partsku->pre_brand_shortcut = $partsku->pre_brand_shortcut;
						$new_partsku->pre_sku_format = $partsku->pre_sku_format;
						$new_partsku->pre_cost = $partsku->pre_cost;
						$new_partsku->pre_pcs_case = $partsku->pre_pcs_case;
						$new_partsku->save();

						$_part_sku[$partsku->id] = $new_partsku->id;
					}

					// copy trade deals
					$tradedeals = Tradedeal::where('activity_id',$activity->id)
						->orderBy('id')
						->get();
					foreach ($tradedeals as $tradedeal) {
						$new_tradedeal = new Tradedeal;
						$new_tradedeal->activity_id = $new_activity->id;
						$new_tradedeal->alloc_in_weeks = $tradedeal->alloc_in_weeks;
						$new_tradedeal->non_ulp_premium = $tradedeal->non_ulp_premium;
						$new_tradedeal->non_ulp_premium_desc = $tradedeal->non_ulp_premium_desc;
						$new_tradedeal->non_ulp_premium_code = $tradedeal->non_ulp_premium_code;
						$new_tradedeal->non_ulp_premium_cost = $tradedeal->non_ulp_premium_cost;
						$new_tradedeal->non_ulp_pcs_case = $tradedeal->non_ulp_pcs_case;
						$new_tradedeal->forced_upload = $tradedeal->forced_upload;
						$new_tradedeal->save();

						// copy tradedealscheme
						$tradedealschemes = TradedealScheme::where('tradedeal_id',$tradedeal->id)
							->orderBy('id')
							->get();

						$_scheme_channels = [];
						foreach ($tradedealschemes as $tradedealscheme) {
							$new_tradedealscheme = new TradedealScheme;
							$new_tradedealscheme->tradedeal_id = $new_tradedeal->id;
							$new_tradedealscheme->name = $tradedealscheme->name;
							$new_tradedealscheme->additional_name = $tradedealscheme->additional_name;
							$new_tradedealscheme->tradedeal_type_id = $tradedealscheme->tradedeal_type_id;
							$new_tradedealscheme->buy = $tradedealscheme->buy;
							$new_tradedealscheme->free = $tradedealscheme->free;
							$new_tradedealscheme->pre_id = $tradedealscheme->pre_id;
							$new_tradedealscheme->coverage = $tradedealscheme->coverage;
							$new_tradedealscheme->tradedeal_uom_id = $tradedealscheme->tradedeal_uom_id;
							$new_tradedealscheme->pre_code = $tradedealscheme->pre_code;
							$new_tradedealscheme->pre_desc = $tradedealscheme->pre_desc;
							$new_tradedealscheme->pre_cost = $tradedealscheme->pre_cost;
							$new_tradedealscheme->pre_pcs_case = $tradedealscheme->pre_pcs_case;
							$new_tradedealscheme->pcs_deal = $tradedealscheme->pcs_deal;
							$new_tradedealscheme->pur_req = $tradedealscheme->pur_req;
							$new_tradedealscheme->free_cost = $tradedealscheme->free_cost;
							$new_tradedealscheme->cost_to_sale = $tradedealscheme->cost_to_sale;
							$new_tradedealscheme->save();

							// copy scheme selected channels
							$scheme_channels = TradedealSchemeChannel::where('tradedeal_scheme_id',$tradedealscheme->id)
								->orderBy('id')
								->get();
							foreach ($scheme_channels as $scheme_channel) {
								$new_scheme_channel = new TradedealSchemeChannel;
								$new_scheme_channel->tradedeal_scheme_id = $new_tradedealscheme->id;
								$new_scheme_channel->channel_node = $scheme_channel->channel_node;
								$new_scheme_channel->save();

								$_scheme_channels[$scheme_channel->id] = $new_scheme_channel->id;
							}

							
							// copy scheme skus
							$scheme_skus = TradedealSchemeSku::where('tradedeal_scheme_id',$tradedealscheme->id)
								->orderBy('id')
								->get();
							// Helper::debug($_scheme_channels);

							foreach ($scheme_skus as $scheme_sku) {
								$new_scheme_sku = new TradedealSchemeSku;
								$new_scheme_sku->tradedeal_scheme_id = $new_tradedealscheme->id;
								$new_scheme_sku->tradedeal_part_sku_id = $_part_sku[$scheme_sku->tradedeal_part_sku_id];
								$new_scheme_sku->qty = $scheme_sku->qty;
								$new_scheme_sku->pur_req = $scheme_sku->pur_req;
								$new_scheme_sku->free_cost = $scheme_sku->free_cost;
								$new_scheme_sku->cost_to_sale = $scheme_sku->cost_to_sale;
								$new_scheme_sku->save();
							}

							// copy scheme sub types
							$scheme_subtypes = TradedealSchemeSubType::where('tradedeal_scheme_id',$tradedealscheme->id)
								->orderBy('id')
								->get();
							foreach ($scheme_subtypes as $scheme_subtype) {
								$new_scheme_subtype = new TradedealSchemeSubType;
								$new_scheme_subtype->tradedeal_scheme_id = $new_tradedealscheme->id;
								$new_scheme_subtype->sub_type = $scheme_subtype->sub_type;
								$new_scheme_subtype->sub_type_desc = $scheme_subtype->sub_type_desc;
								$new_scheme_subtype->tradedeal_scheme_channel_id = $_scheme_channels[$scheme_subtype->tradedeal_scheme_channel_id];
								$new_scheme_subtype->save();
							}

						}

						$new_schemes = TradedealScheme::where('tradedeal_id', $new_tradedeal->id)->get();
						if(!empty($new_schemes)){
							foreach ($new_schemes as $new_scheme) {
								TradedealAllocRepository::updateAllocation($new_scheme);
							}
						}
					}


				}


				// 

				// copy all file
				$path = storage_path().'/uploads/'.$new_activity->cycle_id.'/'.$new_activity->activity_type_id;
				// Helper::debug($path);
				if(!File::exists($path)) {
					try {
						File::makeDirectory($path);
					} catch (Exception $e) {
						mkdir($path, 0755, true); 
					}
				}
				$path2 = storage_path().'/uploads/'.$new_activity->cycle_id.'/'.$new_activity->activity_type_id.'/'.$new_activity->id;

				if(!File::exists($path2)) {
					try {
						File::makeDirectory($path2);
					} catch (Exception $e) {
						mkdir($path2, 0755, true);
					}
					
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
			
			if($activity->scope_type_id == 1){
			}else{
				$members = ActivityMember::where('pre_approve', 1)
					->where('activity_id', $activity->id)
					->get();
				$data['activity'] = Activity::getDetails($activity->id);

				foreach ($members as $member) {
					$user = User::find($member->user_id);
					$data['fullname'] = $user->first_name . ' ' . $user->last_name;
					$data['user'] = $user;
					$data['to_user'] = $user->first_name;
					$data['line1'] = "<p><b>".Auth::user()->first_name. " ". Auth::user()->last_name."</b> has updated timing details for <b>".$activity->circular_name."</b>.</p>";
					$data['line2']= "<p>You may view this activity through this link >> <a href=".route('activity.preapproveedit',$activity->id)."> ".route('activity.preapproveedit', $activity->id)."</a></p>";
					$data['line3']= "<p>Please details below:</p>";

					$data['subject'] = "CUSTOMIZED ACTIVITY - TIMINGS UPDATED";
					if($_ENV['MAIL_TEST']){
						Mail::send('emails.customized', $data, function($message) use ($data){
							$message->to("rbautista@chasetech.com", $data['fullname']);
							$message->bcc("Grace.Erum@unilever.com");
							$message->subject($data['subject']);
						});
					}else{
						Mail::send('emails.customized2', $data, function($message) use ($data){
							$message->to(trim(strtolower($user->email)), $data['fullname'])->subject($data['subject']);
						});
					}
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

	public function updatetradedeal($id){
			$activity = Activity::findOrFail($id);

		// $budgets = ActivityBudget::getBudgets($activity->id);
		// if(count($budgets) > 0){
			$rules = array(
			    'non_ulp_premium_desc' => 'max:13',
			);
			
			$validation = Validator::make(Input::all(), $rules);

			// Helper::debug($validation)

			if($validation->passes()){
				$tradedeal = Tradedeal::where('activity_id', $id)->first();
				if(empty($tradedeal)){
					$tradedeal = new Tradedeal;
					$tradedeal->activity_id = $id;
				}
				$tradedeal->alloc_in_weeks = str_replace(",", "", Input::get('alloc_in_weeks'));
				$tradedeal->non_ulp_premium = (Input::has('non_ulp_premium')) ? Input::get('non_ulp_premium') : 0; 

				$tradedeal->non_ulp_premium_desc = Input::get('non_ulp_premium_desc');
				$tradedeal->non_ulp_premium_code = Input::get('non_ulp_premium_code');
				$tradedeal->non_ulp_premium_cost = str_replace(",", "", Input::get('non_ulp_premium_cost'));
				$tradedeal->non_ulp_pcs_case = Input::get('non_ulp_pcs_case');
				$tradedeal->forced_upload = 0;
				$tradedeal->save();

				// if(count($channels) == 0){
					
				// }

				// update non ulp premium
				if($tradedeal->non_ulp_premium){
					$partskus = TradedealPartSku::getPartSkus($activity);
					foreach ($partskus as $sku) {
						$sku->pre_code = $tradedeal->non_ulp_premium_code;
						$sku->pre_desc = $tradedeal->non_ulp_premium_desc;
						$sku->pre_cost = $tradedeal->non_ulp_premium_cost;
						$sku->pre_pcs_case = $tradedeal->non_ulp_pcs_case;
						$sku->update();
					}
				}

				$schemes = TradedealScheme::where('tradedeal_id', $tradedeal->id)->get();

				if(Input::hasFile('tdupload')){
					$token = md5(uniqid(mt_rand(), true));
					$file_path = Input::file('tdupload')->move(storage_path().'/uploads/temp/',$token.".xls");
					$isError = false;
					try {
						Excel::selectSheets('Allocations')->load($file_path, function($reader) use (&$isError,$activity){
							$firstrow = $reader->skip(1)->first()->toArray();
					       	if (isset($firstrow['activity_id'])) {
					            $rows = $reader->all();
					            if($rows[0]->activity_id != $activity->id){
					            	$isError = true;
					            }
					        }

					    });
					} catch (Exception $e) {
						$isError = true;
					}
					
					
				    if (!$isError) {
						Excel::selectSheets('Allocations')->load($file_path, function($reader) use ($activity) {
							TradedealAllocRepository::manualUpload($reader->get(),$activity);
							$tradedeal = Tradedeal::getActivityTradeDeal($activity);
							$tradedeal->forced_upload = 1;
							$tradedeal->update();
						});
				        
				    }
				}else{
					// update all scheme
					if(!empty($schemes)){
						foreach ($schemes as $scheme) {
							TradedealAllocRepository::updateAllocation($scheme);
						}
					}
				}
				
				// File::deleteDirectory(storage_path('le/'.$activity->id));
				// if(!empty($schemes)){
				// 	foreach ($schemes as $scheme) {
				// 		LeTemplateRepository::generateTemplate($scheme);
				// 	}
				// }
				
				return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity->id)) . "#tradedeal")
					->with('class', 'alert-success')
					->with('message', 'Tradedeal successfuly updated');
				
				
			}
			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity->id)) . "#tradedeal")
					->with('class', 'alert-danger')
					->with('message', 'Error on updating trade');
		// }else{
		// 	Session::flash('class', 'alert-danger');
		// 	Session::flash('message', 'Budget IO is required.');
		// 	return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity->id)) . "#tradedeal");
		// }

	}

	public function addpartskus($id){
		if(Request::ajax()){
			$err = [];
			$activity = Activity::find($id);
			$tradedeal = Tradedeal::where('activity_id', $activity->id)->first();
			if(empty($tradedeal)){
				$err[] = 'No Trade Deal details';
			}else{
				if(empty($activity)){
					$err[] = 'Activity not found.';
				}else{
					$host_sku = Pricelist::getSku(Input::get('host_sku'));
					$ref_sku = Sku::getSku(Input::get('ref_sku'));
					$host_variant = strtoupper(Input::get('variant'));
					$pre_variant = strtoupper(Input::get('pre_variant'));

					$ref_sku2 = Pricelist::getSku(Input::get('ref_sku'));

					$pre_sku = Pricelist::getSku(Input::get('pre_sku'));

					if(empty($host_sku)){
						$err[] = 'No selected Host SKU.';
					}

					if(empty($ref_sku)){
						$err[] = 'Reference SKU is required.';
					}

					if(empty($host_variant)){
						$err[] = 'Host variant is required.';
					}


					if(!$tradedeal->non_ulp_premium){
						if(empty($pre_sku)){
							$err[] = 'No Premium SKU selected';
						}

						if(empty($pre_variant)){
							$err[] = 'Premium variant is required.';
						}
					}

					if($tradedeal->non_ulp_premium){
						if(TradedealPartSku::nonUlpHostExist($activity, $host_sku->sap_code, $host_variant)){
							$err[] = 'Host SKU and variant already exist.';
						}
					}else{
						if(!empty($pre_sku)){
							if(TradedealPartSku::ulpHostExist($activity, $host_sku->sap_code, $host_variant, $pre_sku->sap_code, $pre_variant)){
								$err[] = 'Host / Premium SKU combination exist.';
							}
						}
						
					}

					if(count($err) == 0){
						$part_sku = new TradedealPartSku;
						$part_sku->activity_id = $id;
						$part_sku->host_code = $host_sku->sap_code;
						$part_sku->host_desc = $host_sku->sap_desc;

						$hostsku =  TradedealPartSku::where('host_code', $host_sku->sap_code)->where('activity_id', $activity->id)->first();
						if(!empty($hostsku)){
							$part_sku->variant = $hostsku->variant;
						}else{
							$part_sku->variant = $host_variant;
						}

						$part_sku->brand_shortcut = $host_sku->brand_shortcut;
						$part_sku->host_sku_format = $host_sku->sku_format;
						$part_sku->host_cost = str_replace(",", '', Input::get('host_cost_pcs'));
						$part_sku->host_pcs_case = $host_sku->pack_size;
						$part_sku->ref_code = $ref_sku->sku_code;
						$part_sku->ref_desc = $ref_sku->sku_desc;
						$part_sku->ref_pcs_case = $ref_sku2->pack_size;

						if($tradedeal->non_ulp_premium == 1){
							$part_sku->pre_code = $tradedeal->non_ulp_premium_code;
							$part_sku->pre_desc = $tradedeal->non_ulp_premium_desc;
							$part_sku->pre_cost = $tradedeal->non_ulp_premium_cost;
							$part_sku->pre_pcs_case = $tradedeal->non_ulp_pcs_case;
						}else{
							if(Input::get('pre_sku') != 0){
								$part_sku->pre_code = $pre_sku->sap_code;
								$part_sku->pre_desc = $pre_sku->sap_desc;
								$presku =  TradedealPartSku::where('host_code', $pre_sku->sap_code)->where('activity_id', $activity->id)->first();
								if(!empty($presku)){
									$part_sku->pre_variant = $presku->pre_variant;
								}else{
									$part_sku->pre_variant = $pre_variant;
								}

								$part_sku->pre_brand_shortcut = $pre_sku->brand_shortcut;
								$part_sku->pre_sku_format = $pre_sku->sku_format;
								$part_sku->pre_cost = str_replace(",", '', Input::get('pre_cost_pcs'));
								$part_sku->pre_pcs_case = $pre_sku->pack_size;
							}
						}
						
						$part_sku->save();
					}	
				}

			}
			
			if(count($err) > 0){
				$arr['success'] = 0;
				$arr['err_msg'] = $err;
			}else{
				$arr['success'] = 1;
			}
			
			return json_encode($arr);
		}
	}

	public function getpartskustable($id){

		$skus = TradedealPartSku::select(array('id',
			DB::raw('CONCAT(host_desc," - ",host_code) as host_sku'), 'host_cost', 'host_pcs_case', 'variant', 
			DB::raw('CONCAT(ref_desc," - ",ref_code) as ref_sku'),
			DB::raw('CONCAT(pre_desc," - ",pre_code) as pre_sku'), 'pre_cost', 'pre_pcs_case', 'pre_variant'))
			->where('activity_id', $id)
			->orderBy('id');

		return Datatables::of($skus)
			->remove_column('id')
			->add_column('edit', '<a href="javascript:void(0)" id="{{$id}}" class="editsku" >Edit</a>', 10)
			->add_column('delete', '<a href="javascript:void(0)" id="{{$id}}" class="deletesku" >Delete</a>', 11)
			->edit_column('host_cost', function($row) {
			        return "<span class='pull-right'> {$row->host_cost} </span>";
			    })	
			->edit_column('host_pcs_case', function($row) {
			        return "<span class='pull-right'> {$row->host_pcs_case} </span>";
			    })	
			->edit_column('pre_cost', function($row) {
			        return "<span class='pull-right'> {$row->pre_cost} </span>";
			    })	
			->edit_column('pre_pcs_case', function($row) {
			        return "<span class='pull-right'> {$row->pre_pcs_case} </span>";
			    })			
			->make();
	}

	public function getpartskus($id){
		$tradedeal = Tradedeal::where('activity_id',$id)->first();
		$skus = TradedealPartSku::select(array('id',
			DB::raw('CONCAT(host_desc," - ",host_code) as host_sku'), 'host_cost', 'host_pcs_case','variant',
			DB::raw('CONCAT(ref_desc," - ",ref_code) as ref_sku'),
			DB::raw('CONCAT(pre_desc," - ",pre_code) as pre_sku'), 'pre_cost', 'pre_pcs_case','pre_variant'))
			->where('activity_id', $id)->orderBy('id')->get();
		$data['skus'] = $skus;
		$data['uom'] = TradedealUom::get()->lists('tradedeal_uom', 'id');
		return json_encode($data);

	}

	public function deletepartskus(){
		if(Request::ajax()){
			$id = Input::get('d_id');
			$part_sku = TradedealPartSku::find($id);
			$arr['success'] = 0;

			if(!empty($part_sku)){
				if(!TradedealSchemeSku::idExist($id)){
					$part_sku->delete();
					$arr['success'] = 1;
				}
				
			}
			$arr['id'] = $id;
			return json_encode($arr);
		}
	}

	public function partsku($id){
		if(Request::ajax()){
			$part_sku = TradedealPartSku::find($id);
			return json_encode($part_sku);
		}
	}

	public function updatepartskus(){
		if(Request::ajax()){
			$err = [];
			$part_sku = TradedealPartSku::find(Input::get('sku_id'));
			$activity = Activity::find($part_sku->activity_id);
			$tradedeal = Tradedeal::where('activity_id', $part_sku->activity_id)->first();
			if(empty($part_sku)){
				$err[] = 'Participating SKU not found';
			}else{
				$host_sku = Pricelist::getSku(Input::get('ehost_sku'));
				$ref_sku = Sku::getSku(Input::get('eref_sku'));
				$host_variant = strtoupper(Input::get('evariant'));
				$pre_variant = strtoupper(Input::get('epre_variant'));

				$ref_sku2 = Pricelist::getSku(Input::get('eref_sku'));

				$pre_sku = Pricelist::getSku(Input::get('epre_sku'));

				if(empty($host_sku)){
					$err[] = 'No selected Host SKU.';
				}

				if(empty($ref_sku)){
					$err[] = 'Reference SKU is required.';
				}

				if(empty($host_variant)){
					$err[] = 'Host variant is required.';
				}


				if(!$tradedeal->non_ulp_premium){
					if(empty($pre_sku)){
						$err[] = 'No Premium SKU selected';
					}

					if(empty($pre_variant)){
						$err[] = 'Premium variant is required.';
					}
				}

				if($tradedeal->non_ulp_premium){
					if(TradedealPartSku::nonUlpHostExist($activity, $host_sku->sap_code, $host_variant, $part_sku)){
						$err[] = 'Host SKU and variant already exist.';
					}
				}else{
					if(!empty($pre_sku)){
						if(TradedealPartSku::ulpHostExist($activity, $host_sku->sap_code, $host_variant, $pre_sku->sap_code, $pre_variant, $part_sku)){
							$err[] = 'Host / Premium SKU combination exist.';
						}
					}
					
				}



				if(count($err) == 0){
					$update_host_variant = false;
					$update_pre_variant = true;
					$part_sku->host_code = $host_sku->sap_code;
					$part_sku->host_desc = $host_sku->sap_desc;
					$hostsku =  TradedealPartSku::where('host_code', $host_sku->sap_code)->where('activity_id', $activity->id)->first();
					$host_cost = str_replace(",", '', Input::get('ehost_cost_pcs'));
					if(!empty($hostsku)){
						$part_sku->variant = $hostsku->variant;
						if(($hostsku->variant != $host_variant) || ($hostsku->host_cost != $host_cost)){
							$update_host_variant =  true;
						}
					}else{
						$part_sku->variant = $host_variant;
					}

					$part_sku->brand_shortcut = $host_sku->brand_shortcut;
					$part_sku->host_sku_format = $host_sku->sku_format;
					$part_sku->host_cost = $host_cost;
					$part_sku->host_pcs_case = $host_sku->pack_size;
					$part_sku->ref_code = $ref_sku->sku_code;
					$part_sku->ref_desc = $ref_sku->sku_desc;
					$part_sku->ref_pcs_case = $ref_sku2->pack_size;


					if($tradedeal->non_ulp_premium == 1){
						$part_sku->pre_code = $tradedeal->non_ulp_premium_code;
						$part_sku->pre_desc = $tradedeal->non_ulp_premium_desc;
						$part_sku->pre_cost = $tradedeal->non_ulp_premium_cost;
						$part_sku->pre_pcs_case = $tradedeal->non_ulp_pcs_case;
					}else{
						if(Input::get('epre_sku') != 0){
							$part_sku->pre_code = $pre_sku->sap_code;
							$part_sku->pre_desc = $pre_sku->sap_desc;
							$presku =  TradedealPartSku::where('host_code', $pre_sku->sap_code)->where('activity_id', $activity->id)->first();

							if(!empty($presku)){
								$part_sku->pre_variant = $presku->pre_variant;
								if($part_sku->pre_variant != $pre_variant){
									$update_pre_variant = true;
								}
							}else{
								$part_sku->pre_variant = $pre_variant;
							}
							$part_sku->pre_brand_shortcut = $pre_sku->brand_shortcut;
							$part_sku->pre_sku_format = $pre_sku->sku_format;
							$part_sku->pre_cost =str_replace(",", '', Input::get('epre_cost_pcs'));
							$part_sku->pre_pcs_case = $pre_sku->pack_size;
						}
					}
					$part_sku->save();

					if($update_host_variant){
						$host_skus = TradedealPartSku::where('host_code', $host_sku->sap_code)->where('activity_id', $activity->id)->get();
						foreach ($host_skus as $sku) {
							$sku->variant = $host_variant;
							$sku->host_cost = $host_cost;
							$sku->update();
						}
					}
					if($pre_variant != ''){
						if($update_pre_variant){
							$host_skus = TradedealPartSku::where('pre_code', $pre_sku->sap_code)->where('activity_id', $activity->id)->get();
							foreach ($host_skus as $sku) {
								$sku->pre_variant = $pre_variant;
								$sku->update();
							}
						}
					}
					
					$tradedeal->forced_upload = 0;
					$tradedeal->update();
					
					$schemes = TradedealScheme::where('tradedeal_id', $tradedeal->id)->get();
					if(!empty($schemes)){
						foreach ($schemes as $scheme) {
							TradedealAllocRepository::updateAllocation($scheme);
						}
					}
					
				}	

			}
			
			if(count($err) > 0){
				$arr['success'] = 0;
				$arr['err_msg'] = $err;
			}else{
				$arr['success'] = 1;
			}
			
			return json_encode($arr);
		}
	}

	public function createtradealscheme($id){
		$activity = Activity::findOrFail($id);
		$tradedeal = Tradedeal::getActivityTradeDeal($activity);
		$activity = Activity::findOrFail($activity->id);
		$dealtypes = TradedealType::getList();
		$dealuoms = TradedealUom::get()->lists('tradedeal_uom', 'id');
		$tradedeal_skus = TradedealPartSku::where('activity_id', $activity->id)->get();
		return View::make('activity.createtradedealscheme',compact('dealtypes', 'dealuoms', 'tradedeal_skus', 
			'activity', 'tradedeal'));

	}

	public function storetradealscheme($id){	
		$activity = Activity::findOrFail($id);
		$tradedeal = Tradedeal::where('activity_id', $activity->id)->first();
		$deal_type = TradedealType::find(Input::get('deal_type'));
		$uom = TradedealUom::find(Input::get('uom'));
		$selected = [];
		$free_pcs_case = [];
		$invalid_premiums = true;
		if(Input::has('skus')){
			foreach (Input::get('skus') as $value) {
			 	$selected[] = $value;
			 	$free_sku = TradedealPartSku::find($value);
				$free_pcs_case[] = $free_sku->pre_pcs_case;
			}
			$result = array_unique($free_pcs_case);
			// validation on cases quantitiy
			// if($uom->id == 3){
			// 	if(count($result) > 1){
			// 		$invalid_premiums = false;
			// 	}
			// }
			
		}
		$invalid_collective = true;
		
		if(($deal_type->id == 2) && ($uom->id == 3)){
			$host_pcs_case = [];
			foreach ($selected as $value) {
				$part_sku = TradedealPartSku::find($value);
				$host_pcs_case[] = $part_sku->host_pcs_case;
			}
			$result = array_unique($host_pcs_case);
			if(count($result) > 1){
				$invalid_collective = false;
			}
		}
		Validator::extend('invalid_premiums', function($attribute, $value, $parameters) {
		    return $parameters[0];
		});

		Validator::extend('invalid_collective', function($attribute, $value, $parameters) {
		    return $parameters[0];
		});
		
		$messages = array(
		    'invalid_premiums' => 'Combination of Premium SKU with different pcs/case value is not allowed',
		    'invalid_collective' => 'Combination of participating SKU with different pcs/case value is not allowed',
		    'channels.required' => 'Channels Involved is required'
		);

		$rules = array(
			'scheme_name' => 'required|unique:tradedeal_schemes,name,NULL,id,tradedeal_id,'.$tradedeal->id,
		    'skus' => 'required|invalid_collective:'.$invalid_collective.'|invalid_premiums:'.$invalid_premiums,
		    'buy' => 'required|numeric',
		    'free' => 'required|numeric',
		    'channels' => 'required'
		);

		$validation = Validator::make(Input::all(), $rules, $messages);

		if($validation->passes()){

			if(count($selected) > 0){
				if($deal_type->id == 1){
					$buy = str_replace(",", '', Input::get('buy'));
					$free = str_replace(",", '', Input::get('free'));
					$scheme = new TradedealScheme;
					$scheme->tradedeal_id = $tradedeal->id;
					// $scheme->name = $deal_type->tradedeal_type.": ".$buy."+".$free." ".$uom->tradedeal_uom;
					$scheme->name = strtoupper(Input::get('scheme_name'));
					$scheme->additional_name = strtoupper(Input::get('additional_name'));
					$scheme->tradedeal_type_id = $deal_type->id;
					$scheme->buy = $buy;
					$scheme->free = $free;
					$scheme->coverage = str_replace(",", '', Input::get('coverage'));
					$scheme->tradedeal_uom_id = $uom->id;
					$pcs_deal = 0;
					if($scheme->tradedeal_uom_id == 1){
						$pcs_deal = 1;
					}else if($scheme->tradedeal_uom_id == 2){
						$pcs_deal = 12;
					}else{
						if($tradedeal->non_ulp_premium){
							$pcs_deal = $tradedeal->non_ulp_pcs_case;
						}else{
							if(Input::has('skus')){
								$premium = TradedealPartSku::where('id', Input::get('skus')[0])->first();
							}
							$pcs_deal = $premium->pre_pcs_case;
						}
						
					}
					if($tradedeal->non_ulp_premium){
						$scheme->pre_code = $tradedeal->non_ulp_premium_code;
						$scheme->pre_desc = $tradedeal->non_ulp_premium_desc;
						$scheme->pre_cost = $tradedeal->non_ulp_premium_cost;
						$scheme->pre_pcs_case = $tradedeal->non_ulp_pcs_case;
						$scheme->pcs_deal = $pcs_deal;
					}else{
						// $premuim_sku = TradedealPartSku::find(Input::get('premium_sku'));
						// $scheme->pre_code = $premuim_sku->pre_code;
						// $scheme->pre_desc = $premuim_sku->pre_desc;
						// $scheme->pre_cost = $premuim_sku->pre_cost;
						// $scheme->pre_pcs_case = $premuim_sku->pre_pcs_case;
					}
					$scheme->pcs_deal = $pcs_deal;
					
					$scheme->save();

					TradedealSchemeSku::addHostSku(Input::get('skus'), $scheme);
				}else if($deal_type->id == 2){
					$buy = str_replace(",", '', Input::get('buy'));
					$free = str_replace(",", '', Input::get('free'));
					$scheme = new TradedealScheme;
					$scheme->tradedeal_id = $tradedeal->id;
					$scheme->tradedeal_type_id = $deal_type->id;
					// $scheme->name = $deal_type->tradedeal_type.": ".$buy."+".$free." ".$uom->tradedeal_uom;
					$scheme->name = strtoupper(Input::get('scheme_name'));
					$scheme->buy = $buy;
					$scheme->free = $free;
					$scheme->coverage = str_replace(",", '', Input::get('coverage'));
					$scheme->tradedeal_uom_id = $uom->id;

					$pcs_deal = 0;
					if($scheme->tradedeal_uom_id == 1){
						$pcs_deal = 1;
					}else if($scheme->tradedeal_uom_id == 2){
						$pcs_deal = 12;
					}else{
						if($tradedeal->non_ulp_premium){
							$pcs_deal = $tradedeal->non_ulp_pcs_case;
						}else{
							$premuim_sku = TradedealPartSku::find(Input::get('premium_sku'));
							$pcs_deal = $premuim_sku->pre_pcs_case;
						}
						
					}
					$scheme->pcs_deal = $pcs_deal;

					if($tradedeal->non_ulp_premium){
						$scheme->pre_code = $tradedeal->non_ulp_premium_code;
						$scheme->pre_desc = $tradedeal->non_ulp_premium_desc;
						$scheme->pre_cost = $tradedeal->non_ulp_premium_cost;
						$scheme->pre_pcs_case = $tradedeal->non_ulp_pcs_case;
					}else{
						$premuim_sku = TradedealPartSku::find(Input::get('premium_sku'));
						$scheme->pre_id = $premuim_sku->id;
						$scheme->pre_code = $premuim_sku->pre_code;
						$scheme->pre_desc = $premuim_sku->pre_desc;
						$scheme->pre_cost = $premuim_sku->pre_cost;
						$scheme->pre_pcs_case = $premuim_sku->pre_pcs_case;
					}
					$scheme->save();
					foreach ($selected as $value) {
						TradedealSchemeSku::create(['qty' => Input::get('qty')[$value], 
							'tradedeal_scheme_id' => $scheme->id,
							'tradedeal_part_sku_id' => $value]);
					}
				}else{
					$buy = str_replace(",", '', Input::get('buy'));
					$free = str_replace(",", '', Input::get('free'));
					$scheme = new TradedealScheme;
					$scheme->tradedeal_id = $tradedeal->id;
					$scheme->tradedeal_type_id = $deal_type->id;
					// $scheme->name = $deal_type->tradedeal_type.": ".$buy."+".$free." ".$uom->tradedeal_uom;
					$scheme->name = strtoupper(Input::get('scheme_name'));
					$scheme->buy = $buy;
					$scheme->free = $free;
					$scheme->coverage = str_replace(",", '', Input::get('coverage'));
					$scheme->tradedeal_uom_id = $uom->id;


					$pcs_deal = 0;
					if($scheme->tradedeal_uom_id == 1){
						$pcs_deal = 1;
					}else if($scheme->tradedeal_uom_id == 2){
						$pcs_deal = 12;
					}else{
						if($tradedeal->non_ulp_premium){
							$pcs_deal = $tradedeal->non_ulp_pcs_case;
						}else{
							$premuim_sku = TradedealPartSku::find(Input::get('premium_sku'));
							$pcs_deal = $premuim_sku->pre_pcs_case;
						}
						
					}
					$scheme->pcs_deal = $pcs_deal;
					
					if($tradedeal->non_ulp_premium){
						$scheme->pre_code = $tradedeal->non_ulp_premium_code;
						$scheme->pre_desc = $tradedeal->non_ulp_premium_desc;
						$scheme->pre_cost = $tradedeal->non_ulp_premium_cost;
						$scheme->pre_pcs_case = $tradedeal->non_ulp_pcs_case;
					}else{
						$premuim_sku = TradedealPartSku::find(Input::get('premium_sku'));
						$scheme->pre_id = $premuim_sku->id;
						$scheme->pre_code = $premuim_sku->pre_code;
						$scheme->pre_desc = $premuim_sku->pre_desc;
						$scheme->pre_cost = $premuim_sku->pre_cost;
						$scheme->pre_pcs_case = $premuim_sku->pre_pcs_case;
					}
					$scheme->save();

					TradedealSchemeSku::addHostSku(Input::get('skus'), $scheme);
				}


				TradedealSchemeChannel::createChannelSelection($scheme, $activity, Input::get('channels'));

				TradedealAllocRepository::updateAllocation($scheme);
				// LeTemplateRepository::generateTemplate($scheme);
			}

			return Redirect::to(URL::action('ActivityController@edit', array('id' => $activity->id)) . "#tradedeal")
					->with('class', 'alert-success')
					->with('message', 'Trade Deal scheme was successfuly created.');
		}

		return Redirect::route('activity.createtradealscheme', $activity->id)
				->withInput()
				->withErrors($validation)
				->with('class', 'alert-danger')
				->with('message', 'There were validation errors.');
	}

	public function deletetradedealscheme(){
		if(Request::ajax()){
			$id = Input::get('d_id');
			$tradedealscheme = TradedealScheme::find($id);
			$tradedeal = Tradedeal::find($tradedealscheme->tradedeal_id);
			if(empty($tradedealscheme)){
				$arr['success'] = 0;
				Session::flash('class', 'alert-danger');
				Session::flash('message', 'An error occured while deleting the record.');
			}else{
				TradedealSchemeAllocation::where('tradedeal_scheme_id', $tradedealscheme->id)->delete();
				TradedealSchemeSku::where('tradedeal_scheme_id', $tradedealscheme->id)->delete();
				TradedealSchemeChannel::where('tradedeal_scheme_id', $tradedealscheme->id)->delete();
				TradedealChannelList::where('tradedeal_scheme_id', $tradedealscheme->id)->delete();
				TradedealSchemeSubType::where('tradedeal_scheme_id', $tradedealscheme->id)->delete();
				$tradedealscheme->delete();

				File::deleteDirectory(storage_path('le/'.$tradedeal->activity_id.'/'.$tradedealscheme->id));

				$arr['success'] = 1;	
				Session::flash('class', 'alert-success');
				Session::flash('message', 'Tradedeal Scheme successfully deleted.');			
			}
			$arr['id'] = $id;
			return json_encode($arr);
		}
	}

	public function exporttradedeal($id){
		$activity = Activity::findOrFail($id);
		Excel::create($activity->circular_name, function($excel) use($activity){
			$excel->sheet('SCHEME SUMMARY', function($sheet) use ($activity) {
				$sheet->setCellValueByColumnAndRow(0,1, 'Activity Title:');
				$sheet->setCellValueByColumnAndRow(1,1, $activity->circular_name);
				$sheet->setCellValueByColumnAndRow(0,2, 'Start Date:');
				$sheet->setCellValueByColumnAndRow(1,2, date('d/m/Y', strtotime($activity->eimplementation_date)));
				$sheet->setCellValueByColumnAndRow(0,3, 'End Date:');
				$sheet->setCellValueByColumnAndRow(1,3, date('d/m/Y', strtotime($activity->end_date)));

				$sheet->row(5, array('ACTIVITY', 'Scheme Code', 'Scheme Description', 'HOST CODE', 'HOST DESCRIPTION', 'PREMIUM CODE / PIMS CODE', 'Premium SKU', 'Master Outlet Subtype Name', 'Master Outlet Subtype Code'));
				$sheet->getStyle("A5:I5")->getFont()->setBold(true);
				$row = 6;

				$tradedeal = Tradedeal::getActivityTradeDeal($activity);
				$tradedealschemes = TradedealScheme::where('tradedeal_id',$tradedeal->id)
					->orderBy('tradedeal_type_id')
					->orderBy('tradedeal_uom_id')
					->get();
				foreach ($tradedealschemes as $scheme) {
						$sheet->setCellValueByColumnAndRow(0,$row, $scheme->name);
						if($scheme->tradedeal_type_id == 1){
							$host_skus = TradedealSchemeSku::getHostSku($scheme);
							$sku_cnt = count($host_skus);
							foreach ($host_skus as $key => $host_sku) {
								$deal_id = TradedealSchemeAllocation::getSchemeCode($scheme, $host_sku);
								$sheet->setCellValueByColumnAndRow(1,$row, $deal_id->scheme_code);
								$sheet->setCellValueByColumnAndRow(2,$row, $deal_id->scheme_desc);
								$sheet->setCellValueByColumnAndRow(3,$row, $host_sku->host_code);
								$sheet->setCellValueByColumnAndRow(4,$row, $host_sku->host_desc. ' '.$host_sku->variant);
								$sheet->setCellValueByColumnAndRow(5,$row, $host_sku->pre_code);
								$sheet->setCellValueByColumnAndRow(6,$row, $host_sku->pre_desc. ' '.$host_sku->pre_variant);	
								$row++;	
							}
							$row = $row - $sku_cnt;
							$channels = TradedealSchemeSubType::getSchemeSubtypes($scheme);
							$ch_cnt = count($channels);
							foreach ($channels as $channel) {
								$sheet->setCellValueByColumnAndRow(7,$row, $channel->sub_type_desc);
								$sheet->setCellValueByColumnAndRow(8,$row, $channel->sub_type);
								$row++;
							}

							if($ch_cnt < $sku_cnt){
								$x = $sku_cnt - $ch_cnt;
								$row = $row + $x;
							}
						}
						if($scheme->tradedeal_type_id == 2){
							
						}

						if($scheme->tradedeal_type_id == 3){
							$host_skus = TradedealSchemeSku::getHostSku($scheme);
							$deal_id = TradedealSchemeAllocation::getCollecttiveSchemeCode($scheme);
							$sheet->setCellValueByColumnAndRow(1,$row, $deal_id->scheme_code);
							$sheet->setCellValueByColumnAndRow(2,$row, $deal_id->scheme_desc);
							$host_skus = TradedealSchemeSku::getHostSku($scheme);
							$sku_cnt = count($host_skus);
							foreach ($host_skus as $key => $host_sku) {
								$sheet->setCellValueByColumnAndRow(3,$row, $host_sku->host_code);
								$sheet->setCellValueByColumnAndRow(4,$row, $host_sku->host_desc. ' '.$host_sku->variant);	
								$row++;	
							}
							$row = $row - $sku_cnt;

							if($tradedeal->non_ulp_premium){
								$sheet->setCellValueByColumnAndRow(5,$row, $scheme->pre_code);
								$sheet->setCellValueByColumnAndRow(6,$row, $scheme->pre_desc .' '.$scheme->pre_variant);
							}else{
								$part_sku = TradedealPartSku::find($scheme->pre_id);
								$sheet->setCellValueByColumnAndRow(5,$row, $part_sku->pre_code);
								$sheet->setCellValueByColumnAndRow(6,$row, $part_sku->pre_desc .' '.$part_sku->pre_variant);
							}

							$channels = TradedealSchemeSubType::getSchemeSubtypes($scheme);
							$ch_cnt = count($channels);
							foreach ($channels as $channel) {
								$sheet->setCellValueByColumnAndRow(7,$row, $channel->sub_type_desc);
								$sheet->setCellValueByColumnAndRow(8,$row, $channel->sub_type);
								$row++;
							}

							if($ch_cnt < $sku_cnt){
								$x = $sku_cnt - $ch_cnt;
								$row = $row + $x;
							}
							
						}					
				}
		    });

		    $excel->sheet('ALLOCATIONS', function($sheet) use ($activity) {
		    	
				$tradedeal_skus = TradedealPartSku::where('activity_id', $activity->id)->groupBy('pre_code')->get();
				$tradedeal = Tradedeal::getActivityTradeDeal($activity);
				$allocations = TradedealSchemeAllocation::exportAlloc($tradedeal);

				$sheet->setWidth('A', 16);
				$sheet->setWidth('B', 13);
				$sheet->setWidth('C', 20);
				$sheet->setWidth('D', 10);
				$sheet->setWidth('E', 30);
				$sheet->setWidth('F', 15);
				$sheet->setWidth('G', 20);
				$sheet->setWidth('H', 5);

				$row = 2;
				$sheet->row($row, array('AREA', 'Distributor Code', 'Distributor Name', 'Site Code', 'Site Name', 'Scheme Code', 'Scheme Description', 'UOM'));


				$sheet->getDefaultStyle()
				    ->getAlignment()
				    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

				// premuim
				$premiums = [];
				foreach ($tradedeal_skus as $sku) {
					$premiums[] = $sku->pre_desc. ' '. $sku->pre_variant;
				}

				$col = 8;
				$col_pre = [];
				$col_pre_x = [];
				$sheet->setCellValueByColumnAndRow($col,1, 'DEALS');
				foreach ($premiums as $premuim) {
					$sheet->setCellValueByColumnAndRow($col,2, $premuim);
					$sheet->setWidth(PHPExcel_Cell::stringFromColumnIndex($col), 10);
					$col_pre[$premuim] = $col;
					$col++;
				}
				$style = array(
			        'alignment' => array(
			            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			        )
			    );

			    

				$d_col = $col -1;
				$sheet->mergeCells(\PHPExcel_Cell::stringFromColumnIndex(8).'1:'.\PHPExcel_Cell::stringFromColumnIndex($d_col).'1');
				$sheet->getStyle(\PHPExcel_Cell::stringFromColumnIndex(8).'1:'.\PHPExcel_Cell::stringFromColumnIndex($d_col).'1')
					->applyFromArray(array(
				    'fill' => array(
				        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				        'color' => array('rgb' => '091462')
				    )
				));

				$sheet->cells(\PHPExcel_Cell::stringFromColumnIndex(8).'1:'.\PHPExcel_Cell::stringFromColumnIndex($d_col).'1', function($cells) {
					$cells->setFontColor('#ffffff');
				});

				$sheet->getStyle(\PHPExcel_Cell::stringFromColumnIndex(8).'1:'.\PHPExcel_Cell::stringFromColumnIndex($d_col).'1')->applyFromArray($style);


				$sheet->setCellValueByColumnAndRow($col,1, 'PREMIUMS');
				foreach ($premiums as $premuim) {
					$sheet->setCellValueByColumnAndRow($col,2, $premuim);
					$col_pre_x[$premuim] = $col;
					$col++;
				}
				$d_col++;

				$p_col = $col - 1;

				$sheet->getStyle('A2:'.\PHPExcel_Cell::stringFromColumnIndex($d_col).'2')
					->getFont()
					->setBold(true);
				
				// Set background color for a specific cell
				$sheet->getStyle('A2:'.\PHPExcel_Cell::stringFromColumnIndex($p_col).'2')->applyFromArray(array(
				    'fill' => array(
				        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				        'color' => array('rgb' => 'DAEBF8')
				    )
				));

				$sheet->mergeCells(\PHPExcel_Cell::stringFromColumnIndex($d_col).'1:'.\PHPExcel_Cell::stringFromColumnIndex($p_col).'1');
				$sheet->getStyle(\PHPExcel_Cell::stringFromColumnIndex($d_col).'1:'.\PHPExcel_Cell::stringFromColumnIndex($p_col).'1')
					->applyFromArray(array(
				    'fill' => array(
				        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				        'color' => array('rgb' => '7F00A1')
				    )
				));

				$sheet->cells(\PHPExcel_Cell::stringFromColumnIndex($d_col).'1:'.\PHPExcel_Cell::stringFromColumnIndex($p_col).'1', function($cells) {
					$cells->setFontColor('#ffffff');
				});


				$sheet->getStyle(\PHPExcel_Cell::stringFromColumnIndex($d_col).'1:'.\PHPExcel_Cell::stringFromColumnIndex($p_col).'1')->applyFromArray($style);

				$sheet->getStyle('I2:R2')->getAlignment()
					->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER))
					->setWrapText(true);

				$sheet->setWidth(\PHPExcel_Cell::stringFromColumnIndex($col), 10);


				$last_area = '';
				$last_distributor = '';
				$last_site = '';
				$first_row = 3;
				foreach ($allocations as $alloc) {
					$row++;
					if($alloc->tradedeal_uom_id == 1){
						$pcs_deal = 1;
					}
					if($alloc->tradedeal_uom_id == 2){
						$pcs_deal = 12;
					}
					if($alloc->tradedeal_uom_id == 3){
						$pcs_deal = $alloc->pcs_case;
					}
					
					if($last_area == $alloc->area){
						if($last_distributor == $alloc->sold_to_code){

							if($last_site == $alloc->plant_code){
								$sheet->row($row, ['', '', '', '', '', $alloc->scheme_code, $alloc->scheme_description, $pcs_deal]);
							}else{
								$sheet->row($row, ['', '', '', $last_site.' Total']);
								foreach ($col_pre as $col) {
									$last_row = $row - 1;
									$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
									$sheet->setCellValueByColumnAndRow($col,$row,$sum);
								}

								foreach ($col_pre_x as $col) {
									$last_row = $row - 1;
									$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
									$sheet->setCellValueByColumnAndRow($col,$row,$sum);
								}

								$sheet->getStyle('D'.$row.':'.\PHPExcel_Cell::stringFromColumnIndex($p_col).$row)->applyFromArray(array(
								    'fill' => array(
								        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
								        'color' => array('rgb' => 'DAEBF8')
								    )
								));

								$row++;
								$first_row = $row;
								$sheet->row($row, ['', '', '', $alloc->plant_code, $alloc->ship_to_name, $alloc->scheme_code, $alloc->scheme_description, $pcs_deal]);
							}
							$sheet->setCellValueByColumnAndRow($col_pre[$alloc->pre_desc_variant],$row, $alloc->final_pcs / $pcs_deal);
							$sheet->setCellValueByColumnAndRow($col_pre_x[$alloc->pre_desc_variant],$row, $alloc->final_pcs);
						}else{
							if($last_site != $alloc->plant_code){
								$sheet->row($row, ['', '', '', $last_site.' Total']);
								foreach ($col_pre as $col) {
									$last_row = $row - 1;
									$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
									$sheet->setCellValueByColumnAndRow($col,$row,$sum);
								}

								foreach ($col_pre_x as $col) {
									$last_row = $row - 1;
									$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
									$sheet->setCellValueByColumnAndRow($col,$row,$sum);
								}

								$sheet->getStyle('D'.$row.':'.\PHPExcel_Cell::stringFromColumnIndex($p_col).$row)->applyFromArray(array(
								    'fill' => array(
								        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
								        'color' => array('rgb' => 'DAEBF8')
								    )
								));

								$row++;
								$first_row = $row;
							}

							if($last_distributor != $alloc->sold_to_code){
								if($alloc->plant_code == ''){
									$sheet->row($row, ['', '', '', $last_distributor.' Total']);
									foreach ($col_pre as $col) {
										$last_row = $row - 1;
										$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
										$sheet->setCellValueByColumnAndRow($col,$row,$sum);
									}

									foreach ($col_pre_x as $col) {
										$last_row = $row - 1;
										$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
										$sheet->setCellValueByColumnAndRow($col,$row,$sum);
									}

									$sheet->getStyle('D'.$row.':'.\PHPExcel_Cell::stringFromColumnIndex($p_col).$row)->applyFromArray(array(
									    'fill' => array(
									        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
									        'color' => array('rgb' => 'DAEBF8')
									    )
									));

									$row++;
									$first_row = $row;
								}
							}

							$sheet->row($row, ['', $alloc->sold_to_code, $alloc->sold_to, $alloc->plant_code, $alloc->ship_to_name, $alloc->scheme_code, $alloc->scheme_description, $pcs_deal]);
							$sheet->setCellValueByColumnAndRow($col_pre[$alloc->pre_desc_variant],$row, $alloc->final_pcs / $pcs_deal);
							$sheet->setCellValueByColumnAndRow($col_pre_x[$alloc->pre_desc_variant],$row, $alloc->final_pcs);



						}
					}else{
						if(($last_site != $alloc->plant_code) && ($last_site != '')){
							$sheet->row($row, ['', '', '', $last_site.' Total']);
							foreach ($col_pre as $col) {
								$last_row = $row - 1;
								$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
								$sheet->setCellValueByColumnAndRow($col,$row,$sum);
							}

							foreach ($col_pre_x as $col) {
								$last_row = $row - 1;
								$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
								$sheet->setCellValueByColumnAndRow($col,$row,$sum);
							}

							$sheet->getStyle('D'.$row.':'.\PHPExcel_Cell::stringFromColumnIndex($p_col).$row)->applyFromArray(array(
							    'fill' => array(
							        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
							        'color' => array('rgb' => 'DAEBF8')
							    )
							));

							$row++;
							$first_row = $row;
						}else{
							if(!empty($last_distributor)){
								$sheet->row($row, ['', '', '', $last_distributor.' Total']);
								foreach ($col_pre as $col) {
									$last_row = $row - 1;
									$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
									$sheet->setCellValueByColumnAndRow($col,$row,$sum);
								}

								foreach ($col_pre_x as $col) {
									$last_row = $row - 1;
									$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
									$sheet->setCellValueByColumnAndRow($col,$row,$sum);
								}

								$sheet->getStyle('D'.$row.':'.\PHPExcel_Cell::stringFromColumnIndex($p_col).$row)->applyFromArray(array(
								    'fill' => array(
								        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
								        'color' => array('rgb' => 'DAEBF8')
								    )
								));

								$row++;
								$first_row = $row;
							}
							
						}

						$sheet->row($row, [$alloc->area, $alloc->sold_to_code, $alloc->sold_to, $alloc->plant_code, $alloc->ship_to_name, $alloc->scheme_code, $alloc->scheme_description, $pcs_deal]);
						$sheet->setCellValueByColumnAndRow($col_pre[$alloc->pre_desc_variant],$row, $alloc->final_pcs / $pcs_deal);
						$sheet->setCellValueByColumnAndRow($col_pre_x[$alloc->pre_desc_variant],$row, $alloc->final_pcs);		
					}

					$last_area = $alloc->area;
					$last_distributor = $alloc->sold_to_code;
					$last_site = $alloc->plant_code;
				}

				$row++;
				$sheet->row($row, ['', '', '', $last_site.' Total']);	
				
				foreach ($col_pre as $col) {
					$last_row = $row - 1;
					$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
					$sheet->setCellValueByColumnAndRow($col,$row,$sum);
				}	

				foreach ($col_pre_x as $col) {
					$last_row = $row - 1;
					$sum = "=SUM(".\PHPExcel_Cell::stringFromColumnIndex($col).$first_row.":".\PHPExcel_Cell::stringFromColumnIndex($col).$last_row.")";
					$sheet->setCellValueByColumnAndRow($col,$row,$sum);
				}		

				$sheet->getStyle('D'.$row.':'.\PHPExcel_Cell::stringFromColumnIndex($p_col).$row)->applyFromArray(array(
				    'fill' => array(
				        'type'  => PHPExcel_Style_Fill::FILL_SOLID,
				        'color' => array('rgb' => 'DAEBF8')
				    )
				));
		    });
	
			$excel->sheet('OUTPUT FILE', function($sheet) use ($activity) {
				$tradedeal_skus = TradedealPartSku::where('activity_id', $activity->id)->groupBy('pre_code')->get();
				$tradedeal = Tradedeal::getActivityTradeDeal($activity);
				$allocations = TradedealSchemeAllocation::exportAlloc($tradedeal);

				$row = 1;
				$sheet->row($row, array('AREA', 'Distributor Code', 'Distributor Name', 'Site Code', 'Site Name', 'Scheme Code', 'Scheme Description',
					'Promo Description', 'Promo Type',
		    		'SKU Codes Involved', 'SKUs Involved', 'Premium Code', 'Premium',
		    		'Outlet Sub Types Involved', 'Outlet Codes', 'Allocation (Pieces)', 'UOM', 'Source of Premium', 
		    		'Start Date', 'End Date'));

				$sheet->getStyle("A1:V1")->getFont()->setBold(true);
				$sheet->getDefaultStyle()
				    ->getAlignment()
				    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			
				$last_area = '';
				$last_distributor = '';
				$last_site = '';
				$first_row = 3;
				foreach ($allocations as $alloc) {

					$scheme = TradedealScheme::find($alloc->tradedeal_scheme_id);

					$row++;
					if($alloc->tradedeal_uom_id == 1){
						$pcs_deal = 1;
					}
					if($alloc->tradedeal_uom_id == 2){
						$pcs_deal = 12;
					}
					if($alloc->tradedeal_uom_id == 3){
						$pcs_deal = $alloc->pcs_case;
					}

					$start_date = date('d/m/Y', strtotime($alloc->eimplementation_date));
		    		$end_date = date('d/m/Y', strtotime($alloc->end_date));

		    		$host_code = '';
			    	$host_desc = '';

			    	if($alloc->tradedeal_scheme_sku_id != 0){
			    		$host_sku = TradedealSchemeSku::getHost($alloc->tradedeal_scheme_sku_id);
			    		$host_code = $host_sku->host_code;
			    		$host_desc = $host_sku->host_desc;
			    	}else{

			    		$host_skus = TradedealSchemeSku::getHostSku($scheme);
			    		$code = [];
			    		$desc = [];
			    		foreach ($host_skus as $key => $value) {

			    			$code[] = $value->host_code;
			    			$desc[] = $value->host_desc;
			    		}

			    		$host_code = implode("; ", $code);
			    		$host_desc = implode("; ", $desc);

			    	}

			    	$pre_code = $alloc->pre_code;
			    	$pre_desc = $alloc->pre_desc;
			    	$source = 'Ex-DT';
			    	if(!$alloc->non_ulp_premium){
			    		$source = 'Ex-ULP';
			    	}else{
			    		
			    	}

			    	$channels = TradedealSchemeSubType::getSchemeSubtypes($scheme);
			    	$ch_code = [];
			    	$ch_desc = [];
			    	foreach ($channels as $channel) {
		    			$ch_code[] = $channel->sub_type;
		    			$ch_desc[] = $channel->sub_type_desc;
		    		}

		    		$channel_code = implode("; ", $ch_code);
			    	$channel_desc = implode("; ", $ch_desc);
					
					// if($last_area == $alloc->area){
					// 	if($last_distributor == $alloc->sold_to_code){
					// 		if($last_site == $alloc->plant_code){
					// 			$sheet->row($row, ['', '', '', '', '', $alloc->scheme_code, $alloc->scheme_description, $alloc->scheme_desc, $alloc->tradedeal_type,$host_code, $host_desc, $pre_code, $pre_desc, $channel_code, $channel_desc, $alloc->final_pcs, $pcs_deal, $source, $start_date, $end_date]);
					// 		}else{
					// 			// $row++;
					// 			$first_row = $row;
					// 			$sheet->row($row, ['', '', '', $alloc->plant_code, $alloc->ship_to_name, $alloc->scheme_code, $alloc->scheme_description, $alloc->scheme_desc, $alloc->tradedeal_type, $host_code, $host_desc, $pre_code, $pre_desc, $channel_code, $channel_desc, $alloc->final_pcs, $pcs_deal, $source, $start_date, $end_date]);
					// 		}
							
					// 	}else{
					// 		if($last_site != $alloc->plant_code){
					// 			// $row++;
					// 			$first_row = $row;
					// 		}
					// 		$sheet->row($row, ['', $alloc->sold_to_code, $alloc->sold_to, $alloc->plant_code, $alloc->ship_to_name, $alloc->scheme_code, $alloc->scheme_description, $alloc->scheme_desc, $alloc->tradedeal_type, $host_code, $host_desc, $pre_code, $pre_desc, $channel_code, $channel_desc, $alloc->final_pcs, $pcs_deal, $source, $start_date, $end_date]);
							
					// 	}
					// }else{
					// 	if(($last_site != $alloc->plant_code) && ($last_site != '')){
					// 		// $row++;
					// 		$first_row = $row;
					// 	}
						$sheet->row($row, [$alloc->area, $alloc->sold_to_code, $alloc->sold_to, $alloc->plant_code, $alloc->ship_to_name, $alloc->scheme_code, $alloc->scheme_description, $alloc->scheme_desc, $alloc->tradedeal_type, $host_code, $host_desc, $pre_code, $pre_desc, $channel_code, $channel_desc, $alloc->final_pcs, $pcs_deal, $source, $start_date, $end_date]);
				
					// }

					// $last_area = $alloc->area;
					// $last_distributor = $alloc->sold_to_code;
					// $last_site = $alloc->plant_code;
				}

		    });
		})->download('xls');
	}

	public function exporttddetails($id){
		$activity = Activity::findOrFail($id);
		Excel::create($activity->circular_name.' Bonus Buy Free', function($excel) use($activity){
			$excel->sheet('Allocations', function($sheet) use ($activity) {
				$allocations = TradedealSchemeAllocation::getAll($activity);
				$sheet->row(1, array( 'ID', 'Activity ID', 'Activity Description', 'Scheme Code', 'Scheme Description', 'Host SKU Code', 'Host SKU', 'Premium SKU Code', 'Premium SKU', 'Area Code', 
					'Area', 'Sold To Code', 'Sold To', 'U2K2 Code', 'Ship To Code', 'Ship To Name', '# of deals', 'Computed Allocation (Pieces) ', 'Final Allocation (Pieces) ', 'New Allocation (Pieces) '));

				$sheet->setAutoFilter();
				$row = 2;
				$indhostsku =[];
				$colhostsku =[];
				$indhostskucode =[];
				$colhostskucode =[];
				$hostsku = '';
				$host_code = '';
				$premium_code = '';
				foreach ($allocations as $alloc) {
					if($alloc->tradedeal_scheme_sku_id > 0){
						if(!isset($indhostsku[$alloc->tradedeal_scheme_sku_id])){
							$hs = TradedealSchemeSku::getHost($alloc->tradedeal_scheme_sku_id);
							$indhostsku[$alloc->tradedeal_scheme_sku_id] = $hs->host_desc;
							$indhostskucode[$alloc->tradedeal_scheme_sku_id] = $hs->host_code;
							$hostsku = $indhostsku[$alloc->tradedeal_scheme_sku_id];
							$host_code = $indhostskucode[$alloc->tradedeal_scheme_sku_id];
						}else{
							$hostsku = $indhostsku[$alloc->tradedeal_scheme_sku_id];
							$host_code = $indhostskucode[$alloc->tradedeal_scheme_sku_id];
						}
					}else{

						if(!isset($colhostsku[$alloc->tradedeal_scheme_id])){
							$scheme = TradedealScheme::find($alloc->tradedeal_scheme_id);
							$hs = TradedealSchemeSku::getHostSku($scheme);
							$x = [];
							$y = [];
							foreach ($hs as $value) {
								$x[] = $value->host_desc;
								$y[] = $value->host_code;
							}
							$colhostsku[$alloc->tradedeal_scheme_id] = implode("; ", $x);
							$colhostskucode[$alloc->tradedeal_scheme_id] = implode("; ", $y);
							$hostsku = $colhostsku[$alloc->tradedeal_scheme_id];
							$host_code = $colhostskucode[$alloc->tradedeal_scheme_id];
						}else{
							$hostsku = $colhostsku[$alloc->tradedeal_scheme_id];
							$host_code = $colhostskucode[$alloc->tradedeal_scheme_id];
						}
					}
					$num_of_deal  = 0;
					if($alloc->final_pcs > 0){
						$num_of_deal = $alloc->final_pcs / $alloc->deal_multiplier;
					}
					$sheet->row($row, array(
						$alloc->alloc_id, 
						$alloc->activity_id, 
						$alloc->name, 
						$alloc->scheme_code, 
						$alloc->scheme_desc, 
						$host_code,
						$hostsku, 
						$alloc->pre_code,
						$alloc->pre_desc_variant, 
						$alloc->area_code, 
						$alloc->area, 
						$alloc->sold_to_code, 
						$alloc->sold_to, 
						$alloc->ship_to_code, 
						$alloc->plant_code, 
						$alloc->ship_to_name, 
						$num_of_deal, 
						$alloc->computed_pcs, 
						$alloc->final_pcs, 
						$alloc->total_alloc));
					$row++;
				}
				$cnt = count($allocations) + 3;

				$sheet->getProtection()->setPassword('tradedeal');
				$sheet->getProtection()->setSheet(true);
				$sheet->getStyle('T2:T'.$cnt)->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
		    });

			$excel->sheet('ALLOCATION SUMMARY', function($sheet) use ($activity) {
		    	$sheet->row(1, array('Scheme ID', 'Scheme Description', 'Sum of Computed Allocation (Pieces)', 'Sum of Final Allocation (Pieces)'));
	    		$allocations = TradedealSchemeAllocation::getAllocationSummary($activity);
	    		$row = 2;
	    		foreach ($allocations as $alloc) {				
					$sheet->row($row, array(
						$alloc->scheme_code, 
						$alloc->scheme_desc, 
						$alloc->sum_computed, 
						$alloc->sum_final_pcs));
					$row++;
				}
			    
		    });

		})->download('xls');
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
					if(in_array($user->department_id, $approvers)){
						$new_user->pre_approve = 1;

						$data['activity'] = Activity::getDetails($id);
						$data['fullname'] = $fullname;
						$data['user'] = $user;
						$data['to_user'] = $user->first_name;
						$data['line1'] = "<p>You have been added as an activity approver for <b>".$activity->circular_name."</b>.</p>";
						$data['line2']= "<p>You may view/edit this activity through this link >> <a href=".route('activity.preapproveedit',$activity->id)."> ".route('activity.preapproveedit', $activity->id)."</a></p>";

						
					}
					
					$remarks = $fullname .' is added as an activity pre-approver.';

					if(!in_array($user->department_id, $approvers)){
						$new_user->activity_member_status_id = 3;
						$remarks = $fullname .' is added as an expectator.';

						$data['activity'] = Activity::getDetails($id);
						$data['fullname'] = $fullname;
						$data['user'] = $user;
						$data['to_user'] = $user->first_name;
						$data['line1'] = "<p>You have been added as an activity member for <b>".$activity->circular_name."</b>.</p>";
						$data['line2']= "<p>You may view this activity through this link >> <a href=".route('activity.preapproveedit',$activity->id)."> ".route('activity.preapproveedit', $activity->id)."</a></p>";

					}
					$new_user->save();

					ActivityTimeline::addTimeline($activity, Auth::user(), "add a member", $remarks);

					$data['subject'] = 'CUSTOMIZED ACTIVITY - APPROVER';
					if($_ENV['MAIL_TEST']){
						Mail::send('emails.customized', $data, function($message) use ($data){
							$message->to("rbautista@chasetech.com", $data['fullname']);
							$message->bcc("Grace.Erum@unilever.com");
							$message->subject($data['subject']);
						});
					}else{
						Mail::send('emails.customized', $data, function($message) use ($data){
							$message->to(trim(strtolower($user->email)), $data['fullname'])->subject($data['subject']);
						});
					}

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

	public function removemember($id){
		$member = ActivityMember::findOrFail($id);
		$activity = Activity::findOrFail($member->activity_id);
		if(!Activity::myActivity($activity)){
			return View::make('shared.404');
		}else{
			if(empty($member)){
				return Redirect::to(URL::action('ActivityController@edit', array('id' => $member->activity_id)) . "#member")
					->with('class', 'alert-danger')
					->with('message', 'Member not found');
			}else{
				$member->delete();

				ActivityTimeline::addTimeline($activity, Auth::user(), "removed a member", $member->user_desc .' is removed as activity member.');
				$user = User::find($member->user_id);
				$fullname = $user->first_name . ' ' . $user->last_name;

				$data['activity'] = Activity::getDetails($member->activity_id);
				$data['fullname'] = $fullname;
				$data['user'] = $user;
				$data['to_user'] = $user->first_name;
				$data['line1'] = "<p>You have been remove as an activity member/approver for <b>".$activity->circular_name."</b>.</p>";
				$data['line2']= "";
				$data['subject'] = 'CUSTOMIZED ACTIVITY - REMOVED MEMBER';
				if($_ENV['MAIL_TEST']){
					Mail::send('emails.customized', $data, function($message) use ($data){
						$message->to("rbautista@chasetech.com", $data['fullname']);
						$message->bcc("Grace.Erum@unilever.com");
						$message->subject($data['subject']);
					});
				}else{
					Mail::send('emails.customized', $data, function($message) use ($data){
						$message->to(trim(strtolower($user->email)), $data['fullname'])->subject($data['subject']);
					});
				}

				return Redirect::to(URL::action('ActivityController@edit', array('id' => $member->activity_id)) . "#member")
					->with('class', 'alert-success')
					->with('message', 'Member successfuly removed.');
			}
		}
	}

	public function reapprove($id){
		$member = ActivityMember::findOrFail($id);
		$activity = Activity::findOrFail($member->activity_id);
		if(!Activity::myActivity($activity)){
			return View::make('shared.404');
		}else{
			if(empty($member)){
				return Redirect::to(URL::action('ActivityController@edit', array('id' => $member->activity_id)) . "#member")
					->with('class', 'alert-danger')
					->with('message', 'Member not found');
			}else{
				$member->activity_member_status_id = 1;
				$member->update();

				ActivityTimeline::addTimeline($activity, Auth::user(), "update activity member",'activity is re applied for approval to '. $member->user_desc);
				return Redirect::to(URL::action('ActivityController@edit', array('id' => $member->activity_id)) . "#member")
					->with('class', 'alert-success')
					->with('message', 'Member successfuly removed.');
			}
		}
		
	}

	public function joborder($id){
		$joborder = Joborder::findOrFail($id);
		$artworks = JoborderArtwork::where('joborder_id', $joborder->id)->get();
		$comments = $joborder->comments()->orderBy('created_at', 'asc')->get();
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
			$subtask = SubTask::find($subtask->id);

			$joborder = new Joborder;
			$joborder->activity_id = $activity->id;
			$joborder->created_by = Auth::id();
			$joborder->task_id = $task->id;
			$joborder->task = $task->task;
			$joborder->sub_task_id = $subtask->id;
			$joborder->sub_task = $subtask->sub_task;
			$joborder->department_id = $subtask->department_id;
			$joborder->target_date = date('Y-m-d',strtotime(Input::get('target_date')));
			$joborder->weight = $subtask->weight;
			$joborder->cost = $subtask->cost;
			$joborder->revision = 0;
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

			$url = route('activity.joborder', $joborder->id); 
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
			$timelines = ActivityTimeline::getTop($activity);
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
			'activityIdList','id_index','status', 'soballocation', 'timelines'));

		}
	}

	public function updatecustom($id){
		$activity = Activity::findOrFail($id);
		if((!Auth::user()->isChannelApprover()) || (!ActivityMember::myActivity($activity->id))){
			return View::make('shared.404');
		}else{
			$member = ActivityMember::myActivity($activity->id);
			if((!empty($member)) && ($member->activity_member_status_id = 1)){
				$created_by = User::find($activity->created_by);
				$user = User::find($member->user_id);
				$data['activity'] = Activity::getDetails($id);
				$data['fullname'] = $created_by->first_name . ' ' . $created_by->last_name;
				$data['user'] = $user;
				$data['to_user'] = $created_by->first_name;
				$data['created_by'] = $created_by;

				if(Input::get('update_status') == '1'){
					$member->activity_member_status_id = 3;
					$member->update();
					ActivityTimeline::addTimeline($activity, Auth::user(), "approved the activity",Input::get('submitremarks'));
					
					$data['line1'] = "<p><b>".$activity->circular_name."</b> has been approved by <b>". ucwords(strtolower($user->first_name)). " ". ucwords(strtolower($user->last_name))."</b>. You may now start planning and adding members to your activity.</p>";
					$data['line2'] = "<p>You may view and edit this activity thru this link >> <a href=".route('activity.edit',$activity->id)."> ".route('activity.edit', $activity->id)."</a></p>";
					$data['subject']= 'CUSTOMIZED ACTIVITY - APPROVED';
				}else{
					$member->activity_member_status_id = 2;
					$member->update();
					ActivityTimeline::addTimeline($activity, Auth::user(), "denied the activity",Input::get('submitremarks'));

					$data['line1'] = "<p><b>".$activity->circular_name."</b> has been denied by <b>". ucwords(strtolower($user->first_name)). " ". ucwords(strtolower($user->last_name))."</b>.</p>";
					$data['line2'] = "<p>You may view comments and edit this activity thru this link >> <a href=".route('activity.edit',$activity->id)."> ".route('activity.edit', $activity->id)."</a></p>";
					$data['subject'] = 'CUSTOMIZED ACTIVITY - DENIED';
					
				}

				if($_ENV['MAIL_TEST']){
					Mail::send('emails.customized', $data, function($message) use ($data){
						$message->to("rbautista@chasetech.com", $data['fullname']);
						$message->bcc("Grace.Erum@unilever.com");
						$message->subject($data['subject']);
					});
				}else{
					Mail::send('emails.customized', $data, function($message) use ($data){
						$message->to(trim(strtolower($created_by->email)), $data['fullname'])->subject($data['subject']);
					});
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