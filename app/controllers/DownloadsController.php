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
		$folders = array();
		$zip_path = storage_path().'/zipped/cycles/'.$cycle->cycle_name.'.zip';
		File::delete($zip_path);

		foreach ($activity_types as $type) {
			$activities = Activity::where('activity_type_id',$type->id)
				->where('cycle_id',$cycle->id)
				->where('status_id',8)
				->get();
			$nofile = 'public/nofile/robots.txt';
			if(count($activities) > 0){
				foreach ($activities as $activity) {
					$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
					$distination = storage_path().$path ;
					$files = File::files($distination);
					if(count($files)>0){
						$folder = 'app/storage/'.$path.'/';
					}else{
						$folder = $nofile;
					}
					$folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.$activity->id.'_'.strtoupper(Helper::sanitize($activity->circular_name))] = $folder;
				}
			}else{
				$folders[strtoupper(Helper::sanitize($type->activity_type))] = $nofile;
			}
		}
		Helper::print_array($folders);
		$archive = $zippy->create($zip_path,$folders, true);
		return Response::download($zip_path);
	}
}