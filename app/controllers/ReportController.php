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

			$required_budget_type = ActivityTypeBudgetRequired::required($activity->activity_type_id);
			// dd($required_budget_type);
			// Helper::print_r($skuinvolves);
			return View::make('shared.preview', compact('activity' ,'planner', 'objectives', 'budgets','nobudgets',
				'schemes','skuinvolves', 'activity_roles','materials','fdapermits', 'networks','artworks', 
				'pis' , 'areas','channels', 'approvers' , 'sku_involves', 'required_budget_type'));
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

		$divisions = Sku::getDivisionLists();
		$route = 'reports.activities';
		$recall = $activity->pro_recall;
		$submit_action = 'ActivityController@updateactivity';
		return View::make('shared.activity_readonly', compact('activity', 'sel_planner', 'approvers', 
		 'sel_divisions','divisions', 'timings',
		 'objectives',  'users', 'budgets', 'nobudgets','sel_approver',
		 'sel_objectives',  'schemes', 'scheme_summary', 'networks','areas',
		 'scheme_customers', 'scheme_allcations', 'materials', 'force_allocs','sel_involves',
		 'fdapermits', 'fis', 'artworks', 'backgrounds', 'bandings', 'comments' , 'route', 'recall', 'submit_action'));
	}
	
}