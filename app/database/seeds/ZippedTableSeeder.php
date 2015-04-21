<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Alchemy\Zippy\Zippy;

class ZippedTableSeeder extends Seeder {

	public function run()
	{
		// $zippy = Zippy::load();
		// // creates an archive.zip that contains a directory "folder" that contains
		// // files contained in "/path/to/directory" recursively

		// $cycle = Cycle::where('cycle_name','MARCH 2015')->first();
		// $activity_types = ActivityType::all();
		// $folders = array();
		
		// foreach ($activity_types as $type) {
		// 	$activities = Activity::where('activity_type_id',$type->id)->get();
		// 	$nofile = 'public/nofile/';
		// 	if(count($activities) > 0){
		// 		foreach ($activities as $activity) {
		// 			$path = '/uploads/'.$activity->cycle_id.'/'.$activity->activity_type_id.'/'.$activity->id;
		// 			$distination = storage_path().$path ;
		// 			$files = File::files($distination);
		// 			if(count($files)>0){
		// 				$folder = 'app/storage/'.$path.'/';
		// 			}else{
		// 				$folder = $nofile;
		// 			}
		// 			$folders[strtoupper(Helper::sanitize($type->activity_type)).'/'.strtoupper(Helper::sanitize($activity->circular_name))] = $folder;
		// 		}
		// 	}else{
		// 		$folders[strtoupper(Helper::sanitize($type->activity_type))] = $nofile;
		// 	}
		// }
		
		// // Helper::print_array($folders);
		// $archive = $zippy->create(storage_path().'/zipped/'.$cycle->cycle_name.'.zip',$folders, true);
	}

}