<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class OutletsTableSeeder extends Seeder {

	public function run()
	{
		Excel::selectSheets('outlet')->load(app_path().'/database/seeds/seed_files/masterfile.xlsx', function($reader) {
			Outlet::batchInsert($reader->ignoreEmpty());
		});
	}

}