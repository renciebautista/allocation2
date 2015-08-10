<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class FixActivityMaterialTableSeeder extends Seeder {

	public function run()
	{
		$activitymaterials = ActivityMaterial::all();
		foreach($activitymaterials  as $activitymaterial){
			$source = MaterialSource::find($activitymaterial->source_id);
			$activitymaterial->source_desc = $source->source;
			$activitymaterial->update();
		}
	}

}