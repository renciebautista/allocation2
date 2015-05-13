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
		$statuses = ActivityStatus::orderBy('status')->lists('status', 'id');
		$cycles = Cycle::getLists();
		$scopes = ScopeType::getLists();
		$types = ActivityType::getLists();
		$planners = User::getApprovers(['PMOG PLANNER']);
		$proponents = User::getApprovers(['PROPONENT']);
		$activities = Activity::search(Input::get('pr'),Input::get('st'),Input::get('cy'),Input::get('sc'),
			Input::get('ty'),Input::get('pm'),Input::get('title'));
		return View::make('report.activities',compact('statuses', 'activities', 'cycles', 'scopes', 'types', 'planners', 'proponents'));
	}

	public function preview($id){
		$activity = Activity::find($id);
		if(!empty($activity)){
			$planner = ActivityPlanner::where('activity_id', $activity->id)->first();
			$budgets = ActivityBudget::with('budgettype')
					->where('activity_id', $id)
					->get();

			$nobudgets = ActivityNobudget::with('budgettype')
				->where('activity_id', $id)
				->get();

			$schemes = Scheme::getList($id);

			$skuinvolves = array();
			foreach ($schemes as $scheme) {
				$involves = SchemeHostSku::where('scheme_id',$scheme->id)
					->join('pricelists', 'scheme_host_skus.sap_code', '=', 'pricelists.sap_code')
					->get();
				foreach ($involves as $value) {
					$skuinvolves[] = $value;
				}

				$scheme->allocations = SchemeAllocation::getAllocations($scheme->id);
				
			}

			// Helper::print_r($schemes);

			$materials = ActivityMaterial::where('activity_id', $activity->id)
				->with('source')
				->get();

			$fdapermit = ActivityFdapermit::where('activity_id', $activity->id)->first();
			$networks = ActivityTiming::getTimings($activity->id,true);
			$artworks = ActivityArtwork::getList($activity->id);
			$pispermit = ActivityFis::where('activity_id', $activity->id)->first();

			// $scheme_customers = SchemeAllocation::getCustomers($activity->id);

			//Involved Area
			$areas = ActivityCustomer::getSelectedAreas($activity->id);
			$channels = ActivityChannel::getSelectecdChannels($activity->id);
			// Helper::print_array($areas);
			
			// // Product Information Sheet
			$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
			if(!empty($pispermit)){
				$pis = Excel::selectSheets('Output')->load(storage_path().$path."/".$pispermit->hash_name)->get();
			}else{
				$pis = array();
			}
			
			return View::make('shared.preview', compact('activity' ,'planner','budgets','nobudgets','schemes','skuinvolves','materials',
				'fdapermit', 'networks','artworks', 'pis' , 'areas','channels'));
		}
		
	}

	public function download($id){
		$zippy = Zippy::load();
		$activity = Activity::findOrFail($id);
		$folders = array();
		$zip_path = storage_path().'/zipped/activities/'.$activity->id.'_'.strtoupper(Helper::sanitize($activity->circular_name)).'.zip';
		File::delete($zip_path);
		$nofile = 'public/nofile/robots.txt';
		$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
		$distination = storage_path().$path ;
		$files = File::files($distination);
		if(count($files)>0){
			if (App::isLocal())
			{
			    $folder[Helper::sanitize($activity->circular_name)] = 'app/storage'.$path.'/';
			}else{
				$folder[Helper::sanitize($activity->circular_name)] = storage_path().$path.'/';
			}
			
		}else{
			$folder = $nofile;
		}

		// Helper::print_array($folder);
		$archive = $zippy->create($zip_path,$folder,true);
		return Response::download($zip_path);
	}

}