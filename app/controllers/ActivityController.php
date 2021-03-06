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
	public function create()
	{
		if(Auth::user()->hasRole("PROPONENT")){
			$scope_types = ScopeType::getLists();
			$planners = User::getApprovers(['PMOG PLANNER']);
			$approvers = User::getApprovers(['GCOM APPROVER','CD OPS APPROVER','CMD DIRECTOR']);
			$activity_types = ActivityType::getWithNetworks();
			$cycles = Cycle::getLists();
			$divisions = Pricelist::divisions();
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
			// comments
			$comments = ActivityComment::getList($activity->id);

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

			$trade_allocations = [];
			if($tradedeal != null){
				$tradedealschemes = TradedealScheme::getScheme($tradedeal->id);
				$trade_allocations = TradedealSchemeAllocation::getSummary($tradedeal);
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
				 'pre_skus', 'tradedealschemes', 'td_shiptos', 'td_premiums', 'total_deals', 'total_premium_cost',  'trade_allocations'));
			}

			if($activity->status_id > 3){
				$submitstatus = array('2' => 'RECALL ACTIVITY');
				$divisions = Sku::getDivisionLists();
				$route = 'activity.index';
				$recall = $activity->pro_recall;
				$submit_action = 'ActivityController@updateactivity';

				$participating_skus = TradedealPartSku::getParticipatingSku($activity);

				return View::make('activity.activityreadonly', compact('activity', 'sel_planner', 'approvers', 
				 'sel_divisions','divisions', 'timings',
				 'objectives',  'users', 'budgets', 'nobudgets','sel_approver',
				 'sel_objectives',  'schemes', 'scheme_summary', 'networks','areas',
				 'scheme_customers', 'scheme_allcations', 'materials', 'force_allocs','sel_involves',
				 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings', 'comments' ,'submitstatus', 'route', 'recall', 'submit_action',
				 'tradedeal', 'total_deals', 'total_premium_cost', 'tradedealschemes', 'participating_skus', 'tradedeal_skus', 'trade_allocations'));
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
			// comments
			$comments = ActivityComment::getList($activity->id);

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

			$trade_allocations = [];
			if($tradedeal != null){
				$tradedealschemes = TradedealScheme::getScheme($tradedeal->id);
				$trade_allocations = TradedealSchemeAllocation::getSummary($tradedeal);
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
				 'tradedeal', 'total_deals', 'total_premium_cost', 'tradedealschemes', 'participating_skus', 'trade_allocations'));
			}else{
				$submitstatus = array('3' => 'RECALL ACTIVITY');
				$divisions = Sku::getDivisionLists();
				$route = 'activity.index';
				$recall = $activity->pmog_recall;
				$submit_action = 'ActivityController@submittogcm';

				$participating_skus = TradedealPartSku::getParticipatingSku($activity);
				
				return View::make('activity.activityreadonly', compact('activity', 'sel_planner', 'approvers', 'sel_divisions','divisions' ,
				 'objectives',  'users', 'budgets', 'nobudgets','sel_approver',
				 'sel_objectives',  'schemes', 'scheme_summary', 'networks', 'areas',
				 'scheme_customers', 'scheme_allcations', 'materials', 'force_allocs', 'timings' ,'sel_involves',
				 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings', 'comments' ,'submitstatus', 'route', 'recall', 'submit_action', 'tradedeal_skus',
				 'tradedeal', 'total_deals', 'total_premium_cost', 'tradedealschemes', 'participating_skus', 'trade_allocations'));
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
							
							$activity->activity_code =  ActivityRepository::generateActivityCode($activity,$scope,$cycle,$activity_type,$division_code,$category_code,$brand_code);

							$activity->proponent_name = Auth::user()->getFullname();
							$activity->contact_no = Auth::user()->contact_no;
							
							$activity->scope_type_id = $scope_id;
							$activity->scope_desc = $scope->scope_name;

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
							
							$activity->scope_type_id = $scope_id;
							$activity->scope_desc = $scope->scope_name;

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


	public function updateactivity($id){
		if(Request::ajax()){
			if(Auth::user()->hasRole("PROPONENT")){
				$arr = DB::transaction(function() use ($id)  {
					$activity = Activity::findOrFail($id);
					
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
							$comment->activity_id = $id;
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
					echo $e;
					$arr['success'] = 0;
					Session::flash('class', 'alert-danger');
					Session::flash('message', 'An error occcured while updating activity customers.');
				}
				
				
			}
			
			$arr['id'] = $id;
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
		$fieldfile = new FieldTrade($activity->id);
		$fieldfile->download();
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

}