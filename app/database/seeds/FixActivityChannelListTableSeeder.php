<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivityChannelListTableSeeder extends Seeder {

	public function run()
	{
		$activities = Activity::all();
		foreach ($activities as $activity) {
			$activity_channels = ActivityChannel2::where('activity_id',$activity->id)->get();
			ActivityChannelList::addChannel($activity->id,$activity_channels);
		}
	}

}