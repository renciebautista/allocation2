<?php
use Alchemy\Zippy\Zippy;

class DownloadsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /downloads
	 *
	 * @return Response
	 */
	public function cycles()
	{
		Input::flash();
		if(Auth::user()->hasRole("ADMINISTRATOR")){
			$cycles = Cycle::getAllCycles(Input::get('search'));
			// var_dump($cycles);
			return View::make('downloads.cycleadmin',compact('cycles'));
		}else{
			$cycles = Cycle::getReleasedCycles(Input::get('search'));
			return View::make('downloads.cycles',compact('cycles'));
		}

	}


	public function download($id){
		$zippy = Zippy::load();
		$cycle = Cycle::where('id',$id)->first();
		$activity_types = ActivityType::all();
		$zip_path = storage_path().'/zipped/cycles/'.$cycle->cycle_name.'.zip';
		File::delete($zip_path);
		$with_files = false;
		foreach ($activity_types as $type) {
			$activities = Activity::where('activity_type_id',$type->id)
				->where('cycle_id',$cycle->id)
				->where('status_id','>',7)
				->where('disable',0)
				->get();
			if (App::isLocal())
			{
			    $nofile = 'public/nofile';
			}else{
				$nofile = storage_path().'/nofile';
			}
			
			if(count($activities) > 0){
				foreach ($activities as $activity) {
					$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
					$distination = storage_path().$path ;
					$files = File::files($distination);
					if(count($files)>0){
						if (App::isLocal())
						{
						    $folder = 'app/storage/'.$path.'/';
						}else{
							$folder = storage_path().$path.'/';
						}
						$with_files = true;
						// $folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.$activity->id.'_'.strtoupper(Helper::sanitize($activity->circular_name))] = $folder;
						$foldername = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', $activity->circular_name);
						$folder_name = str_replace(":","_", $foldername);
						$folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.strtoupper(Helper::sanitize($folder_name))] = $folder;
					}else{
						// $with_files = true;
						// $folder = $nofile;
						// $folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.$activity->id.'_'.strtoupper(Helper::sanitize($activity->circular_name))] = $folder;
					}
					
				}
			}else{
				//$folders[strtoupper(Helper::sanitize($type->activity_type))] = $nofile;
			}
			
		}
		if($with_files){
			// dd($folders);
			$archive = $zippy->create($zip_path,$folders, true);
			return Response::download($zip_path);
		}else{
			return View::make('downloads.norecordfound');
		}

	}

	public function released($id){
		$zippy = Zippy::load();
		$cycle = Cycle::where('id',$id)->first();
		$activity_types = ActivityType::all();
		$zip_path = storage_path().'/zipped/cycles/'.$cycle->cycle_name.'.zip';
		File::delete($zip_path);
		$with_files = false;
		foreach ($activity_types as $type) {
			$activities = Activity::where('activity_type_id',$type->id)
				->where('cycle_id',$cycle->id)
				->where('status_id','>',7)
				->where('disable',0)
				->get();
			if (App::isLocal())
			{
			    $nofile = 'public/nofile';
			}else{
				$nofile = storage_path().'/nofile';
			}
			
			if(count($activities) > 0){
				foreach ($activities as $activity) {
					$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
					$distination = storage_path().$path ;
					$files = File::files($distination);
					if(count($files)>0){
						if (App::isLocal())
						{
						    $folder = 'app/storage/'.$path.'/';
						}else{
							$folder = storage_path().$path.'/';
						}
						$with_files = true;
						// $folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.$activity->id.'_'.strtoupper(Helper::sanitize($activity->circular_name))] = $folder;
						$foldername = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', $activity->circular_name);
						$folder_name = str_replace(":","_", $foldername);
						$folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.strtoupper(Helper::sanitize($folder_name))] = $folder;
					}else{
						// $with_files = true;
						// $folder = $nofile;
						// $folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.$activity->id.'_'.strtoupper(Helper::sanitize($activity->circular_name))] = $folder;
					}
					
				}
			}else{
				//$folders[strtoupper(Helper::sanitize($type->activity_type))] = $nofile;
			}
			
		}
		if($with_files){
				
			$archive = $zippy->create($zip_path,$folders, true);
			return Response::download($zip_path);
		}else{
			return View::make('downloads.norecordfound');
		}

	}

	public function downloadall($id){
		$zippy = Zippy::load();
		$cycle = Cycle::where('id',$id)->first();
		$activity_types = ActivityType::all();
		$zip_path = storage_path().'/zipped/cycles/'.$cycle->cycle_name.'.zip';
		File::delete($zip_path);
		$with_files = false;
		foreach ($activity_types as $type) {
			$activities = Activity::where('activity_type_id',$type->id)
				->where('cycle_id',$cycle->id)
				->get();
			if (App::isLocal())
			{
			    $nofile = 'public/nofile';
			}else{
				$nofile = storage_path().'/nofile';
			}
			
			if(count($activities) > 0){
				foreach ($activities as $activity) {
					$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
					$distination = storage_path().$path ;
					$files = File::files($distination);
					if(count($files)>0){
						if (App::isLocal())
						{
						    $folder = 'app/storage/'.$path.'/';
						}else{
							$folder = storage_path().$path.'/';
						}
						$with_files = true;
						// $folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.$activity->id.'_'.strtoupper(Helper::sanitize($activity->circular_name))] = $folder;
						$foldername = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', $activity->circular_name);
						$folder_name = str_replace(":","_", $foldername);
						$folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.strtoupper(Helper::sanitize($folder_name))] = $folder;
					}else{
						// $with_files = true;
						// $folder = $nofile;
						// $folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.$activity->id.'_'.strtoupper(Helper::sanitize($activity->circular_name))] = $folder;
					}
					
				}
			}else{
				//$folders[strtoupper(Helper::sanitize($type->activity_type))] = $nofile;
			}
			
		}
		if($with_files){
				
			$archive = $zippy->create($zip_path,$folders, true);
			return Response::download($zip_path);
		}else{
			return View::make('downloads.norecordfound');
		}

	}

	public function downloadcycle($id){
		$zippy = Zippy::load();
		$cycle = Cycle::where('id',$id)->first();
		$activity_types = ActivityType::all();
		$zip_path = storage_path().'/zipped/cycles/'.$cycle->cycle_name.'.zip';
		File::delete($zip_path);
		$with_files = false;
		foreach ($activity_types as $type) {
			$activities = Activity::where('activity_type_id',$type->id)
				->where('cycle_id',$cycle->id)
				->where('status_id','>',7)
				->where('disable',0)
				->get();
			if (App::isLocal())
			{
			    $nofile = 'public/nofile';
			}else{
				$nofile = storage_path().'/nofile';
			}
			
			if(count($activities) > 0){
				foreach ($activities as $activity) {
					$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
					$distination = storage_path().$path ;
					$files = File::files($distination);
					if(count($files)>0){
						if (App::isLocal())
						{
						    $folder = 'app/storage/'.$path.'/';
						}else{
							$folder = storage_path().$path.'/';
						}
						$with_files = true;
						// $folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.$activity->id.'_'.strtoupper(Helper::sanitize($activity->circular_name))] = $folder;
						$foldername = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', $activity->circular_name);
						$folder_name = str_replace(":","_", $foldername);
						$folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.strtoupper(Helper::sanitize($folder_name))] = $folder;
					}else{
						// $with_files = true;
						// $folder = $nofile;
						// $folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.$activity->id.'_'.strtoupper(Helper::sanitize($activity->circular_name))] = $folder;
					}
					
				}
			}else{
				//$folders[strtoupper(Helper::sanitize($type->activity_type))] = $nofile;
			}
			
		}
		
		if($with_files){
			$archive = $zippy->create($zip_path,$folders, true);
			return Response::download($zip_path);
		}else{
			echo 'File not found';
			// return Response::view('errors.missing', array(), 404);
			// return View::make('downloads.norecordfound');
		}

	}

	public function letemplates(){
		Input::flash();
		if(Auth::user()->hasRole("ADMINISTRATOR")){
			$cycles = Cycle::getReleasedCyclesWithTradeDeal(Input::get('search'));
			return View::make('downloads.cyclesledmin',compact('cycles'));
		}else{
			$cycles = Cycle::getReleasedCyclesWithTradeDeal(Input::get('search'), Auth::user);
			return View::make('downloads.cyclesle',compact('cycles'));
		}
	}

	public function downloadletemplates($id){
		$zippy = Zippy::load();
		$cycle = Cycle::where('id',$id)->first();
		$user_id = Auth::user()->id;
		$folder_path = storage_path().'/zipped/le/'.$user_id;

		if(!File::exists($folder_path)) {
			File::makeDirectory($folder_path);
		}

		$zip_path = $folder_path.'/'.$cycle->cycle_name.'.zip';

		File::delete($zip_path);
		$with_files = false;

		$activities = Activity::select('activities.id', 'activities.circular_name')
			->join('activity_types', 'activity_types.id', '=', 'activities.activity_type_id')
			->where('activity_types.with_tradedeal',1)
			->where('activities.created_by', $user_id)
			->where('cycle_id',$cycle->id)
			->where('status_id',9)
			->where('disable',0)
			->get();

		

		if(count($activities) > 0){
			$folders = [];
			foreach ($activities as $activity) {
				$tradedeal = Tradedeal::getActivityTradeDeal($activity);

				$tradealschemes = TradedealScheme::where('tradedeal_id', $tradedeal->id)->get();

				$foldername = preg_replace('/[^A-Za-z0-9 _ .-]/', '_', $activity->circular_name);
				$folder_name = str_replace(":","_", $foldername);

				$distination = storage_path().'/le/'.$activity->id ;

				foreach ($tradealschemes as $scheme) {
					$_path = $distination.'/'.$scheme->id;
					$folders[$folder_name. '/'.$scheme->name] = $_path;
				}
			}
			// dd($folders);
			$archive = $zippy->create($zip_path,$folders,true);
			return Response::download($zip_path);
		}


		
	}
}