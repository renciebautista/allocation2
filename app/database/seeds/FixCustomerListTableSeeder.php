<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixCustomerListTableSeeder extends Seeder {

	public function run()
	{
		$activities = Activity::all();
		foreach ($activities as $activity) {
			$activity_customers = ActivityCustomer::where('activity_id',$activity->id)->get();
			ActivityCutomerList::addCustomer($activity->id,$activity_customers);
		}
	}

}