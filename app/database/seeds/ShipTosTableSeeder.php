<?php

// Composer: "fzaninotto/faker": "v1.3.0"
// use Faker\Factory as Faker;

class ShipTosTableSeeder extends Seeder {

	public function run()
	{
		DB::table('ship_tos')->truncate();
		
		Excel::selectSheets('ship_to')->load(app_path().'/database/seeds/seed_files/masterfile.xlsx', function($reader) {
			ShipTo::batchInsert($reader->get());
		});
	}

}