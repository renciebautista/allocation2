<?php
use Alchemy\Zippy\Zippy;
class ReportController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /report
	 *
	 * @return Response
	 */
	public function activities()
	{
		Input::flash();
		if(Auth::user()->hasRole("ADMINISTRATOR")){
				
			$statuses = ActivityStatus::getLists();
			$cycles = Cycle::getLists();
			$scopes = ScopeType::getLists();
			$types = ActivityType::getLists();
			$planners = User::getApprovers(['PMOG PLANNER']);
			$proponents = User::getApprovers(['PROPONENT']);
			$activities = Activity::search(Input::get('pr'),Input::get('st'),Input::get('cy'),Input::get('sc'),Input::get('ty'),Input::get('pm'),Input::get('title'));
			return View::make('report.activities',compact('statuses', 'activities', 'cycles', 'scopes', 'types', 'planners', 'proponents'));
		}else{
			$cycles = Cycle::getLists();
			$scopes = ScopeType::getLists();
			$types = ActivityType::getLists();
			$planners = User::getApprovers(['PMOG PLANNER']);
			$proponents = User::getApprovers(['PROPONENT']);
			$status = ['9'];
			$activities = Activity::search2(Input::get('pr'),$status,Input::get('cy'),Input::get('sc'),Input::get('ty'),Input::get('pm'),Input::get('title'));
			return View::make('report.fieldactivities',compact('activities', 'cycles', 'scopes', 'types', 'planners', 'proponents'));
		}

		
	}

	public function preview($id){
		$activity = Activity::find($id);
		if(!empty($activity)){
			$planner = ActivityPlanner::where('activity_id', $activity->id)->first();
			$approvers = ActivityApprover::getNames($activity->id);

			$objectives = ActivityObjective::where('activity_id', $activity->id)->get();

			$budgets = ActivityBudget::with('budgettype')->where('activity_id', $id)->get();
			$nobudgets = ActivityNobudget::with('budgettype')->where('activity_id', $id)->get();

			$schemes = Scheme::getList($id);

			$skuinvolves = array();
			foreach ($schemes as $scheme) {
				$involves = SchemeHostSku::where('scheme_id',$scheme->id)->get();

				$premiums = SchemePremuimSku::where('scheme_id',$scheme->id)->get();
				
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

			$fdapermits = ActivityFdapermit::where('activity_id', $activity->id)->get();

			$networks = ActivityTiming::getTimings($activity->id,true);

			$activity_roles = ActivityRole::getListData($activity->id);

			$artworks = ActivityArtwork::getList($activity->id);

			$pispermit = ActivityFis::where('activity_id', $activity->id)->first();

			$sku_involves = ActivitySku::getInvolves($activity->id);
			
			// // Product Information Sheet
			$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
			if(!empty($pispermit)){
				try {
					$pis = Excel::selectSheets('Output')->load(storage_path().$path."/".$pispermit->hash_name)->get();
				} catch (Exception $e) {
					return View::make('shared.invalidpis');
				}

			}else{
				$pis = array();
			}

			$tradedeal = Tradedeal::getActivityTradeDeal($activity);
			$trade_allocations = [];
			$tradedeal_skus =[];
			if($tradedeal != null){
				$tradedeal_skus = TradedealPartSku::where('activity_id', $activity->id)->get();
				$tradedealschemes = TradedealScheme::getScheme($tradedeal->id);
				$trade_allocations = TradedealSchemeAllocation::getSummary($tradedeal);
			}

			$required_budget_type = ActivityTypeBudgetRequired::required($activity->activity_type_id);
			// dd($required_budget_type);
			// Helper::print_r($skuinvolves);
			return View::make('shared.preview', compact('activity' ,'planner', 'objectives', 'budgets','nobudgets',
				'schemes','skuinvolves', 'activity_roles','materials','fdapermits', 'networks','artworks', 
				'pis' , 'areas','channels', 'approvers' , 'sku_involves', 'required_budget_type', 'tradedealschemes', 'tradedeal',
				'trade_allocations', 'tradedeal_skus'));
		}
	}

	public function document($id){
		set_time_limit(0);
		ini_set('memory_limit', '256M');
		$activity = Activity::find($id);
		$word_name = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', $activity->circular_name);
		$worddoc = new WordDoc($activity->id);
		$worddoc->download(str_replace(":","_", $word_name).'.docx');	
	}

	public function download($id){
		$zippy = Zippy::load();
		$activity = Activity::findOrFail($id);
		$folders = array();
		// $zip_path = storage_path().'/zipped/activities/'.$activity->id.'_'.strtoupper(Helper::sanitize($activity->circular_name)).'.zip';
		$foldername = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', $activity->circular_name);
		$folder_name = str_replace(":","_", $foldername);
		$zip_path = storage_path().'/zipped/activities/'.strtoupper(Helper::sanitize($folder_name)).'.zip';
		File::delete($zip_path);
		$nofile = 'public/nofile/robots.txt';
		$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
		$distination = storage_path().$path ;
		$files = File::files($distination);
		if(count($files)>0){
			$circular_name = strtoupper(Helper::sanitize($folder_name));
			if (App::isLocal())
			{
			    $folder[$circular_name] = 'app/storage'.$path.'/';
			}else{
				$folder[$circular_name] = storage_path().$path.'/';
			}
			
		}else{
			$folder = $nofile;
		}

		// Helper::print_r($folder);
		$archive = $zippy->create($zip_path,$folder,true);
		return Response::download($zip_path);
	}

	public function review($id){
		$activity = Activity::findOrFail($id);

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
			$trade_allocations = TradedealSchemeAllocation::getSummary($tradedeal);
		}
		$participating_skus = TradedealPartSku::getParticipatingSku($activity);
		// end tradedeal

		$divisions = Sku::getDivisionLists();
		$route = 'reports.activities';
		$recall = $activity->pro_recall;
		$submit_action = 'ActivityController@updateactivity';
		return View::make('activity.activityreadonly', compact('activity', 'sel_planner', 'approvers', 
		 'sel_divisions','divisions', 'timings',
		 'objectives',  'users', 'budgets', 'nobudgets','sel_approver',
		 'sel_objectives',  'schemes', 'scheme_summary', 'networks','areas',
		 'scheme_customers', 'scheme_allcations', 'materials', 'force_allocs','sel_involves',
		 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings', 'comments' , 'route', 'recall', 'submit_action',
		 'tradedeal','total_deals', 'total_premium_cost', 'participating_skus', 'tradedealschemes', 'tradedeal_skus', 'trade_allocations'));
	}

	public function scheme($id){
		$scheme = Scheme::findOrFail($id);
		$activity = Activity::findOrFail($scheme->activity_id);

		$activity_schemes = Scheme::getIdList($activity->id);
		$id_index = array_search($id, $activity_schemes);

		$divisions = ActivityDivision::getList($scheme->activity_id);
		$categories = ActivityCategory::selected_category($scheme->activity_id);
		$brands = ActivityBrand::selected_brand($scheme->activity_id);
		$skus = Sku::items($divisions,$categories,$brands);
		// $involves = Pricelist::items();

		$host_sku = Pricelist::involves($brands,$activity);
		$premuim_sku =  Pricelist::items();

		$sel_skus =  SchemeSku::getSkus($scheme->id);
		$sel_hosts = SchemeHostSku::getHosts($scheme->id);
		$sel_premuim = SchemePremuimSku::getPremuim($scheme->id);
		
		$count = SchemeAllocation::where('scheme_id',$scheme->id)->count();

		$alloc_refs = AllocationSource::lists('alloc_ref', 'id');


		$premuim = array();
		if(!empty($sel_premuim)){
			$premuim = Pricelist::getSku($sel_premuim[0]);
		}
		
		$customers = ActivityCustomer::customers($scheme->activity_id);
		$_channels = ActivityChannel::channels($scheme->activity_id);
		$qty = $scheme->quantity;


		$allocations = Allocation::schemeAllocations($id);

		$alloref = AllocationSource::find($scheme->compute);
		$ac_groups = AccountGroup::where('show_in_summary',1)->get();

		if(!empty($ac_groups)){
			foreach ($ac_groups as $ac_group) {
				$customer = array();
				foreach ($allocations  as $allocation) {
					if(!empty($allocation->account_group_name)){
						if($ac_group->account_group_name == $allocation->account_group_name){
							if(array_key_exists($allocation->outlet, $customer)){
								$customer[$allocation->outlet]->computed_alloc +=  $allocation->computed_alloc;
								$customer[$allocation->outlet]->force_alloc +=  $allocation->force_alloc;
								$customer[$allocation->outlet]->final_alloc +=  $allocation->final_alloc;
							}else{
								$object = new StdClass;
								$object->account_name = $allocation->outlet;
								$object->computed_alloc = $allocation->computed_alloc;
								$object->force_alloc = $allocation->force_alloc;
								$object->final_alloc = $allocation->final_alloc;
								$customer[$allocation->outlet] = $object;
							}
							
						}
					}
					
				}
				sort($customer);
				$ac_group->customers = $customer;
			}
		}

		$groups = array();
		foreach ($allocations  as $allocation) {
			if((empty($allocation->customer_id)) && (empty($allocation->shipto_id))){
				if(array_key_exists($allocation->group, $groups)){
					if(array_key_exists($allocation->area, $groups[$allocation->group]->area)){
						$groups[$allocation->group]->area[$allocation->area]->computed_alloc +=  $allocation->computed_alloc;
						$groups[$allocation->group]->area[$allocation->area]->force_alloc +=  $allocation->force_alloc;
						$groups[$allocation->group]->area[$allocation->area]->final_alloc +=  $allocation->final_alloc;
					}else{
						$area_object = new StdClass;
						$area_object->group = $allocation->group;
						$area_object->area_name = $allocation->area;
						$area_object->computed_alloc = $allocation->computed_alloc;
						$area_object->force_alloc = $allocation->force_alloc;
						$area_object->final_alloc = $allocation->final_alloc;
					}

					$groups[$allocation->group]->area[$allocation->area] =  $area_object;
					$groups[$allocation->group]->computed_alloc +=  $allocation->computed_alloc;
					$groups[$allocation->group]->force_alloc +=  $allocation->force_alloc;
					$groups[$allocation->group]->final_alloc +=  $allocation->final_alloc;
				}else{
					

					$area_object = new StdClass;
					$area_object->group = $allocation->group;
					$area_object->area_name = $allocation->area;
					$area_object->computed_alloc = $allocation->computed_alloc;
					$area_object->force_alloc = $allocation->force_alloc;
					$area_object->final_alloc = $allocation->final_alloc;
					

					$object = new StdClass;
					$object->group_name = $allocation->group;
					$object->computed_alloc = $allocation->computed_alloc;
					$object->force_alloc = $allocation->force_alloc;
					$object->final_alloc = $allocation->final_alloc;
					$object->area[$allocation->area] = $area_object;
					$groups[$allocation->group] = $object;
				}

			}
		}

		$ref_sku = SchemeSku::where('scheme_id',$scheme->id)->first();
		
		$total_gsv = SchemeAllocation::totalgsv($id);

		$sobs = AllocationSob::getSob($scheme->id);

		// dd($sobs[0]);
		


		$header = AllocationSob::getHeader($scheme->id);

		$sob_header = array();
		$h_weeks =[];
		if(count($header) >0){
			foreach ($header as $value) {
				if(!in_array($value->weekno, $h_weeks)){
					$h_weeks[] = $value->weekno;
				}
				
				$sob_header[$value->sob_group_id][$value->weekno] = $value->share;
			}
		}

		$sob_group_total = [];
		foreach ($sobs as $sob) {
			foreach ($h_weeks as $weekno) {
				$_wek_no = 'wk_'.$weekno;
				if(!isset($sob_group_total[$sob->sobgroup][$weekno])){
					$sob_group_total[$sob->sobgroup][$weekno] = 0;
				}
				$sob_group_total[$sob->sobgroup][$weekno] += $sob->$_wek_no;
			}
			
		}

		// dd($sob_group_total);
		$sobgroups = SobGroup::all();

		if(count($sobs) >0){
			$prv_header = 0;
			foreach ($sobgroups as $value) {
				if(empty($prv_header)){
					$prv_header = $sob_header[$value->id];
				}
				if(!isset($sob_header[$value->id])){
					$sob_header[$value->id] = $prv_header;
				}
			}
		}
		

		$sobdivisions = Pricelist::divisions();

		return View::make('scheme.read_only',compact('scheme', 'activity_schemes', 'id_index', 'activity', 'skus', 'sel_skus', 'sel_hosts',
					'sel_premuim','allocations', 'total_sales', 'qty','id', 'summary', 'total_gsv','sku', 'host', 'premuim','ac_groups','groups',
					'host_sku','premuim_sku','ref_sku', 'count', 'alloc_refs','alloref', 'sobs', 'sob_header', 'sobdivisions','sobgroups','sob_group_total'));
		
	}
	
}