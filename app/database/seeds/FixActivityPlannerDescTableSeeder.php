<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivityPlannerDescTableSeeder extends Seeder {

	public function run()
	{
		$planners= ActivityPlanner::all();
		foreach($planners as $planner){
			$user = User::find($planner->user_id);
			$planner->planner_desc = $user->getFullname();
			$planner->contact_no = $user->contact_no;
			$planner->update();
		}
	}

}