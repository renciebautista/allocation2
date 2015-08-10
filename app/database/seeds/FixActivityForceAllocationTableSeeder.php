<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivityForceAllocationTableSeeder extends Seeder {

	public function run()
	{
		$forceallocs = ForceAllocation::all();
		foreach ($forceallocs as $forcealloc) {
			$area = Area::getArea($forcealloc->area_code);
			$forcealloc->group_code = $area->group_code;
			$forcealloc->group_desc = $area->group_name;
			$forcealloc->area_desc = $area->area_name;
			$forcealloc->update();
		}
	}

}