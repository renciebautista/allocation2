<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class ActivityGroupingTableSeeder extends Seeder {

	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('activity_groupings')->truncate();
		
		Excel::selectSheets('activity_grouping')->load(app_path().'/database/seeds/seed_files/masterfile2.xlsx', function($reader) {
			ActivityGrouping::batchInsert($reader->ignoreEmpty());
		});

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}