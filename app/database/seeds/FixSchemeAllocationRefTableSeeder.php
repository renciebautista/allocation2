<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixSchemeAllocationRefTableSeeder extends Seeder {

	public function run()
	{
		$schemes = Scheme::all();
		foreach ($schemes as $scheme) {
			if($scheme->compute == 0){
				$scheme->compute = 3;
				$scheme->update();
			}
		}
	}

}