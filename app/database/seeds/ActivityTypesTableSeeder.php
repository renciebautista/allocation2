<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class ActivityTypesTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('activity_types')->truncate();
		
		Excel::selectSheets('activity_type')->load(app_path().'/database/seeds/seed_files/masterfile2.xlsx', function($reader) {
			ActivityType::batchInsert($reader->ignoreEmpty());
		});
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}