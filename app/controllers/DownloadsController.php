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
		$cycles = Cycle::search(Input::get('search'));
		return View::make('downloads.cycles',compact('cycles'));
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
				->where('status_id',8)
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
						$folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.$activity->id.'_'.strtoupper(Helper::sanitize($activity->circular_name))] = $folder;
						//$folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.strtoupper(Helper::sanitize($activity->circular_name))] = $folder;
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
				->where('status_id',8)
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
						$folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.$activity->id.'_'.strtoupper(Helper::sanitize($activity->circular_name))] = $folder;
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
}