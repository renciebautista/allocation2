<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class CyclesTableSeeder extends Seeder {

	public function run()
	{
		Excel::selectSheets('cycles')->load(app_path().'/database/seeds/seed_files/masterfile2.xlsx', function($reader) {
			Cycle::batchInsert($reader->ignoreEmpty());
		});
	}

}