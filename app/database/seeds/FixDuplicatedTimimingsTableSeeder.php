<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixDuplicatedTimimingsTableSeeder extends Seeder {

	public function run()
	{
		$activitytimings = ActivityTiming::all();

		foreach ($activitytimings as $key => $timing) {
			$activity = Activity::find($timing->activity_id);
			
			$network = ActivityTypeNetwork::where('activitytype_id',$activity->activity_type_id)
				->where('milestone',$timing->milestone)
				->where('task',$timing->task)
				->first();
			$timing->show = $network->show;
			$timing->update();
		}
	}

}