<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixUntallyAllocationTableSeeder extends Seeder {

	public function run()
	{	$total_activities = 0;
		$total_schemes = 0;
		$timeFirst  = strtotime(date('Y-m-d H:i:s'));
		
		$activities = Activity::where('status_id', '<', 9)->get();
		foreach ($activities as $activity) {
			$total_activities++;
			$schemes = Scheme::where('activity_id',$activity->id)->get();
			foreach ($schemes as $scheme) {
				echo $scheme->id .PHP_EOL;
				$total_schemes++;
				SchemeAllocRepository::fixUntallyAllocation($scheme);
				
			}
		}
		$timeSecond = strtotime(date('Y-m-d H:i:s'));
		$differenceInSeconds = $timeSecond - $timeFirst;
		echo 'Time used ' . $differenceInSeconds . " sec" .PHP_EOL;
		echo 'Total activities:' .$total_activities .PHP_EOL;
		echo 'Total schemes:' .$total_schemes .PHP_EOL;
	}

}