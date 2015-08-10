<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivityDetailsTableSeeder extends Seeder {

	public function run()
	{
		$activities = Activity::all();
		foreach($activities  as $activity){
			$scope = ScopeType::find($activity->scope_type_id);
			$cycle = Cycle::find($activity->cycle_id);
			$user = User::find($activity->created_by);
			$activitytype = ActivityType::find($activity->activity_type_id);

			$activity->proponent_name = strtoupper($user->first_name).' '.strtoupper($user->last_name);
			$activity->contact_no = $user->contact_no;
			$activity->scope_desc = $scope->scope_name;
			$activity->cycle_desc = $cycle->cycle_name;
			$activity->activitytype_desc = $activitytype->activity_type;
			$activity->uom_desc = $activitytype->uom;
			$activity->update();
		}
	}

}