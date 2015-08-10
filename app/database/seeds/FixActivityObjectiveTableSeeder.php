<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivityObjectiveTableSeeder extends Seeder {

	public function run()
	{
		$activityobjectives = ActivityObjective::all();
		foreach($activityobjectives as $activityobjective){
			$objective = Objective::find($activityobjective->objective_id);
			$activityobjective->objective_desc = $objective->objective;
			$activityobjective->update();
		}
	}

}