<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivityDivisionTableSeeder extends Seeder {

	public function run()
	{
		$activitydivisions = ActivityDivision::all();
		foreach($activitydivisions  as $activitydivision){
			$division = Sku::division($activitydivision->division_code);
			$activitydivision->division_desc = $division->division_desc;
			$activitydivision->update();
		}
	}

}