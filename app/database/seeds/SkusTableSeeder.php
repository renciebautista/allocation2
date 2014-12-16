<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class SkusTableSeeder extends Seeder {

	public function run()
	{
		DB::table('skus')->truncate();
		
		Excel::selectSheets('sku')->load(app_path().'/database/seeds/seed_files/masterfile2.xlsx', function($reader) {
			Sku::batchInsert($reader->get());
		});
	}

}