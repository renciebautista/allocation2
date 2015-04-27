<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class AreasTableSeeder extends Seeder {

	public function run()
	{
		DB::table('areas')->truncate();
		Excel::selectSheets('area')->load(app_path().'/database/seeds/seed_files/masterfile.xlsx', function($reader) {
			Area::batchInsert($reader->get());
		});
	}

}